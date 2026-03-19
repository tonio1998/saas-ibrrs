@extends('layouts.app')
@section('title','Roles Management')

@section('content')

    @php
        $isEdit = isset($role);
    @endphp

    <x-page-header
        title="{{ $isEdit ? 'Edit Role' : 'Add Role' }}"
        subtitle="Manage system roles"
    >

        <x-slot:action>
            <a href="{{ route('roles.index') }}" class="btn btn-light btn-md">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </x-slot:action>

    </x-page-header>

    <x-card>

        <form id="roleForm"
              method="POST"
              action="{{ $isEdit ? route('roles.update',encrypt($role->id)) : route('roles.store') }}">

            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row g-4">

                <x-form.group name="name" label="Role Name" class="col-md-6" required>
                    <x-form.input
                        name="name"
                        value="{{ old('name',$role->name ?? '') }}"
                        placeholder="Enter role name"
                    />
                </x-form.group>

                <x-form.group name="description" label="Description" class="col-md-6">
                    <x-form.input
                        name="details"
                        value="{{ old('details',$role->details ?? '') }}"
                        placeholder="Role description"
                    />
                </x-form.group>

            </div>

            <hr class="my-4">

            <h6 class="fw-bold mb-3">
                <i class="bi bi-shield-lock"></i> Permissions
            </h6>

            <div class="row g-4">

                <div class="col-md-6">

                    <input
                        type="text"
                        id="searchPermissions"
                        class="form-control mb-3"
                        placeholder="Search permissions..."
                    >

                    <div id="availablePermissions" class="perm-box">

                        @foreach($permissions as $permission)

                            @if(!in_array($permission->id, old('permissions',$rolePermissions ?? [])))

                                <div
                                    class="perm-item"
                                    data-permission="{{ $permission->id }}"
                                >

                                    {{ $permission->name }}

                                </div>

                            @endif

                        @endforeach

                    </div>

                </div>

                <div class="col-md-6">

                    <h6 class="small fw-semibold mb-2">Assigned Permissions</h6>

                    <div id="assignedPermissions" class="perm-box">

                        @foreach($permissions as $permission)

                            @if(in_array($permission->id, old('permissions',$rolePermissions ?? [])))

                                <div
                                    class="perm-item"
                                    data-permission="{{ $permission->id }}"
                                >

                                    {{ $permission->name }}

                                </div>

                            @endif

                        @endforeach

                    </div>

                </div>

            </div>

            <div id="permissionInputs"></div>

            <div class="d-flex justify-content-end">

                <div class="form-actions mt-4 d-flex align-items-center gap-2">

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check"></i>
                        {{ $isEdit ? 'Update Role' : 'Save Role' }}
                    </button>

                    <a href="{{ route('roles.index') }}" class="btn btn-light">
                        Cancel
                    </a>

                </div>

            </div>

        </form>

    </x-card>
    <style>

        .perm-box{
            min-height:260px;
            border:1px dashed #ddd;
            border-radius:8px;
            padding:10px;
            background:#fafafa;
        }

        .perm-item{
            padding:8px 10px;
            margin-bottom:8px;
            border:1px solid #eee;
            border-radius:6px;
            background:white;
            cursor:grab;
            font-weight:500;
        }

        .perm-item:hover{
            background:#f1f5f9;
        }

    </style>
@endsection
