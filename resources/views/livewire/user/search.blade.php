<div>
    {{-- The whole world belongs to you. --}}

    <style>
        .search-results {
            position: absolute;
            width: 100%;
            background: #fff;
            border: 1px solid #e1e8ed;
            border-radius: 10px;
            margin-top: 4px;
            z-index: 999;
        }

        .search-section {
            padding: 8px 12px;
        }

        .search-section h6 {
            font-size: 13px;
            color: #536471;
            margin-bottom: 4px;
        }

        .search-item {
            display: block;
            padding: 6px 0;
            color: #0f1419;
            text-decoration: none;
        }

        .search-item:hover {
            background: #f7f9f9;
        }

        .post-snippet {
            font-size: 14px;
            color: #536471;
        }
    </style>


    <div class="search-box position-relative mb-3">
        <form wire:submit.prevent="search">
            <div class="input-group">
                <input type="text" class="form-control border-0" placeholder="Search users..."
                    wire:model.debounce.400ms="query">

                <button type="submit" class="btn btn-alt-primary">
                    <i class="fa fa-fw fa-search"></i>
                </button>
            </div>
        </form>
    @if (!empty($query) && count($result))
        <div class="block-content">
            <div class="table-responsive push">
                <table class="table table-hover table-striped table-borderless table-vcenter mb-0">
                    <tbody>

                          @foreach ($result as $user)
                        <tr>
                            <td class="text-center" style="width: 140px;">
                                <img class="img-avatar img-avatar-thumb" 
                               src="{{ $user->avatar ?? asset('src/assets/media/avatars/avatar10.jpg') }}"
                                    alt="">
                            </td>
                            <td style="min-width: 200px;">
                                <div class="py-4">
                                    <p class="mb-0">
                                        <a class="link-fx fw-bold d-inline-block" href="{{ url('profile/'.$user->username) }}">
                                            {{$user->name}}</a>
                                    </p>
                                    <p class="mb-0">
                                        <a class="fs-sm fw-bold text-lowercase text-muted me-3"
                                            href="javascript:void(0)">@<span>{{ $user->username }}</span></a>
                                    </p>
                                </div>
                            </td>
                            <td style="min-width: 200px;">
                                Following: {{ $user->following }}
                            </td>
                            <td class="text-end" style="min-width: 160px;">
                                Followers: {{ $user->followers}}
                            </td>
                        </tr>
                        @endforeach


                          
                    </tbody>
                </table>
            </div>
        </div>

    @endif

        {{-- <input type="text" class="form-control" placeholder="Search users or posts..." wire:model.debounce.400ms="query"> --}}

        {{-- @if ($query && (count($results['users']) || count($results['posts'])))
        <div class="search-results">

         
            @if (!empty($query) && count($result))
                <div class="search-results ">

                    @foreach ($result as $user)
                        <a href="" class="search-item">
                            {{ $user->name }}
                            <span class="text-muted">@ {{ $user->username }}</span>
                        </a>
                    @endforeach

                </div>
            @endif

            @if (count($results['users']))
                    <div class="search-section">
                        <h6>Users</h6>
                        @foreach ($results['users'] as $user)
                            <a href="{{ route('profile.view', $user->username) }}" class="search-item">
                                {{ $user->name }}
                            </a>
                        @endforeach
                    </div>
                @endif 

            
            @if (count($results['posts']))
                    <div class="search-section">
                        <h6>Posts</h6>
                        @foreach ($results['posts'] as $post)
                            <div class="search-item post-snippet">
                                {!! Str::limit(strip_tags($post->content), 80) !!}
                            </div>
                        @endforeach
                    </div>
                @endif

        </div>
        @endif --}}
    </div>




</div>
