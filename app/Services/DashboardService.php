<?php

namespace App\Services;

use App\Models\Households;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getData($purokId = 'all', $year = null)
    {
        $purokId = $this->normalizePurok($purokId);
        $year = $year ?: date('Y');

        return [
            'cards' => $this->getCards($purokId),

            'charts' => [
                'gender' => $this->format($this->getGender($purokId)),
                'purok' => $this->format($this->getPurok($purokId)),
                'age_groups' => $this->format($this->getAgeGroups($purokId)),
                'civil_status' => $this->format($this->getCivilStatus($purokId)),
            ],

            'operations' => [
                'certificates' => $this->format($this->getCertificateStats($purokId, $year)),
                'monthly' => $this->format($this->getMonthlyTransactions($purokId, $year)),
                'revenue' => $this->getRevenue($purokId, $year),
            ]
        ];
    }

    private function normalizePurok($purokId)
    {
        return (!$purokId || strtolower($purokId) === 'all') ? 'all' : $purokId;
    }

    private function filterPurok($query, $purokId, $column = 'households.purok_id')
    {
        if ($purokId === 'all') return $query;

        try {
            return $query->where($column, $purokId);
        } catch (\Throwable $e) {
            return $query;
        }
    }

    private function getCards($purokId)
    {
        $residents = DB::table('residents')
            ->join('households', 'residents.household_id', '=', 'households.id');

        $households = Households::query();

        $residents = $this->filterPurok($residents, $purokId);
        $households = $households->when($purokId !== 'all', fn($q) =>
        $q->where('households.purok_id', $purokId)
        );

        $totalResidents = (clone $residents)->count();
        $totalHouseholds = (clone $households)->count();

        return [
            'residents' => $totalResidents,
            'households' => $totalHouseholds,
            'voters' => (clone $residents)->where('residents.is_voter', 1)->count(),
            'senior' => (clone $residents)
                ->whereRaw('TIMESTAMPDIFF(YEAR, residents.BirthDate, CURDATE()) >= 60')
                ->count(),
            'avg_household_size' => round($totalResidents / max($totalHouseholds, 1), 2),
        ];
    }

    private function getGender($purokId)
    {
        $query = DB::table('residents')
            ->join('households', 'residents.household_id', '=', 'households.id');

        $query = $this->filterPurok($query, $purokId);

        return $query
            ->selectRaw('residents.gender, COUNT(*) as total')
            ->groupBy('residents.gender')
            ->pluck('total', 'residents.gender');
    }

    private function getAgeGroups($purokId)
    {
        $query = DB::table('residents')
            ->join('households', 'residents.household_id', '=', 'households.id');

        $query = $this->filterPurok($query, $purokId);

        return $query
            ->selectRaw("
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, residents.BirthDate, CURDATE()) <= 12 THEN 'Child'
                    WHEN TIMESTAMPDIFF(YEAR, residents.BirthDate, CURDATE()) BETWEEN 13 AND 17 THEN 'Teen'
                    WHEN TIMESTAMPDIFF(YEAR, residents.BirthDate, CURDATE()) BETWEEN 18 AND 59 THEN 'Adult'
                    ELSE 'Senior'
                END as age_group,
                COUNT(*) as total
            ")
            ->groupBy('age_group')
            ->pluck('total', 'age_group');
    }

    private function getCivilStatus($purokId)
    {
        $query = DB::table('residents')
            ->join('households', 'residents.household_id', '=', 'households.id');

        $query = $this->filterPurok($query, $purokId);

        return $query
            ->selectRaw('residents.CivilStatus, COUNT(*) as total')
            ->groupBy('residents.CivilStatus')
            ->pluck('total', 'CivilStatus');
    }

    private function getPurok($purokId)
    {
        $query = DB::table('households')
            ->join('puroks', 'households.purok_id', '=', 'puroks.id')
            ->join('residents', 'residents.household_id', '=', 'households.id');

        $query = $this->filterPurok($query, $purokId);

        return $query
            ->selectRaw('puroks.PurokName as name, COUNT(residents.id) as total')
            ->groupBy('puroks.PurokName')
            ->pluck('total', 'name');
    }

    private function getCertificateStats($purokId, $year)
    {
        $query = DB::table('certificate_requests')
            ->join('residents', 'certificate_requests.resident_id', '=', 'residents.id')
            ->join('households', 'residents.household_id', '=', 'households.id');

        $query = $this->filterPurok($query, $purokId);

        return $query
            ->whereYear('certificate_requests.requested_at', $year)
            ->selectRaw('certificate_requests.remark, COUNT(*) as total')
            ->groupBy('certificate_requests.remark')
            ->pluck('total', 'remark');
    }

    private function getMonthlyTransactions($purokId, $year)
    {
        $query = DB::table('certificate_requests')
            ->join('residents', 'certificate_requests.resident_id', '=', 'residents.id')
            ->join('households', 'residents.household_id', '=', 'households.id');

        $query = $this->filterPurok($query, $purokId);

        $data = $query
            ->selectRaw('MONTH(certificate_requests.requested_at) as month, COUNT(*) as total')
            ->whereYear('certificate_requests.requested_at', $year)
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->pluck('total', 'month');

        $months = collect(range(1, 12))->mapWithKeys(fn($m) => [$m => $data[$m] ?? 0]);

        return $months;
    }

    private function getRevenue($purokId, $year)
    {
        $query = DB::table('certificate_requests')
            ->join('certificate_types', 'certificate_requests.certificate_type_id', '=', 'certificate_types.id')
            ->join('residents', 'certificate_requests.resident_id', '=', 'residents.id')
            ->join('households', 'residents.household_id', '=', 'households.id')
            ->where('certificate_requests.remark', 'Released')
            ->whereYear('certificate_requests.requested_at', $year);

        $query = $this->filterPurok($query, $purokId);

        return $query->sum('certificate_types.fee');
    }

    private function format($collection)
    {
        return [
            'labels' => $collection->keys()->values(),
            'data' => $collection->values()->values()
        ];
    }
}
