<?php

namespace App\Livewire\Concerns;

use App\Jobs\ProcessPostGiftJob;
use App\Services\PayKoinService;
use Illuminate\Support\Str;
use Livewire\Attributes\Renderless;

trait SendsPostGifts
{
    #[Renderless]
    public function sendPostGift(string $postId, string $artifactId, string $giftableType = 'community_post'): array
    {
        if (! auth()->check()) {
            return ['ok' => false, 'message' => 'Sign in to send gifts.'];
        }

        try {
            $preview = app(PayKoinService::class)->validateCanSendGift(
                auth()->user(),
                $artifactId,
                $giftableType,
                $postId,
            );

            ProcessPostGiftJob::dispatch(
                auth()->id(),
                $artifactId,
                $giftableType,
                $postId,
            );

            $pendingId = 'pending-'.Str::uuid()->toString();

            return [
                'ok' => true,
                'pending' => true,
                'gift' => [
                    'id' => $pendingId,
                    'emoji' => $preview['artifact']['emoji'],
                    'name' => $preview['artifact']['name'],
                    'price' => $preview['pk_amount'],
                    'sender' => auth()->user()->username,
                ],
                'spendable' => $preview['spendable_after'],
                'giftTotal' => $preview['gift_total_after'],
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    #[Renderless]
    public function loadPostGifts(string $postId, string $giftableType = 'community_post'): array
    {
        if (! auth()->check()) {
            return ['total' => 0, 'recent' => [], 'spendable' => 0];
        }

        auth()->user()->loadMissing('wallet');

        $gifts = app(PayKoinService::class)->giftsFor($giftableType, $postId);

        return [
            ...$gifts,
            'spendable' => (int) (auth()->user()->wallet?->paykoin_spendable ?? 0),
        ];
    }
}
