@extends('layouts.app')
@section('title','Household Management')

@section('content')

    @php
        $isEdit = isset($household);
    @endphp

    <x-page-header
        title="{{ $isEdit ? 'Edit Household' : 'Add Household' }}"
        subtitle="Manage household"
    >
        <x-slot:action>
            <a href="{{ route('households.index') }}" class="btn btn-light btn-md">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </x-slot:action>
    </x-page-header>

    <x-card>

        <form method="POST"
              action="{{ $isEdit ? route('households.update',encrypt($household->id)) : route('households.store') }}">

            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row g-4">

                <x-form.group name="household_code" label="Household Code" class="col-md-6">
                    <x-form.input
                        name="household_code"
                        value="{{ $household->household_code ?? 'Auto Generated' }}"
                        readonly
                    />
                </x-form.group>

                <x-form.group name="purok_id" label="Purok" class="col-md-6" required>
                    <x-form.select
                        name="purok_id"
                        ajax="{{ route('select2.puroks') }}"
                        value="{{ old('purok_id',$household->purok_id ?? '') }}"
                        text="{{ isset($household->purok) ? 'Puroks '.$household->purok->PurokNo.' - '.$household->purok->PurokName : '' }}"
                        placeholder="Select purok"
                    />
                </x-form.group>

                <x-form.group name="head_id" label="Head of Household" class="col-md-12" required>
                    <x-form.select
                        name="head_id"
                        ajax="{{ route('select2.residents') }}"
                        value="{{ old('head_id',$household->head_id ?? '') }}"
                        text="{{ isset($household->head) ? $household->head->FirstName.' '.$household->head->LastName : '' }}"
                        placeholder="Search resident (head)"
                    />
                </x-form.group>

            </div>

            <div class="d-flex justify-content-end">
                <div class="form-actions mt-4 d-flex align-items-center gap-2">

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check"></i>
                        {{ $isEdit ? 'Update Household' : 'Save Household' }}
                    </button>

                    <a href="{{ route('households.index') }}" class="btn btn-light">
                        Cancel
                    </a>

                </div>
            </div>

        </form>

    </x-card>
@endsection


@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded',function(){

            const form=document.querySelector('form');

            form.addEventListener('submit',function(e){

                const purok=form.querySelector('[name="purok_id"]').value;
                const address=form.querySelector('[name="address"]').value.trim();

                if(!purok){
                    Swal.fire({
                        icon:'warning',
                        title:'Validation',
                        text:'Puroks is required'
                    });
                    e.preventDefault();
                    return;
                }

                if(!address){
                    Swal.fire({
                        icon:'warning',
                        title:'Validation',
                        text:'Address is required'
                    });
                    e.preventDefault();
                    return;
                }

            });

        });
    </script>
@endsection
