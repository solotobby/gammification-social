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

    $canManagePost = $canManagePost ?? false;
    $hasMenuItems = ($context === 'post' && $isOwner)
        || ($context === 'community' && $canDelete)
        || (! $isOwner);
@endphp

<div class="pk-header-actions">
    @if ($showEarnings)
        @if ($isOwner && $context === 'post')
            <a href="{{ url('post/timeline/' . $post->id . '/analytics') }}" class="pk-earn" wire:navigate>
                {{ getCurrencyCode() }}{{ number_format($estimatedEarnings, 2) }}
            </a>
        @else
            <span class="pk-earn pk-earn--static" title="Estimated earnings">
                {{ getCurrencyCode() }}{{ number_format($estimatedEarnings, 2) }}
            </span>
        @endif
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
                    <a href="{{ url('post/timeline/' . $post->id . '/analytics') }}"
                        class="pk-menu-item" wire:navigate>
                        <i class="far fa-chart-bar"></i>
                        View analytics
                    </a>

                    @if ($canManagePost)
                        <button type="button" class="pk-menu-item" wire:click="openEditPost">
                            <i class="far fa-edit"></i>
                            Edit post
                        </button>

                        <div class="pk-menu-divider"></div>

                        <button type="button" class="pk-menu-item pk-menu-item--danger"
                            wire:click="deleteOwnPost"
                            wire:confirm="Delete this post? All accrued earnings for it will be removed. This can't be undone.">
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
                            wire:click="toggleBookmark">
                            <i class="@if ($isBookmarked) fa @else far @endif fa-bookmark"></i>
                            @if ($isBookmarked)
                                Remove bookmark
                            @else
                                Bookmark post
                            @endif
                        </button>

                        <div class="pk-menu-divider"></div>

                        <button type="button" class="pk-menu-item"
                            wire:click="hidePost">
                            <i class="far fa-eye-slash"></i>
                            Hide post
                        </button>

                        <button type="button" class="pk-menu-item pk-menu-item--danger"
                            wire:click="reportPost">
                            <i class="fa fa-flag"></i>
                            Report post
                        </button>
                    @endif
                @endif
            </div>
        </details>
    @endif
</div>
