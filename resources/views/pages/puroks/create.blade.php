@extends('layouts.app')
@section('title','Puroks Management')

@section('content')

    @php
        $isEdit = isset($purok);
    @endphp

    <x-page-header
        title="{{ $isEdit ? 'Edit Puroks' : 'Add Puroks' }}"
        subtitle="Manage purok"
    >
        <x-slot:action>
            <a href="{{ route('puroks.index') }}" class="btn btn-light btn-md">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </x-slot:action>
    </x-page-header>

    <x-card>

        <form method="POST"
              action="{{ $isEdit ? route('puroks.update',encrypt($purok->id)) : route('puroks.store') }}">

            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row g-4">

                <x-form.group name="PurokNo" label="Purok No" class="col-md-6" required>
                    <x-form.input
                        type="number"
                        name="PurokNo"
                        value="{{ old('PurokNo',$purok->PurokNo ?? '') }}"
                        placeholder="Enter purok number"
                    />
                </x-form.group>

                <x-form.group name="PurokName" label="Purok Name" class="col-md-6" required>
                    <x-form.input
                        name="PurokName"
                        value="{{ old('PurokName',$purok->PurokName ?? '') }}"
                        placeholder="Enter purok name"
                    />
                </x-form.group>

            </div>

            <div class="d-flex justify-content-end">
                <div class="form-actions mt-4 d-flex align-items-center gap-2">

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check"></i>
                        {{ $isEdit ? 'Update Puroks' : 'Save Puroks' }}
                    </button>

                    <a href="{{ route('puroks.index') }}" class="btn btn-light">
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

                const no=form.querySelector('[name="PurokNo"]').value.trim();
                const name=form.querySelector('[name="PurokName"]').value.trim();

                if(!no){
                    Swal.fire({icon:'warning',title:'Validation',text:'Puroks number is required'});
                    e.preventDefault();
                    return;
                }

                if(!name){
                    Swal.fire({icon:'warning',title:'Validation',text:'Puroks name is required'});
                    e.preventDefault();
                    return;
                }

            });

        });
    </script>
@endsection
