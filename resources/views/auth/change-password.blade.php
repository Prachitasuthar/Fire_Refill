@extends('admin-layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card w-50 shadow-sm p-4">
        <h2 class="mb-4">{{ __('Change Password') }}</h2>

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('change.password') }}" method="POST" autocomplete="off">
            @csrf

            <!-- Current Password -->
            <div class="form-group mb-3">
                <label for="current_password">{{ __('Current Password') }}</label>
                <input type="password" name="current_password" id="current_password" class="form-control" required>
            </div>

            <!-- New Password -->
            <div class="form-group mb-3">
                <label for="password">{{ __('New Password') }}</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <!-- Confirm New Password -->
            <div class="form-group mb-4">
                <label for="password_confirmation">{{ __('Confirm New Password') }}</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">{{ __('Change Password') }}</button>
        </form>

        <!-- Forgot Password Link -->
        <div class="mt-3 text-center">
            <a href="{{ route('forgot.password.form') }}" class="text-muted">{{ __('Forgot your password?') }}</a>
        </div>
    </div>
</div>
@endsection
