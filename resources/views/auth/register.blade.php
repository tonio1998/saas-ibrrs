@extends('layouts.auth')

@section('title','Register')

@section('content')

    <div class="auth-card">

        <div class="auth-header">
            <h3>Create Account</h3>
            <p>Register to start</p>
        </div>

        <form method="POST" action="{{ route('register') }}">

            @csrf

            <div class="auth-group">
                <label>Name</label>
                <input type="text" name="name" class="auth-input" required>
            </div>

            <div class="auth-group">
                <label>Email</label>
                <input type="email" name="email" class="auth-input" required>
            </div>

            <div class="auth-group">
                <label>Password</label>
                <input type="password" name="password" class="auth-input" required>
            </div>

            <button type="submit" class="auth-btn">
                Register
            </button>

        </form>

    </div>

@endsection
