@extends('layouts.app')
@section('title','Puroks Management')

@section('content')
    <x-page-header title="Purok Management" subtitle="Manage puroks">
        <x-slot:action>
            <a href="{{ route('puroks.create') }}" class="btn btn-primary btn-md">
                <i class="bi bi-plus"></i> Add Purok
            </a>
        </x-slot:action>
    </x-page-header>

    <x-card>
        <x-datatable
            id="puroksTable"
            :columns="[
                'Actions',
                'Puroks No',
                'Puroks Name',
                'Created At'
            ]"
            :ajax="route('puroks.data')"
            :datatableColumns="[
                ['data'=>'actions','orderable'=>false,'searchable'=>false],
                ['data'=>'PurokNo'],
                ['data'=>'PurokName'],
                ['data'=>'created_at']
            ]"
        />
    </x-card>
@endsection
