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

    <div class="col-md-4 mt-3">
        <h4>Trending Topics</h4>

        <div class="block block-rounded bg-body-dark">
            <div class="block-content block-content-full">

                @foreach (trendingTopics() as $high)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <a class="fw-semibold"
                               href="{{ $high->url }}"
                               style="color: #5A4FDC">
                                {{ $high->phrase }}
                            </a>
                            {{-- <div class="fs-sm text-muted">
                                Score: {{ number_format($high->score) }}
                            </div> --}}
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

        <h4 class="mt-4">Trending Members <small class="text-muted"> (Last 6 hours) </small></h4> 

        @foreach (engagement() as $high)
            <div class="block block-rounded bg-body-dark">
                <div class="block-content block-content-full">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <a class="fw-semibold"
                               href="{{ url('profile/' . $high['username']) }}"
                               style="color: #5A4FDC">
                                {{ displayName($high['name']) }}
                            </a>
                            <div class="fs-sm text-muted">
                                {{ $high['total_engagement'] }} Engagements
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

    </div>

{{-- </div> 👈 END Single Root --}}