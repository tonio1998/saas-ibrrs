@extends('layouts.app')
@section('title','Dashboard')

@section('content')

    <x-page-header
        title="Dashboard"
        subtitle="System analytics overview"
    />

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">

        <select id="purokFilter" class="form-select form-select-sm filter-select">
            <option value="all">All Purok</option>
            @foreach(\App\Models\Puroks::orderBy('PurokNo')->get() as $p)
                <option value="{{ $p->id }}">{{ $p->PurokName }}</option>
            @endforeach
        </select>

        <div class="live-indicator">
            <span></span> Live data
        </div>

    </div>

    <div id="dashboardLoader" class="text-center py-5">
        <div class="spinner-border"></div>
    </div>

    <div id="dashboardContent">

        <div class="row g-4">

            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon bg-primary-soft">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <div class="stat-label">Residents</div>
                        <div class="stat-value" id="residentsCount">0</div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon bg-success-soft">
                        <i class="bi bi-house"></i>
                    </div>
                    <div>
                        <div class="stat-label">Households</div>
                        <div class="stat-value" id="householdsCount">0</div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row g-4 mt-1">

            <div class="col-lg-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <div>Gender Distribution</div>
                    </div>
                    <canvas id="genderChart"></canvas>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <div>Population by Purok</div>
                    </div>
                    <canvas id="purokChart"></canvas>
                </div>
            </div>

        </div>

    </div>

@endsection

@push('styles')
    <style>

        .filter-select {
            width: 200px;
            border-radius: 10px;
            padding: 6px 10px;
        }

        .live-indicator {
            font-size: 13px;
            color: #777;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .live-indicator span {
            width: 8px;
            height: 8px;
            background: #00c853;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { opacity: .3; transform: scale(.8); }
            50% { opacity: 1; transform: scale(1); }
            100% { opacity: .3; transform: scale(.8); }
        }

        .stat-card {
            display: flex;
            align-items: center;
            gap: 14px;
            background: #fff;
            border-radius: 18px;
            padding: 18px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.05);
            transition: .25s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 35px rgba(0,0,0,0.08);
        }

        .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .bg-primary-soft {
            background: rgba(13,110,253,0.1);
            color: #0d6efd;
        }

        .bg-success-soft {
            background: rgba(25,135,84,0.1);
            color: #198754;
        }

        .stat-label {
            font-size: 13px;
            color: #888;
        }

        .stat-value {
            font-size: 30px;
            font-weight: 600;
            margin-top: 2px;
        }

        .chart-card {
            background: #fff;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 8px 28px rgba(0,0,0,0.05);
            transition: .25s ease;
        }

        .chart-card:hover {
            box-shadow: 0 14px 35px rgba(0,0,0,0.08);
        }

        .chart-header {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 14px;
            color: #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        canvas {
            max-height: 280px;
        }

    </style>
@endpush
