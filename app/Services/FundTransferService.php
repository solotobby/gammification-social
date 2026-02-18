<?php

namespace App\Services;

use App\Models\Payout;
use App\Models\Wallet;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

class FundTransferService{
    
    public $amount;
    public function transfer(){

        // $payload = [
        //     "source" => "balance",
        //     "amount" => //intval($amount * 100), // Convert to kobo/ng subunit
        //     "recipient" => $recipientCode,
        //     "reference" => time() . rand(1000, 9999),
        //     "reason" => "Payhankey Payout"
        // ];

        $ref =  generateTransactionRef();

            $payload = [
                "reference" => $ref,
                    "destination" => [
                        "type" => "bank_account",
                        "amount" => 100, // Convert to kobo/ng subunit
                        "currency" => "NGN",
                        "narration" => "Payhankey Payout",
                        "bank_account" => [
                            "bank" => "033",
                            "account" => "0000000000"
                        ],
                        "customer" => [
                            "name" => "John Doe",
                            "email" => ""
                        ]  
                    ]

                        ];

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . config('services.env.kora_sec')
        ])->post('https://api.korapay.com/merchant/api/v1/transactions/disburse', $payload)
            ->throw() // Automatically throws if 4xx or 5xx
            ->json();


    }

}