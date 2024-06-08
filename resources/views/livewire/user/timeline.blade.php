<div>
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
            <!-- Post Update -->
            {{-- @if(session()->has('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif --}}
        <!-- Post Update -->
        <div class="block block-bordered block-rounded">
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
        </div>
        <!-- END Post Update -->
           
        
        <!-- Update #2 -->
              
            @include('layouts.posts', $timelines)
        <!-- END Update #2 -->

        </div>

        @include('layouts.engagement')

    </div>

    @if(auth()->user()->is_onboarded == false)
     <!-- Onboarding Modal -->
     <div class="modal fade" id="modal-onboarding" tabindex="-1" role="dialog" data-bs-backdrop="static" 
     data-bs-keyboard="false" aria-labelledby="modal-onboarding" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
          <div class="modal-content rounded overflow-hidden bg-image bg-image-bottom border-0" style="background-image: url('assets/media/photos/photo23.jpg');">
            <div class="row">
              {{-- <div class="col-md-5">
                <div class="p-3 text-end text-md-start">
                  <a class="fw-semibold text-white" href="#" data-bs-dismiss="modal" aria-label="Close">
                    Skip Intro
                  </a>
                </div>
              </div> --}}
              <div class="col-md-12">
                <div class="bg-body-extra-light shadow-lg">
                  <div class="js-slider slick-dotted-inner" data-dots="true" data-arrows="false" data-infinite="false">
                    <div class="p-5">
                      {{-- <i class="far fa-face-smile fa-3x text-muted my-4"></i> --}}
                      <img src="{{asset('logo.png')}}" alt="" class="logo-light mb-3" height="54" />
                      <h3 class="fs-2 fw-light mb-2">Welcome to Payhankey!</h3>
                      <p class="text-muted">
                        Check your wallet for your Sign up Bonus
                      </p>
                      <button type="button" class="btn btn-primary mb-2" onclick="jQuery('.js-slider').slick('slickGoTo', 1);">
                        Next <i class="fa fa-arrow-right ms-1"></i>
                      </button>
                    </div>
                    <div class="slick-slide p-5">
                        <img src="{{asset('logo.png')}}" alt="" class="logo-light mb-3" height="54" />
                      <h3 class="fs-2 fw-light mb-2">Invoices</h3>
                      <p class="text-muted">
                        They are sent automatically to your clients with the completion of every project, so you don't have to worry about getting paid.
                      </p>
                      
                      <button type="button" class="btn btn-primary mb-2" onclick="jQuery('.js-slider').slick('slickGoTo', 2);">
                        Complete Profile <i class="fa fa-arrow-right ms-1"></i>
                      </button>
                    </div>
                    <div class="slick-slide p-5">
                        <img src="{{asset('logo.png')}}" alt="" class="logo-light mb-3" height="54" />
                      {{-- <i class="fa fa-user-check fa-3x text-muted my-4"></i> --}}
                      <h3 class="fs-2 fw-light">Let us know your name</h3>
                      
                      {{-- <button type="button" class="btn btn-primary mb-2" data-bs-dismiss="modal" aria-label="Close">
                        Get Started <i class="fa fa-check opacity-50 ms-1"></i>
                      </button> --}}
                      <a href="{{ route('complete.onboarding') }}" class="btn btn-primary mb-2">
                        Get Started <i class="fa fa-check opacity-50 ms-1"></i>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- END Onboarding Modal -->
      @endif

      {{-- <script src="assets/js/lib/jquery.min.js"></script>
      <script src="{{ asset('src/assets/js/pages/be_comp_onboarding.min.js')}}"></script> --}}
</div>

