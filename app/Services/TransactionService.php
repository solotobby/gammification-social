<?php

namespace App\Services;

use App\Models\Transaction;
use Symfony\Component\HttpFoundation\Response;

class TransactionService
{
    public function createTransaction(
        $user,
        string $reference,
        float $amount,
        string $currency,
        string $status,
        string $action,
        string $type,
        string $description,
        array $meta = null,
        array $customer = null

    ): Transaction {


        return  Transaction::create([
            'user_id' => $user->id,
            'ref' => $reference,
            'amount' => $amount,
            'currency' => $currency,
            'status' => $status,
            'type' => $type,
            'action' => $action,
            'description' => $description,
            'meta' => $meta,
            'customer' => $customer
        ]);
    }

    public function markSuccessful(Transaction $transaction, array $data): void
    {
        $transaction->update([
            'status' => 'success',
            'meta' => array_merge($transaction->meta ?? [], $data),
        ]);
    }

    public function markFailed(Transaction $transaction, array $data = []): void
    {
        $transaction->update([
            'status' => 'failed',
            'meta' => array_merge($transaction->meta ?? [], $data), //if supplied
        ]);
    }


}
