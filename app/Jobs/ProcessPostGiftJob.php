<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\PayKoinService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPostGiftJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public string $senderId,
        public string $artifactId,
        public string $giftableType,
        public string $giftableId,
    ) {}

    public function handle(PayKoinService $payKoinService): void
    {
        $sender = User::find($this->senderId);

        if (! $sender) {
            return;
        }

        try {
            $payKoinService->sendGift(
                $sender,
                $this->artifactId,
                $this->giftableType,
                $this->giftableId,
            );
        } catch (\Throwable $e) {
            Log::error('ProcessPostGiftJob failed', [
                'sender_id' => $this->senderId,
                'artifact_id' => $this->artifactId,
                'giftable_type' => $this->giftableType,
                'giftable_id' => $this->giftableId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
