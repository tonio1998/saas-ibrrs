<?php

namespace App\Services;

use App\Models\Households;
use App\Models\Residents;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getData($purokId = 'all')
    {
        return Cache::remember($this->cacheKey($purokId), 60, function () use ($purokId) {

            return [
                'cards' => $this->getCards($purokId),
                'charts' => [
                    'gender' => $this->getGender($purokId),
                    'purok' => $this->getPurok($purokId),
                ]
            ];
        });
    }

    private function cacheKey($purokId)
    {
        return "dashboard:v1:purok:{$purokId}";
    }

    private function baseResidentsQuery($purokId)
    {
        return Residents::when($purokId !== 'all', function ($q) use ($purokId) {
            $q->whereHas('household', fn($h) => $h->where('purok_id', $purokId));
        });
    }

    private function baseHouseholdQuery($purokId)
    {
        return Households::when($purokId !== 'all', fn($q) =>
        $q->where('purok_id', $purokId)
        );
    }

    private function getCards($purokId)
    {
        return [
            'residents' => $this->baseResidentsQuery($purokId)->count(),
            'households' => $this->baseHouseholdQuery($purokId)->count(),
        ];
    }

    private function getGender($purokId)
    {
        return $this->baseResidentsQuery($purokId)
            ->selectRaw('gender, COUNT(*) as total')
            ->groupBy('gender')
            ->pluck('total', 'gender');
    }

    private function getPurok($purokId)
    {
        return Residents::query()
            ->join('households', 'residents.household_id', '=', 'households.id')
            ->join('puroks', 'households.purok_id', '=', 'puroks.id')
            ->when($purokId !== 'all', fn($q) =>
            $q->where('households.purok_id', $purokId)
            )
            ->selectRaw('puroks.PurokName as name, COUNT(residents.id) as total')
            ->groupBy('puroks.PurokName')
            ->pluck('total', 'name');
    }
}
