<!-- verification-success.blade.php -->
@extends('layouts.app')

@section('content')
    <div id="verification-success-page">
        <h2>Thank you for verifying your email!</h2>
        <p>Your email has been successfully verified. You can now proceed to your <a href="{{ route('provider.dashboard') }}" id="provider-dashboard-link">Provider Dashboard</a>.</p>
    </div>
@endsection
