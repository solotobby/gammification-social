<?php

namespace App\Services\Payments;

use App\Models\CommunitySubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class FlutterwaveGateway implements PaymentGateway
{
    private string $secretKey;
    private string $baseUrl;
    private string $webhookSecret;

    public function __construct()
    {
        $this->secretKey = (string) config('services.env.secret_key');
        $this->baseUrl = (string) config('services.env.base_url');
        $this->webhookSecret = (string) config('services.env.webhook_hash');
    }

    public function initializeCheckout(CommunitySubscription $subscription): string
    {
        $reference = $subscription->gateway_reference ?: 'sub_'.$subscription->id.'_'.Str::random(8);
        $currency = $subscription->community->currency ?: 'USD';

        $response = Http::withToken($this->secretKey)->acceptJson()
            ->post("{$this->baseUrl}/payments", [
                'tx_ref' => $reference,
                'amount' => (float) $subscription->amount,
                'currency' => $currency,
                'redirect_url' => route('community.show', $subscription->community),
                'customer' => [
                    'email' => $subscription->user->email,
                    'name' => $subscription->user->name,
                ],
                'customizations' => ['title' => $subscription->community->name],
            ]);

        if (! $response->successful() || $response->json('status') !== 'success') {
            throw new RuntimeException('Flutterwave payment initialization failed: '.$response->body());
        }

        $subscription->update([
            'gateway' => 'flutterwave',
            'gateway_reference' => $reference,
            'gateway_meta' => $response->json('data'),
        ]);

        return $response->json('data.link');
    }

    public function verifyWebhookSignature(Request $request): bool
    {
        $signature = $request->header('verif-hash');
        if (! $signature || ! $this->webhookSecret) {
            return false;
        }

        return hash_equals($this->webhookSecret, (string) $signature);
    }

    public function parseWebhookEvent(Request $request): array
    {
        $data = $request->input('data', []);

        return [
            'reference' => $data['tx_ref'] ?? null,
            'status' => (($data['status'] ?? null) === 'successful') ? 'success' : 'failed',
            'raw' => $data,
        ];
    }
}