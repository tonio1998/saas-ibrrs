@extends('layouts.app')
@section('title','Resident Management')

@section('content')
    <x-page-header title="Resident Management" subtitle="Manage residents">
        <x-slot:action>
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
@endsection
