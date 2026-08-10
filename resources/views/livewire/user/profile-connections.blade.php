<div class="xc-page">
    @php
        $defaultAvatar = asset('src/assets/media/avatars/avatar13.jpg');
    @endphp

    @verbatim
        <style>
            .xc-page {
                --xc-text: #0F1419;
                --xc-muted: #536471;
                --xc-line: #EFF3F4;
                --xc-hover: #F7F9F9;
                --xc-blue: #1D9BF0;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                color: var(--xc-text);
                background: #fff;
                border: 1px solid var(--xc-line);
                border-radius: 0;
                overflow: hidden;
                margin: 0 -12px 16px;
            }

            @media (min-width: 992px) {
                .xc-page {
                    margin-inline: 0;
                    border-radius: 16px;
                }
            }

            .xc-page * { box-sizing: border-box; }

            .xc-top {
                position: sticky;
                top: 0;
                z-index: 10;
                background: rgba(255, 255, 255, .85);
                backdrop-filter: blur(12px);
                border-bottom: 1px solid var(--xc-line);
            }

            .xc-top-row {
                display: flex;
                align-items: center;
                gap: 24px;
                padding: 6px 16px;
                min-height: 53px;
            }

            .xc-back {
                width: 34px;
                height: 34px;
                border-radius: 50%;
                display: grid;
                place-items: center;
                color: var(--xc-text);
                text-decoration: none;
                transition: background .15s ease;
                flex-shrink: 0;
            }

            .xc-back:hover {
                background: var(--xc-hover);
                color: var(--xc-text);
            }

            .xc-back svg { width: 20px; height: 20px; }

            .xc-title-block { min-width: 0; }

            .xc-title {
                font-size: 20px;
                font-weight: 800;
                line-height: 1.2;
                margin: 0;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .xc-subtitle {
                font-size: 13px;
                color: var(--xc-muted);
                margin: 0;
            }

            .xc-tabs {
                display: flex;
                border-bottom: 1px solid var(--xc-line);
            }

            .xc-tab {
                flex: 1;
                position: relative;
                border: none;
                background: none;
                padding: 14px 12px 16px;
                font-family: inherit;
                font-size: 15px;
                font-weight: 500;
                color: var(--xc-muted);
                cursor: pointer;
                transition: background .15s ease, color .15s ease;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 2px;
            }

            .xc-tab-label {
                font-size: 15px;
                font-weight: inherit;
                line-height: 1.2;
            }

            .xc-tab-count {
                font-size: 13px;
                font-weight: 500;
                color: var(--xc-muted);
                line-height: 1.2;
            }

            .xc-tab--active .xc-tab-count {
                color: var(--xc-text);
                font-weight: 700;
            }

            .xc-tab:hover { background: var(--xc-hover); }

            .xc-tab--active {
                color: var(--xc-text);
                font-weight: 700;
            }

            .xc-tab--active::after {
                content: '';
                position: absolute;
                left: 50%;
                bottom: 0;
                transform: translateX(-50%);
                width: 56px;
                height: 4px;
                border-radius: 999px;
                background: var(--xc-blue);
            }

            .xc-list { list-style: none; margin: 0; padding: 0; }

            .xc-user {
                display: flex;
                align-items: flex-start;
                gap: 12px;
                padding: 12px 16px;
                border-bottom: 1px solid var(--xc-line);
                transition: background .15s ease;
            }

            .xc-user:hover { background: var(--xc-hover); }

            .xc-user-avatar {
                width: 48px;
                height: 48px;
                border-radius: 50%;
                object-fit: cover;
                flex-shrink: 0;
                background: var(--xc-line);
            }

            .xc-user-body {
                flex: 1;
                min-width: 0;
                padding-top: 2px;
            }

            .xc-user-top {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 12px;
            }

            .xc-user-names { min-width: 0; }

            .xc-user-name {
                display: block;
                font-size: 15px;
                font-weight: 700;
                color: var(--xc-text);
                text-decoration: none;
                line-height: 1.25;
            }

            .xc-user-name:hover { text-decoration: underline; }

            .xc-user-handle {
                display: block;
                font-size: 15px;
                color: var(--xc-muted);
                text-decoration: none;
                line-height: 1.25;
            }

            .xc-user-handle:hover { text-decoration: underline; }

            .xc-user-bio {
                margin: 4px 0 0;
                font-size: 15px;
                line-height: 1.35;
                color: var(--xc-text);
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .xc-follow-btn {
                flex-shrink: 0;
                min-width: 92px;
                padding: 0 16px;
                height: 32px;
                border-radius: 999px;
                border: 1px solid #CFD9DE;
                background: var(--xc-text);
                color: #fff;
                font-family: inherit;
                font-size: 14px;
                font-weight: 700;
                cursor: pointer;
                transition: background .15s ease, color .15s ease, border-color .15s ease;
            }

            .xc-follow-btn:hover {
                background: #272C30;
            }

            .xc-follow-btn--following {
                background: #fff;
                color: var(--xc-text);
                border-color: #CFD9DE;
            }

            .xc-follow-btn--following:hover {
                background: #FDE8E8;
                color: #F4212E;
                border-color: #F5B7B1;
            }

            .xc-empty {
                padding: 48px 24px;
                text-align: center;
            }

            .xc-empty h3 {
                font-size: 24px;
                font-weight: 800;
                margin: 0 0 8px;
            }

            .xc-empty p {
                margin: 0;
                color: var(--xc-muted);
                font-size: 15px;
                line-height: 1.5;
            }

            .xc-pagination {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 14px 16px;
                border-top: 1px solid var(--xc-line);
            }

            .xc-pg-info {
                font-size: 13px;
                color: var(--xc-muted);
            }

            .xc-pg-btns {
                display: flex;
                gap: 8px;
            }

            .xc-pg-btn {
                min-width: 36px;
                height: 36px;
                padding: 0 12px;
                border-radius: 999px;
                border: 1px solid #CFD9DE;
                background: #fff;
                color: var(--xc-text);
                font-family: inherit;
                font-size: 14px;
                font-weight: 700;
                cursor: pointer;
            }

            .xc-pg-btn:hover:not(:disabled) { background: var(--xc-hover); }

            .xc-pg-btn:disabled {
                opacity: .45;
                cursor: not-allowed;
            }
        </style>
    @endverbatim

    <header class="xc-top">
        <div class="xc-top-row">
            <a href="{{ url('profile/' . $user->username) }}" class="xc-back" aria-label="Back to profile">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M7.414 13 16 4.414 14.586 3l-9 9 9 9L16 19.586 7.414 13z"/></svg>
            </a>
            <div class="xc-title-block">
                <h1 class="xc-title">{{ displayName($user->name) }}</h1>
                <p class="xc-subtitle">@<span>{{ $user->username }}</span></p>
            </div>
        </div>

        <div class="xc-tabs" role="tablist">
            <button type="button" role="tab"
                class="xc-tab {{ $activeTab === 'followers' ? 'xc-tab--active' : '' }}"
                wire:click="switchTab('followers')"
                aria-selected="{{ $activeTab === 'followers' ? 'true' : 'false' }}">
                <span class="xc-tab-label">Followers</span>
                <span class="xc-tab-count">{{ number_format($user->followers) }}</span>
            </button>
            <button type="button" role="tab"
                class="xc-tab {{ $activeTab === 'following' ? 'xc-tab--active' : '' }}"
                wire:click="switchTab('following')"
                aria-selected="{{ $activeTab === 'following' ? 'true' : 'false' }}">
                <span class="xc-tab-label">Following</span>
                <span class="xc-tab-count">{{ number_format($user->following) }}</span>
            </button>
        </div>
    </header>

    <ul class="xc-list">
        @forelse ($connections as $person)
            @php
                $isFollowing = isset($followingIds[$person->id]);
                $isSelf = auth()->id() === $person->id;
                $avatarUrl = $person->avatar ?: $defaultAvatar;
            @endphp
            <li class="xc-user" wire:key="conn-{{ $activeTab }}-{{ $person->id }}">
                <a href="{{ url('profile/' . $person->username) }}">
                    <img src="{{ $avatarUrl }}" alt="{{ $person->name }}" class="xc-user-avatar">
                </a>

                <div class="xc-user-body">
                    <div class="xc-user-top">
                        <div class="xc-user-names">
                            <a href="{{ url('profile/' . $person->username) }}" class="xc-user-name">
                                {{ displayName($person->name) }}
                            </a>
                            <a href="{{ url('profile/' . $person->username) }}" class="xc-user-handle">
                                @<span>{{ $person->username }}</span>
                            </a>
                        </div>

                        @if (! $isSelf)
                            <button type="button"
                                wire:click="toggleFollow('{{ $person->id }}')"
                                wire:loading.attr="disabled"
                                wire:target="toggleFollow"
                                @class(['xc-follow-btn', 'xc-follow-btn--following' => $isFollowing])>
                                <span wire:loading.remove wire:target="toggleFollow">
                                    {{ $isFollowing ? 'Following' : 'Follow' }}
                                </span>
                                <span wire:loading wire:target="toggleFollow">…</span>
                            </button>
                        @endif
                    </div>

                    @if ($person->profile?->about)
                        <p class="xc-user-bio">{{ $person->profile->about }}</p>
                    @endif
                </div>
            </li>
        @empty
            <li class="xc-empty">
                <h3>
                    @if ($activeTab === 'followers')
                        @if ($isOwner)
                            Looking for followers?
                        @else
                            No followers yet
                        @endif
                    @else
                        @if ($isOwner)
                            You are not following anyone
                        @else
                            Not following anyone
                        @endif
                    @endif
                </h3>
                <p>
                    @if ($activeTab === 'followers')
                        @if ($isOwner)
                            When someone follows you, they will show up here.
                        @else
                            {{ displayName($user->name) }} does not have any followers yet.
                        @endif
                    @else
                        @if ($isOwner)
                            When you follow people, they will show up here.
                        @else
                            {{ displayName($user->name) }} is not following anyone yet.
                        @endif
                    @endif
                </p>
            </li>
        @endforelse
    </ul>

    @if ($connections->hasPages())
        <div class="xc-pagination">
            <div class="xc-pg-info">
                @if ($connections->total() > 0)
                    {{ number_format($connections->firstItem()) }}–{{ number_format($connections->lastItem()) }}
                    of {{ number_format($connections->total()) }}
                @endif
            </div>
            <div class="xc-pg-btns">
                <button type="button" class="xc-pg-btn" wire:click="previousPage"
                    @disabled($connections->onFirstPage()) aria-label="Previous page">Prev</button>
                <button type="button" class="xc-pg-btn" wire:click="nextPage"
                    @disabled(! $connections->hasMorePages()) aria-label="Next page">Next</button>
            </div>
        </div>
    @endif
</div>
