<div>

     <div class="row">
        <div class="col-md-8 ph-feed-wrap">
    {{-- ===== Feed ===== --}}
    <div class="ph-feed-head">Posts for #{{ $hashtag->name }}</div>

    @forelse (@$posts as $post)
        <livewire:user.post-content :post="$post" :wire:key="'post-'.$post->id" />
    @empty
        <div class="ph-empty">
            <div class="ph-empty-ic"><i class="fa fa-feather-alt"></i></div>
            <h6>Your feed is waiting</h6>
            <p>Share your first post above — it can start earning the moment people engage.</p>
        </div>
    @endforelse

        </div>

          @include('layouts.engagement')
     </div>
       @include('layouts.onboarding')
</div>
