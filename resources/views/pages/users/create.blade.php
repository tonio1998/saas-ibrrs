@extends('layouts.app')

@section('title','Create User')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <x-card>
                @include('pages.users.form')
            </x-card>
        </div>
    </div>
@endsection
