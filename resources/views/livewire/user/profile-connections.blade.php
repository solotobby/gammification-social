<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}



    <div class="block block-rounded">

        <div class="fs-3 fw-semibold pt-2 pb-4 mb-2 text-center border-bottom">
            {{ $user->name }} Connection
        </div>


        {{-- Tabs --}}
        <ul class="nav nav-tabs nav-tabs-block" role="tablist">
            <li class="nav-item">
                <button wire:click="switchTab('followers')"
                    class="nav-link {{ $activeTab === 'followers' ? 'active' : '' }}" role="tab">
                    Followers ({{ $user->followers == null ? '0' : $user->followers }})
                </button>
            </li>
            <li class="nav-item">
                <button wire:click="switchTab('following')"
                    class="nav-link {{ $activeTab === 'following' ? 'active' : '' }}" role="tab">
                    Following ({{ $user->following == null ? '0' : $user->following }})
                </button>
            </li>
        </ul>

        <div class="block-content tab-content overflow-hidden mt-3">

            <div class="row">
                @forelse($connections as $person)
                    @php
                        $isFollowing = auth()->user()->isFollowings($person->id);
                    @endphp

                    <div class="col-md-6 col-xl-3">
                        <div class="block block-rounded block-link-shadow text-center">

                            {{-- Avatar --}}
                            <a href="{{ url('profile/' . $person->username) }}">
                                <div class="block-content block-content-full">
                                    <img class="img-avatar rounded-circle"
                                        src="{{ asset('src/assets/media/avatars/avatar13.jpg') }}" {{-- src="{{ $person->avatar ?? asset('default-avatar.png') }}"  --}}
                                        alt="{{ $person->name }}">
                                </div>
                            </a>

                            {{-- Name & Username --}}
                            <div class="block-content block-content-full block-content-sm bg-body-light">
                                <p class="fw-semibold mb-0">{{ $person->name }}</p>
                                <p class="fs-sm fw-medium text-muted mb-0">
                                    <span>@</span><span>{{ $person->username }}</span></p>
                            </div>

                            {{-- Followers / Following counts --}}
                            <div class="block-content block-content-full">
                                <div class="row g-sm mb-2">
                                    <div class="col-6">
                                        <p class="mb-1 fs-sm fw-medium text-muted">Followers</p>
                                        <p class="fw-semibold">{{ $person->followers }}</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1 fs-sm fw-medium text-muted">Following</p>
                                        <p class="fw-semibold">{{ $person->following }}</p>
                                    </div>
                                </div>

                                {{-- Follow/Unfollow button --}}
                                {{-- @if (auth()->id() !== $person->id)
                                <button wire:click="toggleFollow({{ $person->id }})"
                                        class="btn w-100 {{ $isFollowing ? 'btn-secondary' : 'btn-primary' }}">
                                    {{ $isFollowing ? 'Following' : 'Follow' }}
                                </button>
                            @endif --}}
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-400 py-6 w-100">
                        @if ($activeTab === 'followers')
                            No followers yet.
                        @else
                            Not following anyone yet.
                        @endif
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $connections->links() }}
            </div>

        </div>
    </div>





    <div class="container mx-auto px-4">


        {{-- <div class="flex border-b mb-4 space-x-4">
            <button wire:click="switchTab('followers')"
                    class="px-4 py-2 font-semibold 
                        {{ $activeTab === 'followers' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500' }}">
                Followers ({{ $user->followers }})
            </button>

            <button wire:click="switchTab('following')"
                    class="px-4 py-2 font-semibold 
                        {{ $activeTab === 'following' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500' }}">
                Following ({{ $user->following }})
            </button>
        </div> --}}

        {{-- Connections List --}}
        {{-- <div class="bg-white shadow rounded-lg">
            @forelse($connections as $conn)
                @php
                    $person = $activeTab === 'followers' ? $conn->followers : $conn->following;
                    $isFollowing = auth()->user()->isFollowing($person->id);
                @endphp
                <div class="flex items-center justify-between px-4 py-3 border-b hover:bg-gray-50">
                    <div class="flex items-center space-x-3">
                        <a href="{{ url('profile/'.$person->username) }}">
                            <img src="{{ $person->avatar ?? asset('default-avatar.png') }}"
                                class="w-10 h-10 rounded-full object-cover" alt="{{ $person->name }}">
                        </a>
                        <div>
                            <a href="{{ url('profile/'.$person->username) }}" class="font-semibold hover:underline">
                                {{ $person->name }}
                            </a>
                            <div class="text-sm text-gray-500">@ {{ $person->username }}</div>
                        </div>
                    </div>

                    @if (auth()->id() !== $person->id)
                            <button wire:click="toggleFollow({{ $person->id }})"
                                    class="px-3 py-1 rounded border text-sm font-semibold
                                    {{ $isFollowing ? 'bg-gray-200 text-gray-700 border-gray-300' : 'bg-blue-500 text-white border-blue-500' }}">
                                {{ $isFollowing ? 'Following' : 'Follow' }}
                            </button>
                        @endif
                </div>
            @empty
                <div class="text-center text-gray-400 py-6">
                    @if ($activeTab === 'followers')
                        No followers yet.
                    @else
                        Not following anyone yet.
                    @endif
                </div>
            @endforelse

            {{-- Pagination --
            <div class="p-4">
                {{ $connections->links() }}
            </div>
        </div>
 --}}





    </div>
</div>
