

<!-- Modal -->
<div class="modal fade" id="modal-onboarding" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="modal-onboarding" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            {{-- <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
        
      </div> --}}
            <div class="modal-body">
                <div class="col-md-12">

                    <img src="{{ asset('logo.png') }}" alt="" class="logo-light mb-3" height="54" />


                    <h3 class="fs-2 fw-light mb-2">Welcome to Payhankey!</h3>
                    <p class="text-muted">
                        You just won a Sign Up Bonus! To claim your bonus, please enter the Access Code sent to your email (check spam/junk folder)
                    </p>

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


                    <form wire:submit.prevent="verifyAccessCode">
                        {{-- Access Code --}}
                        <input
                            type="text"
                            class="form-control"
                            wire:model.defer="access_code"
                            placeholder="Enter the Access Code"
                            required
                        >

                        @error('access_code')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror

                        {{-- Currency Select --}}
                        <select
                            class="form-control mt-2"
                            wire:model.defer="currency"
                            required
                        >
                            <option value="">Select Currency</option>
                            <option value="USD">USD – US Dollar</option>
                            <option value="EUR">EUR – Euro</option>
                            <option value="GBP">GBP – British Pound</option>
                            <option value="NGN">NGN – Nigerian Naira</option>
                        </select>

                        @error('currency')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror

                        {{-- Submit Button --}}
                        <button
                            type="submit"
                            class="btn btn-primary mt-3"
                            wire:loading.attr="disabled"
                            wire:target="verifyAccessCode"
                        >
                            {{-- Normal State --}}
                            <span wire:loading.remove wire:target="verifyAccessCode">
                                Claim Bonus
                                <i class="fa fa-check opacity-50 ms-1"></i>
                            </span>

                            {{-- Loading State --}}
                            <span wire:loading wire:target="verifyAccessCode">
                                <i class="fa fa-spinner fa-spin me-1"></i>
                                Processing...
                            </span>
                        </button>
                    </form>




                    {{-- <form wire:submit.prevent="verifyAccessCode">
                        <input type="text" class="form-control" wire:model.defer="access_code"
                            placeholder="Enter the Access Code" required>

                        @error('access_code')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror

                        <button type="submit" class="btn btn-primary mt-2" wire:loading.attr="disabled"
                            wire:target="submit">
                           
                            <span wire:loading.remove wire:target="submit">
                                Claim Bonus
                                <i class="fa fa-check opacity-50 ms-1"></i>
                            </span>

                           
                            <span wire:loading wire:target="submit">
                                <i class="fa fa-spinner fa-spin me-1"></i>
                                Processing...
                            </span>
                        </button>
                    </form> --}}






                </div>
            </div>
            {{-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div> --}}
        </div>
    </div>
</div>
