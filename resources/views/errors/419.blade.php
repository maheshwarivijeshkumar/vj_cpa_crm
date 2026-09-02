@extends('errors.layout')
@section('content')
<div class="error-card">
    <div class="error-icon error-icon-warning">⏱</div>
    <p class="error-code">419 — Session Expired</p>
    <h1 class="error-title">Your session has expired</h1>
    <p class="error-message">
        For your security, your session has timed out. Please refresh the page and try your action again.
    </p>
    <div class="error-actions">
        <a href="{{ url()->current() }}" class="btn btn-primary">Refresh Page</a>
        <a href="{{ route('login') }}" class="btn btn-secondary">Back to Login</a>
    </div>
</div>
@endsection
