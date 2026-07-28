<?php

namespace App\Services\Payments;

use App\Models\CommunitySubscription;

class PaymentGatewayResolver
{
    public function forCurrency(string $currency): PaymentGateway
    {
        return strtoupper($currency) === 'NGN'
            ? app(KorapayGateway::class)
            : app(FlutterwaveGateway::class);
    }

    public function forSubscription(CommunitySubscription $subscription): PaymentGateway
    {
        return $this->forCurrency($subscription->community->currency ?? 'NGN');
    }
}
