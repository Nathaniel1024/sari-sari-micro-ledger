@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <h1>Login</h1>
    <p>Sign in to access your Sari-Sari ledger.</p>
    <p>Default admin: <strong>alingmaria@sarisari.local</strong> / <strong>password123</strong></p>

    <form method="POST" action="{{ route('login.attempt') }}">
        @csrf
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>

        <label for="remember">
            <input id="remember" type="checkbox" name="remember" value="1">
            Remember me
        </label>

        <button type="submit">Login</button>
    </form>

    <div class="row">
        <span>Need a user account?</span>
        <a href="{{ route('register') }}">Register</a>
    </div>

@endsection
