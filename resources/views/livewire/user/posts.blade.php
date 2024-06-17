<div>
    {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}

    <style>
        .form-control {
            resize: none;
            height: 100px; /* Adjust the height as needed */
        }
    
        .stylish-button {
            display: block;
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            transition: background-color 0.3s ease;
            margin-top: 10px; /* Space between textarea and button */
            text-align: center;
        }
    
        .stylish-button:hover {
            background-color: #0056b3;
        }
    
        .stylish-button i {
            margin-right: 5px;
        }
    
        .btn-block {
            display: block;
            width: 100%;
        }
    </style>

    <div class="row">
        <div class="col-md-8">

            {{-- <div class="block block-bordered block-rounded">
                <div class="block-content block-content-full">
                    <form wire:submit.prevent="post">
                        <div class="input-group mb-3">
                            <textarea wire:model="content" name="content" class="form-control form-control-alt @error('content') is-invalid @enderror" placeholder="Say something amazing" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fa fa-pencil-alt opacity-50 me-1"></i> Post
                        </button>
                    </form>
                </div>
            </div> --}}

            <div class="card mb-4">
                <form wire:submit.prevent="post">
                <div class="card-body">
                    <textarea wire:model="content" name="content" class="form-control form-control-alt @error('content') is-invalid @enderror" placeholder="Say something amazing" required></textarea>
                   @if(auth()->user()->level->name == 'Influencer')
                    <div class="d-flex align-items-center justify-content-between mt-3">
                       

                        {{-- <button class="btn btn-light"><i class="fas fa-font"></i></button>
                        <button class="btn btn-light"><i class="fas fa-image"></i></button> --}}
                        <!-- Image upload button -->

                        <label for="image-upload" class="btn btn-light m-0">
                            <i class="fas fa-image"></i>
                        </label>
                        <input type="file" id="image-upload" wire:model="images" multiple accept="image/*" style="display: none;">

                        @if ($imagePreviews)
                            <div class="form-group">
                                <div class="row py-4">
                                    @foreach ($imagePreviews as $preview)
                                        <div class="col-md-3">
                                            <img src="{{ $preview }}" class="img-fluid" alt="Preview Image">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary btn-block"> <i class="fa fa-pencil-alt opacity-50 me-1"></i> Post</button>
                </div>
                </form>
            </div>


            {{-- @foreach ($posts as $post)
                {{ $post->content }} : {{$post->created_at}} <br>
            @endforeach --}}

            @include('layouts.posts', $posts)

            
        </div>
        @include('layouts.engagement')
    </div>



    @include('layouts.onboarding')

</div>
