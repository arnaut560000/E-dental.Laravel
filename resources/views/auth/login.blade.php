@extends('layouts.staff')

@section('title', 'Staff login')

@section('content')
    <div class="login-form">
        <h1>Staff login</h1>
        <p>Sign in to review waiting client requests.</p>
        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <label for="username">Username</label>
            <input id="username" name="username" type="text" value="{{ old('username') }}"
                   autocomplete="username" maxlength="50" required autofocus>
            @error('username')
                <p class="error" role="alert">{{ $message }}</p>
            @enderror

            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
            @error('password')
                <p class="error" role="alert">{{ $message }}</p>
            @enderror

            <button class="submit-button" type="submit">Sign in</button>
        </form>
    </div>
@endsection
