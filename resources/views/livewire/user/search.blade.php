<div class="pk-app">
    @include('livewire.user.partials.pk-app-ui')

    @php
        $defaultAvatar = asset('src/assets/media/avatars/avatar13.jpg');
        $queryLen = strlen($trimmedQuery);
        $isSearching = $queryLen >= 2;
    @endphp

    <div class="pk-search-page">
        <div class="pk-search-head">
            <h1>Search</h1>
            <p>Find creators and people you know by name or @username.</p>
        </div>

        <div class="pk-search-bar">
            <i class="fa fa-search"></i>
            <input type="search"
                wire:model.live.debounce.350ms="query"
                placeholder="Search by name or username…"
                aria-label="Search users"
                autofocus>
            @if ($query !== '')
                <button type="button" class="pk-search-clear" wire:click="clearSearch" aria-label="Clear search">
                    <i class="fa fa-times"></i>
                </button>
            @endif
        </div>

        <div class="pk-search-meta">
            @if ($queryLen > 0 && $queryLen < 2)
                Keep typing — at least 2 characters to search.
            @elseif ($isSearching && $users)
                {{ number_format($users->total()) }} {{ Str::plural('result', $users->total()) }} for “{{ $trimmedQuery }}”
            @endif
        </div>

        @if (! $isSearching && $queryLen === 0)
            <div class="pk-search-hints">
                <span class="pk-search-hints-label">Suggestions</span>
                @foreach (['creator', 'influencer', 'payhankey'] as $hint)
                    <button type="button" class="pk-search-hint" wire:click="$set('query', '{{ $hint }}')">
                        {{ $hint }}
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    @if ($isSearching)
        <div class="pk-panel">
            @if ($users && $users->count())
                <div>
                    @foreach ($users as $person)
                        @php
                            $isFollowing = in_array($person->id, $followingIds, true);
                        @endphp
                        <div class="pk-search-row" wire:key="search-user-{{ $person->id }}">
                            <a href="{{ url('profile/' . $person->username) }}" class="d-inline-flex">
                                <x-user-avatar :user="$person" size="md" :href="false" />
                            </a>

                            <div class="pk-search-row-body">
                                <a href="{{ url('profile/' . $person->username) }}" class="pk-search-row-name">
                                    {{ displayName($person->name) }}
                                </a>
                                <span class="pk-search-row-handle">@<span>{{ $person->username }}</span></span>
                                <div class="pk-search-row-stats">
                                    <span>{{ number_format($person->followers) }} followers</span>
                                    <span>·</span>
                                    <span>{{ number_format($person->following) }} following</span>
                                </div>
                            </div>

                            <button type="button"
                                @class(['pk-follow-btn', 'pk-follow-btn--active' => $isFollowing])
                                wire:click="toggleFollow('{{ $person->id }}')">
                                {{ $isFollowing ? 'Following' : 'Follow' }}
                            </button>
                        </div>
                    @endforeach
                </div>

                @if ($users->hasPages())
                    <div class="pk-pagination">
                        <div class="pk-pg-info">
                            {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}
                        </div>
                        <div class="pk-pg-btns">
                            <button type="button" class="pk-pg-btn" wire:click="previousPage" @disabled($users->onFirstPage())>
                                Prev
                            </button>
                            <button type="button" class="pk-pg-btn" wire:click="nextPage" @disabled(! $users->hasMorePages())>
                                Next
                            </button>
                        </div>
                    </div>
                @endif
            @else
                <div class="pk-empty">
                    <h3>No one found</h3>
                    <p>We couldn't find anyone matching “{{ $trimmedQuery }}”. Check the spelling or try another name.</p>
                    <button type="button" class="pk-btn pk-btn--ghost" wire:click="clearSearch" style="margin-top:12px">
                        Clear search
                    </button>
                </div>
            @endif
        </div>
    @endif

    @if (auth()->user()->email_verified_at == null)
        @include('layouts.accesscode_verification')
    @else
        @include('layouts.onboarding')
    @endif
</div>
