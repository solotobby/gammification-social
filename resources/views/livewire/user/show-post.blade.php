<div>
    <div class="row">
        <div class="col-md-8">
            <div wire:poll.visible.420s class="block block-rounded block-bordered" id="timelines">
                <div class="block-header block-header-default">
                <div>
                    <a class="img-link me-1" href="javascript:void(0)">
                    <img class="img-avatar img-avatar32 img-avatar-thumb" src="{{asset('src/assets/media/avatars/avatar11.jpg')}}" alt="">
                    </a>
                    <a class="fw-semibold" href="javascript:void(0)">{{$timeline->user->name}}</a>
                    <span class="fs-sm text-muted">{{$timeline->created_at?->shortAbsoluteDiffForHumans()}} ago</span>
                </div>
                <div class="block-options">
                    <div class="dropdown">
                    <button type="button" class="btn-block-option dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="javascript:void(0)">
                        <i class="far fa-fw fa-times-circle text-danger me-1"></i> Hide similar posts
                        </a>
                        <a class="dropdown-item" href="javascript:void(0)">
                        <i class="far fa-fw fa-thumbs-down text-warning me-1"></i> Stop following this user
                        </a>
                        <div role="separator" class="dropdown-divider"></div>
                        <a class="dropdown-item" href="javascript:void(0)">
                        <i class="fa fa-fw fa-exclamation-triangle me-1"></i> Report this post
                        </a>
                        <a class="dropdown-item" href="javascript:void(0)">
                        <i class="fa fa-fw fa-bookmark me-1"></i> Bookmark this post
                        </a>
                    </div>
                    </div>
                </div>
                </div>
                <div class="block-content">
                    {{-- <a href="#" wire:click="show({{$timeline->id}})"> --}}
                        <p style="color: dimgrey">
                            {!! $timeline->content !!}
                        </p>
                    {{-- </a> --}}
                <hr>
                <ul class="nav nav-pills fs-sm push">
                    <li class="nav-item me-1">
                        @if($timeline->isLikedBy(auth()->user()))
                                        <a class="nav-link"  wire:click="toggleLike({{$timeline->unicode}})" href="javascript:void(0)">
                                            <i class="fa fa-thumbs-down opacity-50 me-1"></i> {{ $timeline->likes }}
                                        </a>
                                @else
                                        <a class="nav-link"  wire:click="toggleLike({{$timeline->unicode}})" href="javascript:void(0)">
                                            <i class="fa fa-thumbs-up opacity-50 me-1"></i> {{ $timeline->likes }}
                                        </a>
                                @endif
                                
                        {{-- @if($timeline->userLikes()->count() > 0)
                        <a class="nav-link"  wire:click="dislike({{$timeline->unicode}})" href="javascript:void(0)">
                            <i class="fa fa-thumbs-down opacity-50 me-1"></i> {{ $timeline->likes }}
                        </a>
                        @else
                            <a class="nav-link"  wire:click="like({{$timeline->unicode}})" href="javascript:void(0)">
                                <i class="fa fa-thumbs-up opacity-50 me-1"></i> {{ $timeline->likes }}
                            </a>
                        @endif --}}
                    </li>
                    <li class="nav-item me-1">
                    <a class="nav-link" href="javascript:void(0)">
                        <i class="fa fa-comment-alt opacity-50 me-1"></i> {{$timeline->comments}}
                    </a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)">
                        <i class="fa fa-eye opacity-50 me-1"></i> {{ $timeline->views }}
                    </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-block-fromright-{{ $timeline->id }}">
                            <i class="fa fa-share opacity-50 me-1"></i>
                        </a>
                    </li>
                </ul>
                </div>
                <div class="block-content block-content-full bg-body-light">
                    {{-- <p class="fs-sm">
                    <i class="fa fa-thumbs-up text-info"></i>
                    <i class="fa fa-heart text-danger"></i>
                    <i class="far fa-smile text-warning me-1"></i>
                    <a class="fw-semibold" href="javascript:void(0)">Brian Cruz</a>,
                    <a class="fw-semibold" href="javascript:void(0)">Lori Grant</a>,
                    <a class="fw-semibold" href="javascript:void(0)">and 150 others</a>
                    </p> --}}
                   <form method="POST" wire:submit.prevent="comment">
                        <input type="text" wire:model="message" name="message"  :value="old('message')" class="form-control form-control-alt" placeholder="Write a comment.." required>
                    </form>
                    <div class="pt-3 fs-sm">

                        @foreach ($comments as $comment)
                            <div class="d-flex">
                                <a class="flex-shrink-0 img-link me-2" href="javascript:void(0)">
                                <img class="img-avatar img-avatar32 img-avatar-thumb" src="{{asset('src/assets/media/avatars/avatar3.jpg')}}" alt="">
                                </a>
                                <div class="flex-grow-1">
                                    <p class="mb-1">
                                        <a class="fw-semibold" href="javascript:void(0)">{{ $comment->user->name }}</a>   <span class="fs-sm text-muted">{{$comment->created_at?->shortAbsoluteDiffForHumans()}} ago</span> <br>
                                        {{ $comment->message }}
                                    </p>
                                </div>
                            </div>
                        @endforeach

                        {{-- @if($comments->hasMorePages())
                            <div class="d-flex justify-content-center">
                                <button wire:click="loadMore" class="btn btn-primary">Load More</button>
                            </div>
                        @endif --}}

                        @if ($this->page <= $this->timeline->postComments()->paginate($this->perPage)->lastPage())
                           
                                <p> 
                                    <a href="javascript:void(0)" wire:click="loadMore">See More</a>
                                </p>
                           
                        @endif
                       
                    </div> 
                </div>
            </div>
        </div>


 <!-- From Right Block Modal -->
 <div class="modal fade" id="modal-block-fromright-{{ $timeline->id }}" tabindex="-1" role="dialog" aria-labelledby="modal-block-fromright" aria-hidden="true">
    <div class="modal-dialog modal-dialog-fromright" role="document">
      <div class="modal-content">
        <div class="block block-rounded block-themed block-transparent mb-0">
          <div class="block-header bg-primary-dark">
            <h3 class="block-title">Share Post</h3>
            <div class="block-options">
              <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                <i class="fa fa-fw fa-times"></i>
              </button>
            </div>
          </div>
          <div class="block-content">
            <p>
               Share this Post on all social media and make money when people view, like or comment on the post
            </p>
            <p>
                {{ url('post/'.$timeline->id) }}

               
            </p>

            <?php 
            
            $url = url('post/'.$timeline->id);
            ?>

            
            <button type="button" onclick="copyToClipboard('{{ $url }}')" class="btn btn-sm btn-alt-secondary" data-bs-dismiss="modal">Copy Link</button>
            <hr>
            <ul class="nav nav-pills fs-sm push">
                <li class="nav-item me-1">
    
                
                    <a class="nav-link"  href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($url) }}" target="_blank">
                        <i class="fab fa-facebook fa-2x opacity-50 me-1"></i> 
                    </a>
    
                    
                </li>
                <li class="nav-item me-1">
                    <a class="nav-link" href="https://twitter.com/intent/tweet?url={{ urlencode($url) }}&text=Check%20this%20out!" target="_blank">
                        <i class="fab fa-square-x-twitter fa-2x opacity-50 me-1"></i> 
                    </a>
                </li>
                <li class="nav-item me-1">
                    <a class="nav-link" href="https://www.instagram.com/?url={{ urlencode($url) }}" target="_blank">
                        <i class="fab fa-instagram fa-2x opacity-50 me-1"></i> 
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode($url) }}" target="_blank">
                        <i class="fab fa-linkedin-in fa-2x opacity-50 me-1"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://pinterest.com/pin/create/button/?url={{ urlencode($url) }}" target="_blank">
                        <i class="fab fa-pinterest-p fa-2x opacity-50 me-1"></i>
                    </a>
                </li>
                {{-- <button type="button" class="btn btn-primary push mb-md-0" data-bs-toggle="modal" data-bs-target="#modal-block-fromright">Block Design</button> --}}
                {{-- <button type="button" class="btn btn-alt-primary push mb-md-0" data-bs-toggle="modal" data-bs-target="#modal-default-fromright">Default</button> --}}
            </ul>

          </div>
          <div class="block-content block-content-full text-end bg-body">
            {{-- <button type="button" class="btn btn-sm btn-alt-secondary" data-bs-dismiss="modal">Close</button> --}}
            <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END From Right Block Modal -->


     <!-- From Right Default Modal -->
    {{-- <div class="modal fade" id="modal-default-fromright" tabindex="-1" role="dialog" aria-labelledby="modal-default-fromright" aria-hidden="true">
        <div class="modal-dialog modal-dialog-fromright" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Modal Title</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-1">
              <p>Potenti elit lectus augue eget iaculis vitae etiam, ullamcorper etiam bibendum ad feugiat magna accumsan dolor, nibh molestie cras hac ac ad massa, fusce ante convallis ante urna molestie vulputate bibendum tempus ante justo arcu erat accumsan adipiscing risus, libero condimentum venenatis sit nisl nisi ultricies sed, fames aliquet consectetur consequat nostra molestie neque nullam scelerisque neque commodo turpis quisque etiam egestas vulputate massa, curabitur tellus massa venenatis congue dolor enim integer luctus, nisi suscipit gravida fames quis vulputate nisi viverra luctus id leo dictum lorem, inceptos nibh orci.</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-sm btn-alt-secondary" data-bs-dismiss="modal">Close</button>
              <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">Done</button>
            </div>
          </div>
        </div>
    </div> --}}
      <!-- END From Right Default Modal -->
      <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Link copied to clipboard');
            }, function(err) {
                alert('Could not copy text: ', err);
            });
        }
    </script>


        @include('layouts.engagement')
    </div>
</div>
