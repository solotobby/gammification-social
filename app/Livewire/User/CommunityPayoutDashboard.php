<?php

namespace App\Livewire\User;

use App\Models\Community;
use App\Models\CommunitySubscription;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class CommunityPayoutDashboard extends Component
{
    use WithPagination;

    public Community $community;

    public string $period = 'all';

    protected $queryString = [
        'period' => ['except' => 'all', 'as' => 'period'],
    ];

    public function mount(Community $community): void
    {
        abort_unless(
            $community->type === 'paid' && $community->user_id === auth()->id(),
            403
        );

        $this->community = $community;
    }

    public function updatedPeriod(): void
    {
        $this->resetPage();
    }

    private function paidSubscriptionQuery()
    {
        return CommunitySubscription::query()
            ->where('community_id', $this->community->id)
            ->whereIn('status', ['active', 'expired', 'cancelled']);
    }

    private function periodQuery()
    {
        $query = $this->paidSubscriptionQuery();

        return match ($this->period) {
            '7d' => $query->where('starts_at', '>=', now()->subDays(7)),
            '30d' => $query->where('starts_at', '>=', now()->subDays(30)),
            '90d' => $query->where('starts_at', '>=', now()->subDays(90)),
            default => $query,
        };
    }

    public function stats(): array
    {
        $base = $this->periodQuery();

        return [
            'gross' => (float) (clone $base)->sum('amount'),
            'platform' => (float) (clone $base)->sum('platform_fee'),
            'creator' => (float) (clone $base)->sum('creator_amount'),
            'count' => (clone $base)->count(),
            'active_members' => CommunitySubscription::query()
                ->where('community_id', $this->community->id)
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->count(),
            'fee_percent' => (int) ($this->community->platform_fee_percent ?? config('community.platform_fee_percent', 10)),
        ];
    }

    /** @return array<string, string> user_id => role */
    private function memberRolesFor(array $userIds): array
    {
        if ($userIds === []) {
            return [];
        }

        return DB::table('community_users')
            ->where('community_id', $this->community->id)
            ->whereIn('user_id', $userIds)
            ->where('status', 'active')
            ->pluck('role', 'user_id')
            ->all();
    }

    public function formatMoney(float $amount): string
    {
        $from = $this->community->currency ?? userBaseCurrency();
        $to = userBaseCurrency();

        try {
            $converted = $from === $to
                ? $amount
                : convertCurrency($amount, $from, $to);
        } catch (\Throwable) {
            $converted = $amount;
        }

        return getCurrencyCode().number_format($converted, 2);
    }

    public function render()
    {
        $stats = $this->stats();
        $community = $this->community;

        $payments = $this->periodQuery()
            ->with('user')
            ->latest('starts_at')
            ->simplePaginate(10);

        $paymentRoles = $this->memberRolesFor(
            $payments->getCollection()->pluck('user_id')->filter()->unique()->values()->all()
        );

        $activeSubscribers = CommunitySubscription::query()
            ->where('community_id', $community->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->with('user')
            ->latest('starts_at')
            ->get();

        $subscriberRoles = $this->memberRolesFor(
            $activeSubscribers->pluck('user_id')->filter()->unique()->values()->all()
        );

        return view('livewire.user.community-payout-dashboard', [
            'stats' => $stats,
            'community' => $community,
            'payments' => $payments,
            'paymentRoles' => $paymentRoles,
            'activeSubscribers' => $activeSubscribers,
            'subscriberRoles' => $subscriberRoles,
        ]);
    }
}
