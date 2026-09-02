<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Referral Reward Earned!</title>
  <style>
    body { background:#F4FAFA; font-family:'Inter',Arial,sans-serif; margin:0; padding:0; }
    .container { max-width:600px; margin:40px auto; background:#FEFDFD; border-radius:12px; overflow:hidden; border:1px solid #D4ECEA; }
    .header { background:#055E5A; padding:32px 40px; text-align:center; }
    .header h1 { color:#fff; font-size:22px; font-weight:700; margin:0; }
    .header p  { color:#C5E8E5; font-size:14px; margin:8px 0 0; }
    .body   { padding:36px 40px; }
    .body p { color:#0D2B2A; font-size:15px; line-height:1.6; margin:0 0 16px; }
    .reward-box { background:#E6F5F4; border-radius:10px; padding:24px; margin:24px 0; text-align:center; }
    .reward-box .amount { font-size:42px; font-weight:800; color:#055E5A; line-height:1.1; }
    .reward-box .type   { font-size:15px; font-weight:600; color:#1D9792; margin-top:4px; }
    .reward-box .sub    { font-size:13px; color:#6B9294; margin-top:8px; }
    .cta    { text-align:center; margin:28px 0; }
    .cta a  { display:inline-block; background:#1D9792; color:#fff; text-decoration:none; padding:14px 32px; border-radius:9px; font-size:15px; font-weight:600; }
    .footer { background:#F4FAFA; border-top:1px solid #D4ECEA; padding:20px 40px; text-align:center; }
    .footer p { font-size:12px; color:#6B9294; margin:0; line-height:1.6; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>You earned a reward! 🎉</h1>
      <p>{{ $appName }}</p>
    </div>

    <div class="body">
      <p>Hi {{ $referrer->name }},</p>

      <p>
        Great news — someone you referred has signed up and verified their account.
        Your referral reward has been credited to your account:
      </p>

      <div class="reward-box">
        <div class="amount">{{ $rewardAmount }}</div>
        <div class="type">{{ $rewardType }}</div>
        <div class="sub">Credited to your account — use it on your next subscription renewal.</div>
      </div>

      <p>
        You can view your full referral history and redeem your balance any time from your portal.
      </p>

      <div class="cta">
        <a href="{{ $portalUrl }}">View My Referrals & Balance</a>
      </div>

      <p style="color:#6B9294;font-size:13px;">
        Keep sharing your referral link to earn more! Every verified sign-up earns you a reward.
      </p>
    </div>

    <div class="footer">
      <p>© {{ date('Y') }} {{ $appName }}. This confirms a referral reward was issued to your account.</p>
    </div>
  </div>
</body>
</html>
