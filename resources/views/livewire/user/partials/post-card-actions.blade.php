{{--
    Shared post card header actions (earnings + ⋮ menu).
    Include from a Livewire view so wire:click binds to the parent component.

    Required: $post (Post or CommunityPost with ->id, ->user)
    Optional:
      $isOwner, $estimatedEarnings, $showPostMenu, $showEarnings,
      $isFollowing, $isBookmarked, $context ('post'|'community'),
      $canDelete (community owner/admin delete)
--}}
@php
    $context = $context ?? 'post';
    $showPostMenu = $showPostMenu ?? true;
    $showEarnings = $showEarnings ?? ($context === 'post');
    $isOwner = $isOwner ?? (auth()->id() === ($post->user_id ?? null));
    $estimatedEarnings = $estimatedEarnings ?? 0;
    $isFollowing = $isFollowing ?? false;
    $isBookmarked = $isBookmarked ?? false;
    $canDelete = $canDelete ?? false;
    $username = $post->user->username ?? 'user';

    $boostEnabled = \App\Models\SystemSetting::isBoostEnabled();
    $canManagePost = $canManagePost ?? false;
    $hasMenuItems = ($context === 'post' && $isOwner)
        || ($context === 'community' && $canDelete)
        || (! $isOwner);
@endphp

<div class="pk-header-actions"
    x-data="{
        isBoosted: @js((bool) ($post->is_boosted ?? false)) || Boolean(sessionStorage.getItem('pk_boosted_{{ $post->id }}'))
    }"
    @pk-post-boosted.window="if ($event.detail.postId == '{{ $post->id }}') isBoosted = true">
    @if ($showEarnings)
        <template x-if="isBoosted && @js($boostEnabled)">
            <div style="display:inline-flex;align-items:center;gap:6px">
                @if ($isOwner && $context === 'post')
                    <a href="{{ url('post/timeline/' . $post->id . '/analytics?tab=boost') }}"
                        class="pk-boosted-pill"
                        title="Promoted across Payhankey & Partner Websites · Click for Campaign Analytics"
                        style="text-decoration:none;cursor:pointer"
                        wire:navigate>
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        Boosted
                    </a>
                @else
                    <span class="pk-boosted-pill" title="Promoted across Payhankey & Partner Websites">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        Boosted
                    </span>
                @endif
            </div>
        </template>
        <template x-if="!isBoosted || !@js($boostEnabled)">
            <div>
                @if ($isOwner && $context === 'post')
                    <a href="{{ url('post/timeline/' . $post->id . '/analytics') }}" class="pk-earn" wire:navigate>
                        {{ getCurrencyCode() }}{{ number_format($estimatedEarnings, 2) }}
                    </a>
                @else
                    <span class="pk-earn pk-earn--static" title="Estimated earnings">
                        {{ getCurrencyCode() }}{{ number_format($estimatedEarnings, 2) }}
                    </span>
                @endif
            </div>
        </template>
    @endif

    @if ($showPostMenu && $hasMenuItems)
        <details class="pk-menu"
            x-data
            x-on:click.outside="$el.removeAttribute('open')"
            x-on:keydown.escape.window="$el.removeAttribute('open')">
            <summary class="pk-options-btn" aria-label="Post options">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <circle cx="5" cy="12" r="2"/>
                    <circle cx="12" cy="12" r="2"/>
                    <circle cx="19" cy="12" r="2"/>
                </svg>
            </summary>

            <div class="pk-menu-panel">
                @if ($context === 'post' && $isOwner)
                    @if ($boostEnabled)
                        <a href="{{ url('post/timeline/' . $post->id . '/boost') }}" 
                            class="pk-menu-item"
                            style="color: #6D28D9; font-weight: 600;"
                            wire:navigate
                            @click="$el.closest('details')?.removeAttribute('open')">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px; flex-shrink: 0;"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                            <span x-text="isBoosted ? 'Boost post (Extend)' : 'Boost post'"></span>
                        </a>
                    @endif

                    <a href="{{ url('post/timeline/' . $post->id . '/analytics') }}"
                        class="pk-menu-item" wire:navigate
                        @click="$el.closest('details')?.removeAttribute('open')">
                        <i class="far fa-chart-bar"></i>
                        View analytics
                    </a>

                    <button type="button" class="pk-menu-item"
                        onclick="navigator.clipboard.writeText('{{ url('timeline/' . $post->id) }}').then(() => { if (typeof window.pkToast === 'function') window.pkToast('Link copied to clipboard'); }); this.closest('details')?.removeAttribute('open');">
                        <i class="far fa-copy"></i>
                        Copy link
                    </button>

                    @if ($canManagePost)
                        <button type="button" class="pk-menu-item" wire:click="openEditPost"
                            @click="$el.closest('details')?.removeAttribute('open')">
                            <i class="far fa-edit"></i>
                            Edit post
                        </button>

                        <div class="pk-menu-divider"></div>

                        <button type="button" class="pk-menu-item pk-menu-item--danger"
                            wire:click="deleteOwnPost"
                            wire:confirm="Delete this post? All accrued earnings for it will be removed. This can't be undone."
                            @click="$el.closest('details')?.removeAttribute('open')">
                            <i class="far fa-trash-alt"></i>
                            Delete post
                        </button>
                    @endif
                @elseif ($context === 'community' && $canDelete)
                    <button type="button" class="pk-menu-item pk-menu-item--danger"
                        wire:click="deletePost('{{ $post->id }}')"
                        onclick="return confirm('Delete this post? This can\'t be undone.')">
                        <i class="far fa-trash-alt"></i>
                        Delete post
                    </button>
                @elseif (! $isOwner)
                    @if ($context === 'community')
                        <button type="button" class="pk-menu-item @if ($isFollowing) pk-menu-item--active @endif"
                            wire:click="toggleFollowAuthor('{{ $post->user_id }}')">
                            <i class="fa @if ($isFollowing) fa-user-minus @else fa-user-plus @endif"></i>
                            @if ($isFollowing)
                                Unfollow {{ '@'.$username }}
                            @else
                                Follow {{ '@'.$username }}
                            @endif
                        </button>

                        <button type="button" class="pk-menu-item pk-menu-item--danger"
                            wire:click="reportCommunityPost('{{ $post->id }}')">
                            <i class="fa fa-flag"></i>
                            Report post
                        </button>
                    @else
                        <button type="button" class="pk-menu-item @if ($isFollowing) pk-menu-item--active @endif"
                            wire:click="toggleFollow">
                            <i class="fa @if ($isFollowing) fa-user-minus @else fa-user-plus @endif"></i>
                            @if ($isFollowing)
                                Unfollow {{ '@'.$username }}
                            @else
                                Follow {{ '@'.$username }}
                            @endif
                        </button>

                        @auth
                            @if (auth()->user()->hasRole('user'))
                                <a href="{{ route('messages', ['start' => $username]) }}"
                                    class="pk-menu-item" wire:navigate>
                                    <i class="fa fa-comment-dots"></i>
                                    Message {{ '@'.$username }}
                                </a>
                            @endif
                        @endauth

                        <button type="button" class="pk-menu-item @if ($isBookmarked) pk-menu-item--active @endif"
                            wire:click="toggleBookmark"
                            @click="$el.closest('details')?.removeAttribute('open')">
                            <i class="@if ($isBookmarked) fa @else far @endif fa-bookmark"></i>
                            @if ($isBookmarked)
                                Remove bookmark
                            @else
                                Bookmark post
                            @endif
                        </button>

                        <button type="button" class="pk-menu-item"
                            onclick="navigator.clipboard.writeText('{{ url('timeline/' . $post->id) }}').then(() => { if (typeof window.pkToast === 'function') window.pkToast('Link copied to clipboard'); }); this.closest('details')?.removeAttribute('open');">
                            <i class="far fa-copy"></i>
                            Copy link
                        </button>

                        <div class="pk-menu-divider"></div>

                        <button type="button" class="pk-menu-item"
                            wire:click="hidePost"
                            @click="$el.closest('details')?.removeAttribute('open')">
                            <i class="far fa-eye-slash"></i>
                            Hide post
                        </button>

                        <button type="button" class="pk-menu-item pk-menu-item--danger"
                            wire:click="reportPost"
                            @click="$el.closest('details')?.removeAttribute('open')">
                            <i class="fa fa-flag"></i>
                            Report post
                        </button>
                    @endif
                @endif
            </div>
        </details>
    @endif
</div>
