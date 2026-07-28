<?php
// app/Services/Payments/PaymentGateway.php
namespace App\Services\Payments;

use App\Models\CommunitySubscription;
use Illuminate\Http\Request;

interface PaymentGateway
{
    public function initializeCheckout(CommunitySubscription $subscription): string;

    public function verifyWebhookSignature(Request $request): bool;

    /** @return array{reference: ?string, status: 'success'|'failed', raw: array} */
    public function parseWebhookEvent(Request $request): array;
}