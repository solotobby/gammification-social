<?php

namespace App\Services;

use App\Models\Transaction;
use Symfony\Component\HttpFoundation\Response;

class TransactionService
{
    public function createTransaction(
        $user,
        string $idempotencyKey,
        string $provider,
        string $reference,
        float $amount,
        string $currency,
        string $status,
        string $action,
        string $type,
        string $description,
        array $meta = [],
        ?array $customer = null

    ): Transaction {


        return  Transaction::create([
            'user_id' => $user->id,
            'idempotency_key' => $idempotencyKey,
            'provider' => $provider,
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
            'status' => 'successful',
            'meta' => array_merge($transaction->meta ?? [], $data),
            'paid_at' => now(),
        ]);
    }

    public function markFailed(Transaction $transaction, array $data = []): void
    {
        $transaction->update([
            'status' => 'failed',
            'meta' => array_merge($transaction->meta ?? [], $data), //if supplied
        ]);
    }

     public function markProcessing(Transaction $transaction, array $data = []): void
    {
        $transaction->update([
            'status' => 'processing',
            'meta' => array_merge($transaction->meta ?? [], $data), //if supplied
        ]);
    }

     public function markCancelled(Transaction $transaction, array $data = []): void
    {
        $transaction->update([
            'status' => 'cancelled',
            'meta' => array_merge($transaction->meta ?? [], $data), //if supplied
        ]);
    }

     public function markFlagged(Transaction $transaction, array $data = []): void
    {
        $transaction->update([
            'status' => 'flagged',
            'meta' => array_merge($transaction->meta ?? [], $data), //if supplied
        ]);
    }

    

    

}
