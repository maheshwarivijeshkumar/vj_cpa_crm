@extends('errors.layout')
@section('content')
<div class="error-card">
    <div class="error-icon error-icon-server">⚙️</div>
    <p class="error-code">500 — Server Error</p>
    <h1 class="error-title">Something went wrong</h1>
    <p class="error-message">
        @if(config('app.debug') && isset($message) && $message)
            {{ $message }}
        @else
            An unexpected error occurred on our end. Our team has been notified and is working on a fix.
        @endif
    </p>
    <div class="error-actions">
        <button onclick="location.reload()" class="btn btn-primary">Try Again</button>
        <a href="{{ url('/') }}" class="btn btn-secondary">Go to Dashboard</a>
    </div>
    <div class="divider"></div>
    <p class="help-text">
        If this keeps happening, <a href="mailto:support@cpacrm.com">contact support</a> and mention error code: <strong>500</strong>.
    </p>
</div>
@endsection
