@extends('admin-layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card w-50 shadow-sm p-4">
        <h2 class="mb-4">{{ __('Forgot Password') }}</h2>

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('admin.forgot.password') }}" method="POST" autocomplete="off">
            @csrf

            <div class="form-group mb-3">
                <label for="email">{{ __('Email Address') }}</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">{{ __('Send Reset Link') }}</button>
        </form>
    </div>
</div>
@endsection
