<?php

namespace App\Livewire\User;

use App\Mail\GeneralMail;
use App\Models\Community;
use App\Models\CommunityCategory;
use App\Models\CommunityJoinRequest;
use App\Models\CommunityPostComment;
use App\Models\CommunityPostView;
use App\Models\CommunitySubscription;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\CommunitySubscriptionService;

class CommunityDetails extends Component
{
    use WithFileUploads;

    public Community $community;
    public string $tab = 'feed';

    // ---- post composer ----
    public string $content = '';
    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile[] */
    public array $media = [];

    // ---- inline comment inputs, keyed by post id ----
    public array $newComment = [];

    // ---- "Load more" windows ----
    public int $postsPerPage = 10;
    public int $membersPerPage = 10;
    public string $memberSearch = '';
    private const PAGE_STEP = 10;

    // ---- settings form ----
    public string $settingsName = '';
    public string $settingsDescription = '';
    public string $settingsCategoryId = '';
    public string $settingsType = 'public';
    public string $settingsFeePayer = 'creator';
    public ?float $settingsPrice = null;
    public string $settingsBillingType = 'subscription';
    public ?string $settingsBillingInterval = 'monthly';
    public $settingsLogo;
    public $settingsBanner;
    public int $platformFeePercent;
    public array $billingIntervals = [];

    public function mount(Community $community): void
    {
        $this->community = $community;
        $this->platformFeePercent = (int) config('community.platform_fee_percent', 25);
        $this->billingIntervals = config('community.billing_intervals', []);

        $this->settingsName = $community->name;
        $this->settingsDescription = (string) $community->description;
        $this->settingsCategoryId = (string) $community->community_categories_id;
        $this->settingsType = $community->type;
        $this->settingsFeePayer = $community->fee_payer ?? 'creator';
        $this->settingsPrice = $community->monthly_fee ? (float) $community->monthly_fee : null;
        $this->settingsBillingType = $community->billing_type ?? 'subscription';
        $this->settingsBillingInterval = $community->billing_interval ?? 'monthly';
    }

    public function isOwner(): bool
    {
        return $this->community->user_id === auth()->id();
    }

    public function isMember(): bool
    {
        return $this->community->members()->where('users.id', auth()->id())->exists();
    }

    public function isAdmin(): bool
    {
        return $this->community->members()
            ->where('users.id', auth()->id())
            ->wherePivot('role', 'admin')
            ->exists();
    }

    public function isOwnerOrAdmin(): bool
    {
        return $this->isOwner() || $this->isAdmin();
    }

    /**
     * Public communities' member lists are open to anyone (browsing a
     * public community shouldn't require joining first). Every other type
     * — private, approval, paid — keeps its member list admin/owner-only.
     */
    public function canViewMembers(): bool
    {
        return $this->community->type === 'public' || $this->isOwnerOrAdmin();
    }

    // =========================================================
    // Composer / feed
    // =========================================================

    public function removeMedia(int $index): void
    {
        unset($this->media[$index]);
        $this->media = array_values($this->media);
    }

    public function publishPost(): void
    {
        // The composer is already hidden from non-members in the view, but
        // that's a UI convenience, not security — a direct Livewire request
        // could still call this method. Enforce membership here too, for
        // every community type: you must have joined before you can post.
        if (! $this->isMember()) {
            $this->addError('content', 'Only members can post in this community.');

            return;
        }

        $this->validate([
            'content' => ['nullable', 'string', 'max:2000'],
            'media' => ['array', 'max:4'],
            'media.*' => ['file', 'mimes:jpg,jpeg,png,gif,mp4,mov', 'max:20480'],
        ]);

        if (trim($this->content) === '' && empty($this->media)) {
            $this->addError('content', 'Write something or add a photo/video before posting.');

            return;
        }

        DB::transaction(function () {
            $post = $this->community->posts()->create([
                'user_id' => auth()->id(),
                'content' => $this->content,
            ]);

            foreach ($this->media as $index => $file) {
                $isVideo = str_starts_with((string) $file->getMimeType(), 'video');

                $path = $file->store('communities/' . $this->community->id . '/posts', 'spaces');

                $post->media()->create([
                    'path' => $path,
                    'type' => $isVideo ? 'video' : 'image',
                    'sort' => $index,
                ]);
            }
        });

        $this->reset(['content', 'media']);
        $this->postsPerPage = self::PAGE_STEP;
    }

    public function loadMorePosts(): void
    {
        $this->postsPerPage += self::PAGE_STEP;
    }

    public function toggleLike(string $postId): void
    {
        $post = $this->community->posts()->findOrFail($postId);
        $result = $post->likes()->toggle(auth()->id());

        if (! empty($result['attached'])) {
            $post->increment('likes_count');
        } elseif (! empty($result['detached'])) {
            $post->decrement('likes_count');
        }
    }

    public function addComment(string $postId): void
    {
        $text = trim($this->newComment[$postId] ?? '');

        if ($text === '' || mb_strlen($text) > 500) {
            return;
        }

        $post = $this->community->posts()->findOrFail($postId);

        CommunityPostComment::create([
            'community_post_id' => $post->id,
            'user_id' => auth()->id(),
            'content' => $text,
        ]);

        $post->increment('comments_count');

        $this->newComment[$postId] = '';
    }

    /**
     * Admins and the owner can remove any post in the community — not just
     * their own. Regular members can't delete posts at all (not even
     * their own) through this method.
     */
    public function deletePost(string $postId): void
    {
        if (! $this->isOwnerOrAdmin()) {
            session()->flash('error', 'Only admins can delete posts.');

            return;
        }

        $post = $this->community->posts()->with('media')->find($postId);

        if (! $post) {
            return;
        }

        foreach ($post->media as $item) {
            \Illuminate\Support\Facades\Storage::disk('spaces')->delete($item->path);
        }

        $post->delete();

        session()->flash('status', 'Post deleted.');
    }

    /**
     * Records a view the first time this user sees this post, and only
     * that first time — the unique (post, user) index on
     * community_post_views makes repeat visits a no-op instead of
     * inflating the count. Called from the view via wire:init so it fires
     * once per post per page load.
     */
    public function recordView(string $postId): void
    {
        $post = $this->community->posts()->find($postId);

        if (! $post) {
            return;
        }

        $view = CommunityPostView::firstOrCreate(
            ['community_post_id' => $post->id, 'user_id' => auth()->id()],
            ['ip_address' => request()->ip()]
        );

        if ($view->wasRecentlyCreated) {
            $post->increment('views_count');
        }
    }

    // =========================================================
    // Membership
    // =========================================================

    public function join(): void
    {
        if ($this->community->type !== 'public') {
            return;
        }

        $this->community->members()->syncWithoutDetaching([
            auth()->id() => ['id' => (string) Str::uuid(), 'role' => 'member', 'status' => 'active'],
        ]);
    }

    /**
     * Sends a join request to the community's admin for an approval-type
     * community. firstOrNew keyed on (community_id, user_id, status:pending)
     * means a second click while one is already pending is a no-op — but a
     * new request can always be raised again after a previous one was
     * denied (that old row has status:denied, so it just won't match).
     */
    public function requestToJoin(): void
    {
        if ($this->community->type !== 'approval' || $this->isMember() || $this->hasPendingRequest()) {
            return;
        }

        $request = CommunityJoinRequest::firstOrNew([
            'community_id' => $this->community->id,
            'user_id' => auth()->id(),
            'status' => 'pending',
        ]);

        if ($request->exists) {
            return;
        }

        $request->id = (string) Str::uuid();
        $request->save();

        $owner = $this->community->user;
        $requester = auth()->user();

        $owner?->notify(new GeneralNotification([
            'title' => displayName($requester->name) . ' requested to join ' . $this->community->name,
            'message' => displayName($requester->name) . ' wants to join your community "' . $this->community->name . '".',
            'icon' => 'fa-user-plus text-primary',
            'url' => url('community/' . $this->community->slug),
        ]));

        if ($owner?->email) {
            Mail::to($owner->email)->send(new GeneralMail(
                (object) ['name' => $owner->name, 'email' => $owner->email],
                'New join request for ' . $this->community->name,
                displayName($requester->name) . ' has requested to join your community "' . $this->community->name
                    . '". Review and respond from the Members tab of your community settings.'
            ));
        }

        session()->flash('status', 'Your request has been sent to the admin.');
    }

    public function hasPendingRequest(): bool
    {
        return CommunityJoinRequest::where('community_id', $this->community->id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->exists();
    }

    public function approveRequest(string $requestId): void
    {
        if (! $this->authorizeManage()) {
            return;
        }

        $request = $this->community->joinRequests()->find($requestId);

        if (! $request || $request->status !== 'pending') {
            return;
        }

        DB::transaction(function () use ($request) {
            $this->community->members()->syncWithoutDetaching([
                $request->user_id => ['id' => (string) Str::uuid(), 'role' => 'member', 'status' => 'active'],
            ]);

            $request->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);
        });

        $requester = $request->user;

        $requester?->notify(new GeneralNotification([
            'title' => 'Your request to join ' . $this->community->name . ' was approved',
            'message' => 'You are now a member of "' . $this->community->name . '".',
            'icon' => 'fa-check-circle text-success',
            'url' => url('community/' . $this->community->slug),
        ]));

        if ($requester?->email) {
            Mail::to($requester->email)->send(new GeneralMail(
                (object) ['name' => $requester->name, 'email' => $requester->email],
                "You're in! Approved for " . $this->community->name,
                'Good news — your request to join "' . $this->community->name . '" has been approved. '
                    . 'You can visit the community now.'
            ));
        }

        session()->flash('status', 'Request approved.');
    }

    public function denyRequest(string $requestId, string $reason = ''): void
    {
        if (! $this->authorizeManage()) {
            return;
        }

        $request = $this->community->joinRequests()->find($requestId);

        if (! $request || $request->status !== 'pending') {
            return;
        }

        $reason = trim($reason);

        $request->update([
            'status' => 'denied',
            'reason' => $reason !== '' ? $reason : null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $requester = $request->user;

        $requester?->notify(new GeneralNotification([
            'title' => 'Your request to join ' . $this->community->name . ' was declined',
            'message' => $reason !== '' ? "Reason: {$reason}" : 'The admin did not approve your request at this time.',
            'icon' => 'fa-times-circle text-danger',
            'url' => url('community/' . $this->community->slug),
        ]));

        if ($requester?->email) {
            Mail::to($requester->email)->send(new GeneralMail(
                (object) ['name' => $requester->name, 'email' => $requester->email],
                'Update on your request to join ' . $this->community->name,
                'Your request to join "' . $this->community->name . '" was not approved.'
                    . ($reason !== '' ? " Reason given: {$reason}" : '')
            ));
        }

        session()->flash('status', 'Request denied.');
    }

    public function loadMoreMembers(): void
    {
        $this->membersPerPage += self::PAGE_STEP;
    }

    public function updatedMemberSearch(): void
    {
        $this->membersPerPage = self::PAGE_STEP;
    }

    /**
     * Only the owner can grant/revoke admin — not admins themselves.
     * Letting an admin create other admins (or demote a fellow admin) is a
     * bigger permission than the member-management actions below, so it's
     * kept to the owner alone.
     */
    public function promoteToAdmin(string $userId): void
    {
        if (! $this->isOwner() || $userId === $this->community->user_id) {
            return;
        }

        $this->community->members()->updateExistingPivot($userId, ['role' => 'admin']);
    }

    public function demoteToMember(string $userId): void
    {
        if (! $this->isOwner() || $userId === $this->community->user_id) {
            return;
        }

        $this->community->members()->updateExistingPivot($userId, ['role' => 'member']);
    }

    public function banMember(string $userId): void
    {
        if (! $this->authorizeManage() || $userId === $this->community->user_id) {
            return;
        }

        $this->community->members()->updateExistingPivot($userId, ['status' => 'banned']);
    }

    public function unbanMember(string $userId): void
    {
        if (! $this->authorizeManage()) {
            return;
        }

        $this->community->bannedMembers()->updateExistingPivot($userId, ['status' => 'active']);
    }

    public function removeMember(string $userId): void
    {
        if (! $this->authorizeManage() || $userId === $this->community->user_id) {
            return;
        }

        $this->community->members()->detach($userId);
    }

    private function authorizeManage(): bool
    {
        if (! $this->isOwner()) {
            session()->flash('error', 'Only the community owner can manage members.');

            return false;
        }

        return true;
    }

    // =========================================================
    // Settings
    // =========================================================

    public function updatedSettingsType(): void
    {
        if ($this->settingsType !== 'paid') {
            $this->settingsPrice = null;
            $this->settingsFeePayer = 'creator';
            $this->settingsBillingType = 'subscription';
            $this->settingsBillingInterval = 'monthly';
        }
    }

    public function updatedSettingsBillingType(): void
    {
        if ($this->settingsBillingType !== 'subscription') {
            $this->settingsBillingInterval = null;
        } elseif (empty($this->settingsBillingInterval)) {
            $this->settingsBillingInterval = 'monthly';
        }
    }

    public function settingsFeePreview(): ?array
    {
        if ($this->settingsType !== 'paid' || ! is_numeric($this->settingsPrice) || $this->settingsPrice <= 0) {
            return null;
        }

        $rate = $this->platformFeePercent / 100;

        $memberCharge = $this->settingsFeePayer === 'members'
            ? round($this->settingsPrice / (1 - $rate), 2)
            : round((float) $this->settingsPrice, 2);

        $platformCut = round($memberCharge * $rate, 2);
        $creatorPayout = round($memberCharge - $platformCut, 2);

        return compact('memberCharge', 'platformCut', 'creatorPayout');
    }

    public function saveSettings(): void
    {
        if (! $this->authorizeManage()) {
            return;
        }

        $isPaid = $this->settingsType === 'paid';
        $isSubscription = $isPaid && $this->settingsBillingType === 'subscription';

        $validated = $this->validate([
            'settingsName' => ['required', 'string', 'max:255'],
            'settingsDescription' => ['required', 'string', 'max:1000'],
            'settingsCategoryId' => ['required', 'exists:community_categories,id'],
            'settingsType' => ['required', Rule::in(['public', 'private', 'paid', 'approval'])],
            'settingsPrice' => ['required_if:settingsType,paid', 'nullable', 'numeric', 'min:100'],
            'settingsFeePayer' => ['required_if:settingsType,paid', 'nullable', Rule::in(['creator', 'members'])],
            'settingsBillingType' => ['required_if:settingsType,paid', 'nullable', Rule::in(['one_off', 'subscription'])],
            'settingsBillingInterval' => [
                'required_if:settingsBillingType,subscription',
                'nullable',
                Rule::in(array_keys($this->billingIntervals)),
            ],
            'settingsLogo' => ['nullable', 'image', 'max:4096'],
            'settingsBanner' => ['nullable', 'image', 'max:6144'],
        ]);

        $data = [
            'name' => $validated['settingsName'],
            'description' => $validated['settingsDescription'],
            'community_categories_id' => $validated['settingsCategoryId'],
            'type' => $validated['settingsType'],
            'monthly_fee' => $isPaid ? $validated['settingsPrice'] : null,
            'fee_payer' => $isPaid ? $validated['settingsFeePayer'] : null,
            'billing_type' => $isPaid ? $validated['settingsBillingType'] : null,
            'billing_interval' => $isSubscription ? $validated['settingsBillingInterval'] : null,
            'platform_fee_percent' => $isPaid ? ($this->community->platform_fee_percent ?? $this->platformFeePercent) : null,
        ];

        if ($this->settingsLogo) {
            $data['image'] = $this->settingsLogo->store('communities/' . $this->community->id, 'spaces');
        }

        if ($this->settingsBanner) {
            $data['banner'] = $this->settingsBanner->store('communities/' . $this->community->id, 'spaces');
        }

        $this->community->update($data);
        $this->community->refresh();

        $this->reset(['settingsLogo', 'settingsBanner']);
        $this->tab = 'about';
        session()->flash('status', 'Community settings updated.');
    }

    public function archiveCommunity(): void
    {
        if (! $this->authorizeManage()) {
            return;
        }

        $this->community->update(['type' => 'private']);
        $this->settingsType = 'private';
    }

    public function deleteCommunity()
    {
        if (! $this->authorizeManage()) {
            return;
        }

        $this->community->delete();

        return redirect('community');
    }

    public function hasPendingSubscription(): bool
    {

        return CommunitySubscription::where('community_id', $this->community->id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->exists();
    }

    public function subscribeLabel(): string
{
    $price = number_format((float) $this->community->monthly_fee, 2);

    if ($this->community->billing_type === 'one_off') {
        return "Pay ₦{$price}";
    }

    $suffix = config("community.billing_intervals.{$this->community->billing_interval}.suffix", '/mo');

    return "Subscribe ₦{$price}{$suffix}";
}


public function subscribe(): void
{
    if ($this->community->type !== 'paid' || $this->isMember()) {
        return;
    }

    $service = app(CommunitySubscriptionService::class);
    $existing = $service->pendingOrActiveFor($this->community, auth()->user());

    if ($existing?->status === 'active') {
        return;
    }

    $subscription = $existing ?? $service->initiate($this->community, auth()->user());

    dd($subscription);

    // -----------------------------------------------------------------
    // TODO: payment gateway goes here, for BOTH billing types — the only
    // difference between one_off and subscription is what you send the
    // gateway (a single charge vs a plan/recurring authorization) and
    // what expires_at ends up being once activate() runs. Typical shape:
    //   $url = $this->paymentGateway->checkoutUrl($subscription);
    //   return redirect()->away($url);
    //
    // On successful payment (webhook/callback controller, NOT this
    // component instance — it won't exist when the webhook fires):
    //   app(CommunitySubscriptionService::class)->activate($subscription);
    // -----------------------------------------------------------------
    $service->activate($subscription);
    session()->flash('status', 'Payment step isn\'t wired up yet — this is a placeholder.');
}


    public function render()
    {
        $posts = $this->community->posts()
            ->with(['user', 'media', 'comments.user'])
            ->withExists(['likes as liked_by_me' => fn($q) => $q->where('users.id', auth()->id())])
            ->latest()
            ->simplePaginate($this->postsPerPage, ['*'], 'postsPage');

        $members = $this->canViewMembers()
            ? $this->community->members()
            ->when($this->memberSearch !== '', function ($q) {
                $q->where(function ($qq) {
                    $qq->where('name', 'like', "%{$this->memberSearch}%")
                        ->orWhere('email', 'like', "%{$this->memberSearch}%");
                });
            })
            ->orderByDesc('community_users.created_at')
            ->simplePaginate($this->membersPerPage, ['*'], 'membersPage')
            : null;

        return view('livewire.user.community-details', [
            'posts' => $posts,
            'members' => $members,
            'bannedMembers' => $this->isOwner() ? $this->community->bannedMembers()->get() : collect(),
            'pendingRequests' => $this->isOwner()
                ? $this->community->pendingJoinRequests()->with('user')->get()
                : collect(),
            'categories' => CommunityCategory::all(),
        ]);
    }
}
