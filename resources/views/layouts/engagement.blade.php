{{-- <div class="col-md-4 mt-3">
    <h4>Trending Topics</h4>
    <div class="block block-rounded bg-body-dark">
        <div class="block-content block-content-full">
            @foreach (trendingTopics() as $high)
            {{-- <div class="row g-sm mb-2">
                <div class="col-6">
                    <img class="img-fluid" src="assets/media/photos/photo18.jpg" alt="">
                </div>
                <div class="col-6">
                    <img class="img-fluid" src="assets/media/photos/photo16.jpg" alt="">
                </div>
            </div> --}
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    {{-- <a class="fw-semibold" href="" style="color: #5A4FDC">{{ $high->phrases }}</a> --}}
{{-- <div class="fs-sm text-muted"> 5786 Engagements</div> --}
                </div>
                {{-- <a class="btn btn-sm btn-alt-secondary d-inline-block" href="{{url('profile/'.$high->user->username)}}">
                <i class="fa fa-fw fa-plus-circle"></i>
            </a> --}
            </div>
        </div>
          @endforeach
    </div>


    <h4>Trending Members</h4>
    <!-- Group Suggestions -->
    @foreach (engagement() as $high)
        <div class="block block-rounded bg-body-dark">
            <div class="block-content block-content-full">
                {{-- <div class="row g-sm mb-2">
                    <div class="col-6">
                        <img class="img-fluid" src="assets/media/photos/photo18.jpg" alt="">
                    </div>
                    <div class="col-6">
                        <img class="img-fluid" src="assets/media/photos/photo16.jpg" alt="">
                    </div>
                </div> --}
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a class="fw-semibold" href="{{ url('profile/' . $high->user->username) }}"
                            style="color: #5A4FDC">{{ displayName($high->user->name) }}</a>
                        <div class="fs-sm text-muted">{{ $high->total }} Engagements</div>
                    </div>
                    <a class="btn btn-sm btn-alt-secondary d-inline-block"
                        href="{{ url('profile/' . $high->user->username) }}">
                        <i class="fa fa-fw fa-plus-circle"></i>
                    </a>
                </div>
            </div>
        </div>
    @endforeach
    <!-- END Group Suggestions -->
</div>
 --}}


{{-- <div> 👈 Single Root Wrapper Required by Livewire --}}

{{-- <div class="col-md-4 mt-3"> --}}
    {{-- <h4>Trending Topics</h4>
    @foreach (trendingTopics() as $high)
        <div class="block block-rounded bg-body-dark">
            <div class="block-content block-content-full">


                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <a class="fw-semibold" href="" style="color: #5A4FDC">
                            {{ $high->phrase }}
                        </a>
                       
                    </div>
                </div>


            </div>
        </div>
    @endforeach --}}

    {{-- <h4 class="mt-4">Trending Members <small class="text-muted"> (Last 6 hours) </small></h4>

    @foreach (engagement() as $high)
        <div class="block block-rounded bg-body-dark">
            <div class="block-content block-content-full">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a class="fw-semibold" href="{{ url('profile/' . $high['username']) }}" style="color: #5A4FDC">
                            {{ displayName($high['name']) }}
                        </a>
                        <div class="fs-sm text-muted">
                            {{ formatNumber($high['total_engagement']) }} Engagements
                        </div>
                    </div>

                    <a class="btn btn-sm btn-alt-secondary d-inline-block"
                        href="{{ url('profile/' . $high['username']) }}">
                        <i class="fa fa-fw fa-plus-circle"></i>
                    </a>
                </div>
            </div>
        </div>
    @endforeach

</div> --}}

{{-- </div> 👈 END Single Root --}}



<div class="col-md-4 mt-3">

    {{-- TRENDING TOPICS --}}
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="fw-bold mb-0">
                <i class="fa fa-fire text-danger me-1"></i> Trending Topics
            </h5>
            <small class="text-muted">Top 5</small>
        </div>

        <div class="block block-rounded overflow-hidden">
            @foreach (trendingTopics() as $index => $trend)
                <div class="d-flex align-items-center px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }} trend-item">

                    {{-- Rank number --}}
                    <div class="me-3 text-center" style="min-width:28px">
                        @if ($index === 0)
                            <span class="badge rounded-pill bg-danger" style="font-size:0.7rem;">
                                <i class="fa fa-fire"></i>
                            </span>
                        @elseif ($index === 1)
                            <span class="badge rounded-pill bg-warning text-dark" style="font-size:0.7rem;">
                                <i class="fa fa-bolt"></i>
                            </span>
                        @elseif ($index === 2)
                            <span class="badge rounded-pill bg-info" style="font-size:0.7rem;">
                                <i class="fa fa-star"></i>
                            </span>
                        @else
                            <span class="text-muted fw-bold" style="font-size:0.8rem;">
                                {{ $index + 1 }}
                            </span>
                        @endif
                    </div>

                    {{-- Trend name + count --}}
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="color:#5A4FDC; font-size:0.95rem;">
                            #{{ $trend->name }}
                        </div>
                        <div class="text-muted" style="font-size:0.75rem;">
                            {{ formatNumber($trend->post_count) }}
                            {{ Str::plural('post', $trend->post_count) }}
                        </div>
                    </div>

                    {{-- Trending bar --}}
                    @php
                        $max = trendingTopics()->first()->post_count ?: 1;
                        $pct = $trend->post_count > 0 ? round(($trend->post_count / $max) * 100) : 3;
                    @endphp
                    <div style="width:50px">
                        <div class="rounded-pill" style="
                            height:4px;
                            background: linear-gradient(90deg, #5A4FDC, #a78bfa);
                            width:{{ $pct }}%;
                            min-width:6px;
                            opacity:{{ $index === 0 ? 1 : 0.5 + ($pct / 200) }};
                        "></div>
                    </div>

                </div>
            @endforeach

            @if (trendingTopics()->isEmpty())
                <div class="px-3 py-3 text-muted text-center" style="font-size:0.85rem;">
                    No trending topics yet.
                </div>
            @endif
        </div>
    </div>

    {{-- TRENDING MEMBERS --}}
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h5 class="fw-bold mb-0">
            <i class="fa fa-users text-primary me-1"></i> Trending Members
        </h5>
        <small class="text-muted">Last 6 hours</small>
    </div>

    @foreach (engagement() as $index => $high)
        <div class="block block-rounded mb-2">
            <div class="block-content block-content-full">
                <div class="d-flex align-items-center">

                    {{-- Rank badge --}}
                    <div class="me-2 text-center" style="min-width:24px">
                        @if ($index === 0)
                            <i class="fa fa-trophy text-warning"></i>
                        @elseif ($index === 1)
                            <i class="fa fa-medal text-secondary"></i>
                        @elseif ($index === 2)
                            <i class="fa fa-medal" style="color:#cd7f32"></i>
                        @else
                            <span class="text-muted fw-bold" style="font-size:0.8rem;">{{ $index + 1 }}</span>
                        @endif
                    </div>

                    {{-- Name + engagements --}}
                    <div class="flex-grow-1">
                        <a class="fw-semibold d-block" href="{{ url('profile/' . $high['username']) }}"
                            style="color:#5A4FDC; font-size:0.9rem; text-decoration:none;">
                            {{ displayName($high['name']) }}
                        </a>
                        <div class="text-muted" style="font-size:0.75rem;">
                            <i class="fa fa-chart-line me-1"></i>
                            {{ formatNumber($high['total_engagement']) }} engagements
                        </div>
                    </div>

                    {{-- Visit profile --}}
                    <a class="btn btn-sm btn-alt-secondary"
                        href="{{ url('profile/' . $high['username']) }}"
                        title="View Profile">
                        <i class="fa fa-fw fa-user-plus"></i>
                    </a>

                </div>
            </div>
        </div>
    @endforeach

</div>
