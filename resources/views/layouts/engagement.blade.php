<div class="col-md-4">
    <!-- Group Suggestions -->
    @foreach (engagement() as $high)
    <div  class="block block-rounded bg-body-dark">
        <div class="block-content block-content-full">
            <div class="row g-sm mb-2">
            <div class="col-6">
                <img class="img-fluid" src="assets/media/photos/photo18.jpg" alt="">
            </div>
            <div class="col-6">
                <img class="img-fluid" src="assets/media/photos/photo16.jpg" alt="">
            </div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
            <div>
                <a class="fw-semibold" href="{{url('profile/'.$high->user->id)}}">{{$high->user->name}}</a>
                <div class="fs-sm text-muted">{{ $high->total }} Engagements</div>
            </div>
            <a class="btn btn-sm btn-alt-secondary d-inline-block" href="{{url('profile/'.$high->user->id)}}">
                <i class="fa fa-fw fa-plus-circle"></i>
            </a>
            </div>
        </div>
    </div>
    @endforeach
    <!-- END Group Suggestions -->
</div>