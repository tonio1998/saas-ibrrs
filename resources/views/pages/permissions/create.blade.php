@extends('layouts.app')
@section('title','Permissions Management')

@section('content')

    @php
        $isEdit = isset($permission);
    @endphp

    <x-page-header
        title="{{ $isEdit ? 'Edit Permission' : 'Add Permission' }}"
        subtitle="Manage system permissions"
    >

        <x-slot:action>
            <a href="{{ route('permissions.index') }}" class="btn btn-light btn-md">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </x-slot:action>

    </x-page-header>

    <x-card>

        <form method="POST"
              action="{{ $isEdit ? route('permissions.update',encrypt($permission->id)) : route('permissions.store') }}">

            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row g-4">

                <x-form.group name="name" label="Permission Name" class="col-md-6" required>
                    <x-form.input
                        name="name"
                        value="{{ old('name',$permission->name ?? '') }}"
                        placeholder="ex: create users"
                    />
                </x-form.group>
                <x-form.group name="name" label="Permission Details" class="col-md-6" required>
                    <x-form.input
                        name="details"
                        value="{{ old('details',$permission->details ?? '') }}"
                        placeholder="ex: create users"
                    />
                </x-form.group>

            </div>

            <div class="d-flex justify-content-end">
                <div class="form-actions mt-4 d-flex align-items-center gap-2">

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check"></i>
                        {{ $isEdit ? 'Update Permission' : 'Save Permission' }}
                    </button>

                    <a href="{{ route('permissions.index') }}" class="btn btn-light">
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

                const name=form.querySelector('[name="name"]').value.trim();

                if(!name){
                    Swal.fire({
                        icon:'warning',
                        title:'Validation',
                        text:'Permission name is required'
                    });
                    e.preventDefault();
                    return;
                }

            });

        });

    </script>

@endsection
