@extends('layouts.auth')

@section('title','Login')

@section('content')

    <div class="auth-wrapper">
        <div class="auth-card">

            <div class="auth-header">

                <div class="auth-logo">
                    SNSU
                </div>

                <h3 class="auth-title">
                    Sign in to your account
                </h3>

                <p class="auth-subtitle">
                    Enter your credentials to continue
                </p>

            </div>

            @if($errors->any())
                <div class="auth-error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="auth-form">

                @csrf

                <div class="auth-group">

                    <label class="form-label">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="Enter your email"
                    >

                    @error('email')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                    @enderror

                </div>

                <div class="auth-group">

                    <label class="form-label">
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Enter your password"
                    >

                    @error('password')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                    @enderror

                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Login
                </button>

            </form>

        </div>
    </div>

@endsection
