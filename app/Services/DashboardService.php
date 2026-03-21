<?php

namespace App\Services;

use App\Models\Households;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    private function normalizePurok($purokId)
    {
        return (!$purokId || strtolower($purokId) === 'all') ? 'all' : $purokId;
    }

    private function filterPurok($query, $purokId, $column = 'households.purok_id')
    {
        if ($purokId === 'all') return $query;
        return $query->where($column, $purokId);
    }

    private function baseResidentsQuery($purokId)
    {
        return $this->filterPurok(
            DB::table('residents')
                ->join('households', 'residents.household_id', '=', 'households.id'),
            $purokId
        );
    }

    private function baseCertificatesQuery($purokId, $year)
    {
        return $this->filterPurok(
            DB::table('certificate_requests')
                ->join('residents', 'certificate_requests.resident_id', '=', 'residents.id')
                ->join('households', 'residents.household_id', '=', 'households.id')
                ->whereYear('certificate_requests.requested_at', $year),
            $purokId
        );
    }

    public function getCards($purokId, $year)
    {
        $purokId = $this->normalizePurok($purokId);
        $year = $year ?: date('Y');

        return Cache::remember("cards:$purokId:$year", 300, function () use ($purokId, $year) {

            $stats = $this->baseResidentsQuery($purokId)
                ->selectRaw("
                    COUNT(*) as total,
                    SUM(CASE WHEN residents.is_voter = 1 THEN 1 ELSE 0 END) as voters,
                    SUM(CASE WHEN residents.BirthDate <= DATE_SUB(CURDATE(), INTERVAL 60 YEAR) THEN 1 ELSE 0 END) as senior
                ")
                ->first();

            $households = Households::when($purokId !== 'all', fn($q) => $q->where('purok_id', $purokId))->count();

            return [
                'residents' => $stats->total ?? 0,
                'households' => $households,
                'voters' => $stats->voters ?? 0,
                'senior' => $stats->senior ?? 0,
                'avg_household_size' => round(($stats->total ?? 0) / max($households, 1), 2),
                'revenue' => $this->getRevenue($purokId, $year),
            ];
        });
    }

    public function getGender($purokId)
    {
        return $this->baseResidentsQuery($purokId)
            ->selectRaw('residents.gender, COUNT(*) as total')
            ->groupBy('residents.gender')
            ->pluck('total', 'residents.gender');
    }

    public function getAgeGroups($purokId)
    {
        return $this->baseResidentsQuery($purokId)
            ->selectRaw("
                CASE
                    WHEN residents.BirthDate >= DATE_SUB(CURDATE(), INTERVAL 12 YEAR) THEN 'Child'
                    WHEN residents.BirthDate >= DATE_SUB(CURDATE(), INTERVAL 17 YEAR) THEN 'Teen'
                    WHEN residents.BirthDate >= DATE_SUB(CURDATE(), INTERVAL 59 YEAR) THEN 'Adult'
                    ELSE 'Senior'
                END as age_group,
                COUNT(*) as total
            ")
            ->groupBy('age_group')
            ->pluck('total', 'age_group');
    }

    public function getCivilStatus($purokId)
    {
        return $this->baseResidentsQuery($purokId)
            ->selectRaw('residents.CivilStatus, COUNT(*) as total')
            ->groupBy('residents.CivilStatus')
            ->pluck('total', 'CivilStatus');
    }

    public function getPurok($purokId)
    {
        return $this->filterPurok(
            DB::table('households')
                ->join('puroks', 'households.purok_id', '=', 'puroks.id')
                ->join('residents', 'residents.household_id', '=', 'households.id'),
            $purokId
        )
            ->selectRaw('puroks.PurokName as name, COUNT(residents.id) as total')
            ->groupBy('puroks.PurokName')
            ->pluck('total', 'name');
    }

    public function getCertificateStats($purokId, $year)
    {
        $purokId = $this->normalizePurok($purokId);
        $year = $year ?: date('Y');

        return collect(Cache::remember("cert_stats:$purokId:$year", 300, function () use ($purokId, $year) {

            return $this->baseCertificatesQuery($purokId, $year)
                ->whereBetween('certificate_requests.requested_at', [
                    "$year-01-01",
                    "$year-12-31 23:59:59"
                ])
                ->select('certificate_requests.remark', DB::raw('COUNT(*) as total'))
                ->groupBy('certificate_requests.remark')
                ->pluck('total', 'certificate_requests.remark')
                ->toArray();

        }));
    }

    public function getMonthlyTransactions($purokId, $year)
    {
        $purokId = $this->normalizePurok($purokId);
        $year = $year ?: date('Y');

        return collect(Cache::remember("monthly_tx:$purokId:$year", 300, function () use ($purokId, $year) {

            $data = $this->baseCertificatesQuery($purokId, $year)
                ->whereBetween('certificate_requests.requested_at', [
                    "$year-01-01",
                    "$year-12-31 23:59:59"
                ])
                ->selectRaw('MONTH(certificate_requests.requested_at) as month, COUNT(*) as total')
                ->groupByRaw('MONTH(certificate_requests.requested_at)')
                ->pluck('total', 'month')
                ->toArray();

            return collect(range(1, 12))
                ->mapWithKeys(fn ($m) => [$m => (int) ($data[$m] ?? 0)])
                ->toArray();

        }));
    }

    public function getRevenue($purokId, $year)
    {
        return $this->filterPurok(
            DB::table('certificate_requests')
                ->join('certificates', 'certificate_requests.id', '=', 'certificates.request_id')
                ->join('residents', 'certificate_requests.resident_id', '=', 'residents.id')
                ->join('households', 'residents.household_id', '=', 'households.id')
                ->whereYear('certificates.created_at', $year),
            $purokId
        )->sum('certificates.Fee');
    }

    public function format($collection)
    {
        return [
            'labels' => $collection->keys()->values(),
            'data' => $collection->values()->values()
        ];
    }
}
