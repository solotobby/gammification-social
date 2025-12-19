
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

                 @if (session()->has('success'))
                    <div class="alert alert-success mb-2" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="alert alert-danger mb-2" role="alert">
                        {{ session('error') }}
                    </div>
                @endif


                 <p class="text-muted">
                  Start earning money by posting engaging content. The more views, likes and comments you get, the more you earn!
                  </p>
                  
                  
                 <button type="button" class="btn btn-primary mb-2" onclick="jQuery('.js-slider').slick('slickGoTo', 1);">
                   Continue <i class="fa fa-arrow-right ms-1"></i>
                 </button>
               </div>

               {{-- <div class="slick-slide p-5">
                   <img src="{{asset('logo.png')}}" alt="" class="logo-light mb-3" height="54" />
                 <h3 class="fs-2 fw-light mb-2">Just before that...</h3>
                 <p class="text-muted">
                   We've got $50 daily for you when you make viral videos about Payhankey. Simply tag us @payhankey_official to be funded daily.
                 </p>
                 
                 <button type="button" class="btn btn-primary mb-2" onclick="jQuery('.js-slider').slick('slickGoTo', 2);">
                  Continue <i class="fa fa-arrow-right ms-1"></i>
                 </button>
               </div> --}}

               <div class="slick-slide p-5">
                 <img src="{{asset('logo.png')}}" alt="" class="logo-light mb-3" height="54" />
               {{-- <i class="fa fa-user-check fa-3x text-muted my-4"></i> --}}

               <h3 class="fs-2 fw-light">Refer and earn more</h3>
               Share your unique referral link with your friends and followers to earn even more.
               <br> Your referral link is:
               <b>{{ url('/reg?referral_code=' . auth()->user()->referral_code) }}</b>
                 <br>
               <button type="button" class="btn btn-primary mb-2 mt-4" onclick="jQuery('.js-slider').slick('slickGoTo', 3);">
                 Finally <i class="fa fa-check opacity-50 ms-1"></i>
               </button>
              
             </div>

               <div class="slick-slide p-5">
                   <img src="{{asset('logo.png')}}" alt="" class="logo-light mb-3" height="54" />
                

                   <h3 class="fs-2 fw-light">More posts, more earnings</h3>
                   Start posting and sharing posts to get views and comments
                 
                 <br> 
                 <h5 class="mb-2 mt-3">Tell us, how you heard About Us</h5>

                 <form action="{{ route('complete.onboarding') }}" method="POST" >
                  @csrf
                 {{-- <textarea class="form-control" name="heard" placeholder="Enter how you heard about us" rows="3" cols="3" required></textarea>  --}}
                 <select class="form-control mt-2" name="heard" required>
                   <option value="">Select your interest</option>
                   <option value="Tiktok">Tiktok</option>
                   <option value="Facebook">Facebook</option>
                   <option value="Youtube">Youtube</option>
                   <option value="Instagram">Instagram</option>
                   <option value="X">Twitter(X)</option>
                   
                 </select>                 
                  <button type="submit" class="btn btn-primary mt-2">  Get Started <i class="fa fa-check opacity-50 ms-1"></i> </button>
                 </form>
                 {{-- <a href="{{ route('complete.onboarding') }}" class="btn btn-primary mb-2 mt-4">
                   Get Started <i class="fa fa-check opacity-50 ms-1"></i>
                 </a> --}}
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
