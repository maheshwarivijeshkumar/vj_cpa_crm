@extends('emails.layout')
@section('subject', "Welcome to {$appName}")
@section('content')
<p class="greeting">Hi {{ $user->first_name }},</p>
<p>Welcome to <strong>{{ $appName }}</strong>! Your firm <strong>{{ $firmName }}</strong> has been set up and your account is ready.</p>
<p>You're on a <strong>{{ $trialDays }}-day free trial</strong> with full access to all features. No credit card required during your trial.</p>
<a href="{{ $loginUrl }}" class="btn">Log in to your account</a>
<p>Here's a quick overview of what you can do:</p>
<ul style="padding-left:20px;line-height:2;">
  <li>Add clients and organise them by type and service</li>
  <li>Track filing deadlines — never miss a CRA due date</li>
  <li>Set up automated workflows for engagements</li>
  <li>Send invoices and track payments</li>
  <li>Share documents securely via the client portal</li>
</ul>
<div class="divider"></div>
<p class="note">If you have any questions, reply to this email or contact us at <a href="mailto:{{ config('cpa.support_email', 'support@cpacrm.com') }}">{{ config('cpa.support_email', 'support@cpacrm.com') }}</a>.</p>
@endsection
