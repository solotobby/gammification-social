<div>
    <div class="row">
        <h2 class="content-heading">How to Earn on Payhankey</h2>
        <div class="col-md-12">
         
            <div class="block block-rounded">
              <div class="block-header block-header-default block-header">
                {{-- <h3 class="block-title">How to Earn</h3> --}}
                
              </div>

             
            
                <div class="block-content">
                  <div class="row justify-content-center py-sm-1 py-md-2">
                    
                    <div class="col-12">
                    Earning is easy on Payhankey when you post, comment, like and share as many times as possible. 
                    All users get up to $5 (equivalent of N7500) sign up bonus. 

                    <br> 
                    You can earn from:<br><br>
                    1. Internal Content post and Engagements: We pay you for every post you make on Payhankey. This means even every like and comment your posts gets earns you money. Payhankey pays you for the likes, comments and views your posts gets.  
                    <br> 

                    To monetize your post, you must have an active badge of a Creator or an Influencer. Each badge is valid for only 30 days after which you can renew to continue to have access to monetization. Payhankey pays up to $0.8 for each view, and $0.4 each for each like and comment. 
                    Creators and Influencers earn more while Basic user accounts are not monetized. 

                    <br><br>2. Earn as a Promoter: You earn from every viral review videos you make about Payhankey on Instagram or TikTok. Make sure you tag us @payhankeyofficial before making the post. Once the video (s) goes viral (20, 000 views and above), you earn up to $20.
                    <br><br>3. Sign up bonus: For registering on Payhankey, you earn up to $1 sign up bonu
                    
                    <br><br>4.Referral bonus: We pay you up to $0.75 on each user you refer. Copy your referral link from your <a href="{{ url('profile/'.auth()->user()->username) }}"> Profile </a>, share with your friends, add to your social media bio for users to sign up using your referral link. 
                    </div>
                  </div>
                </div>
            

              <div class="block-content block-content-full block-content-sm bg-body-light text-end">
             
               
              </div>
            </div>
        
        </div>






          {{-- <div class="col-md-6 col-xl-6">
              
              

              @if(empty($wallets->usdt_wallet_address))
                  <form action="" method="POST" onsubmit="" wire:submit.prevent="updateUSDTWallet">
                      <div class="mb-4">
                      <label class="form-label" for="dm-profile-edit-password">USDT Wallet Address(TRC 20)</label>
                      <input type="text" class="form-control" id="dm-profile-edit-password" wire:model="usdt_wallet_address" placeholder="Enter Withdrawal Wallet Address" value="{{ $wallets->usdt_wallet_address }}" required>
                      </div>
                      
                      <button type="submit" class="btn btn-alt-primary">
                      <i class="fa fa-check-circle opacity-50 me-1"></i> Add Withdrawal Wallet
                      </button>
                  </form>
              @else

                  USDT Wallet Address: {{  maskCode($wallets->usdt_wallet_address) }}

               

              @endif
              
          </div> --}}
      </div>



      @if(auth()->user()->email_verified_at == null)
        @include('layouts.accesscode_verification')
    @else

        @include('layouts.onboarding')

    @endif

    
</div>
