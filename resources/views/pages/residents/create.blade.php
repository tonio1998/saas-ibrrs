@extends('layouts.app')
@section('title','Resident Management')

@section('content')

    @php
        $isEdit = isset($resident);
    @endphp

    <x-page-header
        title="{{ $isEdit ? 'Edit Resident' : 'Add Resident' }}"
        subtitle="Manage resident"
    >
        <x-slot:action>
            <a href="{{ route('residents.index') }}" class="btn btn-light btn-md">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </x-slot:action>
    </x-page-header>

    <x-card>

        <form method="POST"
              action="{{ $isEdit ? route('residents.update',encrypt($resident->id)) : route('residents.store') }}"
              enctype="multipart/form-data">

            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row g-4">

                <x-form.group name="FirstName" label="First Name" class="col-md-3" required>
                    <x-form.input
                        name="FirstName"
                        value="{{ old('FirstName',$resident->FirstName ?? '') }}"
                        placeholder="Enter first name"
                    />
                </x-form.group>

                <x-form.group name="MiddleName" label="Middle Name" class="col-md-3">
                    <x-form.input
                        name="MiddleName"
                        value="{{ old('MiddleName',$resident->MiddleName ?? '') }}"
                        placeholder="Enter middle name"
                    />
                </x-form.group>

                <x-form.group name="LastName" label="Last Name" class="col-md-3" required>
                    <x-form.input
                        name="LastName"
                        value="{{ old('LastName',$resident->LastName ?? '') }}"
                        placeholder="Enter last name"
                    />
                </x-form.group>

                <x-form.group name="Suffix" label="Suffix" class="col-md-3">
                    <select name="Suffix" class="form-select">
                        <option value=""></option>
                        @foreach(['Jr','Sr','III', 'IV', 'V'] as $suffix)
                            <option value="{{ $suffix }}" {{ old('Suffix',$resident->Suffix ?? '') == $suffix ? 'selected':'' }}>
                                {{ $suffix }}
                            </option>
                        @endforeach
                    </select>
                </x-form.group>

                <x-form.group name="Gender" label="Gender" class="col-md-3" required>
                    <select name="gender" class="form-select">
                        <option value="">Select sex</option>
                        @foreach(['Male','Female'] as $sex)
                            <option value="{{ $sex }}" {{ old('Sex',$resident->gender ?? '') == $sex ? 'selected':'' }}>
                                {{ $sex }}
                            </option>
                        @endforeach
                    </select>
                </x-form.group>

                <x-form.group name="BirthDate" label="Birth Date" class="col-md-3" required>
                    <x-form.input
                        type="date"
                        name="BirthDate"
                        value="{{ old('BirthDate',date('Y-m-d',strtotime($resident->BirthDate ?? ''))) }}"
                    />
                </x-form.group>

                <x-form.group name="CivilStatus" label="Civil Status" class="col-md-3">
                    <select name="CivilStatus" class="form-select">
                        <option value="">Select status</option>
                        @foreach(['Single','Married','Widowed','Separated'] as $status)
                            <option value="{{ $status }}" {{ old('CivilStatus',$resident->CivilStatus ?? '') == $status ? 'selected':'' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </x-form.group>

                <x-form.group name="Occupation" label="Occupation" class="col-md-3">
                    <x-form.input
                        name="Occupation"
                        value="{{ old('Occupation',$resident->Occupation ?? '') }}"
                        placeholder="Enter occupation"
                    />
                </x-form.group>

                <x-form.group name="household_id" label="Household" class="col-md-6" required>
                    <x-form.select
                        name="household_id"
                        ajax="{{ route('select2.households') }}"
                        value="{{ old('household_id',$resident->household_id ?? '') }}"
                        text="{{ $resident->household->household_code ?? '' }}"
                        placeholder="Select household"
                    />
                </x-form.group>

                <x-form.group name="is_voter" label="Voter" class="col-md-3">
                    <select name="is_voter" class="form-select">
                        <option value="0">No</option>
                        <option value="1" {{ old('is_voter',$resident->is_voter ?? 0) == 1 ? 'selected':'' }}>Yes</option>
                    </select>
                </x-form.group>

            </div>

            <div class="d-flex justify-content-end">
                <div class="form-actions mt-4 d-flex align-items-center gap-2">

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check"></i>
                        {{ $isEdit ? 'Update Resident' : 'Save Resident' }}
                    </button>

                    <a href="{{ route('residents.index') }}" class="btn btn-light">
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

                const FirstName=form.querySelector('[name="FirstName"]').value.trim();
                const LastName=form.querySelector('[name="LastName"]').value.trim();
                const Sex=form.querySelector('[name="Sex"]').value;
                const BirthDate=form.querySelector('[name="BirthDate"]').value;
                const Household=form.querySelector('[name="household_id"]').value;

                if(!FirstName){
                    Swal.fire({icon:'warning',title:'Validation',text:'First name is required'});
                    e.preventDefault();
                    return;
                }

                if(!LastName){
                    Swal.fire({icon:'warning',title:'Validation',text:'Last name is required'});
                    e.preventDefault();
                    return;
                }

                if(!Sex){
                    Swal.fire({icon:'warning',title:'Validation',text:'Sex is required'});
                    e.preventDefault();
                    return;
                }

                if(!BirthDate){
                    Swal.fire({icon:'warning',title:'Validation',text:'Birth date is required'});
                    e.preventDefault();
                    return;
                }

                if(!Household){
                    Swal.fire({icon:'warning',title:'Validation',text:'Household is required'});
                    e.preventDefault();
                    return;
                }

            });

        });

    </script>

@endsection
