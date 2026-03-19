@extends('layouts.app')
@section('title','Household Management')

@section('content')
    <x-page-header title="Household Management" subtitle="Manage households">
        <x-slot:action>
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
@endsection
