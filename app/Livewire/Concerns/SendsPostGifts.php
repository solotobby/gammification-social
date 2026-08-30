<?php

namespace App\Livewire\Concerns;

// use App\Jobs\ProcessPostGiftJob;
use App\Services\PayKoinService;
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
            // ProcessPostGiftJob::dispatch(
            //     auth()->id(),
            //     $artifactId,
            //     $giftableType,
            //     $postId,
            // );

            $result = app(PayKoinService::class)->sendGift(
                auth()->user(),
                $artifactId,
                $giftableType,
                $postId,
            );

            return [
                'ok' => true,
                'pending' => false,
                'gift' => $result['gift'],
                'spendable' => $result['spendable'],
                'giftTotal' => $result['giftTotal'],
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
