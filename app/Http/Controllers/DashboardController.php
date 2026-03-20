<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('pages.dashboard.index');
    }

    public function data(Request $request, DashboardService $service)
    {
        $purokId = $request->input('purok_id', 'all');
        $year = $request->input('year', date('Y'));

        return response()->json(
            $service->getData($purokId, $year)
        );
    }
}
