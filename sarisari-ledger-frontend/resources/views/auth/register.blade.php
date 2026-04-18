@extends('layouts.auth')

@section('title', 'Register')

@section('content')
    <h1>Create Account</h1>
    <p>Register your store owner account to secure ledger access.</p>

    <form method="POST" action="{{ route('register.store') }}">
        @csrf
        <label for="name">Name</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>

        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required>

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>

        <label for="password_confirmation">Confirm Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required>

        <button type="submit">Register</button>
    </form>

    <div class="row">
        <span>Already registered?</span>
        <a href="{{ route('login') }}">Sign in</a>
    </div>
@endsection
