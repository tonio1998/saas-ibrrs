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
                'Purok No',
                'Purok Name',
                'Households',
                'Created At'
            ]"
            :ajax="route('puroks.data')"
            :datatableColumns="[
                ['data'=>'actions','orderable'=>false,'searchable'=>false],
                ['data'=>'PurokNo'],
                ['data'=>'PurokName'],
                ['data'=>'households_count','orderable'=>false,'searchable'=>false],
                ['data'=>'created_at']
            ]"
        />
    </x-card>
@endsection
