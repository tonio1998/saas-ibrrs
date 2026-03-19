@extends('layouts.app')

@section('title','Edit User')

@section('content')

    <x-page-header title="Edit User" />

    <x-card>

        @include('pages.users.form')

    </x-card>

@endsection
