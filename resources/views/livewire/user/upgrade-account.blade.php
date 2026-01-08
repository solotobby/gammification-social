<div>
    {{-- The best athlete wants his opponent at his best. --}}
    <main id="main-container">

        <!-- Hero -->
        <div class="bg-image" style="background-image: url({{ asset('src/assets/media/photos/photo11@2x.jpg') }} );">
            <div class="bg-black-75">
                <div class="content content-boxed text-center">
                    <div class="py-5">
                        <h1 class="fs-2 fw-normal text-white mb-2">
                            <i class="fa fa-arrow-up me-1"></i> Upgrade Account
                        </h1>
                        <h2 class="fs-4 fw-normal text-white-75">To Cretor or Influencer to monentize your account!
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        <!-- END Hero -->

            @include('layouts.upgrade')


    </main>



    @if(auth()->user()->email_verified_at == null)
        @include('layouts.accesscode_verification')
    @else
        @include('layouts.onboarding')
    @endif

    
</div>
