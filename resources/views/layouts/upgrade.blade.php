<!-- Pricing Tables -->
        <div class="content content-boxed overflow-hidden">

            @if (session()->has('success'))
                <div class="alert alert-success mb-2" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row py-5">

                @foreach (getLevels() as $level)
                    <div class="col-md-6 col-xl-4">
                        @if (userLevel() && userLevel() == $level->name)
                            <!-- Freelancer Plan -->
                            <div class="block block-rounded block-themed text-center">
                                <div class="block-header bg-muted">
                                    <h3 class="block-title">{{ $level->name }}</h3>
                                </div>
                                <div class="block-content bg-body-light">
                                    <div class="py-2">
                                        <p class="h1 fw-bold mb-2">{{ getCurrencyCode() }}{{ convertToBaseCurrency($level->amount, auth()->user()->wallet->currency) }}</p>
                                        <p class="h6 text-muted">per month</p>
                                    </div>
                                </div>
                                <div class="block-content">
                                    <div class="py-2">
                                        <p>
                                            <strong>{{ getCurrencyCode() }}{{ convertToBaseCurrency($level->reg_bonus, auth()->user()->wallet->currency) }} </strong> Upgrade Bonus
                                        </p>
                                        <p>
                                            <strong>{{ getCurrencyCode() }}{{ convertToBaseCurrency($level->ref_bonus, auth()->user()->wallet->currency) }} </strong> Referral Bonus
                                        </p>
                                        
                                        @if($level->name != 'Basic')
                                         <p>
                                            <strong>Account</strong> Monetization
                                        </p>
                                        @endif
                                        
                                        
                                        <p>
                                            <strong> Email</strong> Support
                                        </p>
                                        
                                    </div>
                                </div>
                                <div class="block-content block-content-full bg-body-light">
                                    <span class="btn btn-hero btn-secondary disabled px-4">
                                        <i class="fa fa-check opacity-50 me-1"></i> Active Plan
                                    </span>
                                </div>
                            </div>
                            <!-- END Freelancer Plan -->
                        @else
                            <!-- Startup Plan -->

                            <div class="block block-link-pop block-rounded text-center"
                                {{-- wire:click="upgradeLevel('{{ $level->id }}')" --}}
                                >
                                <a href="{{ url('subscribe/'.$level->id) }}" style="color: black">
                                    <div class="block-header">

                                        <h3 class="block-title">{{ $level->name }}</h3>
                                    </div>
                                    <div class="block-content bg-body-light">
                                        <div class="py-2">
                                            <p class="h1 fw-bold mb-2">
                                                {{ getCurrencyCode() }}{{ convertToBaseCurrency($level->amount, auth()->user()->wallet->currency) }}
                                            </p>
                                            <p class="h6 text-muted">per month</p>
                                        </div>
                                    </div>
                                    <div class="block-content">
                                        <div class="py-2">
                                            <p>
                                                <strong>{{ getCurrencyCode() }}{{ convertToBaseCurrency($level->reg_bonus, auth()->user()->wallet->currency) }} </strong> Upgrade Bonus
                                            </p>
                                            <p>
                                                <strong>{{ getCurrencyCode() }}{{ convertToBaseCurrency($level->ref_bonus, auth()->user()->wallet->currency) }} </strong> Referral Bonus
                                            </p>
                                            
                                            @if($level->name != 'Basic')
                                            <p>
                                                <strong>Account</strong> Monetization
                                            </p>
                                            @endif

                                            <p>
                                                <strong>Email</strong> Support
                                            </p>
                                            
                                        </div>
                                    </div>
                                    <div class="block-content block-content-full bg-body-light">
                                        <span class="btn btn-hero btn-primary px-4">
                                            <i class="fa fa-arrow-up opacity-50 me-1"></i> Upgrade
                                        </span>
                                    </div>
                                </a>
                            </div>
                            <!-- END Startup Plan -->
                        @endif

                    </div>
                @endforeach





            </div>
        </div>
        <!-- END Pricing Tables -->



        <!-- Special Offer -->
        <div class="bg-body-light">
          <div class="content content-boxed content-full">
            <div class="py-5">
              <h2 class="mb-2 text-center">
                Special Offer
              </h2>
              <h3 class="fw-light text-muted push text-center">
                If you upgrade today you will also get all the following at no extra cost.
              </h3>
            </div>
            <div class="row py-3">
              <div class="col-sm-6 col-md-4 mb-5">
                <div class="my-3">
                  <i class="fa fa-2x fa-phone text-xeco"></i>
                </div>
                <h4 class="h5 mb-2">
                  Lifetime Support
                </h4>
                <p class="mb-0 text-muted">
                  Our high quality and award winning phone support will be available 24/7 and for as long as you are using our service.
                </p>
              </div>
              <div class="col-sm-6 col-md-4 mb-5">
                <div class="my-3">
                  <i class="fa fa-2x fa-briefcase text-danger"></i>
                </div>
                <h4 class="h5 mb-2">
                  Unlimited Monetizable Posts
                </h4>
                <p class="mb-0 text-muted">
                  You will have unlimited posts where you can get paid on views, comments and likes. You can also post longer contents.
                </p>
              </div>
              <div class="col-sm-6 col-md-4 mb-5">
                <div class="my-3">
                  <i class="fa fa-2x fa-photo-video text-xinspire"></i>
                </div>
                <h4 class="h5 mb-2">
                  Post Images & Videos
                </h4>
                <p class="mb-0 text-muted">
                  With your post, you will be able to posts images and videos that can be monetized
                </p>
              </div>
            </div>
          </div>
        </div>
        <!-- END Special Offer -->

        <!-- Call to Action -->
        <div class="content content-boxed text-center">
          <div class="py-5">
            <h2 class="mb-3 text-center">
              Why Upgrade?
            </h2>
            <h3 class="h4 fw-light text-muted push text-center">
              Upgrading your account can help you expand your reach and acquire much more followers!
            </h3>
            {{-- <span class="m-2 d-inline-block">
              <a class="btn btn-hero btn-primary" href="javascript:void(0)" data-toggle="click-ripple">
                <i class="fa fa-link opacity-50 me-1"></i> Learn How..
              </a>
            </span> --}}
          </div>
        </div>
