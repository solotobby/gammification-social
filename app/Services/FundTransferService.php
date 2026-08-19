<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class FundTransferService
{
    protected string $secretKey;

    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.env.kora_base_url'), '/');
        $this->secretKey = (string) config('services.env.kora_sec');
    }

    public function transfer($user, float $amount, string $bankCode, string $accountNumber): array
    {
        if ($amount <= 0) {
            throw new Exception('Invalid transfer amount.');
        }

        if ($this->secretKey === '' || $this->baseUrl === '') {
            throw new Exception('Korapay credentials are not configured.');
        }

        $reference = generateTransactionRef();

        // Korapay expects major units with 2 decimal places (not kobo).
        // @see https://developers.korapay.com/docs/payout-via-api
        $payload = [
            'reference' => $reference,
            'destination' => [
                'type' => 'bank_account',
                'amount' => round($amount, 2),
                'currency' => 'NGN',
                'narration' => 'Payhankey Payout',
                'bank_account' => [
                    'bank' => (string) $bankCode,
                    'account' => (string) $accountNumber,
                ],
                'customer' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ],
        ];

        try {
            $response = Http::timeout(30)
                ->retry(2, 500)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.$this->secretKey,
                ])
                ->post($this->baseUrl.'/transactions/disburse', $payload);

            Log::info('Korapay disburse response', [
                'reference' => $reference,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            if (! $response->successful()) {
                $message = data_get($response->json(), 'message')
                    ?: data_get($response->json(), 'error')
                    ?: 'Bank transfer request failed.';

                Log::error('Kora Transfer Failed', [
                    'reference' => $reference,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new Exception($message);
            }

            $responseData = $response->json();

            if (! isset($responseData['status']) || $responseData['status'] !== true) {
                $message = $responseData['message'] ?? 'Transfer was not successful.';

                Log::error('Kora Transfer Unsuccessful', [
                    'reference' => $reference,
                    'response' => $responseData,
                ]);

                throw new Exception($message);
            }

            return [
                'reference' => $reference,
                'provider_reference' => $responseData['data']['reference'] ?? null,
                'status' => $responseData['data']['status'] ?? 'processing',
            ];
        } catch (Exception $e) {
            Log::critical('FundTransferService Exception', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        } catch (\Throwable $e) {
            Log::critical('FundTransferService Exception', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            throw new Exception('Transfer service temporarily unavailable. '.$e->getMessage(), 0, $e);
        }
    }
}
