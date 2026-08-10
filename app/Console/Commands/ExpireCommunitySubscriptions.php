<?php

namespace App\Console\Commands;

use App\Models\CommunitySubscription;
use App\Services\CommunitySubscriptionService;
use Illuminate\Console\Command;

class ExpireCommunitySubscriptions extends Command
{
    protected $signature = 'communities:expire-subscriptions';

    protected $description = 'Expire active community subscriptions whose billing period has ended';

    public function handle(CommunitySubscriptionService $subscriptions): int
    {
        $expired = 0;

        CommunitySubscription::query()
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->orderBy('expires_at')
            ->chunkById(100, function ($rows) use ($subscriptions, &$expired) {
                foreach ($rows as $subscription) {
                    $subscriptions->expire($subscription);
                    $expired++;
                }
            });

        $this->info("Expired {$expired} community subscription(s).");

        return self::SUCCESS;
    }
}
