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
                        <option>Nigeria</option>
                        <option>Germany</option>
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
                    <div class="alert alert-info">You have successfully applied to become a partner. Check your email for a 15min introductory call with our team. </div>
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
                                  Begginer 
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
                        <div class="col-xl-6 order-xl-0">
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
                                    <option>USDT</option>
                                    <option>Naira</option>
                                </select>
                                <div style="color: brown">@error('currency') {{ $message }} @enderror</div>
                              </div>
                             
                              <button type="submit" class="btn btn-alt-primary">
                                <i class="fa fa-check-circle opacity-50 me-1"></i> Continue
                              </button>
                            </form>
                        </div>
                       
                    </div>


                @endif
      @endif
</div>