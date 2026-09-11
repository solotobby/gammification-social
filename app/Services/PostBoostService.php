<?php

namespace App\Services;

use App\Models\PaykoinTransaction;
use App\Models\Post;
use App\Models\PostBoost;
use App\Models\PostBoostClick;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\PostBoostCompletedNotification;
use App\Notifications\PostBoostedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Stevebauman\Location\Facades\Location;

class PostBoostService
{
    public const RATE_PER_CLICK = 3; // 3 PayKoin = ₦30 / click

    /**
     * Launch a new post boost campaign.
     *
     * @param  array{target_url: string, cta: string, clicks: int, platforms: array<string, bool>}  $data
     */
    public function createBoost(User $user, Post $post, array $data): PostBoost
    {
        if ($post->user_id !== $user->id) {
            throw new RuntimeException('Unauthorized: You can only boost your own posts.');
        }

        $targetUrl = trim($data['target_url'] ?? '');
        if (empty($targetUrl) || ! filter_var($targetUrl, FILTER_VALIDATE_URL)) {
            throw new RuntimeException('Please provide a valid destination URL (e.g. https://yourwebsite.com).');
        }

        $cta = trim($data['cta'] ?? 'Shop Now');
        $clicks = (int) ($data['clicks'] ?? 100);

        if ($clicks < 10) {
            throw new RuntimeException('Minimum click package is 10 clicks.');
        }

        $platforms = $data['platforms'] ?? ['payhankey' => true, 'partner' => true];
        $platformPayhankey = (bool) ($platforms['payhankey'] ?? true);
        $platformPartner = (bool) ($platforms['partner'] ?? true);

        if (! $platformPayhankey && ! $platformPartner) {
            throw new RuntimeException('At least one advertising platform (Payhankey or Partner Websites) must be selected.');
        }

        $pkCost = $clicks * self::RATE_PER_CLICK;
        $ref = generateTransactionRef('BST');

        return DB::transaction(function () use (
            $user,
            $post,
            $targetUrl,
            $cta,
            $clicks,
            $pkCost,
            $platformPayhankey,
            $platformPartner,
            $ref
        ) {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

            if (! $wallet) {
                throw new RuntimeException('User wallet not found.');
            }

            if ((int) $wallet->paykoin_spendable < $pkCost) {
                throw new RuntimeException("Insufficient PayKoin balance. You need {$pkCost} PK but only have {$wallet->paykoin_spendable} PK.");
            }

            // Debit user spendable PayKoin
            $wallet->paykoin_spendable = (int) $wallet->paykoin_spendable - $pkCost;
            $wallet->save();

            // Record Paykoin transaction audit ledger
            PaykoinTransaction::create([
                'user_id' => $user->id,
                'type' => 'post_boost',
                'pk_amount' => -$pkCost,
                'fiat_amount' => (float) ($pkCost * 10), // ₦10 / PK
                'currency' => $wallet->currency ?: 'NGN',
                'ref' => $ref,
                'description' => "Boosted post #{$post->id} for {$clicks} guaranteed clicks",
                'meta' => [
                    'post_id' => $post->id,
                    'clicks' => $clicks,
                    'rate_pk' => self::RATE_PER_CLICK,
                    'target_url' => $targetUrl,
                    'cta' => $cta,
                    'platform_payhankey' => $platformPayhankey,
                    'platform_partner' => $platformPartner,
                ],
            ]);

            // Create Post Boost record
            $boost = PostBoost::create([
                'post_id' => $post->id,
                'user_id' => $user->id,
                'target_url' => $targetUrl,
                'cta' => $cta,
                'total_clicks' => $clicks,
                'delivered_clicks' => 0,
                'remaining_clicks' => $clicks,
                'pk_cost' => $pkCost,
                'rate_pk' => self::RATE_PER_CLICK,
                'platform_payhankey' => $platformPayhankey,
                'platform_partner' => $platformPartner,
                'status' => 'active',
                'ref' => $ref,
            ]);

            // Update post status: flagged as boosted and creator monetization paused
            $post->is_boosted = true;
            $post->monetization_paused = true;
            $post->save();

            // Dispatch in-app notification and email confirmation
            try {
                $user->notify(new PostBoostedNotification($boost));
            } catch (\Throwable $e) {
                report($e);
            }

            return $boost;
        });
    }

    /**
     * Record a verified click from a user or visitor, logging location, device, and browser.
     */
    public function recordClick(PostBoost $boost, Request $request, string $platform = 'payhankey'): string
    {
        $targetUrl = $boost->target_url;

        // If campaign is not active or has run out of clicks, just redirect
        if ($boost->status !== 'active' || $boost->remaining_clicks <= 0) {
            return $targetUrl;
        }

        $ip = $this->resolveClientIp($request);
        $loc = $this->resolveLocation($ip);
        $ua = (string) $request->userAgent();
        $deviceInfo = $this->parseUserAgent($ua);

        DB::transaction(function () use ($boost, $request, $platform, $ip, $loc, $deviceInfo, $ua) {
            $lockedBoost = PostBoost::where('id', $boost->id)->lockForUpdate()->first();

            if (! $lockedBoost || $lockedBoost->status !== 'active' || $lockedBoost->remaining_clicks <= 0) {
                return;
            }

            // Record click event with full visitor telemetry
            PostBoostClick::create([
                'post_boost_id' => $lockedBoost->id,
                'post_id' => $lockedBoost->post_id,
                'user_id' => auth()->id() ?? null,
                'platform' => in_array($platform, ['payhankey', 'partner'], true) ? $platform : 'payhankey',
                'ip' => $ip,
                'country' => $loc['country'] ?? null,
                'region' => $loc['region'] ?? null,
                'city' => $loc['city'] ?? null,
                'device' => $deviceInfo['device'] ?? 'Desktop',
                'browser' => $deviceInfo['browser'] ?? 'Unknown',
                'os' => $deviceInfo['os'] ?? 'Unknown',
                'user_agent' => Str::limit($ua, 500),
                'referrer' => Str::limit((string) $request->header('referer'), 500),
            ]);

            $lockedBoost->delivered_clicks += 1;
            $lockedBoost->remaining_clicks = max(0, $lockedBoost->remaining_clicks - 1);

            // Increment clicks count on post if applicable
            $post = Post::where('id', $lockedBoost->post_id)->first();
            if ($post) {
                $post->increment('clicks');
            }

            // Check campaign completion
            if ($lockedBoost->remaining_clicks <= 0) {
                $lockedBoost->status = 'completed';

                // Check if any other active boost remains for this post
                $hasOtherActive = PostBoost::where('post_id', $lockedBoost->post_id)
                    ->where('id', '!=', $lockedBoost->id)
                    ->where('status', 'active')
                    ->where('remaining_clicks', '>', 0)
                    ->exists();

                if (! $hasOtherActive && $post) {
                    $post->is_boosted = false;
                    $post->monetization_paused = false;
                    $post->save();
                }

                // Notify creator of campaign completion
                try {
                    $creator = User::find($lockedBoost->user_id);
                    if ($creator) {
                        $creator->notify(new PostBoostCompletedNotification($lockedBoost));
                    }
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            $lockedBoost->save();
        });

        return $targetUrl;
    }

    /**
     * Resolve visitor client IP address.
     */
    protected function resolveClientIp(Request $request): string
    {
        if (app()->environment('local') && config('admin.bypass_ip_check_on_local', true)) {
            return (string) config('admin.local_client_ip', '127.0.0.1');
        }

        return (string) $request->getClientIp();
    }

    /**
     * Resolve visitor geographic location via IP.
     *
     * @return array{country: string, region: string, city: string}
     */
    protected function resolveLocation(string $ip): array
    {
        try {
            if ($ip === '127.0.0.1' || $ip === '::1') {
                return ['country' => 'Localhost', 'region' => 'Local', 'city' => 'Local'];
            }

            $loc = Location::get($ip);

            if ($loc) {
                return [
                    'country' => (string) ($loc->countryName ?? ''),
                    'region' => (string) ($loc->regionName ?? ''),
                    'city' => (string) ($loc->cityName ?? ''),
                ];
            }
        } catch (\Throwable $e) {
            // Fallback gracefully on network timeout or lookup failure
        }

        return ['country' => '', 'region' => '', 'city' => ''];
    }

    /**
     * Parse User-Agent string to extract device type, browser, and OS.
     *
     * @return array{device: string, browser: string, os: string}
     */
    public function parseUserAgent(?string $ua): array
    {
        $ua = (string) $ua;

        // Device detection
        $device = 'Desktop';
        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $ua)) {
            $device = 'Tablet';
        } elseif (preg_match('/Mobile|Android|iP(hone|od)|IEMobile|BlackBerry|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/i', $ua)) {
            $device = 'Mobile';
        }

        // Operating System detection
        $os = 'Unknown OS';
        if (preg_match('/iphone/i', $ua)) {
            $os = 'iOS (iPhone)';
        } elseif (preg_match('/ipad/i', $ua)) {
            $os = 'iOS (iPad)';
        } elseif (preg_match('/android/i', $ua)) {
            $os = 'Android';
        } elseif (preg_match('/windows nt 10/i', $ua)) {
            $os = 'Windows 10/11';
        } elseif (preg_match('/windows nt 6\.3/i', $ua)) {
            $os = 'Windows 8.1';
        } elseif (preg_match('/windows nt 6\.2/i', $ua)) {
            $os = 'Windows 8';
        } elseif (preg_match('/windows nt 6\.1/i', $ua)) {
            $os = 'Windows 7';
        } elseif (preg_match('/macintosh|mac os x/i', $ua)) {
            $os = 'macOS';
        } elseif (preg_match('/linux/i', $ua)) {
            $os = 'Linux';
        }

        // Browser detection
        $browser = 'Unknown Browser';
        if (preg_match('/edg/i', $ua)) {
            $browser = 'Edge';
        } elseif (preg_match('/chrome|crios/i', $ua) && ! preg_match('/edg/i', $ua)) {
            $browser = 'Chrome';
        } elseif (preg_match('/firefox|fxios/i', $ua)) {
            $browser = 'Firefox';
        } elseif (preg_match('/safari/i', $ua) && ! preg_match('/chrome|crios|android/i', $ua)) {
            $browser = 'Safari';
        } elseif (preg_match('/opera|opr/i', $ua)) {
            $browser = 'Opera';
        }

        return [
            'device' => $device,
            'browser' => $browser,
            'os' => $os,
        ];
    }
}
