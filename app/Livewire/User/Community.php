<?php

namespace App\Livewire\User;

use App\Http\Controllers\CommunityInviteController;
use App\Models\Community as CommunityModel;
use App\Models\CommunityCategory;
use App\Models\CommunityInvite;
use App\Models\CommunitySubscription;
use App\Support\CommunityFeeCalculator;
use App\Services\CommunityMembershipService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Community extends Component
{
    use WithPagination;

    // ---- list filters ----
    public string $filter = 'all';
    public string $search = '';
    public $category;

    public int $perPage = 6;

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
        $this->platformFeePercent = (int) config('community.platform_fee_percent', 10);
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
            'monthly_fee' => ['required_if:type,paid', 'nullable', 'numeric', 'min:'.communityMinimumPrice()],
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
       

        $breakdown = CommunityFeeCalculator::breakdown(
            (float) $this->monthly_fee,
            $this->platformFeePercent,
            $this->fee_payer,
        );

        $memberCharge = $breakdown['memberCharge'];
        $platformCut = $breakdown['platformCut'];
        $creatorPayout = $breakdown['creatorPayout'];

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
                'monthly_fee' => $isPaid ? $validated['monthly_fee'] : 0,
                'fee_payer' => $isPaid ? $validated['fee_payer'] : 'creator',
                'billing_type' => $isPaid ? $validated['billing_type'] : null,
                'billing_interval' => $isSubscription ? $validated['billing_interval'] : null,
                'platform_fee_percent' => $isPaid ? $this->platformFeePercent : null,
                'user_id' => auth()->id(),
            ]);

            // Whoever creates a community is its owner — always, regardless
            // of type (public/private/paid/approval). Wrapped in the same
            // transaction as the insert above so the two can never diverge:
            // either both succeed, or neither does.
            $community->members()->attach(auth()->id(), [
                'id' => (string) Str::uuid(),
                'role' => 'owner',
                'status' => 'active',
            ]);

            if ($validated['type'] === 'private') {
                CommunityInviteController::regenerateLinkInvite($community, auth()->user());
            }

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

    private function resetList(): void
    {
        $this->resetPage();
    }

    // ---- membership actions ----

    public function join(string $communityId): void
    {
        $community = CommunityModel::query()
            ->where('type', 'public')
            ->whereNull('archived_at')
            ->forUserCurrency()
            ->findOrFail($communityId);

        if (app(CommunityMembershipService::class)->isBanned($community, auth()->id())) {
            session()->flash('error', 'You cannot rejoin this community.');

            return;
        }

        if (! $community->isInCurrency()) {
            session()->flash('error', 'This community is not available in your currency.');

            return;
        }

        if (! app(CommunityMembershipService::class)->attachMember($community, auth()->id())) {
            session()->flash('error', 'Unable to join this community.');

            return;
        }

        session()->flash('status', 'Joined ' . $community->name . '.');
    }

    public function hasPendingInvite(string $communityId): bool
    {
        return CommunityInvite::where('community_id', $communityId)
            ->where('user_id', auth()->id())
            ->where('type', 'direct')
            ->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->exists();
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
            ->whereNull('archived_at')
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

        // Discovery tabs only show communities in the viewer's wallet currency.
        // Joined/mine always include memberships regardless of currency.
        if (! in_array($this->filter, ['joined', 'mine'], true)) {
            $query->forUserCurrency();
        }

        // Private communities are hidden from discovery unless you own,
        // belong to, or have a pending direct invitation.
        if ($this->filter === 'all' || ($this->filter !== 'joined' && $this->filter !== 'mine')) {
            $query->where(function ($q) use ($userId) {
                $q->where('type', '!=', 'private')
                    ->orWhere('user_id', $userId)
                    ->orWhereHas('members', fn ($m) => $m->where('users.id', $userId))
                    ->orWhereHas('invites', function ($inv) use ($userId) {
                        $inv->where('user_id', $userId)
                            ->where('type', 'direct')
                            ->where('status', 'pending')
                            ->where(function ($exp) {
                                $exp->whereNull('expires_at')->orWhere('expires_at', '>', now());
                            });
                    });
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
            ->first()?->status;
    }

    public function render()
    {
        // Paginated list — filters/search reset the page via resetList().
        $communities = $this->communitiesQuery()->paginate($this->perPage);

        return view('livewire.user.community', [
            'communities' => $communities,
            'trending' => CommunityModel::query()
                ->withCount('members')
                ->where('type', '!=', 'private')
                ->forUserCurrency()
                ->orderByDesc('members_count')
                ->limit(3)
                ->get(),
            'suggested' => CommunityModel::query()
                ->withCount('members')
                ->where('type', 'public')
                ->forUserCurrency()
                ->whereDoesntHave('members', fn($q) => $q->where('users.id', auth()->id()))
                ->inRandomOrder()
                ->limit(2)
                ->get(),
        ]);
    }
}
