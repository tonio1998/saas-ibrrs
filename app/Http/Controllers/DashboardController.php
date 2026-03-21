<?php

namespace App\Http\Controllers;

use App\Models\Households;
use App\Models\Residents;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('pages.dashboard.index');
    }

    public function cards(Request $r, DashboardService $s)
    {
        return response()->json(
            $s->getCards($r->purok_id, $r->year)
        );
    }

    public function charts(Request $r, DashboardService $s)
    {
        return response()->json([
            'gender' => $s->format($s->getGender($r->purok_id)),
            'purok' => $s->format($s->getPurok($r->purok_id)),
            'age_groups' => $s->format($s->getAgeGroups($r->purok_id)),
            'civil_status' => $s->format($s->getCivilStatus($r->purok_id)),
        ]);
    }

    public function operations(Request $r, DashboardService $s)
    {
        $purok = $r->purok_id;
        $year = $r->year ?: date('Y');

        $cert = $s->getCertificateStats($purok, $year);
        $monthly = $s->getMonthlyTransactions($purok, $year);

        if ($cert->isEmpty()) {
            $cert = collect(['No Data' => 0]);
        }

        if ($monthly->isEmpty()) {
            $monthly = collect(range(1,12))->mapWithKeys(fn($m)=>[$m=>0]);
        }

        return response()->json([
            'certificates' => $s->format($cert),
            'monthly' => $s->format($monthly),
        ]);
    }
}
