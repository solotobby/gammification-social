<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class FundTransferService{
    
   
    protected string $secretKey;
    protected string $baseUrl;

      public function __construct()
    {
        $this->baseUrl = config('services.env.kora_base_url');
        $this->secretKey = config('services.env.kora_sec');
    }

    public function transfer($user, float $amount, string $bankCode, string $accountNumber): array
    {
        if ($amount <= 0) {
            throw new Exception('Invalid transfer amount.');
        }

        // 🔐 Generate idempotent reference
        $reference = generateTransactionRef();

        // 💰 Convert to kobo (VERY IMPORTANT)
        $amountInKobo = (int) round($amount * 100);

        $payload = [
            "reference" => $reference,
            "destination" => [
                "type" => "bank_account",
                "amount" => $amountInKobo,
                "currency" => "NGN",
                "narration" => "Payhankey Payout",
                "bank_account" => [
                    "bank" => $bankCode,
                    "account" => $accountNumber
                ],
                "customer" => [
                    "name" => $user->name,
                    "email" => $user->email
                ]
            ]
        ];

        try {

            $response = Http::timeout(15) // prevent hanging
                ->retry(2, 500) // retry twice on network failure
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->secretKey,
                ])
                ->post($this->baseUrl . '/transactions/disburse', $payload);

                Log::info('Raw Response Data', [
                    'reference' => $reference,
                    'response' => $response->json()
                ]);

            if (!$response->successful()) {
                Log::error('Kora Transfer Failed', [
                    'reference' => $reference,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                throw new Exception('Bank transfer request failed.');
            }

            $responseData = $response->json();

            // ✅ Validate expected response structure
            if (!isset($responseData['status']) || $responseData['status'] !== true) {

                Log::error('Kora Transfer Unsuccessful', [
                    'reference' => $reference,
                    'response' => $responseData
                ]);

                throw new Exception('Transfer was not successful.');
            }

            return [
                'reference' => $reference,
                'provider_reference' => $responseData['data']['reference'] ?? null,
                'status' => $responseData['data']['status'] ?? 'processing',
                // 'raw' => $responseData
            ];

        } catch (\Throwable $e) {

            Log::critical('FundTransferService Exception', [
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);

            throw new Exception('Transfer service temporarily unavailable.');
        }
    }


}