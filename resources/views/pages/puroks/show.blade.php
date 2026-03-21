@extends('layouts.app')
@section('title','View Request')
@section('content')
    <x-page-header
        title="Purok Details"
        subtitle="View certificate request information"
    >
        <x-slot:action>
            <a href="{{ route('puroks.index') }}" class="btn btn-light">
                Back
            </a>
        </x-slot:action>
    </x-page-header>

    <div class="row g-4">
        <div class="col-lg-12">
            <x-card>
                <x-datatable
                    id="householdsTable"
                    :columns="[
                    'Actions',
                    'households_code',
                    'head_id',
                    'residents_count',
                    'created_at'
                ]"
                    :ajax="route('puroks.households', ['PurokNo' => $purok->PurokNo])"
                    :datatableColumns="[
                    ['data'=>'actions','orderable'=>false,'searchable'=>false],
                    ['data'=>'households_code'],
                    ['data'=>'head_id'],
                    ['data'=>'residents_count'],
                    ['data'=>'created_at']
                ]"
                />
            </x-card>
        </div>
    </div>

@endsection

@push('scripts')
    <script>

    </script>
@endpush
