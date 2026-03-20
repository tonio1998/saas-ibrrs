@extends('layouts.app')
@section('title','Dashboard')

@section('content')

    <x-page-header title="Dashboard" subtitle="System analytics overview" />

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div class="d-flex">
            <select id="purokFilter" class="form-select form-select-sm filter-select me-2">
                <option value="all">All Purok</option>
                @foreach(\App\Models\Puroks::orderBy('PurokNo')->get() as $p)
                    <option value="{{ $p->id }}">{{ $p->PurokName }}</option>
                @endforeach
            </select>

            <select id="yearFilter" class="form-select form-select-sm filter-select">
                @php
                    $currentYear = date('Y');
                @endphp

                @for($year = $currentYear; $year >= $currentYear - 5; $year--)
                    <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endfor
            </select>
        </div>

        <div class="live-indicator">
            <span></span> Live data
        </div>
    </div>

    <div class="dashboard-wrapper position-relative">

        <div id="dashboardLoader" class="dashboard-overlay d-none">
            <div class="loader-box">
                <div class="spinner-border"></div>
                <div class="mt-2 small text-muted">Updating data...</div>
            </div>
        </div>

        <div id="dashboardContent">

            <div class="row g-3">

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="stat-card compact">
                        <div class="stat-icon bg-primary-soft"><i class="bi bi-people"></i></div>
                        <div>
                            <div class="stat-value" id="residentsCount">
                                <span class="spinner-border spinner-border-sm"></span>
                            </div>
                            <div class="stat-label">Residents</div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="stat-card compact">
                        <div class="stat-icon bg-success-soft"><i class="bi bi-house"></i></div>
                        <div>
                            <div class="stat-value" id="householdsCount">
                                <span class="spinner-border spinner-border-sm"></span>
                            </div>
                            <div class="stat-label">Households</div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="stat-card compact">
                        <div class="stat-icon bg-warning-soft"><i class="bi bi-check2-square"></i></div>
                        <div>
                            <div class="stat-value" id="votersCount">
                                <span class="spinner-border spinner-border-sm"></span>
                            </div>
                            <div class="stat-label">Voters</div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="stat-card compact">
                        <div class="stat-icon bg-danger-soft"><i class="bi bi-person-badge"></i></div>
                        <div>
                            <div class="stat-value" id="seniorCount">
                                <span class="spinner-border spinner-border-sm"></span>
                            </div>
                            <div class="stat-label">Senior</div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="stat-card compact">
                        <div class="stat-icon bg-info-soft"><i class="bi bi-graph-up"></i></div>
                        <div>
                            <div class="stat-value" id="avgHousehold">
                                <span class="spinner-border spinner-border-sm"></span>
                            </div>
                            <div class="stat-label">Avg HH</div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row g-3 mt-1">
                <div class="col-lg-6">
                    <div class="chart-card">
                        <div class="chart-header">Gender Distribution</div>
                        <canvas id="genderChart"></canvas>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="chart-card">
                        <div class="chart-header">Population by Purok</div>
                        <canvas id="purokChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-lg-6">
                    <div class="chart-card">
                        <div class="chart-header">Age Distribution</div>
                        <canvas id="ageChart"></canvas>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="chart-card">
                        <div class="chart-header">Civil Status</div>
                        <canvas id="civilChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-lg-6">
                    <div class="chart-card">
                        <div class="chart-header">Certificate Status</div>
                        <canvas id="certChart"></canvas>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="chart-card">
                        <div class="chart-header">Monthly Transactions</div>
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
