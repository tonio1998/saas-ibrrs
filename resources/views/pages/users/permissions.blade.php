@extends('layouts.app')
@section('title','Assign Permissions')

@section('content')

    <div class="container-fluid">

        <div class="card shadow-sm">

            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Assign Permissions</h5>
                    <small class="text-muted">{{ $user->name }} ({{ $user->email }})</small>
                </div>

                <a href="{{ route('users.index') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>

            <div class="card-body">

                <form id="permissionsForm" method="POST" action="{{ route('users.permissions.update',$user->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">

                        <div class="col-md-6">

                            <h6 class="fw-semibold mb-2">Available Permissions</h6>

                            <input
                                type="text"
                                id="searchPermissions"
                                class="form-control mb-3"
                                placeholder="Search permissions..."
                            >

                            <div id="availablePermissions" class="perm-box">

                                @foreach($permissions as $permission)

                                    @if(!$user->hasPermissionTo($permission->name))

                                        <div
                                            class="perm-item"
                                            data-permission="{{ $permission->name }}"
                                        >
                                            {{ $permission->name }}
                                        </div>

                                    @endif

                                @endforeach

                            </div>

                        </div>


                        <div class="col-md-6">

                            <h6 class="fw-semibold mb-3">Assigned Permissions</h6>

                            <div id="assignedPermissions" class="perm-box">

                                @foreach($permissions as $permission)

                                    @if($user->hasPermissionTo($permission->name))

                                        <div
                                            class="perm-item"
                                            data-permission="{{ $permission->name }}"
                                        >
                                            {{ $permission->name }}
                                        </div>

                                    @endif

                                @endforeach

                            </div>

                        </div>

                    </div>


                    <div id="permissionInputs"></div>

                    <div class="mt-4">
                        <button class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Save Permissions
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>
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
@push('scripts')
    <script>

        document.addEventListener('DOMContentLoaded',function(){

            const form = document.getElementById('permissionsForm')
            const assigned = document.getElementById('assignedPermissions')

            if(!form || !assigned) return

            form.addEventListener('submit',function(){

                const container = document.getElementById('permissionInputs')

                container.innerHTML=''

                assigned.querySelectorAll('.perm-item').forEach(permission=>{

                    const input = document.createElement('input')

                    input.type='hidden'
                    input.name='permissions[]'
                    input.value=permission.dataset.permission

                    container.appendChild(input)

                })

            })

        })

    </script>
@endpush
