@extends('emails.layout')
@section('subject', "Reset your {$appName} password")
@section('content')
<p class="greeting">Hi {{ $user->first_name }},</p>
<p>We received a request to reset the password for your <strong>{{ $appName }}</strong> account.</p>
<a href="{{ $resetUrl }}" class="btn">Reset My Password</a>
<p class="note">This link expires in <strong>{{ $expiresIn }} minutes</strong>. If you did not request a password reset, you can safely ignore this email — your password will not be changed.</p>
<div class="divider"></div>
<p class="note">For security, this link can only be used once. If you need a new link, visit the <a href="{{ config('app.url') }}/forgot-password">forgot password page</a>.</p>
@endsection
