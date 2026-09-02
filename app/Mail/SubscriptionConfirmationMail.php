<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * SubscriptionConfirmationMail
 *
 * Sent to a firm owner immediately after a subscription is created or renewed.
 * Confirms the plan, billing period, amount paid, and links to the portal.
 */
final class SubscriptionConfirmationMail extends BaseMailable
{
    public function __construct(
        public readonly Subscription $subscription,
    ) {
        parent::__construct();
    }

    public function envelope(): Envelope
    {
        $plan = $this->subscription->plan?->label() ?? $this->subscription->plan;

        return new Envelope(
            subject: "Your {$plan} subscription is confirmed — " . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlTemplate: 'emails.subscription.confirmation',
            textTemplate: 'emails.subscription.confirmation-text',
            with: [
                'subscription' => $this->subscription,
                'tenant'       => $this->subscription->tenant,
                'plan'         => $this->subscription->plan?->label() ?? $this->subscription->plan,
                'billingCycle' => ucfirst($this->subscription->billing_cycle ?? 'monthly'),
                'startsAt'     => $this->subscription->starts_at?->format('M j, Y'),
                'endsAt'       => $this->subscription->ends_at?->format('M j, Y'),
                'amountPaid'   => number_format((float) $this->subscription->amount_paid, 2),
                'currency'     => $this->subscription->currency?->currency ?? 'USD',
                'portalUrl'    => rtrim(config('app.url'), '/') . '/portal/subscription',
            ],
        );
    }
}
