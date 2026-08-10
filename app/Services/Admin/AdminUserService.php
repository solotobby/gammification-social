<?php

namespace App\Services\Admin;

use App\Models\AccessCode;
use App\Models\Level;
use App\Models\Payout;
use App\Models\Post;
use App\Models\SubscriptionStat;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLevel;
use App\Models\Wallet;
use App\Models\WithdrawalMethod;
use App\Models\Withdrawals;
use App\Services\AdminAuditService;
use App\Services\FundTransferService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AdminUserService
{
    public function __construct(
        protected FundTransferService $fundTransferService,
        protected AdminAuditService $audit,
    ) {}

    public function listUsers(?string $level = null): LengthAwarePaginator
    {
        return User::query()
            ->with(['userLevel:id,user_id,plan_name,level_id,status,next_payment_date'])
            ->byLevel($level)
            ->latest()
            ->paginate(50);
    }

    public function searchUsers(string $query): LengthAwarePaginator
    {
        return User::query()
            ->with(['userLevel:id,user_id,plan_name,level_id,status,next_payment_date'])
            ->select('id', 'name', 'username', 'email', 'email_verified_at', 'created_at', 'heard')
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('username', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->orderByRaw('
                CASE
                    WHEN username = ? THEN 1
                    WHEN name = ? THEN 2
                    WHEN username LIKE ? THEN 3
                    ELSE 4
                END
            ', [$query, $query, "%{$query}%"])
            ->paginate(20)
            ->withQueryString();
    }

    public function profileData(string $userId): array
    {
        $user = User::query()
            ->with(['wallet', 'activeLevel'])
            ->findOrFail($userId);

        $level = $user?->activeLevel?->plan_name;

        return [
            'user' => $user,
            'levels' => Level::query()->orderBy('name')->get(['id', 'name', 'amount', 'reg_bonus']),
            'withdrawals' => Transaction::query()
                ->where('user_id', $user->id)
                ->whereIn('type', ['reg_bonus', 'reg_bonus_admin_assisted'])
                ->latest()
                ->limit(20)
                ->get(),
            'postsCount' => Post::query()->where('user_id', $user->id)->count(),
            'level' => $level,
            'access' => AccessCode::query()->where('email', $user->email)->latest()->first(),
            'userLevel' => Level::query()->where('name', $level)->first(),
            'withdrawalMethod' => WithdrawalMethod::query()->where('user_id', $user->id)->first(),
            'payouts' => Payout::query()
                ->where('user_id', $user->id)
                ->where('month', now()->subMonth()->format('Y-m'))
                ->first(),
            'totalWithdrawals' => Withdrawals::query()
                ->where('user_id', $user->id)
                ->where('status', 'Paid')
                ->sum('amount'),
        ];
    }

    public function updateCurrency(string $userId, string $currency): Wallet
    {
        $wallet = Wallet::query()->where('user_id', $userId)->firstOrFail();
        $wallet->currency = $currency;
        $wallet->save();

        WithdrawalMethod::query()->where('user_id', $userId)->delete();

        $this->audit->log('user.currency_updated', User::findOrFail($userId), [
            'currency' => $wallet->currency,
        ]);

        return $wallet;
    }

    public function changeStatus(string $userId, string $status): User
    {
        $user = User::query()->findOrFail($userId);
        $user->status = $status;
        $user->save();

        $this->audit->log('user.status_changed', $user, [
            'status' => $user->status,
        ]);

        return $user;
    }

    public function upgradeUser(User $user, Level $level): void
    {
        $nextPaymentDate = now()->addDays(30);

        UserLevel::updateOrCreate(
            ['user_id' => $user->id],
            [
                'level_id' => $level->id,
                'plan_name' => $level->name,
                'plan_code' => $level->id,
                'subscription_code' => $level->id,
                'email_token' => $level->id,
                'start_date' => now(),
                'status' => 'active',
                'next_payment_date' => $nextPaymentDate,
            ]
        );

        $currency = $user->wallet->currency;
        $convertedAmount = convertToBaseCurrency($level->reg_bonus, $currency);

        Transaction::create([
            'user_id' => $user->id,
            'ref' => generateTransactionRef(),
            'amount' => $convertedAmount,
            'currency' => $currency,
            'status' => 'successful',
            'type' => 'upgrade_purchase_admin_assisted',
            'action' => 'Credit',
            'description' => $user->name . ' upgraded to ' . $level->name . ' by admin',
            'meta' => null,
            'customer' => null,
        ]);

        SubscriptionStat::create([
            'user_id' => $user->id,
            'level_id' => $level->id,
            'plan_name' => $level->name,
            'amount' => convertToBaseCurrency($level->amount, $currency),
            'currency' => $currency,
            'start_date' => now(),
            'end_date' => $nextPaymentDate,
        ]);

        $this->audit->log('user.upgraded', $user, [
            'level' => $level->name,
            'amount' => $convertedAmount,
        ]);
    }

    public function creditBonus(string $userId, string $levelName): float
    {
        $levelInfo = Level::query()->where('name', $levelName)->firstOrFail();
        $wallet = Wallet::query()->where('user_id', $userId)->firstOrFail();
        $convertedAmount = convertToBaseCurrency($levelInfo->reg_bonus, $wallet->currency);

        $wallet->balance = $convertedAmount;
        $wallet->save();

        Transaction::create([
            'user_id' => $userId,
            'ref' => generateTransactionRef(),
            'amount' => $convertedAmount,
            'currency' => $wallet->currency,
            'status' => 'successful',
            'type' => 'reg_bonus_admin_assisted',
            'action' => 'Credit',
            'description' => 'Upgrade bonus by admin for ' . $levelInfo->name,
            'meta' => null,
            'customer' => null,
        ]);

        $this->audit->log('user.bonus_credited', User::findOrFail($userId), [
            'level' => $levelName,
            'amount' => $convertedAmount,
            'currency' => $wallet->currency,
        ]);

        return $convertedAmount;
    }

    public function transferWallet(User $user, float $amount, string $bankCode, string $accountNumber): void
    {
        DB::transaction(function () use ($user, $amount, $bankCode, $accountNumber) {
            $this->fundTransferService->transfer($user, $amount, $bankCode, $accountNumber);
        });

        $this->audit->log('user.wallet_transfer', $user, [
            'amount' => $amount,
            'bank_code' => $bankCode,
            'account_number' => $accountNumber,
        ]);
    }
}
