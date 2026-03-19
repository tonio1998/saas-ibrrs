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

    public function data(Request $request, DashboardService $service)
    {
        $purokId = $request->input('purok_id', 'all');

        return response()->json(
            $service->getData($purokId)
        );
    }
}
