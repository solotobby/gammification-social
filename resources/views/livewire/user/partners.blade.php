<div>
   
   
@if(!$partners)
    <!-- Page Content -->
    <div class="content">
        <!-- Groups -->
        <div class="block block-rounded">
          <div class="block-header block-header-default">
            <h3 class="block-title">Become a Partner</h3>
          </div>
          <div class="block-content">

            <form action="#" method="POST" wire:submit.prevent="partner">
              <!-- Text -->
              {{-- <h2 class="content-heading pt-0">Text</h2> --}}
              <div class="row">
                
                <div class="col-lg-3">
                    <p class="text-muted">
                        Fill the following information to get you started.
                      </p>
                </div>
                <div class="col-lg-6">
                    <div class="alert alert-info">
                        Our partners earn up to $500 daily by selling access codes on behalf of Payhankey. We sell access codes to you at 10% discount for you to invite your followers and fans. An individual or organisation can be our partner.
                    </div>
                    <div class="mb-4">
                      <div class="input-group">
                        <span class="input-group-text">
                          Display Name
                        </span>
                        <input type="text" class="form-control" id="example-group1-input1" wire:model="name">
                        <div style="color: brown">@error('name') {{ $message }} @enderror</div>
                      </div>
                    </div>
                  <div class="mb-4">
                    <div class="input-group">
                      <span class="input-group-text">
                        Phone
                      </span>
                      <input type="text" class="form-control" id="example-group1-input1" wire:model="phone">
                      <div style="color: brown">@error('phone') {{ $message }} @enderror</div>
                    </div>
                  </div>
                  <div class="mb-4">
                    <div class="input-group">
                      <span class="input-group-text">
                       ID  
                      </span>
                      <select class="form-control" wire:model="identification" required>
                        <option value="">Select means of identification</option>
                        <option>National Identity Number(NIN)</option>
                        <option>Passport</option>
                      </select>
                    </div>
                    <div style="color: brown">@error('identification') {{ $message }} @enderror</div>
                  </div>
                  <div class="mb-4">
                    <div class="input-group">
                      <span class="input-group-text">
                        Country
                      </span>
                      <select class="form-control" wire:model="country" required>
                        <option value="">Select Country</option>
                        @include('layouts.country_list')
                      </select>
                     
                    </div>
                    <div style="color: brown">@error('country') {{ $message }} @enderror</div>
                  </div>
                  <button type="submit" class="btn btn-primary mb-4">
                    <i class="fa fa-check-circle opacity-50 me-1"></i> Submit
                  </button>
                </div>
                <div class="col-lg-3">
                </div>
              </div>
              <!-- END Text -->

              
            </form>
          </div>
        </div>
        <!-- END Groups -->
      </div>
      <!-- END Page Content -->

      @else
                <h2 class="content-heading">You're a Partner!</h2>
                @if($partners->status == false)
                    <div class="alert alert-info">You have successfully applied to become a partner. We have sent a link to your email for a 15min introductory call with our team. </div>
                @else
                    

                    <div class="row">
                        <div class="col-md-6 col-xl-4">
                          <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                            <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                              <div>
                                <i class="fa fa-2x fa-user text-primary"></i>
                              </div>
                              <div class="ms-3 text-end">
                                <p class="fs-3 fw-medium mb-0">
                                    {{ $slot->beginner }}
                                </p>
                                <p class="text-muted mb-0">
                                  Beginner 
                                </p>
                              </div>
                            </div>
                          </a>
                        </div>
                        <div class="col-md-6 col-xl-4">
                          <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                            <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                              <div>
                                <i class="fa fa-2x fa-users text-primary"></i>
                              </div>
                              <div class="ms-3 text-end">
                                <p class="fs-3 fw-medium mb-0">
                                    {{ $slot->creator }}
                                </p>
                                <p class="text-muted mb-0">
                                  Creator 
                                </p>
                              </div>
                            </div>
                          </a>
                        </div>
                        <div class="col-md-6 col-xl-4">
                          <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                            <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                              <div>
                                <i class="fa fa-2x fa-users text-primary"></i>
                              </div>
                              <div class="ms-3 text-end">
                                <p class="fs-3 fw-medium mb-0">
                                    {{ $slot->influencer }}
                                </p>
                                <p class="text-muted mb-0">
                                    Influencer 
                                </p>
                              </div>
                            </div>
                          </a>
                        </div>

                        <h2 class="content-heading">Purchase Slot</h2>

                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="col-xl-6 order-xl-0">
                          <div class="alert alert-info">
                          As a Partner, you can buy Access codes (as slots) at discounted price (10% discount) and resell to your friends and followers. Select the number of slots and category below to continue. Once payment is successful, you'll be alloted the number of access codes (as slots). You can proceed to sell the codes.
                          </div>
                            <form action="" method="POST" onsubmit="" wire:submit.prevent="purchaseSlot">
                              <div class="mb-4">
                                <label class="form-label" for="dm-profile-edit-password">Package</label>
                                <select wire:model="package" class="form-control">
                                    <option value="">Select Package</option>
                                    <option>Beginner</option>
                                    <option>Creator</option>
                                    <option>Influencer</option>
                                </select>
                                <div style="color: brown">@error('package') {{ $message }} @enderror</div>
                              </div>
                             
                              <div class="mb-4">
                                <label class="form-label" for="dm-profile-edit-password">Number of Slot</label>
                                <select wire:model="slot_number" class="form-control">
                                    <option value="">Select Number of Slot</option>
                                    <option>1</option>
                                    <option>10</option>
                                    <option>25</option>
                                    <option>50</option>
                                    <option>75</option>
                                    <option>100</option>
                                </select>
                                <div style="color: brown">@error('slot_number') {{ $message }} @enderror</div>
                              </div>
                             
                              <div class="mb-4">
                                <label class="form-label" for="dm-profile-edit-password">Currency</label>
                                <select wire:model="currency" class="form-control">
                                    <option value="">Select Currency</option>
                                    <option>USD</option>
                                    {{-- <option>USDT</option> --}}
                                    <option>Naira</option>
                                </select>
                                <div style="color: brown">@error('currency') {{ $message }} @enderror</div>
                              </div>
                             
                              <button type="submit" class="btn btn-alt-primary">
                                <i class="fa fa-check-circle opacity-50 me-1"></i> Continue
                              </button>
                            </form>
                        </div>
                      

                        <h2 class="content-heading">Sell Slot</h2>

                        @if(session()->has('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if(session()->has('fail'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('fail') }}
                            </div>
                        @endif


                        <div class="col-xl-6 order-xl-0">
                          <div class="alert alert-info">
                            Selling access codes means sending access codes slot that you have pre-purchased to your customers email. Please provide correct email of the user, name and send. Thank you
                          </div>

                          <form action="" method="POST" onsubmit="" wire:submit.prevent="sendSlot">
                            <div class="mb-4">
                              <label class="form-label" for="dm-profile-edit-password">Package</label>
                              <select wire:model="package" class="form-control" required>
                                  <option value="">Select Package</option>
                                  <option>Beginner</option>
                                  <option>Creator</option>
                                  <option>Influencer</option>
                              </select>
                              <div style="color: brown">@error('package') {{ $message }} @enderror</div>
                            </div>
                           
                            <div class="mb-4">
                              <label class="form-label" for="dm-profile-edit-password">Recipient Full Name</label>
                              <input type="text" wire:model="full_name"  class="form-control" placeholder="Enter full name" required>
                              <div style="color: brown">@error('full_name') {{ $message }} @enderror</div>
                            </div>

                            <div class="mb-4">
                              <label class="form-label" for="dm-profile-edit-password">Recipient Email Address</label>
                              <input type="text" wire:model="email"  class="form-control"  placeholder="Enter email adress">
                              <div style="color: brown">@error('email') {{ $message }} @enderror</div>
                            </div>

                         
                           
                            <button type="submit" class="btn btn-alt-primary">
                              <i class="fa fa-location opacity-50 me-1"></i> Send
                            </button>
                          </form>
                      </div>
                       
                    </div>


                 

                    




                @endif
      @endif
      @include('layouts.onboarding')
</div>
