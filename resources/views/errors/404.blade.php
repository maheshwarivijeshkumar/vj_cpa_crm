@extends('errors.layout')
@section('content')
<div class="error-card">
    <div class="error-icon error-icon-info">🔍</div>
    <p class="error-code">404 — Not Found</p>
    <h1 class="error-title">Page not found</h1>
    <p class="error-message">
        {{ $message ?? 'The page you are looking for doesn\'t exist or may have been moved.' }}
    </p>
    <div class="error-actions">
        <a href="{{ url('/') }}" class="btn btn-primary">Go to Dashboard</a>
        <button onclick="history.back()" class="btn btn-secondary">Go Back</button>
    </div>
    <div class="divider"></div>
    <p class="help-text">
        Need help? <a href="mailto:support@cpacrm.com">Contact support</a>
    </p>
</div>
@endsection
