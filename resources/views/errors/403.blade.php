@extends('errors.layout')
@section('content')
<div class="error-card">
    <div class="error-icon error-icon-warning">🔒</div>
    <p class="error-code">403 — Forbidden</p>
    <h1 class="error-title">Access denied</h1>
    <p class="error-message">
        {{ $message ?? 'You don\'t have permission to access this resource. Please contact your administrator if you believe this is a mistake.' }}
    </p>
    <div class="error-actions">
        <a href="{{ url('/') }}" class="btn btn-primary">Go to Dashboard</a>
        <button onclick="history.back()" class="btn btn-secondary">Go Back</button>
    </div>
    <div class="divider"></div>
    <p class="help-text">
        If you need access, contact your <a href="mailto:support@cpacrm.com">account administrator</a>.
    </p>
</div>
@endsection
