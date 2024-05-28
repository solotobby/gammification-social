<div>
    <div class="row">
        <div class="col-md-8">
            <!-- Post Update -->
            @if(session()->has('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
        <!-- Post Update -->
        <div class="block block-bordered block-rounded">
            <div class="block-content block-content-full">
            <form wire:submit.prevent="post">
            
                <div class="input-group">
                <input type="text" wire:model="content" name="content"  :value="old('content')" class="form-control form-control-alt @error('content') is-invalid @enderror" placeholder="Say something amazing" oninput="convertUrlsToLinks(this)" required>
                
                
                <button type="submit" class="btn btn-primary border-0">
                    <i class="fa fa-pencil-alt opacity-50 me-1"></i> Post
                </button>

                </div>
            </form>
            </div>
        </div>
        <!-- END Post Update -->

        
        <!-- Update #2 -->
              
            @include('layouts.posts', $timelines)
        <!-- END Update #2 -->

        </div>

        @include('layouts.engagement')

    </div>

</div>

<script>
    function convertUrlsToLinks(textarea) {
        let text = input.value;
        let pattern = /\b(?:https?:\/\/|www\.)\S+\b/g;
        let replacement = '<a href="$&" target="_blank" rel="noopener noreferrer">$&</a>';
        input.innerHTML = text.replace(pattern, replacement);
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let timelines = document.querySelectorAll('.timelines');

        timelines.forEach(post => {
            let observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Trigger Livewire function when post becomes visible
                        alert('show');
                        Livewire.emit('postVisible', post.dataset.postId);
                        observer.unobserve(timelines);
                    }
                });
            });

            observer.observe(timelines);
        });
    });
</script>
