<?php

namespace App\Services;

use App\Models\Payout;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PublicSiteService
{
    public function topEarners(int $limit = 10): array
    {
        $topPayouts = Payout::query()
            ->select(
                'payouts.user_id',
                'users.name',
                'users.username',
                DB::raw("
                    CASE
                        WHEN payouts.created_at BETWEEN '" .
                            Carbon::now()->subMonth()->startOfMonth() . "' AND '" .
                            Carbon::now()->subMonth()->endOfMonth() . "'
                        THEN 'last_month'
                        ELSE 'all_time'
                    END AS period
                "),
                DB::raw('SUM(payouts.amount) as total_paid')
            )
            ->join('users', 'users.id', '=', 'payouts.user_id')
            ->whereIn('payouts.status', ['queued', 'paid', 'Queued', 'Paid'])
            ->groupBy('payouts.user_id', 'users.name', 'users.username', 'period')
            ->orderBy('period')
            ->orderByDesc('total_paid')
            ->get();

        return [
            'lastMonth' => $topPayouts->where('period', 'last_month')->values()->take($limit),
            'allTime' => $topPayouts->where('period', 'all_time')->values()->take($limit),
        ];
    }

    public function landingPreviewEarners(int $limit = 3): Collection
    {
        $earners = $this->topEarners(max($limit, 10))['lastMonth'];

        if ($earners->isEmpty()) {
            $earners = $this->topEarners(max($limit, 10))['allTime'];
        }

        return $earners->take($limit)->values();
    }

    public function platformStats(): array
    {
        return [
            'users' => User::role('user')->count(),
            'verified' => User::role('user')->whereNotNull('email_verified_at')->count(),
            'paid_out' => (float) Payout::query()
                ->whereIn('status', ['queued', 'paid', 'Queued', 'Paid'])
                ->sum('amount'),
        ];
    }
}
