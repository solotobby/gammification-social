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
                    <br> 
                    You can earn from:<br><br>
                    1. Internal Content post and Engagements: We pay you for every comment you make on Payhankey. Every like and comment your posts gets earns you money. This means we pay you for liking and comment on your posts
                    <br><br>2. External Views, comment and Likes: Payhankey pays you when you share your posts on other social media to get views, likes and comment.
                    <br><br>3. Earn as a Promoter : You earn $1 from every 1000 views on review videos you make about Payhankey on Instagram or TikTok. Once the video (s) goes viral (20, 000 views and above), you earn $20 
                    <br><br>4. Sign up bonus: For registering on Payhankey, you earn up to $3 sign up bonus
                    <br><br>5. Referal bonus: We pay you up to $5 on each user you refer. Copy your referral link from your <a href="{{ url('profile/'.auth()->user()->id)}}"> Profile,</a>
                         share with your friends, add to your social media bio for users to sign up using your referral link.
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
</div>
