<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Subscription Confirmed</title>
  <style>
    body { background:#F4FAFA; font-family:'Inter',Arial,sans-serif; margin:0; padding:0; }
    .container { max-width:600px; margin:40px auto; background:#FEFDFD; border-radius:12px; overflow:hidden; border:1px solid #D4ECEA; }
    .header { background:#055E5A; padding:32px 40px; text-align:center; }
    .header h1 { color:#fff; font-size:22px; font-weight:700; margin:0; }
    .header p  { color:#C5E8E5; font-size:14px; margin:8px 0 0; }
    .body   { padding:36px 40px; }
    .body p { color:#0D2B2A; font-size:15px; line-height:1.6; margin:0 0 16px; }
    .details { background:#E6F5F4; border-radius:10px; padding:20px 24px; margin:20px 0; }
    .details table { width:100%; border-collapse:collapse; }
    .details td { padding:8px 0; font-size:14px; }
    .details td:first-child { color:#4D7374; font-weight:500; width:140px; }
    .details td:last-child  { color:#0D2B2A; font-weight:600; }
    .badge { display:inline-block; background:#DCFCE7; color:#16A34A; font-size:12px; font-weight:600; padding:3px 10px; border-radius:999px; }
    .cta    { text-align:center; margin:28px 0; }
    .cta a  { display:inline-block; background:#1D9792; color:#fff; text-decoration:none; padding:14px 32px; border-radius:9px; font-size:15px; font-weight:600; }
    .footer { background:#F4FAFA; border-top:1px solid #D4ECEA; padding:20px 40px; text-align:center; }
    .footer p { font-size:12px; color:#6B9294; margin:0; line-height:1.6; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Subscription Confirmed ✓</h1>
      <p>{{ $appName ?? config('app.name') }}</p>
    </div>

    <div class="body">
      <p>Hi {{ $tenant?->name ?? 'there' }},</p>

      <p>Your <strong>{{ $plan }}</strong> subscription is active. Here's a summary:</p>

      <div class="details">
        <table>
          <tr>
            <td>Plan</td>
            <td>{{ $plan }} <span class="badge">Active</span></td>
          </tr>
          <tr>
            <td>Billing Cycle</td>
            <td>{{ $billingCycle }}</td>
          </tr>
          <tr>
            <td>Starts</td>
            <td>{{ $startsAt }}</td>
          </tr>
          <tr>
            <td>Renews / Expires</td>
            <td>{{ $endsAt }}</td>
          </tr>
          <tr>
            <td>Amount Paid</td>
            <td>{{ $currency }} {{ $amountPaid }}</td>
          </tr>
        </table>
      </div>

      <p>
        You now have full access to all {{ $plan }} features. Log into your portal to get started.
      </p>

      <div class="cta">
        <a href="{{ $portalUrl }}">Go to My Portal</a>
      </div>

      <p style="color:#6B9294;font-size:13px;">
        Questions about your subscription? Reply to this email and we'll help right away.
      </p>
    </div>

    <div class="footer">
      <p>© {{ date('Y') }} {{ config('app.name') }}. This email confirms a subscription transaction.</p>
    </div>
  </div>
</body>
</html>
