<?php

namespace Tests\Feature;

use App\Models\Level;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        Level::firstOrCreate(
            ['name' => 'Basic'],
            [
                'amount' => 0,
                'reg_bonus' => 0,
                'ref_bonus' => 0,
                'min_withdrawal' => 0,
                'earning_per_view' => 0,
                'earning_per_like' => 0,
                'earning_per_comment' => 0,
            ]
        );
    }

    public function test_registration_page_loads(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_login_page_loads(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_get_process_reg_redirects_to_register(): void
    {
        $response = $this->get('/process/reg');
        $response->assertRedirect('/register');
    }

    public function test_user_can_register_via_process_reg(): void
    {
        $username = 'testreg_' . Str::lower(Str::random(6));
        $email = $username . '@example.com';

        $response = $this->post('/process/reg', [
            'name' => 'Test Register User',
            'username' => $username,
            'email' => $email,
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'username' => $username,
            'email' => $email,
        ]);

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_register_via_register_endpoint(): void
    {
        $username = 'testreg2_' . Str::lower(Str::random(6));
        $email = $username . '@example.com';

        $response = $this->post('/register', [
            'name' => 'Test Register Two',
            'username' => $username,
            'email' => $email,
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'username' => $username,
            'email' => $email,
        ]);
    }

    public function test_user_can_login_via_email(): void
    {
        $username = 'loguser_' . Str::lower(Str::random(6));
        $email = $username . '@example.com';

        $user = User::create([
            'name' => 'Login User',
            'username' => $username,
            'email' => $email,
            'password' => Hash::make('secretpassword123'),
            'referral_code' => strtoupper(Str::random(8)),
        ]);
        $user->email_verified_at = now();
        $user->save();

        $response = $this->post('/user/login', [
            'email' => $email,
            'password' => 'secretpassword123',
        ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_login_via_username(): void
    {
        $username = 'unameuser_' . Str::lower(Str::random(6));
        $email = $username . '@example.com';

        $user = User::create([
            'name' => 'Username Login User',
            'username' => $username,
            'email' => $email,
            'password' => Hash::make('secretpassword123'),
            'referral_code' => strtoupper(Str::random(8)),
        ]);
        $user->email_verified_at = now();
        $user->save();

        $response = $this->post('/user/login', [
            'email' => $username,
            'password' => 'secretpassword123',
        ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_login_via_standard_login_route(): void
    {
        $username = 'stdlogin_' . Str::lower(Str::random(6));
        $email = $username . '@example.com';

        $user = User::create([
            'name' => 'Standard Login User',
            'username' => $username,
            'email' => $email,
            'password' => Hash::make('secretpassword123'),
            'referral_code' => strtoupper(Str::random(8)),
        ]);
        $user->email_verified_at = now();
        $user->save();

        $response = $this->post('/login', [
            'email' => $email,
            'password' => 'secretpassword123',
        ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->from('/login')->post('/user/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
