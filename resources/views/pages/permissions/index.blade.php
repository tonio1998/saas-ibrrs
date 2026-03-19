@extends('layouts.app')
@section('title','Permissions Management')

@section('content')

    <x-page-header title="Permissions Management" subtitle="Manage system permissions">

        <x-slot:action>
            <a href="{{ route('permissions.create') }}" class="btn btn-primary btn-md">
                <i class="bi bi-plus"></i> Add Permission
            </a>
        </x-slot:action>

    </x-page-header>

    <x-card>

        <x-datatable
            id="permissionsTable"
            :columns="['Actions','Permission Name','Details','Created At']"
            :ajax="route('permissions.data')"
            :datatableColumns="[
['data'=>'actions','orderable'=>false,'searchable'=>false],
['data'=>'name'],
['data'=>'details'],
['data'=>'created_at'],
]"
        />

    </x-card>

@endsection
