@extends('errors.layout')
@section('content')
<div class="error-card">
    <div class="error-icon error-icon-server">⚠️</div>
    <p class="error-code">Error {{ $code ?? '' }}</p>
    <h1 class="error-title">Something went wrong</h1>
    <p class="error-message">
        {{ $message ?? 'An unexpected error occurred. Please try again or contact support if the problem persists.' }}
    </p>
    <div class="error-actions">
        <button onclick="history.back()" class="btn btn-primary">Go Back</button>
        <a href="{{ url('/') }}" class="btn btn-secondary">Go to Dashboard</a>
    </div>
    <div class="divider"></div>
    <p class="help-text">
        Need help? <a href="mailto:support@cpacrm.com">Contact support</a>
    </p>
</div>
@endsection
