@extends('layouts.app')
@section('title','Certificate Types')

@section('content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <x-page-header title="Certificate Types" subtitle="Manage certificate document types">
        <x-slot:action>
            <div class="d-flex gap-2">

                <button id="reloadTable" class="btn btn-light">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>

                <a href="{{ route('certificate-types.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> New Type
                </a>

            </div>
        </x-slot:action>
    </x-page-header>

    <x-card>
        <x-datatable
            id="typesTable"
            :columns="[
            'Actions',
            'Name',
            'Description',
            'Fee',
            'Created At'
        ]"
            :ajax="route('certificate-types.data')"
            :datatableColumns="[
            ['data'=>'actions','orderable'=>false,'searchable'=>false],
            ['data'=>'name'],
            ['data'=>'description'],
            ['data'=>'fee'],
            ['data'=>'created_at']
        ]"
        />

    </x-card>

@endsection


@push('scripts')
    <script>
    </script>
@endpush
