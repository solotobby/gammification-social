<?php

namespace App\Livewire\User;

use App\Http\Controllers\CommunityInviteController;
use App\Mail\GeneralMail;
use App\Models\Community;
use App\Models\CommunityCategory;
use App\Models\CommunityInvite;
use App\Models\CommunityJoinRequest;
use App\Models\CommunityPostComment;
use App\Models\CommunityPostLike;
use App\Models\CommunityPostView;
use App\Models\CommunitySubscription;
use App\Models\Follow;
use App\Models\User;
use App\Support\CommunityFeeCalculator;
use App\Support\StoredMedia;
use App\Services\CommunityMembershipService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

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
    public string $postSearch = '';
    private const PAGE_STEP = 10;

    // ---- settings form ----
    public string $settingsName = '';
    public string $settingsDescription = '';
    public string $settingsCategoryId = '';
    public string $settingsType = 'public';
    public string $settingsFeePayer = 'creator';
    public ?float $settingsMonthlyFee = null;
    public string $settingsBillingType = 'subscription';
    public ?string $settingsBillingInterval = 'monthly';
    public $settingsLogo;
    public $settingsBanner;
    public int $platformFeePercent;
    public array $billingIntervals = [];

    // ---- private community invites ----
    public string $inviteIdentifier = '';

    public function mount(Community $community): void
    {
        $this->community = $community->loadCount(['members', 'posts'])->load(['category', 'user']);

        if ($community->isArchived() && ! $this->canAccessArchived()) {
            abort(404);
        }

        if (
            auth()->check()
            && ! $community->isInCurrency()
            && ! $this->isOwner()
            && ! $this->isMember()
        ) {
            abort(404);
        }

        $this->platformFeePercent = (int) config('community.platform_fee_percent', 10);
        $this->billingIntervals = config('community.billing_intervals', []);

        $this->settingsName = $community->name;
        $this->settingsDescription = (string) $community->description;
        $this->settingsCategoryId = (string) $community->community_categories_id;
        $this->settingsType = $community->type;
        $this->settingsFeePayer = $community->fee_payer ?? 'creator';
        $this->settingsMonthlyFee = $community->monthly_fee ? (float) $community->monthly_fee : null;
        $this->settingsBillingType = $community->billing_type ?? 'subscription';
        $this->settingsBillingInterval = $community->billing_interval ?? 'monthly';
    }

    public function setTab(string $tab): void
    {
        $allowed = ['feed', 'about', 'members', 'earnings', 'analytics', 'settings'];

        if (in_array($tab, $allowed, true)) {
            $this->tab = $tab;
        }
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

    public function ownerEarningsTotal(): float
    {
        if (! $this->isOwner() || $this->community->type !== 'paid') {
            return 0.0;
        }

        return (float) CommunitySubscription::query()
            ->where('community_id', $this->community->id)
            ->whereIn('status', ['active', 'expired', 'cancelled'])
            ->sum('creator_amount');
    }

    public function formatCommunityMoney(float $amount): string
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

    public function isOwnerOrAdminOrMember(): bool
    {
        return $this->isOwner() || $this->isAdmin() || $this->isMember();
    }

    public function canAccessArchived(): bool
    {
        return $this->isOwnerOrAdmin();
    }

    public function canDeletePost(string $postId): bool
    {
        return $this->isOwnerOrAdmin();
    }

    public function canDeleteComment(string $commentId): bool
    {
        if ($this->isOwner()) {
            return true;
        }

        $comment = CommunityPostComment::query()
            ->where('id', $commentId)
            ->whereHas('post', fn ($q) => $q->where('community_id', $this->community->id))
            ->first();

        return $comment && (string) $comment->user_id === (string) auth()->id();
    }

    /**
     * Public communities' member lists are open to anyone (browsing a
     * public community shouldn't require joining first). Every other type
     * — private, approval, paid — keeps its member list admin/owner-only.
     */
    public function canViewMembers(): bool
    {
        return $this->community->type === 'public' || $this->isOwnerOrAdminOrMember();
    }

    /**
     * Feed visibility rules by community type:
     * - public: everyone can browse
     * - private / paid / approval: members only
     */
    public function canViewFeed(): bool
    {
        return match ($this->community->type) {
            'private', 'paid', 'approval' => $this->isMember(),
            default => true,
        };
    }

    public function feedGateMessage(): string
    {
        return match ($this->community->type) {
            'paid' => 'Subscribe or pay to join before you can view posts in this community.',
            'approval' => 'Your join request must be approved before you can view posts here.',
            'private' => $this->hasPendingInvite()
                ? 'You have been invited — accept the invitation above to see posts and participate.'
                : 'Only invited members can view the feed. Ask the admin for an invite link.',
            default => 'Join this community to view the feed.',
        };
    }

    public function feedGateTitle(): string
    {
        return match ($this->community->type) {
            'paid' => 'Members-only feed',
            'approval' => 'Approval required',
            'private' => 'This community is private',
            default => 'Join to view',
        };
    }

    public function hasPendingInvite(): bool
    {
        return CommunityInviteController::pendingDirectInviteFor($this->community, auth()->user()) !== null;
    }

    public function pendingInviteToken(): ?string
    {
        return CommunityInviteController::pendingDirectInviteFor($this->community, auth()->user())?->token;
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

    public function updatedPostSearch(): void
    {
        $this->postsPerPage = self::PAGE_STEP;
    }

    public function loadMorePosts(): void
    {
        $this->postsPerPage += self::PAGE_STEP;
    }

    public function openCommunityPhotoViewer(string $postId, int $imageIndex = 0): void
    {
        $this->dispatch('openPhotoViewer', postId: $postId, imageIndex: $imageIndex, source: 'community');
    }

    public function toggleLike(string $postId): void
    {
        if (! $this->isMember()) {
            return;
        }

        $post = $this->community->posts()->findOrFail($postId);

        $existing = CommunityPostLike::where('community_post_id', $post->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            $existing->delete();
            $post->decrement('likes_count');
        } else {
            CommunityPostLike::create([
                'community_post_id' => $post->id,
                'user_id' => auth()->id(),
            ]);
            $post->increment('likes_count');
        }
    }

    public function addComment(string $postId): void
    {
        if (! $this->isMember()) {
            return;
        }

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
        if (! $this->canDeletePost($postId)) {
            session()->flash('error', 'You cannot delete this post.');

            return;
        }

        $post = $this->community->posts()->with('media')->find($postId);

        if (! $post) {
            return;
        }

        foreach ($post->media as $item) {
            StoredMedia::delete($item->path);
        }

        $post->delete();

        session()->flash('status', 'Post deleted.');
    }

    public function toggleFollowAuthor(string $userId): void
    {
        if (! auth()->check() || auth()->id() === $userId) {
            return;
        }

        $targetUser = User::find($userId);

        if (! $targetUser) {
            return;
        }

        $authUser = auth()->user();

        if ($authUser->isFollowing($targetUser)) {
            Follow::where([
                'follower_id' => $authUser->id,
                'following_id' => $targetUser->id,
            ])->delete();

            if ($authUser->following > 0) {
                $authUser->decrement('following');
            }

            if ($targetUser->followers > 0) {
                $targetUser->decrement('followers');
            }
        } else {
            Follow::firstOrCreate([
                'follower_id' => $authUser->id,
                'following_id' => $targetUser->id,
            ]);

            $authUser->increment('following');
            $targetUser->increment('followers');
        }
    }

    public function reportCommunityPost(string $postId): void
    {
        if (! auth()->check()) {
            return;
        }

        $exists = $this->community->posts()->where('id', $postId)->exists();

        if (! $exists) {
            return;
        }

        session()->flash('status', 'Post reported. Thanks for letting us know.');
    }

    public function deleteComment(string $commentId): void
    {
        if (! $this->canDeleteComment($commentId)) {
            session()->flash('error', 'You cannot delete this comment.');

            return;
        }

        $comment = CommunityPostComment::query()
            ->where('id', $commentId)
            ->whereHas('post', fn ($q) => $q->where('community_id', $this->community->id))
            ->first();

        if (! $comment) {
            return;
        }

        $comment->post->decrement('comments_count');
        $comment->delete();

        session()->flash('status', 'Comment deleted.');
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
        if ($this->community->type !== 'public' || $this->community->isArchived()) {
            return;
        }

        if (app(CommunityMembershipService::class)->isBanned($this->community, auth()->id())) {
            session()->flash('error', 'You cannot rejoin this community.');

            return;
        }

        if (! app(CommunityMembershipService::class)->attachMember($this->community, auth()->id())) {
            session()->flash('error', 'Unable to join this community.');

            return;
        }

        $this->community->refresh();
        session()->flash('status', 'Welcome to ' . $this->community->name . '!');
    }

    public function leaveCommunity(): void
    {
        if ($this->isOwner()) {
            session()->flash('error', 'Owners cannot leave — transfer ownership or delete the community.');

            return;
        }

        if (! $this->isMember()) {
            return;
        }

        if (! app(CommunityMembershipService::class)->leave($this->community, auth()->user())) {
            session()->flash('error', 'Unable to leave this community.');

            return;
        }

        $this->community->refresh();
        session()->flash('status', 'You have left ' . $this->community->name . '.');
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
            if (! app(CommunityMembershipService::class)->attachMember($this->community, $request->user_id)) {
                session()->flash('error', 'Could not approve — user may be banned from this community.');

                return;
            }

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

    // =========================================================
    // Private community invites
    // =========================================================

    public function inviteMember(): void
    {
        if (! $this->authorizeInvite() || $this->community->type !== 'private') {
            return;
        }

        $this->validate([
            'inviteIdentifier' => ['required', 'string', 'max:255'],
        ]);

        $identifier = trim($this->inviteIdentifier);

        $user = User::query()
            ->where('status', 'ACTIVE')
            ->where(function ($q) use ($identifier) {
                $q->where('username', $identifier)
                    ->orWhere('email', $identifier);
            })
            ->first();

        if (! $user) {
            $this->addError('inviteIdentifier', 'No active user found with that username or email.');

            return;
        }

        if ($user->id === auth()->id()) {
            $this->addError('inviteIdentifier', 'You cannot invite yourself.');

            return;
        }

        if ($this->community->members()->where('users.id', $user->id)->exists()) {
            $this->addError('inviteIdentifier', 'That user is already a member.');

            return;
        }

        CommunityInviteController::createDirectInvite($this->community, auth()->user(), $user);

        $this->reset('inviteIdentifier');
        $this->resetErrorBag('inviteIdentifier');
        session()->flash('status', 'Invitation sent to ' . displayName($user->name) . '.');
    }

    public function generateInviteLink(): void
    {
        if (! $this->authorizeInvite() || $this->community->type !== 'private') {
            return;
        }

        CommunityInviteController::regenerateLinkInvite($this->community, auth()->user());
        session()->flash('status', 'Invite link ready — share it with people you want to join.');
    }

    public function revokeInviteLink(): void
    {
        if (! $this->authorizeInvite()) {
            return;
        }

        CommunityInvite::where('community_id', $this->community->id)
            ->where('type', 'link')
            ->where('status', 'pending')
            ->update(['status' => 'revoked']);

        session()->flash('status', 'Invite link revoked.');
    }

    public function revokeDirectInvite(string $inviteId): void
    {
        if (! $this->authorizeInvite()) {
            return;
        }

        CommunityInvite::where('community_id', $this->community->id)
            ->where('id', $inviteId)
            ->where('type', 'direct')
            ->where('status', 'pending')
            ->update(['status' => 'revoked']);

        session()->flash('status', 'Invitation revoked.');
    }

    /**
     * Accept a direct invitation from the community page (Livewire action).
     * Link-based invites use the /community/invite/{token} route instead.
     */
    public function acceptInvite(): void
    {
        if ($this->community->type !== 'private' || $this->isMember()) {
            return;
        }

        $invite = CommunityInviteController::pendingDirectInviteFor($this->community, auth()->user());

        if (! $invite) {
            session()->flash('error', 'You do not have a pending invitation for this community.');

            return;
        }

        DB::transaction(function () use ($invite) {
            if (! app(CommunityMembershipService::class)->attachMember($this->community, auth()->id())) {
                return;
            }

            $invite->update([
                'status' => 'accepted',
                'accepted_at' => now(),
            ]);
        });

        $this->community->refresh();
        session()->flash('status', 'Welcome to ' . $this->community->name . '!');
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
        if (! $this->isOwnerOrAdmin()) {
            session()->flash('error', 'Only the community owner or an admin can manage members.');

            return false;
        }

        return true;
    }

    private function authorizeOwner(): bool
    {
        if (! $this->isOwner()) {
            session()->flash('error', 'Only the community owner can change these settings.');

            return false;
        }

        return true;
    }

    private function authorizeInvite(): bool
    {
        if (! $this->isOwnerOrAdmin()) {
            session()->flash('error', 'Only the owner or an admin can manage invitations.');

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
            $this->settingsMonthlyFee = null;
            $this->settingsFeePayer = 'creator';
            $this->settingsBillingType = 'subscription';
            $this->settingsBillingInterval = 'monthly';

            return;
        }

        $this->normalizePaidBillingDefaults();
    }

    public function updatedSettingsBillingType(): void
    {
        if ($this->settingsBillingType !== 'subscription') {
            $this->settingsBillingInterval = null;
        } elseif (empty($this->settingsBillingInterval)) {
            $this->settingsBillingInterval = 'monthly';
        }
    }

    private function normalizePaidBillingDefaults(): void
    {
        if ($this->settingsType !== 'paid') {
            return;
        }

        if (userBaseCurrency() === 'NGN' && $this->community->billing_type !== 'subscription') {
            $this->settingsBillingType = 'one_off';
            $this->settingsBillingInterval = null;
        }
    }

    public function settingsFeePreview(): ?array
    {
        if ($this->settingsType !== 'paid' || ! is_numeric($this->settingsMonthlyFee) || $this->settingsMonthlyFee <= 0) {
            return null;
        }

        return CommunityFeeCalculator::breakdown(
            (float) $this->settingsMonthlyFee,
            $this->platformFeePercent,
            $this->settingsFeePayer,
        ) + [
            'suffix' => $this->settingsBillingType === 'subscription' && $this->settingsBillingInterval
                ? config("community.billing_intervals.{$this->settingsBillingInterval}.suffix", '')
                : ($this->settingsBillingType === 'one_off'
                    ? config('community.billing_types.one_off.suffix', '')
                    : ''),
        ];
    }

    public function updatedSettingsLogo(): void
    {
        if (! $this->settingsLogo || ! $this->authorizeOwner()) {
            return;
        }

        $this->validateOnly('settingsLogo', [
            'settingsLogo' => ['nullable', 'image', 'max:4096'],
        ]);

        $this->deleteStoredAsset($this->community->image);
        $path = $this->storeCommunityAsset($this->settingsLogo, 'logo');

        $this->community->update(['image' => $path]);
        $this->community->refresh();
        $this->reset('settingsLogo');

        session()->flash('status', 'Community logo updated.');
    }

    public function updatedSettingsBanner(): void
    {
        if (! $this->settingsBanner || ! $this->authorizeOwner()) {
            return;
        }

        $this->validateOnly('settingsBanner', [
            'settingsBanner' => ['nullable', 'image', 'max:6144'],
        ]);

        $this->deleteStoredAsset($this->community->banner);
        $path = $this->storeCommunityAsset($this->settingsBanner, 'banner');

        $this->community->update(['banner' => $path]);
        $this->community->refresh();
        $this->reset('settingsBanner');

        session()->flash('status', 'Banner image updated.');
    }

    public function removeLogo(): void
    {
        if (! $this->authorizeOwner()) {
            return;
        }

        $this->deleteStoredAsset($this->community->image);
        $this->community->update(['image' => null]);
        $this->community->refresh();
        $this->reset('settingsLogo');

        session()->flash('status', 'Community logo removed.');
    }

    public function removeBanner(): void
    {
        if (! $this->authorizeOwner()) {
            return;
        }

        $this->deleteStoredAsset($this->community->banner);
        $this->community->update(['banner' => null]);
        $this->community->refresh();
        $this->reset('settingsBanner');

        session()->flash('status', 'Banner image removed.');
    }

    public function clearPendingLogo(): void
    {
        $this->reset('settingsLogo');
    }

    public function clearPendingBanner(): void
    {
        $this->reset('settingsBanner');
    }

    private function storeCommunityAsset($file, string $prefix): string
    {
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = $prefix.'-'.Str::uuid().'.'.$extension;

        return Storage::disk('spaces')->putFileAs(
            'communities/'.$this->community->id,
            $file,
            $filename,
            'public',
        );
    }

    private function deleteStoredAsset(?string $path): void
    {
        StoredMedia::delete($path);
    }

    private function uniqueSlug(string $name, ?string $exceptId = null): string
    {
        $base = Str::slug($name) ?: 'community';
        $slug = $base;
        $suffix = 2;

        while (Community::where('slug', $slug)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    public function saveSettings(): void
    {
        if (! $this->authorizeOwner()) {
            return;
        }

        $this->tab = 'settings';

        if ($this->settingsType !== 'paid') {
            $this->settingsMonthlyFee = null;
        } else {
            $this->normalizePaidBillingDefaults();
        }

        $isPaid = $this->settingsType === 'paid';
        $isSubscription = $isPaid && $this->settingsBillingType === 'subscription';
        $minimumPrice = communityMinimumPrice($this->community->currency ?? userBaseCurrency());

        try {
            $validated = $this->validate([
                'settingsName' => ['required', 'string', 'max:255'],
                'settingsDescription' => ['required', 'string', 'max:1000'],
                'settingsCategoryId' => ['required', 'exists:community_categories,id'],
                'settingsType' => ['required', Rule::in(['public', 'private', 'paid', 'approval'])],
                'settingsMonthlyFee' => [
                    Rule::excludeIf(! $isPaid),
                    'required',
                    'numeric',
                    'min:'.$minimumPrice,
                ],
                'settingsFeePayer' => [
                    Rule::excludeIf(! $isPaid),
                    'required',
                    Rule::in(['creator', 'members']),
                ],
                'settingsBillingType' => [
                    Rule::excludeIf(! $isPaid),
                    'required',
                    Rule::in(['one_off', 'subscription']),
                ],
                'settingsBillingInterval' => [
                    Rule::excludeIf(! $isSubscription),
                    'required',
                    Rule::in(array_keys($this->billingIntervals)),
                ],
            ], [], [
                'settingsName' => 'community name',
                'settingsDescription' => 'description',
                'settingsCategoryId' => 'category',
                'settingsType' => 'privacy type',
                'settingsMonthlyFee' => 'price',
                'settingsFeePayer' => 'fee payer',
                'settingsBillingType' => 'billing type',
                'settingsBillingInterval' => 'billing interval',
            ]);
        } catch (ValidationException $e) {
            $this->dispatch('settings-scroll-to-errors');
            throw $e;
        }

        $data = [
            'name' => $validated['settingsName'],
            'description' => $validated['settingsDescription'],
            'community_categories_id' => $validated['settingsCategoryId'],
            'type' => $validated['settingsType'],
            'monthly_fee' => $isPaid ? $validated['settingsMonthlyFee'] : 0,
            'fee_payer' => $isPaid ? $validated['settingsFeePayer'] : 'creator',
            'billing_type' => $isPaid ? $validated['settingsBillingType'] : null,
            'billing_interval' => $isSubscription ? $validated['settingsBillingInterval'] : null,
            'platform_fee_percent' => $isPaid ? ($this->community->platform_fee_percent ?? $this->platformFeePercent) : null,
        ];

        if ($validated['settingsName'] !== $this->community->getOriginal('name')) {
            $data['slug'] = $this->uniqueSlug($validated['settingsName'], $this->community->id);
        }

        if ($this->settingsLogo) {
            $this->deleteStoredAsset($this->community->image);
            $data['image'] = $this->storeCommunityAsset($this->settingsLogo, 'logo');
        }

        if ($this->settingsBanner) {
            $this->deleteStoredAsset($this->community->banner);
            $data['banner'] = $this->storeCommunityAsset($this->settingsBanner, 'banner');
        }

        $this->community->update($data);
        $this->community->refresh();

        if ($validated['settingsType'] === 'private'
            && ! CommunityInviteController::activeLinkInvite($this->community)) {
            CommunityInviteController::regenerateLinkInvite($this->community, auth()->user());
        }

        $this->reset(['settingsLogo', 'settingsBanner']);

        session()->flash('status', 'Community settings updated.');

        $this->redirectRoute('community.show', $this->community, navigate: true);
    }

    public function archiveCommunity(): void
    {
        if (! $this->authorizeOwner()) {
            return;
        }

        $this->community->update([
            'type' => 'private',
            'archived_at' => now(),
        ]);
        $this->community->refresh();
        $this->settingsType = 'private';

        if (! CommunityInviteController::activeLinkInvite($this->community)) {
            CommunityInviteController::regenerateLinkInvite($this->community, auth()->user());
        }

        session()->flash('status', 'Community archived — hidden from discovery and closed to new joins.');
    }

    public function unarchiveCommunity(): void
    {
        if (! $this->authorizeOwner()) {
            return;
        }

        $this->community->update(['archived_at' => null]);
        $this->community->refresh();

        session()->flash('status', 'Community restored to discovery.');
    }

    public function deleteCommunity()
    {
        if (! $this->authorizeOwner()) {
            return;
        }

        $this->deleteStoredAsset($this->community->image);
        $this->deleteStoredAsset($this->community->banner);

        $communityId = $this->community->id;
        $this->community->delete();

        Storage::disk('spaces')->deleteDirectory('communities/'.$communityId);

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


    public function userSubscriptionStatus(string $communityId)
    {
        return CommunitySubscription::where('community_id', $communityId)
            ->where('user_id', auth()->id())
            // ->where('status', 'pending')
            ->first()?->status;
    }


    public function render()
    {
        $this->community->loadMissing(['category', 'user']);
        $this->community->loadCount(['members', 'posts']);

        $postsQuery = $this->community->posts()
            ->with(['user', 'media', 'comments.user'])
            ->withExists(['likes as liked_by_me' => fn ($q) => $q->where('user_id', auth()->id())])
            ->when($this->postSearch !== '', function ($q) {
                $term = '%'.$this->postSearch.'%';
                $q->where(function ($qq) use ($term) {
                    $qq->where('content', 'like', $term)
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $term)->orWhere('username', 'like', $term));
                });
            })
            ->latest();

        $posts = $this->canViewFeed()
            ? $postsQuery->simplePaginate($this->postsPerPage, ['*'], 'postsPage')
            : $postsQuery->whereRaw('0 = 1')->simplePaginate($this->postsPerPage, ['*'], 'postsPage');

        $linkInvite = $this->community->type === 'private'
            ? CommunityInviteController::activeLinkInvite($this->community)
            : null;

        $followingAuthorIds = collect();

        if (auth()->check() && $posts->count() > 0) {
            $authorIds = $posts->pluck('user_id')->unique()->filter();
            $followingAuthorIds = auth()->user()->following()
                ->whereIn('following_id', $authorIds)
                ->pluck('following_id');
        }

        $members = $this->canViewMembers()
            ? $this->community->members()
            ->when($this->memberSearch !== '', function ($q) {
                $q->where(function ($qq) {
                    $qq->where('name', 'like', "%{$this->memberSearch}%")
                        ->orWhere('username', 'like', "%{$this->memberSearch}%");
                });
            })
            ->orderByDesc('community_users.created_at')
            ->simplePaginate($this->membersPerPage, ['*'], 'membersPage')
            : null;

        return view('livewire.user.community-details', [
            'posts' => $posts,
            'members' => $members,
            'bannedMembers' => $this->isOwnerOrAdmin() ? $this->community->bannedMembers()->get() : collect(),
            'pendingRequests' => $this->isOwnerOrAdmin()
                ? $this->community->pendingJoinRequests()->with('user')->get()
                : collect(),
            'categories' => CommunityCategory::all(),
            'followingAuthorIds' => $followingAuthorIds,
            'inviteLinkUrl' => $linkInvite ? route('community.invite.accept', $linkInvite->token) : null,
            'pendingDirectInvites' => $this->isOwnerOrAdmin() && $this->community->type === 'private'
                ? CommunityInvite::where('community_id', $this->community->id)
                    ->where('type', 'direct')
                    ->where('status', 'pending')
                    ->with('user')
                    ->latest()
                    ->get()
                : collect(),
        ]);
    }
}
