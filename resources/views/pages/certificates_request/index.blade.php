@extends('layouts.app')
@section('title','Certificate Requests')

@section('content')
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    <x-page-header title="Certificate Requests" subtitle="Manage certificate requests">
        <x-slot:action>
            <a href="{{ route('certificates_request.create') }}" class="btn btn-primary btn-md">
                <i class="bi bi-plus"></i> New Request
            </a>
        </x-slot:action>
    </x-page-header>

    <x-card>
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
    </x-card>

@endsection
