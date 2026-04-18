@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <section class="login-header">
        <p class="login-kicker">Sari-Sari Micro-Ledger</p>
        <h1 class="login-title">Welcome back, Aling Maria.</h1>
        <p class="login-subtitle">Sign in to continue managing customer credit and payment records.</p>
        <p class="login-demo">Demo admin: <strong>alingmaria@sarisari.local</strong> / <strong>password123</strong></p>
    </section>

    <form class="auth-form" method="POST" action="{{ route('login.attempt') }}">
        @csrf
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>

        <label class="remember-inline" for="remember">
            <input id="remember" type="checkbox" name="remember" value="1">
            <span>Remember me</span>
        </label>

        <button class="btn-primary login-btn" type="submit">Sign In</button>
    </form>

    <div class="row auth-row">
        <span>Need a user account?</span>
        <a href="{{ route('register') }}">Register</a>
    </div>

@endsection
