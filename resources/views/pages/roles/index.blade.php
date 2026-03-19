@extends('layouts.app')
@section('title','Roles Management')
@section('content')
    <x-page-header title="Roles Management" subtitle="Manage roles">
        <x-slot:action>
            <a href="{{ route('roles.create') }}" class="btn btn-primary btn-md">
                <i class="bi bi-plus"></i> Add Role
            </a>
        </x-slot:action>
    </x-page-header>

    <x-card>
        <x-datatable
            id="rolesTable"
            :columns="['Actions','Name','Permissions','Created At']"
            :ajax="route('roles.data')"
            :datatableColumns="[
                ['data'=>'actions','orderable'=>false,'searchable'=>false],
                ['data'=>'name'],
                ['data'=>'permissions'],
                ['data'=>'created_at'],
            ]"
        />
    </x-card>
@endsection
