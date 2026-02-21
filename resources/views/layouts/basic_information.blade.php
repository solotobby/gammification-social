@if(userLevel() === 'Basic' && !session('onboarding_completed'))
    <!-- Onboarding Modal -->
    <div class="modal fade" id="modal-onboarding" tabindex="-1" role="dialog"
       aria-labelledby="modal-onboarding" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content rounded overflow-hidden bg-image bg-image-bottom border-0"
                style="background-image: url('assets/media/photos/photo23.jpg');">
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


                            <div class="js-slider slick-dotted-inner" data-dots="true" data-arrows="false"
                                data-infinite="false">
                                <div class="p-5">
                                    {{-- <i class="far fa-face-smile fa-3x text-muted my-4"></i> --}}
                                    <img src="{{ asset('logo.png') }}" alt="" class="logo-light mb-3"
                                        height="54" />
                                    <h3 class="fs-2 fw-light mb-2">Invite Friend and unlock Monetization!</h3>


                                    <p class="text-muted">
                                        Invite friends, share your unique referral link, and unlock monetization features to start earning from your content!
                                    </p>

                                      <div class="input-group mb-4">
                                            <input type="text" id="referralLink" class="form-control"
                                                value="{{ url('/reg?referral_code=' . auth()->user()->referral_code) }}"
                                                readonly />
                                            <button class="btn btn-outline-primary" type="button"
                                                onclick="copyReferralLink()" title="Copy to clipboard">
                                                <i class="fa fa-copy"></i>
                                            </button>
                                        </div>



                                    <a href="{{ url('referral/list') }}" class="btn btn-primary mb-2">
                                        View Referral List<i class="fa fa-arrow-right ms-1"></i>
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


    <script>
        function copyReferralLink() {
            const input = document.getElementById('referralLink');
            input.select();
            input.setSelectionRange(0, 99999); // for mobile

            navigator.clipboard.writeText(input.value).then(() => {
                const feedback = document.getElementById('copyFeedback');
                feedback.classList.remove('d-none');

                setTimeout(() => {
                    feedback.classList.add('d-none');
                }, 2000);
            });
        }
    </script>
@endif


