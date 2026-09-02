@extends('errors.layout')
@section('content')
<div class="error-card">
    <div class="error-icon error-icon-info">🛠</div>
    <p class="error-code">503 — Maintenance</p>
    <h1 class="error-title">We'll be right back</h1>
    <p class="error-message">
        {{ config('app.name', 'VJ CPA CRM') }} is currently undergoing scheduled maintenance to improve your experience.
        Please check back shortly.
    </p>
    <div class="error-actions">
        <button onclick="location.reload()" class="btn btn-primary">Check Again</button>
    </div>
    <div class="divider"></div>
    <p class="help-text">
        For urgent matters, email <a href="mailto:support@cpacrm.com">support@cpacrm.com</a>
    </p>
</div>
@endsection
