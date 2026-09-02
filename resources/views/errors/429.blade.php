@extends('errors.layout')
@section('content')
<div class="error-card">
    <div class="error-icon error-icon-warning">⚡</div>
    <p class="error-code">429 — Too Many Requests</p>
    <h1 class="error-title">Slow down</h1>
    <p class="error-message">
        You've made too many requests in a short period. Please wait a moment and try again.
    </p>
    <div class="error-actions">
        <button onclick="location.reload()" class="btn btn-primary">Try Again</button>
        <button onclick="history.back()" class="btn btn-secondary">Go Back</button>
    </div>
</div>
@endsection
