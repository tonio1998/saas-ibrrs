@extends('layouts.app')
@section('title','Household Management')

@section('content')
    <x-page-header title="Household Management" subtitle="Manage households">
        <x-slot:action>
            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="bi bi-funnel me-1"></i> Filters
            </button>
            <a href="{{ route('households.create') }}" class="btn btn-primary btn-md">
                <i class="bi bi-plus"></i> Add Household
            </a>
        </x-slot:action>
    </x-page-header>

    <x-card>
        <x-datatable
            id="householdsTable"
            :columns="[
                'Actions',
                'Household Code',
                'Puroks',
                'Head',
                'Members',
                'Created At'
            ]"
            :ajax="route('households.data')"
            :datatableColumns="[
                ['data'=>'actions','orderable'=>false,'searchable'=>false],
                ['data'=>'household_code'],
                ['data'=>'purok'],
                ['data'=>'head'],
                ['data'=>'members','orderable'=>false,'searchable'=>false],
                ['data'=>'created_at']
            ]"
        />
    </x-card>

    <x-modal.modal id="filterModal" title="Filters">

        <div class="mb-3">
            <label class="small">Purok</label>
            <select data-filter="purok" class="form-select form-select-sm">
                <option value="">All</option>
                @foreach($puroks as $p)
                    <option value="{{ $p->id }}">
                        Purok {{ $p->PurokNo }} - {{ $p->PurokName }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="small">Head</label>
            <select data-filter="has_head" class="form-select form-select-sm">
                <option value="">All</option>
                <option value="1">With Head</option>
                <option value="0">No Head</option>
            </select>
        </div>

        <div class="row g-2">
            <div class="col-6">
                <input type="date" data-filter="date_from" class="form-control form-control-sm">
            </div>
            <div class="col-6">
                <input type="date" data-filter="date_to" class="form-control form-control-sm">
            </div>
        </div>

        <x-slot:footer>
            <button class="btn btn-light" data-reset>Reset</button>
            <button class="btn btn-primary" data-apply>Apply</button>
        </x-slot:footer>

    </x-modal.modal>
@endsection
