@extends('emails.layout')
@section('subject', "[Contact] {$subject}")
@section('content')
<p class="greeting">New contact form submission</p>
<table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
  <tr><td style="padding:8px 0;font-weight:600;color:#374151;width:120px;">Name</td><td style="padding:8px 0;color:#374151;">{{ $name }}</td></tr>
  <tr><td style="padding:8px 0;font-weight:600;color:#374151;">Email</td><td style="padding:8px 0;"><a href="mailto:{{ $email }}" style="color:#1D9792;">{{ $email }}</a></td></tr>
  @if($company)<tr><td style="padding:8px 0;font-weight:600;color:#374151;">Company</td><td style="padding:8px 0;color:#374151;">{{ $company }}</td></tr>@endif
  <tr><td style="padding:8px 0;font-weight:600;color:#374151;">Subject</td><td style="padding:8px 0;color:#374151;">{{ $subject }}</td></tr>
</table>
<div style="background:#F4FAFA;border-radius:8px;padding:16px 18px;border:1px solid #E6F5F4;white-space:pre-line;color:#374151;line-height:1.7;">{{ $message }}</div>
@endsection
