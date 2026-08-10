<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'name',
        'slug',
        'currency',
        'community_categories_id',
        'image',
        'banner',
        'description',
        'type',
        'monthly_fee',
        'billing_type',
        'billing_interval',
        'fee_payer',
        'platform_fee_percent',
        'user_id',
    ];

    protected $casts = [
        'monthly_fee' => 'decimal:2',
        'platform_fee_percent' => 'integer',
    ];

    /** Display-only palette cycled deterministically per community — no extra column needed. */
    private const COLOR_PALETTE = ['#5A4FDC', '#1FAE64', '#EF4467', '#37A2F4', '#E3A421', '#9C7BF5'];

    /**
     * Communities are looked up by slug in routes (/community/{community}),
     * not by their UUID — matches the shareable public link built earlier.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(CommunityCategory::class, 'community_categories_id');
    }

    public function posts()
    {
        return $this->hasMany(CommunityPost::class);
    }

    /**
     * Active members only. This is the relation used everywhere counts,
     * "is this person a member" checks, and the feed care about — a banned
     * member should never show up as a member anywhere in the app.
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'community_users')
            ->wherePivot('status', 'active')
            ->withPivot(['role', 'status'])
            ->withTimestamps();
    }

    public function bannedMembers()
    {
        return $this->belongsToMany(User::class, 'community_users')
            ->wherePivot('status', 'banned')
            ->withPivot(['role', 'status'])
            ->withTimestamps();
    }

    public function joinRequests()
    {
        return $this->hasMany(CommunityJoinRequest::class);
    }

    public function pendingJoinRequests()
    {
        return $this->joinRequests()->where('status', 'pending')->latest();
    }

    public function invites()
    {
        return $this->hasMany(CommunityInvite::class);
    }

    public function payouts()
    {
        return $this->hasMany(CommunityPayout::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(CommunitySubscription::class);
    }

    public function isPrivate(): bool
    {
        return $this->type === 'private';
    }

    /**
     * Two-letter monogram for the community's icon tile, e.g.
     * "Side Hustle Naija" -> "SH".
     */
    public function getInitialsAttribute(): string
    {
        $words = preg_split('/\s+/', trim((string) $this->name));

        $first = mb_substr($words[0] ?? '', 0, 1);
        $second = mb_substr($words[1] ?? '', 0, 1);

        return mb_strtoupper($first.$second) ?: '—';
    }

    /**
     * A stable brand color for the icon tile, deterministic per community
     * (same community always renders the same color, no DB column needed).
     */
    public function getColorAttribute(): string
    {
        $palette = self::COLOR_PALETTE;

        return $palette[crc32((string) $this->id) % count($palette)];
    }

    /**
     * What a member actually pays, per charge.
     *
     * If the creator absorbs the platform fee, this is simply the sticker
     * price they set. If members bear the cost, the price is grossed up so
     * that after Payhankey takes its cut, the creator still nets exactly
     * the amount they asked for. This holds whether it's a one-off payment
     * or a recurring subscription charge — the math is identical, only the
     * frequency (billing_interval) differs.
     */
    public function getMemberChargeAttribute(): ?float
    {
        if ($this->type !== 'paid' || is_null($this->monthly_fee)) {
            return null;
        }

        $rate = $this->feeRate();

        return $this->fee_payer === 'members'
            ? round((float) $this->monthly_fee / (1 - $rate), 2)
            : (float) $this->monthly_fee;
    }

    public function getPlatformFeeAmountAttribute(): ?float
    {
        if (is_null($this->member_charge)) {
            return null;
        }

        return round($this->member_charge * $this->feeRate(), 2);
    }

    public function getCreatorPayoutAttribute(): ?float
    {
        if (is_null($this->member_charge)) {
            return null;
        }

        return round($this->member_charge - $this->platform_fee_amount, 2);
    }

    /**
     * Short, human-readable billing description, e.g. "One-off payment",
     * "Billed monthly", "Billed annually" — used anywhere a price is shown
     * (cards, hero, about tab) so the phrasing never drifts out of sync.
     */
    public function getBillingLabelAttribute(): ?string
    {
        if ($this->type !== 'paid') {
            return null;
        }

        if ($this->billing_type === 'one_off') {
            return 'One-off payment';
        }

        $adverb = config("community.billing_intervals.{$this->billing_interval}.adverb", $this->billing_interval);

        return $this->billing_interval ? "Billed {$adverb}" : 'Subscription';
    }

    /**
     * The short suffix appended to a formatted price, e.g. "/mo", "/yr",
     * or empty for a one-off payment.
     */
    public function getPriceSuffixAttribute(): string
    {
        if ($this->billing_type === 'one_off') {
            return '';
        }

        return config("community.billing_intervals.{$this->billing_interval}.suffix", '');
    }

    private function feeRate(): float
    {
        return ((int) ($this->platform_fee_percent ?? 0)) / 100;
    }
}