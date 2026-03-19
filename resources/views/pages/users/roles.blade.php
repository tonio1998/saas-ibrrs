@extends('layouts.app')
@section('title','Assign Roles')

@section('content')

    <div class="container-fluid">

        <div class="card shadow-sm">

            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Assign Roles</h5>
                    <small class="text-muted">{{ $user->name }} ({{ $user->email }})</small>
                </div>

                <a href="{{ route('users.index') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>

            <div class="card-body">

                <form method="POST" id="rolesForm" action="{{ route('users.roles.update',$user->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">

                        <div class="col-md-6">

                            <div class="d-flex justify-content-between mb-2">
                                <h6 class="fw-semibold">Available Roles</h6>
                            </div>

                            <input type="text"
                                   id="searchRoles"
                                   class="form-control mb-3"
                                   placeholder="Search roles...">

                            <div id="availableRoles" class="role-box">

                                @foreach($roles as $role)

                                    @if(!$user->roles->contains('name',$role->name))

                                        <div class="role-item"
                                             data-role="{{ $role->name }}">
                                            {{ $role->name }}
                                        </div>

                                    @endif

                                @endforeach

                            </div>

                        </div>


                        <div class="col-md-6">

                            <h6 class="fw-semibold mb-3">Assigned Roles</h6>

                            <div id="assignedRoles" class="role-box">

                                @foreach($roles as $role)

                                    @if($user->roles->contains('name',$role->name))

                                        <div class="role-item"
                                             data-role="{{ $role->name }}">
                                            {{ $role->name }}
                                        </div>

                                    @endif

                                @endforeach

                            </div>

                        </div>

                    </div>


                    <div id="roleInputs"></div>

                    <div class="mt-4">
                        <button class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Save Roles
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>
    <style>

        .role-box{
            min-height:260px;
            border:1px dashed #dcdcdc;
            border-radius:8px;
            padding:10px;
            background:#fafafa;
        }

        .role-item{
            padding:8px 10px;
            margin-bottom:8px;
            background:white;
            border-radius:6px;
            border:1px solid #eee;
            cursor:grab;
            font-weight:500;
        }

        .role-item:hover{
            background:#f1f5f9;
        }

    </style>
@endsection
