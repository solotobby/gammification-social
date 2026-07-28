<?php

// app/Http/Controllers/Webhooks/KorapayWebhookController.php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CommunitySubscription;
use App\Services\CommunitySubscriptionService;
use App\Services\Payments\KorapayGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KorapayWebhookController extends Controller
{
    public function __invoke(Request $request, KorapayGateway $gateway, CommunitySubscriptionService $service)
    {
        if (! $gateway->verifyWebhookSignature($request)) {
            Log::warning('Korapay webhook: invalid signature');
            return response()->json(['message' => 'invalid signature'], 401);
        }

        $event = $gateway->parseWebhookEvent($request);
        $subscription = CommunitySubscription::where('gateway', 'korapay')
            ->where('gateway_reference', $event['reference'])->first();

        if (! $subscription || $subscription->status !== 'pending') {
            return response()->json(['message' => 'ignored']);
        }

        $event['status'] === 'success' ? $service->activate($subscription) : $service->markFailed($subscription);

        return response()->json(['message' => 'ok']);
    }
}
