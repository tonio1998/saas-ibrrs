@extends('layouts.app')
@section('title','Resident Management')

@section('content')
    <x-page-header title="Resident Management" subtitle="Manage residents">
        <x-slot:action>
            <button class="btn btn-light btn-md" data-bs-toggle="modal" data-bs-target="#residentFilter">
                <i class="bi bi-funnel"></i> Filters
            </button>
            <a href="{{ route('residents.create') }}" class="btn btn-primary btn-md">
                <i class="bi bi-plus"></i> Add Resident
            </a>
        </x-slot:action>
    </x-page-header>

    <x-card>
        <x-datatable
            id="residentsTable"
            :columns="[
                'Actions',
                'Name',
                'Household',
                'Sex',
                'Birthdate',
                'Age',
                'Civil Status',
                'Occupation',
                'Voter',
                'Created At',
                'Created By'
            ]"
            :ajax="route('residents.data')"
            :datatableColumns="[
                ['data'=>'actions','orderable'=>false,'searchable'=>false],
                ['data'=>'name'],
                ['data'=>'household'],
                ['data'=>'gender'],
                ['data'=>'birthdate'],
                ['data'=>'age'],
                ['data'=>'civil_status'],
                ['data'=>'occupation'],
                ['data'=>'voter'],
                ['data'=>'created_at'],
                ['data'=>'createdBy']
            ]"
        />
    </x-card>

    <x-modal.modal id="residentFilter" title="Filter Residents" size="md">

        <div class="mb-3">
            <label class="small">Search Name</label>
            <input type="text" data-filter="name" class="form-control form-control-sm">
        </div>

        <div class="mb-3">
            <label class="small">Gender</label>
            <select data-filter="gender" class="form-select form-select-sm">
                <option value="">All</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="small">Civil Status</label>
            <select data-filter="civil_status" class="form-select form-select-sm">
                <option value="">All</option>
                <option value="Single">Single</option>
                <option value="Married">Married</option>
            </select>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-6">
                <input type="number" data-filter="age_from" class="form-control form-control-sm" placeholder="Min Age">
            </div>
            <div class="col-6">
                <input type="number" data-filter="age_to" class="form-control form-control-sm" placeholder="Max Age">
            </div>
        </div>

        <div class="mb-3">
            <label class="small">Voter</label>
            <select data-filter="voter" class="form-select form-select-sm">
                <option value="">All</option>
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>

        <x-slot:footer>
            <button class="btn btn-light" data-reset>Reset</button>
            <button class="btn btn-primary" data-apply>Apply</button>
        </x-slot:footer>

    </x-modal.modal>
@endsection
