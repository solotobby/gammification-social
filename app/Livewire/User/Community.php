<?php

namespace App\Livewire\User;

use App\Models\Community as CommunityModel;
use App\Models\CommunityCategory;
use App\Models\CommunitySubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Services\CommunitySubscriptionService;
use Livewire\Component;

class Community extends Component
{
    // ---- list filters ----
    public string $filter = 'all';
    public string $search = '';
    public $category;

    // ---- "Load more" pagination window ----
    public int $perPage = 6;

    private const PAGE_STEP = 6;

    // ---- create-community form state ----
    public string $name = '';
    public string $description = '';
    public string $community_categories_id = '';
    public string $type = 'public';
    public ?float $monthly_fee = null;
    public string $fee_payer = 'creator';
    public string $billing_type = 'subscription';
    public ?string $billing_interval = 'monthly';
 

    // ---- platform economics ----
    public int $platformFeePercent;

    public function mount()
    {
        $this->category = CommunityCategory::all();
        $this->filter = request()->query('filter', 'all');
        $this->search = request()->query('search', '');
        $this->platformFeePercent = (int) config('community.platform_fee_percent', 25);
         if (userBaseCurrency() === 'NGN') {
            $this->billing_type = 'one_off';
            $this->billing_interval = null;
        }
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'community_categories_id' => ['required', 'exists:community_categories,id'],
            'type' => ['required', Rule::in(['public', 'private', 'paid', 'approval'])],
            'monthly_fee' => ['required_if:type,paid', 'nullable', 'numeric', 'min:100'],
            'fee_payer' => ['required_if:type,paid', 'nullable', Rule::in(['creator', 'members'])],
            'billing_type' => ['required_if:type,paid', 'nullable', Rule::in(['one_off', 'subscription'])],
            'billing_interval' => [
                'required_if:billing_type,subscription',
                'nullable',
                Rule::in(array_keys(config('community.billing_intervals', []))),
            ],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'community_categories_id' => 'category',
            'monthly_fee' => 'price',
            'fee_payer' => 'fee payer',
            'billing_type' => 'billing type',
            'billing_interval' => 'billing interval',
        ];
    }

    public function updatedType(): void
    {
        if ($this->type !== 'paid') {
            $this->monthly_fee = null;
            $this->fee_payer = 'creator';
            $this->billing_type = 'subscription';
            $this->billing_interval = 'monthly';
        }
    }

    public function updatedBillingType(): void
    {
        // No interval on a one-off payment — it only happens once.
        if ($this->billing_type === 'one_off') {
            $this->billing_interval = null;
        } elseif (empty($this->billing_interval)) {
            $this->billing_interval = 'monthly';
        }
    }

    /**
     * Live members-pay / platform-fee / creator-receives breakdown for the
     * create-community modal — interval-aware so the preview always shows
     * the right suffix ("/mo", "/yr", one-time, etc.).
     */
    public function feePreview(): ?array
    {
        if ($this->type !== 'paid' || ! is_numeric($this->monthly_fee) || $this->monthly_fee <= 0) {
            return null;
        }

        // dd($this->platformFeePercent);

        $userCurrencyCode = getCurrencyCode(); //userBaseCurrency();
        $userBaseCurrency = userBaseCurrency();
       

        $rate = $this->platformFeePercent / 100;


        $memberCharge = $this->fee_payer === 'members'
            ? round($this->monthly_fee / (1 - $rate), 2)
            : round((float) $this->monthly_fee, 2);


        $platformCut = round($memberCharge * $rate, 2);
        $creatorPayout = round($memberCharge - $platformCut, 2);

        // dd($memberCharge, $platformCut, $creatorPayout);

        $suffix = $this->billing_type === 'one_off'
            ? ' one-time'
            : config("community.billing_intervals.{$this->billing_interval}.suffix", '');

        return compact('memberCharge', 'platformCut', 'creatorPayout', 'userCurrencyCode', 'userBaseCurrency', 'suffix');
    }

    public function createCommunity(): void
    {
        $validated = $this->validate();

        $isPaid = $validated['type'] === 'paid';
        $isSubscription = $isPaid && $validated['billing_type'] === 'subscription';

        $currency = userBaseCurrency();

        $community = DB::transaction(function () use ($validated, $isPaid, $isSubscription, $currency) {
            $community = CommunityModel::create([
                'name' => $validated['name'],
                'slug' => $this->uniqueSlug($validated['name']),
                'description' => $validated['description'],
                'community_categories_id' => $validated['community_categories_id'],
                'type' => $validated['type'],
                'currency' => $currency,
                'monthly_fee' => $isPaid ? $validated['monthly_fee'] : 0.00,
                'fee_payer' => $isPaid ? $validated['fee_payer'] : 0.00,
                'billing_type' => $isPaid ? $validated['billing_type'] : 0.00,
                'billing_interval' => $isSubscription ? $validated['billing_interval'] : 0.00,
                'platform_fee_percent' => $isPaid ? $this->platformFeePercent : 0.00,
                'user_id' => auth()->id(),
            ]);

            // Whoever creates a community is its owner — always, regardless
            // of type (public/private/paid/approval). Wrapped in the same
            // transaction as the insert above so the two can never diverge:
            // either both succeed, or neither does.
            $community->members()->attach(auth()->id(), [
                'id' => (string) Str::uuid(),
                'role' => 'owner',
            ]);

            return $community;
        });

        $this->resetForm();
        $this->resetList();

        $this->dispatch('community-created', id: $community->id);
    }

    public function resetForm(): void
    {
        $this->reset([
            'name',
            'description',
            'community_categories_id',
            'type',
            'monthly_fee',
            'fee_payer',
            'billing_type',
            'billing_interval',
        ]);
        $this->type = 'public';
        $this->fee_payer = 'creator';
        $this->billing_type = 'subscription';
        $this->billing_interval = 'monthly';
        $this->resetErrorBag();
    }

    // ---- list: search / filter / pagination ----

    public function setFilter(string $value): void
    {
        $this->filter = $value;
        $this->resetList();
    }

    public function updatedSearch(): void
    {
        $this->resetList();
    }

    public function loadMore(): void
    {
        $this->perPage += self::PAGE_STEP;
    }

    private function resetList(): void
    {
        $this->perPage = self::PAGE_STEP;
    }

    // ---- membership actions ----

    public function join(string $communityId): void
    {
        $community = CommunityModel::query()->where('type', 'public')->findOrFail($communityId);

        $community->members()->syncWithoutDetaching([
            auth()->id() => ['id' => (string) Str::uuid(), 'role' => 'member'],
        ]);
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 1;

        while (CommunityModel::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    /**
     * The main list query. Deliberately built once and reused so the
     * search/filter logic and the "how many communities exist" numbers
     * (used elsewhere in the UI) never drift apart.
     */
    private function communitiesQuery()
    {
        $userId = auth()->id();

        $query = CommunityModel::query()
            ->with('category')
            ->withCount('members')
            ->withExists(['members as is_member' => fn($q) => $q->where('users.id', $userId)]);

        if ($this->filter === 'joined') {
            $query->whereHas('members', fn($q) => $q->where('users.id', $userId));
        } elseif ($this->filter === 'mine') {
            $query->where('user_id', $userId);
        } elseif ($this->filter !== 'all') {
            $query->where('community_categories_id', $this->filter);
        }

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        return $query->latest();
    }

    public function hasPendingSubscription(string $communityId): bool
    {

        return CommunitySubscription::where('community_id', $communityId)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->exists();
    }

    public function userSubscriptionStatus(string $communityId ){
         return CommunitySubscription::where('community_id', $communityId)
            ->where('user_id', auth()->id())
            // ->where('status', 'pending')
            ->first()?->status;
    }

    public function subscribe(string $communityId)
    {
        $community = CommunityModel::where('type', 'paid')->findOrFail($communityId);

        if ($community->members()->where('users.id', auth()->id())->exists()) {
            return;
        }

        $service = app(CommunitySubscriptionService::class);
        $existing = $service->pendingOrActiveFor($community, auth()->user());

        if ($existing?->status === 'active') {
            return;
        }

        $subscription = $existing ?? $service->initiate($community, auth()->user());
      
        // TODO: payment gateway — see the same note in CommunityDetails::subscribe()

        try {
            $checkoutUrl = $service->checkoutUrl($subscription);
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', "We couldn't start your payment. Please try again shortly.");
            return;
        }
        // dd($checkoutUrl);
        return redirect($checkoutUrl);

        // $service->activate($subscription);
        // session()->flash('status', 'Payment step isn\'t wired up yet — this is a placeholder.');
    }

    public function render()
    {
        // simplePaginate() skips the extra COUNT(*) query a normal paginator
        // needs for page numbers — all a "Load more" button needs is
        // hasMorePages(), so there's no reason to pay for that count.
        $communities = $this->communitiesQuery()->simplePaginate($this->perPage);

        return view('livewire.user.community', [
            'communities' => $communities,
            'trending' => CommunityModel::query()
                ->withCount('members')
                ->orderByDesc('members_count')
                ->limit(3)
                ->get(),
            'suggested' => CommunityModel::query()
                ->withCount('members')
                ->where('type', 'public')
                ->whereDoesntHave('members', fn($q) => $q->where('users.id', auth()->id()))
                ->inRandomOrder()
                ->limit(2)
                ->get(),
        ]);
    }
}
