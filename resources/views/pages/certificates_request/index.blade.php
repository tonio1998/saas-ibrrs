@extends('layouts.app')
@section('title','Certificate Requests')

@section('content')

    <div class="d-flex flex-column gap-3">

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-0">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <x-page-header
            title="Certificate Requests"
            subtitle="Manage certificate requests"
        >
            <x-slot:action>
                <button class="btn btn-light btn-md" data-bs-toggle="modal" data-bs-target="#filterModal">
                    <i class="bi bi-funnel"></i>
                    <span class="d-none d-sm-inline">Filters</span>
                </button>
                <a href="{{ route('certificates_request.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i>
                    <span class="d-none d-sm-inline">New Request</span>
                </a>
            </x-slot:action>
        </x-page-header>

        <x-card class="p-0">

            <div class="table-responsive">

                <x-datatable
                    id="requestsTable"
                    :columns="[
                    'Actions',
                    'Control No',
                    'Resident',
                    'Type',
                    'Status',
                    'Requested At',
                    'Created At'
                ]"
                    :ajax="route('certificates_request.data')"
                    :datatableColumns="[
                    ['data'=>'actions','orderable'=>false,'searchable'=>false],
                    ['data'=>'control_no'],
                    ['data'=>'resident'],
                    ['data'=>'type'],
                    ['data'=>'status_badge','orderable'=>false,'searchable'=>false],
                    ['data'=>'requested_at'],
                    ['data'=>'created_at']
                ]"
                />

            </div>

        </x-card>

    </div>
    <x-modal.modal id="filterModal" title="Filter Requests">

        <div class="row g-3">

            <div class="col-12">
                <label>Status</label>
                <select class="form-select" data-filter="status">
                    <option value="">All</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
                </select>
            </div>

            <div class="col-12">
                <label>Type</label>
                <select class="form-select" data-filter="type">
                    <option value="">All</option>
                    @foreach($certificateTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-6">
                <label>From</label>
                <input type="date" class="form-control" data-filter="date_from">
            </div>

            <div class="col-6">
                <label>To</label>
                <input type="date" class="form-control" data-filter="date_to">
            </div>

        </div>

        <x-slot:footer>
            <button class="btn btn-light" data-reset>Reset</button>
            <button class="btn btn-primary" data-apply>Apply</button>
        </x-slot:footer>

    </x-modal.modal>
@endsection
