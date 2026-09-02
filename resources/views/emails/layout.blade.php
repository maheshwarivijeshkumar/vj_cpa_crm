<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>@yield('subject', config('app.name'))</title>
<style>
  body{margin:0;padding:0;background:#F4FAFA;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:15px;color:#374151;}
  .wrapper{max-width:600px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;border:1px solid #D4ECEA;}
  .header{background:#055E5A;padding:28px 36px;text-align:left;}
  .logo{display:inline-flex;align-items:center;gap:10px;text-decoration:none;}
  .logo-mark{width:36px;height:36px;background:#1D9792;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;}
  .logo-name{color:#fff;font-size:17px;font-weight:700;letter-spacing:-.3px;}
  .body{padding:36px;}
  .greeting{font-size:20px;font-weight:700;color:#0D2B2A;margin-bottom:12px;}
  p{line-height:1.7;margin:0 0 16px;}
  .btn{display:inline-block;background:#1D9792;color:#fff !important;padding:13px 28px;border-radius:9px;text-decoration:none;font-weight:600;font-size:15px;margin:8px 0 20px;}
  .btn:hover{background:#055E5A;}
  .divider{height:1px;background:#E6F5F4;margin:24px 0;}
  .note{font-size:13px;color:#6B7280;line-height:1.6;}
  .footer{background:#F4FAFA;padding:24px 36px;text-align:center;border-top:1px solid #E6F5F4;}
  .footer p{font-size:12.5px;color:#9CA3AF;margin:0;}
  .footer a{color:#1D9792;text-decoration:none;}
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <span class="logo">
      <span class="logo-mark">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" stroke="white" stroke-width="2" stroke-linecap="round"/>
          <rect x="9" y="3" width="6" height="4" rx="1" stroke="white" stroke-width="2"/>
          <path d="M9 12h6M9 16h4" stroke="white" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </span>
      <span class="logo-name">{{ config('app.name', 'VJ CPA CRM') }}</span>
    </span>
  </div>
  <div class="body">
    @yield('content')
  </div>
  <div class="footer">
    <p>© {{ date('Y') }} {{ config('app.name', 'VJ CPA CRM') }}. All rights reserved.</p>
    <p style="margin-top:6px;"><a href="{{ config('app.url') }}">{{ config('app.url') }}</a> · <a href="mailto:{{ config('cpa.support_email', 'support@cpacrm.com') }}">{{ config('cpa.support_email', 'support@cpacrm.com') }}</a></p>
  </div>
</div>
</body>
</html>
