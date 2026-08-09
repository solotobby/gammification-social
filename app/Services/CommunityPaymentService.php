<?php

namespace App\Services;

use App\Models\Community;


class CommunityPaymentService
{

     private string $flutterwaveBaseUrl;
    private string $korapayBaseUrl;
    private string $flutterwaveSecretKey;
    private string $korapaySecretKey;
    
    public TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->flutterwaveBaseUrl = rtrim(config('services.env.flutterwave_base_url'), '/');
        $this->flutterwaveSecretKey = config('services.env.flutterwave_secret_key');
        // $this->korapayBaseUrl = rtrim(config('services.env.korapay_base_url'), '/');
        // $this->korapaySecretKey = config('services.env.korapay_secret_key');
        $this->transactionService = $transactionService;
    }



   
    public function generateLink($communityId)
    {
        
        // Logic to generate a payment link for the community
        $community = Community::findOrFail($communityId);

        //get user base currency
        $userBaseCurrency = userBaseCurrency();

        if($userBaseCurrency == 'NGN') {
            // Use Korapay for NGN currency
            $paymentLink = $this->createKorapayPaymentLink($community);

        } else {
            // Use Flutterwave for other currencies
            $paymentLink = $this->createFlutterwavePaymentLink($community);
        }   


        // Assuming you have a method to create a payment link
        // $paymentLink = $this->createPaymentLink($community);

        return $paymentLink;
    }

    private function createSubscription(Community $community)
    {
        // Logic to create a subscription for the community
        // This could involve creating a record in your database, etc.
    }

    private function createKorapayPaymentLink(Community $community)
    {
        $amountNGN = convertToBaseCurrency($community->price, 'NGN');
        // return "https://korapay.com/pay/{$community->id}?amount={$amountNGN}";
    }

    // public function initializeCheckout(CommunitySubscription $subscription): string
    // {
        
    // }

    private function createFlutterwavePaymentLink(Community $community)
    {

        // create Subscription

        // $reference = $subscription->gateway_reference ?: 'sub_'.$subscription->id.'_'.Str::random(8);
        // $currency = $subscription->community->currency ?: 'USD';

        // $response = Http::withToken($this->secretKey)->acceptJson()
        //     ->post("{$this->baseUrl}/payments", [
        //         'tx_ref' => $reference,
        //         'amount' => (float) $subscription->amount,
        //         'currency' => $currency,
        //         'redirect_url' => route('community.show', $subscription->community),
        //         'customer' => [
        //             'email' => $subscription->user->email,
        //             'name' => $subscription->user->name,
        //         ],
        //         'customizations' => ['title' => $subscription->community->name],
        //     ]);

        // if (! $response->successful() || $response->json('status') !== 'success') {
        //     throw new RuntimeException('Flutterwave payment initialization failed: '.$response->body());
        // }

        // $subscription->update([
        //     'gateway' => 'flutterwave',
        //     'gateway_reference' => $reference,
        //     'gateway_meta' => $response->json('data'),
        // ]);

        // return $response->json('data.link');
        // $amountUSD = convertToBaseCurrency($community->price, 'USD');
        // return "https://flutterwave.com/pay/{$community->id}?amount={$amountUSD}";
    }


}