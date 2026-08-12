<div>
    <style>
        .td-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: .88rem;
            font-weight: 600;
            color: #536471;
            text-decoration: none;
            padding: 16px 0 12px;
            transition: color .15s;
        }

        .td-back:hover {
            color: #5A4FDC;
        }

        .td-back svg {
            width: 18px;
            height: 18px;
        }
    </style>

    <div class="row">
        <div class="col-12">
            <a href="{{ url('timeline') }}" class="td-back" wire:navigate>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Back to feed
            </a>

            <livewire:user.post-content
                :post="$post"
                :estimated-earnings="$estimatedEarnings ?? 0"
                :standalone="true"
                :format-text="true"
                :show-post-menu="true"
                wire:key="post-{{ $post->id }}" />
        </div>
    </div>

    <livewire:user.post-photo-viewer />

    @include('layouts.onboarding')
</div>
