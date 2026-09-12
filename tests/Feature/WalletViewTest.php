<?php

namespace Tests\Feature;

use App\Livewire\User\Wallets;
use App\Models\Level;
use App\Models\User;
use App\Models\UserLevel;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class WalletViewTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'username' => 'wallet_' . Str::lower(Str::random(6)),
            'referral_code' => strtoupper(Str::random(8)),
            'email_verified_at' => now(),
            'status' => 'ACTIVE',
        ]);

        Wallet::create([
            'user_id' => $this->user->id,
            'balance' => 5000,
            'promoter_balance' => 0,
            'referral_balance' => 0,
            'currency' => 'NGN',
            'paykoin_spendable' => 100,
            'paykoin_earned' => 50,
        ]);

        $level = Level::firstOrCreate(
            ['name' => 'Creator'],
            [
                'amount' => 5000,
                'reg_bonus' => 0,
                'ref_bonus' => 0,
                'min_withdrawal' => 1000,
                'earning_per_view' => 1,
                'earning_per_like' => 1,
                'earning_per_comment' => 1,
            ]
        );

        UserLevel::create([
            'user_id' => $this->user->id,
            'level_id' => $level->id,
            'plan_name' => 'Creator',
            'status' => 'active',
            'start_date' => now(),
            'next_payment_date' => now()->addMonth(),
        ]);
    }

    public function test_wallet_view_renders_without_refresh_button(): void
    {
        Livewire::actingAs($this->user)
            ->test(Wallets::class)
            ->assertSee('Subscription & payout', false)
            ->assertDontSee('wire:click="refresh"', false)
            ->assertDontSee('fa-refresh', false);
    }
}
