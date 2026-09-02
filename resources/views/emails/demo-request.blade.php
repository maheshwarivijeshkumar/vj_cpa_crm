@extends('emails.layout')
@section('subject', "[Demo Request] {$company}")
@section('content')
<p class="greeting">New Demo Request</p>
<table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
  <tr><td style="padding:8px 0;font-weight:600;color:#374151;width:120px;">Name</td><td style="padding:8px 0;color:#374151;">{{ $name }}</td></tr>
  <tr><td style="padding:8px 0;font-weight:600;color:#374151;">Email</td><td style="padding:8px 0;"><a href="mailto:{{ $email }}" style="color:#1D9792;">{{ $email }}</a></td></tr>
  <tr><td style="padding:8px 0;font-weight:600;color:#374151;">Firm</td><td style="padding:8px 0;color:#374151;">{{ $company }}</td></tr>
  <tr><td style="padding:8px 0;font-weight:600;color:#374151;">Team size</td><td style="padding:8px 0;color:#374151;">{{ $teamSize }}</td></tr>
</table>
<a href="mailto:{{ $email }}?subject=Re: Demo Request for {{ $company }}" class="btn">Reply to {{ $name }}</a>
@endsection
