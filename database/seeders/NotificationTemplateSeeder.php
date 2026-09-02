<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds all system-level notification templates.
 * These are platform defaults (tenant_id = null, is_system = true).
 * Tenants can override any template by creating a tenant-scoped row
 * with the same key + channel.
 */
class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $now     = now();
        $appName = config('app.name', 'VJ CPA CRM');
        $appUrl  = rtrim(config('app.url', 'https://cpacrm.com'), '/');

        $templates = [
            // ── Auth ──────────────────────────────────────────────────────────
            [
                'key'      => 'auth.welcome',
                'name'     => 'Welcome Email',
                'channel'  => 'email',
                'category' => 'auth',
                'subject'  => "Welcome to {$appName} — Your account is ready",
                'body_html' => $this->welcomeBody($appName, $appUrl),
                'body_text' => "Hi {{user.first_name}}, welcome to {$appName}! Log in at {$appUrl}/login",
                'available_variables' => json_encode(['user.first_name','user.name','firm.name','app.name','login_url','trial_days']),
                'description' => 'Sent to new users immediately after registration.',
            ],
            [
                'key'      => 'auth.password_reset',
                'name'     => 'Password Reset',
                'channel'  => 'email',
                'category' => 'auth',
                'subject'  => "Reset your {$appName} password",
                'body_html' => $this->passwordResetBody($appName),
                'body_text' => "Hi {{user.first_name}}, click this link to reset your password: {{reset_url}} — expires in {{expires_in}} minutes.",
                'available_variables' => json_encode(['user.first_name','reset_url','expires_in','app.name']),
                'description' => 'Triggered when a user requests a password reset.',
            ],
            [
                'key'      => 'auth.email_verified',
                'name'     => 'Email Verified Confirmation',
                'channel'  => 'in_app',
                'category' => 'auth',
                'subject'  => null,
                'body_short' => 'Your email address has been verified. You now have full access.',
                'available_variables' => json_encode(['user.first_name']),
                'description' => 'In-app notification shown after email verification.',
            ],
            [
                'key'      => 'auth.login_new_device',
                'name'     => 'New Device Login Alert',
                'channel'  => 'email',
                'category' => 'auth',
                'subject'  => "New sign-in to your {$appName} account",
                'body_html' => $this->newDeviceBody($appName),
                'body_text' => "Hi {{user.first_name}}, a new sign-in was detected from {{ip_address}} at {{login_time}}. If this was not you, change your password immediately.",
                'available_variables' => json_encode(['user.first_name','ip_address','login_time','device','app.name','security_url']),
                'description' => 'Sent when a login is detected from a new IP/device.',
            ],

            // ── Filing / Deadlines ────────────────────────────────────────────
            [
                'key'      => 'filing.deadline.30d',
                'name'     => 'Filing Deadline — 30 Days',
                'channel'  => 'email',
                'category' => 'filing',
                'subject'  => '{{filing_type}} filing due in 30 days — {{client_name}}',
                'body_html' => $this->deadlineBody($appName, $appUrl, 30),
                'body_text' => "Reminder: {{filing_type}} for {{client_name}} is due on {{deadline_date}} (30 days).",
                'available_variables' => json_encode(['user.first_name','filing_type','client_name','deadline_date','days_remaining','filing_url']),
                'description' => '30-day deadline reminder email.',
            ],
            [
                'key'      => 'filing.deadline.7d',
                'name'     => 'Filing Deadline — 7 Days',
                'channel'  => 'email',
                'category' => 'filing',
                'subject'  => 'URGENT: {{filing_type}} due in 7 days — {{client_name}}',
                'body_html' => $this->deadlineBody($appName, $appUrl, 7),
                'body_text' => "URGENT: {{filing_type}} for {{client_name}} is due on {{deadline_date}} (7 days).",
                'available_variables' => json_encode(['user.first_name','filing_type','client_name','deadline_date','days_remaining','filing_url']),
                'description' => '7-day urgent deadline reminder.',
            ],
            [
                'key'      => 'filing.deadline.7d',
                'name'     => 'Filing Deadline — 7 Days (In-App)',
                'channel'  => 'in_app',
                'category' => 'filing',
                'subject'  => null,
                'body_short' => '{{filing_type}} for {{client_name}} is due in 7 days ({{deadline_date}}).',
                'available_variables' => json_encode(['filing_type','client_name','deadline_date','filing_url']),
                'description' => '7-day in-app deadline alert.',
            ],
            [
                'key'      => 'filing.deadline.1d',
                'name'     => 'Filing Deadline — Tomorrow',
                'channel'  => 'email',
                'category' => 'filing',
                'subject'  => '⚠️ {{filing_type}} due TOMORROW — {{client_name}}',
                'body_html' => $this->deadlineBody($appName, $appUrl, 1),
                'body_text' => "URGENT: {{filing_type}} for {{client_name}} is due TOMORROW ({{deadline_date}}).",
                'available_variables' => json_encode(['user.first_name','filing_type','client_name','deadline_date','days_remaining','filing_url']),
                'description' => 'Final 1-day deadline warning.',
            ],

            // ── Billing ───────────────────────────────────────────────────────
            [
                'key'      => 'billing.invoice_created',
                'name'     => 'Invoice Created',
                'channel'  => 'email',
                'category' => 'billing',
                'subject'  => "Invoice {{invoice_number}} from {{firm_name}}",
                'body_html' => $this->invoiceBody($appName),
                'body_text' => "Hi {{client_name}}, a new invoice {{invoice_number}} for {{amount}} has been issued. View it at: {{invoice_url}}",
                'available_variables' => json_encode(['client_name','invoice_number','amount','due_date','firm_name','invoice_url']),
                'description' => 'Sent to client when an invoice is created and sent.',
            ],
            [
                'key'      => 'billing.payment_received',
                'name'     => 'Payment Received',
                'channel'  => 'in_app',
                'category' => 'billing',
                'subject'  => null,
                'body_short' => 'Payment of {{amount}} received for invoice {{invoice_number}}.',
                'available_variables' => json_encode(['amount','invoice_number','client_name']),
                'description' => 'In-app notification when payment is received.',
            ],

            // ── Documents ─────────────────────────────────────────────────────
            [
                'key'      => 'documents.uploaded',
                'name'     => 'Document Uploaded by Client',
                'channel'  => 'in_app',
                'category' => 'documents',
                'subject'  => null,
                'body_short' => '{{client_name}} uploaded {{document_count}} document(s) to {{request_name}}.',
                'available_variables' => json_encode(['client_name','document_count','request_name','documents_url']),
                'description' => 'In-app alert when a client uploads documents.',
            ],
            [
                'key'      => 'documents.signature_completed',
                'name'     => 'Document Signed',
                'channel'  => 'email',
                'category' => 'documents',
                'subject'  => '{{signer_name}} has signed {{document_name}}',
                'body_html' => "<p>Hi {{user.first_name}},</p><p><strong>{{signer_name}}</strong> has completed signing <strong>{{document_name}}</strong>.</p><p><a href='{{document_url}}'>Download signed document</a></p>",
                'body_text' => "Hi {{user.first_name}}, {{signer_name}} signed {{document_name}}. View at: {{document_url}}",
                'available_variables' => json_encode(['user.first_name','signer_name','document_name','document_url','signed_at']),
                'description' => 'Sent to the accountant when a client completes an e-signature.',
            ],

            // ── System ────────────────────────────────────────────────────────
            [
                'key'      => 'system.trial_expiring',
                'name'     => 'Trial Expiring Soon',
                'channel'  => 'email',
                'category' => 'system',
                'subject'  => "Your {$appName} trial expires in {{days_remaining}} days",
                'body_html' => $this->trialExpiryBody($appName, $appUrl),
                'body_text' => "Hi {{user.first_name}}, your {$appName} trial expires in {{days_remaining}} days. Upgrade at: {$appUrl}/pricing",
                'available_variables' => json_encode(['user.first_name','days_remaining','trial_end_date','upgrade_url']),
                'description' => 'Sent 7 days and 1 day before trial expiry.',
            ],
        

            // Subscription templates
            [
                'key'      => 'subscription.created',
                'name'     => 'Subscription Confirmation',
                'channel'  => 'email',
                'category' => 'subscription',
                'subject'  => "Your {{plan}} subscription is confirmed",
                'body_html' => $this->subscriptionCreatedBody($appName, $appUrl),
                'body_text' => "Hi {{tenant.name}}, your {{plan}} subscription is confirmed. Active {{starts_at}} to {{ends_at}}.",
                'available_variables' => json_encode(['tenant.name','plan','billing_cycle','starts_at','ends_at','amount_paid','currency','portal_url']),
                'description' => 'Sent immediately after a subscription is created or renewed.',
            ],
            [
                'key'      => 'subscription.created',
                'name'     => 'New Subscription (In-App)',
                'channel'  => 'in_app',
                'category' => 'subscription',
                'subject'  => null,
                'body_short' => 'Your {{plan}} subscription is now active until {{ends_at}}.',
                'available_variables' => json_encode(['plan','ends_at']),
                'description' => 'In-app notification confirming subscription activation.',
            ],
            [
                'key'      => 'subscription.lapsed',
                'name'     => 'Subscription Lapsed',
                'channel'  => 'email',
                'category' => 'subscription',
                'subject'  => "Your subscription has expired",
                'body_html' => "<p>Hi {{tenant.name}},</p><p>Your <strong>{{plan}}</strong> subscription expired on <strong>{{ended_at}}</strong>.</p><p><a href='{{pricing_url}}' style='background:#1D9792;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block;margin-top:8px;'>Re-subscribe Now</a></p>",
                'body_text' => "Hi {{tenant.name}}, your {{plan}} subscription expired on {{ended_at}}. Re-subscribe: {{pricing_url}}",
                'available_variables' => json_encode(['tenant.name','plan','ended_at','pricing_url']),
                'description' => 'Sent when a subscription transitions to lapsed.',
            ],
            // Discount templates
            [
                'key'      => 'discount.received',
                'name'     => 'Discount Code Received',
                'channel'  => 'email',
                'category' => 'discount',
                'subject'  => "You have a new discount code",
                'body_html' => $this->discountReceivedBody($appName, $appUrl),
                'body_text' => "Hi {{tenant.name}}, your discount code: {{discount_code}} gives {{discount_value}} off. Valid until {{valid_until}}.",
                'available_variables' => json_encode(['tenant.name','discount_name','discount_code','discount_value','valid_until','pricing_url']),
                'description' => 'Sent when a discount code is assigned to a tenant.',
            ],
            [
                'key'      => 'discount.winback',
                'name'     => 'Win-Back Discount',
                'channel'  => 'email',
                'category' => 'discount',
                'subject'  => "We miss you — here is {{discount_value}} off",
                'body_html' => $this->discountWinbackBody($appName, $appUrl),
                'body_text' => "Hi {{tenant.name}}, win-back code: {{discount_code}} — {{discount_value}} off. Valid until {{valid_until}}.",
                'available_variables' => json_encode(['tenant.name','discount_code','discount_value','valid_until','pricing_url']),
                'description' => 'Sent 30 days after lapse. 20% win-back offer.',
            ],
            // Referral templates
            [
                'key'      => 'referral.signed_up',
                'name'     => 'Referral Signed Up',
                'channel'  => 'in_app',
                'category' => 'referral',
                'subject'  => null,
                'body_short' => 'Someone used your referral link and signed up.',
                'available_variables' => json_encode(['referee_email']),
                'description' => 'In-app alert when a referred user signs up.',
            ],
            [
                'key'      => 'referral.rewarded',
                'name'     => 'Referral Reward Issued',
                'channel'  => 'email',
                'category' => 'referral',
                'subject'  => 'You earned a referral reward!',
                'body_html' => $this->referralRewardedBody($appName, $appUrl),
                'body_text' => "Hi {{tenant.name}}, your referral reward of {{reward_amount}} {{reward_type}} has been credited.",
                'available_variables' => json_encode(['tenant.name','reward_amount','reward_type','portal_url']),
                'description' => 'Sent when a referral reward is issued.',
            ],
            [
                'key'      => 'referral.rewarded',
                'name'     => 'Referral Reward (In-App)',
                'channel'  => 'in_app',
                'category' => 'referral',
                'subject'  => null,
                'body_short' => 'You earned {{reward_amount}} {{reward_type}} for a successful referral!',
                'available_variables' => json_encode(['reward_amount','reward_type','portal_url']),
                'description' => 'In-app notification confirming reward credit.',
            ],
        ];
        foreach ($templates as $tmpl) {
            DB::table('notification_templates')->updateOrInsert(
                [
                    'key'       => $tmpl['key'],
                    'channel'   => $tmpl['channel'],
                    'tenant_id' => null,
                ],
                array_merge($tmpl, [
                    'tenant_id' => null,
                    'status'    => 'published',
                    'version'   => 1,
                    'is_system' => true,
                    'is_active' => true,
                    'created_at'=> $now,
                    'updated_at'=> $now,
                ]),
            );
        }

        $this->command->info('NotificationTemplateSeeder: ' . count($templates) . ' templates seeded.');
    }

    // ── HTML body helpers ─────────────────────────────────────────────────────

    private function welcomeBody(string $appName, string $appUrl): string
    {
        return "<p>Hi {{user.first_name}},</p>
<p>Welcome to <strong>{$appName}</strong>! Your firm <strong>{{firm.name}}</strong> is set up and your account is ready.</p>
<p>You're on a <strong>{{trial_days}}-day free trial</strong> with full access. No credit card required.</p>
<p><a href='{{login_url}}' style='background:#1D9792;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block;'>Log in to your account</a></p>
<p>If you have questions, reply to this email — we're here to help.</p>";
    }

    private function passwordResetBody(string $appName): string
    {
        return "<p>Hi {{user.first_name}},</p>
<p>We received a request to reset the password for your <strong>{$appName}</strong> account.</p>
<p><a href='{{reset_url}}' style='background:#1D9792;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block;'>Reset My Password</a></p>
<p style='color:#6B7280;font-size:13px;'>This link expires in <strong>{{expires_in}} minutes</strong>. If you didn't request this, ignore this email.</p>";
    }

    private function newDeviceBody(string $appName): string
    {
        return "<p>Hi {{user.first_name}},</p>
<p>A new sign-in was detected for your <strong>{$appName}</strong> account.</p>
<table style='font-size:14px;'><tr><td style='padding:4px 12px 4px 0;color:#6B7280;'>IP Address</td><td><strong>{{ip_address}}</strong></td></tr><tr><td style='padding:4px 12px 4px 0;color:#6B7280;'>Time</td><td><strong>{{login_time}}</strong></td></tr></table>
<p>If this was you, no action is needed. If not, <a href='{{security_url}}'>change your password immediately</a>.</p>";
    }

    private function deadlineBody(string $appName, string $appUrl, int $days): string
    {
        $urgency = $days <= 1 ? '⚠️ ' : ($days <= 7 ? '' : '');
        return "<p>Hi {{user.first_name}},</p>
<p>{$urgency}A filing deadline is approaching for one of your clients.</p>
<table style='font-size:14px;border-collapse:collapse;'>
<tr><td style='padding:6px 16px 6px 0;color:#6B7280;'>Client</td><td><strong>{{client_name}}</strong></td></tr>
<tr><td style='padding:6px 16px 6px 0;color:#6B7280;'>Filing Type</td><td><strong>{{filing_type}}</strong></td></tr>
<tr><td style='padding:6px 16px 6px 0;color:#6B7280;'>Due Date</td><td><strong>{{deadline_date}}</strong></td></tr>
<tr><td style='padding:6px 16px 6px 0;color:#6B7280;'>Days Remaining</td><td><strong style='color:" . ($days <= 7 ? '#DC2626' : '#1D9792') . ";'>{{days_remaining}} days</strong></td></tr>
</table>
<p><a href='{{filing_url}}' style='background:#1D9792;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block;margin-top:8px;'>View Filing</a></p>";
    }

    private function invoiceBody(string $appName): string
    {
        return "<p>Hi {{client_name}},</p>
<p>A new invoice has been issued by <strong>{{firm_name}}</strong>.</p>
<table style='font-size:14px;'><tr><td style='padding:4px 12px 4px 0;color:#6B7280;'>Invoice</td><td><strong>{{invoice_number}}</strong></td></tr><tr><td style='padding:4px 12px 4px 0;color:#6B7280;'>Amount</td><td><strong>{{amount}}</strong></td></tr><tr><td style='padding:4px 12px 4px 0;color:#6B7280;'>Due Date</td><td><strong>{{due_date}}</strong></td></tr></table>
<p><a href='{{invoice_url}}' style='background:#1D9792;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block;margin-top:8px;'>View Invoice</a></p>";
    }

    private function trialExpiryBody(string $appName, string $appUrl): string
    {
        return "<p>Hi {{user.first_name}},</p>
<p>Your <strong>{$appName}</strong> trial expires in <strong>{{days_remaining}} days</strong> on <strong>{{trial_end_date}}</strong>.</p>
<p>Upgrade now to keep access to all your clients, filings, and data — no interruption.</p>
<p><a href='{{upgrade_url}}' style='background:#1D9792;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block;'>View Pricing &amp; Upgrade</a></p>
<p style='color:#6B7280;font-size:13px;'>Questions? Reply to this email — we're happy to help find the right plan for your firm.</p>";
    }

    private function subscriptionCreatedBody(string $appName, string $appUrl): string
    {
        return "<p>Hi {{tenant.name}},</p>
<p>Your <strong>{{plan}}</strong> subscription is now <span style='color:#16A34A;font-weight:600;'>active</span>.</p>
<table style='font-size:14px;border-collapse:collapse;'>
<tr><td style='padding:6px 16px 6px 0;color:#6B7280;'>Plan</td><td><strong>{{plan}}</strong></td></tr>
<tr><td style='padding:6px 16px 6px 0;color:#6B7280;'>Billing</td><td><strong>{{billing_cycle}}</strong></td></tr>
<tr><td style='padding:6px 16px 6px 0;color:#6B7280;'>Active From</td><td><strong>{{starts_at}}</strong></td></tr>
<tr><td style='padding:6px 16px 6px 0;color:#6B7280;'>Renews / Expires</td><td><strong>{{ends_at}}</strong></td></tr>
<tr><td style='padding:6px 16px 6px 0;color:#6B7280;'>Amount Paid</td><td><strong>{{currency}} {{amount_paid}}</strong></td></tr>
</table>
<p><a href='{{portal_url}}' style='background:#1D9792;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block;margin-top:12px;'>Go to My Portal</a></p>";
    }

    private function discountReceivedBody(string $appName, string $appUrl): string
    {
        return "<p>Hi {{tenant.name}},</p>
<p>You have received a new discount code from <strong>{$appName}</strong>.</p>
<div style='background:#E6F5F4;border:2px dashed #1D9792;border-radius:10px;text-align:center;padding:20px 24px;margin:20px 0;'>
<div style='font-size:12px;font-weight:600;color:#4D7374;text-transform:uppercase;letter-spacing:.8px;margin-bottom:8px;'>Your Discount Code</div>
<div style='font-size:26px;font-weight:800;color:#055E5A;letter-spacing:3px;font-family:monospace;'>{{discount_code}}</div>
<div style='font-size:13px;color:#6B9294;margin-top:6px;'><strong>{{discount_value}}</strong> off &mdash; valid until <strong>{{valid_until}}</strong></div>
</div>
<p><a href='{{pricing_url}}' style='background:#1D9792;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block;'>Apply Discount</a></p>";
    }

    private function discountWinbackBody(string $appName, string $appUrl): string
    {
        return "<p>Hi {{tenant.name}},</p>
<p>We noticed you've been away and we'd love to have you back.</p>
<div style='background:#E6F5F4;border:2px dashed #1D9792;border-radius:10px;text-align:center;padding:20px 24px;margin:20px 0;'>
<div style='font-size:12px;font-weight:600;color:#4D7374;text-transform:uppercase;letter-spacing:.8px;margin-bottom:8px;'>Your Exclusive Win-Back Code</div>
<div style='font-size:26px;font-weight:800;color:#055E5A;letter-spacing:3px;font-family:monospace;'>{{discount_code}}</div>
<div style='font-size:13px;color:#6B9294;margin-top:6px;'><strong>{{discount_value}} off</strong> &mdash; valid until <strong>{{valid_until}}</strong></div>
</div>
<p><a href='{{pricing_url}}' style='background:#1D9792;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block;'>Claim My Discount</a></p>
<p style='color:#6B7280;font-size:13px;'>This offer expires on {{valid_until}}. Questions? Reply to this email.</p>";
    }

    private function referralRewardedBody(string $appName, string $appUrl): string
    {
        return "<p>Hi {{tenant.name}},</p>
<p>Great news — someone you referred has signed up and subscribed!</p>
<div style='background:#E6F5F4;border-radius:10px;padding:24px;margin:20px 0;text-align:center;'>
<div style='font-size:42px;font-weight:800;color:#055E5A;line-height:1.1;'>{{reward_amount}}</div>
<div style='font-size:15px;font-weight:600;color:#1D9792;margin-top:4px;'>{{reward_type}} credited to your account</div>
<div style='font-size:13px;color:#6B9294;margin-top:8px;'>Apply your balance on your next subscription renewal.</div>
</div>
<p><a href='{{portal_url}}' style='background:#1D9792;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block;'>View My Referrals &amp; Balance</a></p>";
    }
}
