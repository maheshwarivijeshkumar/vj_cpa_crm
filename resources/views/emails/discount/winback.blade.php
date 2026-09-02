<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>We miss you!</title>
  <style>
    body { background:#F4FAFA; font-family:'Inter',Arial,sans-serif; margin:0; padding:0; }
    .container { max-width:600px; margin:40px auto; background:#FEFDFD; border-radius:12px; overflow:hidden; border:1px solid #D4ECEA; }
    .header { background:#055E5A; padding:32px 40px; text-align:center; }
    .header h1 { color:#fff; font-size:22px; font-weight:700; margin:0; letter-spacing:-0.3px; }
    .header p  { color:#C5E8E5; font-size:14px; margin:8px 0 0; }
    .body   { padding:36px 40px; }
    .body p { color:#0D2B2A; font-size:15px; line-height:1.6; margin:0 0 16px; }
    .code-box { background:#E6F5F4; border:2px dashed #1D9792; border-radius:10px; text-align:center; padding:20px 24px; margin:24px 0; }
    .code-box .label { font-size:12px; font-weight:600; color:#4D7374; text-transform:uppercase; letter-spacing:.8px; margin-bottom:8px; }
    .code-box .code  { font-size:28px; font-weight:800; color:#055E5A; letter-spacing:3px; font-family:monospace; }
    .code-box .valid { font-size:13px; color:#6B9294; margin-top:6px; }
    .cta    { text-align:center; margin:28px 0; }
    .cta a  { display:inline-block; background:#1D9792; color:#fff; text-decoration:none; padding:14px 32px; border-radius:9px; font-size:15px; font-weight:600; }
    .footer { background:#F4FAFA; border-top:1px solid #D4ECEA; padding:20px 40px; text-align:center; }
    .footer p { font-size:12px; color:#6B9294; margin:0; line-height:1.6; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>{{ $appName }}</h1>
      <p>We've been thinking about you</p>
    </div>

    <div class="body">
      <p>Hi {{ $tenant->name }},</p>

      <p>
        We noticed your subscription lapsed and we'd love to have you back.
        As a special thank-you, we've created an exclusive discount just for you:
      </p>

      <div class="code-box">
        <div class="label">Your exclusive discount code</div>
        <div class="code">{{ $discountCode }}</div>
        <div class="valid">
          <strong>{{ $discountValue }} off</strong> your next subscription — valid until <strong>{{ $validUntil }}</strong>
        </div>
      </div>

      <p>
        Use the code at checkout when you renew your subscription. It applies to any plan and takes
        effect immediately — no strings attached.
      </p>

      <div class="cta">
        <a href="{{ $pricingUrl }}">Claim My Discount</a>
      </div>

      <p style="color:#6B9294;font-size:13px;">
        This offer expires on {{ $validUntil }}. If you have any questions or need help getting started
        again, just reply to this email — we're happy to help.
      </p>
    </div>

    <div class="footer">
      <p>
        © {{ date('Y') }} {{ $appName }}. You're receiving this because you previously had an active subscription.<br />
        <a href="{{ rtrim(config('app.url'), '/') }}/unsubscribe" style="color:#1D9792;">Unsubscribe</a>
      </p>
    </div>
  </div>
</body>
</html>
