<div>

     <div class="row">
        <div class="col-12 ph-feed-wrap">
    {{-- ===== Feed ===== --}}
    <div class="ph-feed-head">Posts for #{{ $hashtag->name }}</div>

    @forelse ($posts as $post)
        <livewire:user.post-content
            :post="$post"
            :estimated-earnings="$earnings[$post->id] ?? 0"
            :format-text="true"
            :show-post-menu="true"
            wire:key="post-{{ $post->id }}" />
    @empty
        <div class="ph-empty">
            <div class="ph-empty-ic"><i class="fa fa-feather-alt"></i></div>
            <h6>Your feed is waiting</h6>
            <p>Share your first post above — it can start earning the moment people engage.</p>
        </div>
    @endforelse

        </div>
     </div>
       @include('layouts.onboarding')

    <livewire:user.post-photo-viewer />
</div>
