Subscription Confirmed — {{ config('app.name') }}

Hi {{ $tenant?->name ?? 'there' }},

Your {{ $plan }} subscription is now ACTIVE.

Plan:       {{ $plan }}
Billing:    {{ $billingCycle }}
Start:      {{ $startsAt }}
End/Renew:  {{ $endsAt }}
Amount:     {{ $currency }} {{ $amountPaid }}

Access your portal: {{ $portalUrl }}

Questions? Reply to this email.

© {{ date('Y') }} {{ config('app.name') }}
