<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Discount;
use App\Models\Tenant;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * WinbackDiscountMail
 *
 * Sent to a lapsed tenant's firm owner after they have been inactive for
 * 30+ days. Contains a personalised discount code to encourage them to
 * re-subscribe.
 */
final class WinbackDiscountMail extends BaseMailable
{
    public function __construct(
        public readonly Tenant   $tenant,
        public readonly Discount $discount,
    ) {
        parent::__construct();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "We miss you, {$this->tenant->name} — here's " . $this->discount->value . '% off',
        );
    }

    public function content(): Content
    {
        $validUntil = $this->discount->valid_until?->format('M j, Y');

        return new Content(
            htmlTemplate: 'emails.discount.winback',
            textTemplate: 'emails.discount.winback-text',
            with: [
                'tenant'       => $this->tenant,
                'discount'     => $this->discount,
                'discountCode' => $this->discount->code,
                'discountValue'=> $this->discount->value . '%',
                'validUntil'   => $validUntil,
                'pricingUrl'   => rtrim(config('app.url'), '/') . '/pricing?code=' . urlencode($this->discount->code),
                'appName'      => config('app.name'),
            ],
        );
    }
}
