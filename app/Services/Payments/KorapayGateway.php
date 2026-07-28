<?php 

// app/Services/Payments/KorapayGateway.php
namespace App\Services\Payments;

use App\Models\CommunitySubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class KorapayGateway implements PaymentGateway
{
    private string $secretKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->secretKey = (string) config('services.env.kora_sec');
        // $this->secretKey = (string) config('services.korapay.secret_key');
        $this->baseUrl = (string) config('services.env.kora_base_url');
    }

    public function initializeCheckout(CommunitySubscription $subscription): string
    {
        $reference = $subscription->gateway_reference ?: 'sub_'.$subscription->id.'_'.Str::random(8);

        $response = Http::withToken($this->secretKey)->acceptJson()
            ->post("{$this->baseUrl}/charges/initialize", [
                'amount' => (float) $subscription->amount,
                'currency' => 'NGN',
                'reference' => $reference,
                'narration' => 'Community subscription: '.$subscription->community->name,
                'notification_url' => route('webhooks.korapay'),
                'redirect_url' => route('community.show', $subscription->community),
                'customer' => [
                    'name' => $subscription->user->name,
                    'email' => $subscription->user->email,
                ],
            ]);

        if (! $response->successful() || ! $response->json('status')) {
            throw new RuntimeException('Korapay charge initialization failed: '.$response->body());
        }

        $subscription->update([
            'gateway' => 'korapay',
            'gateway_reference' => $reference,
            'gateway_meta' => $response->json('data'),
        ]);

        return $response->json('data.checkout_url');
    }

    public function verifyWebhookSignature(Request $request): bool
    {
        $signature = $request->header('x-korapay-signature');
        if (! $signature || ! $this->secretKey) {
            return false;
        }

        // Korapay signs ONLY the `data` object, not the full payload.
        $expected = hash_hmac('sha256', json_encode($request->input('data', [])), $this->secretKey);

        return hash_equals($expected, (string) $signature);
    }

    public function parseWebhookEvent(Request $request): array
    {
        $data = $request->input('data', []);

        return [
            'reference' => $data['reference'] ?? null,
            'status' => ($request->input('event') === 'charge.success' && ($data['status'] ?? null) === 'success')
                ? 'success' : 'failed',
            'raw' => $data,
        ];
    }
}