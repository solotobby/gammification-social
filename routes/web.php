<?php

use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\EngagementPayoutController;
use App\Http\Controllers\Admin\LevelManagementController;
use App\Http\Controllers\Admin\MonthlyPayoutController;
use App\Http\Controllers\Admin\PayoutController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserEngagementController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CloudinaryWebhookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\VideoAnalyticsController;
use App\Livewire\CreateProduct;
use App\Livewire\Level;
use App\Livewire\User\Analytics;
use App\Livewire\User\BankInformation;
use App\Livewire\User\HowItWorks;
use App\Livewire\User\HowToEarn;
use App\Livewire\User\NewTimeline;
use App\Livewire\User\Partners;
use App\Livewire\User\PostAnalytics;
use App\Livewire\User\Posts;
use App\Livewire\User\Profile;
use App\Livewire\User\ProfileConnections;
use App\Livewire\User\PromotionalContent;
use App\Livewire\User\ReferralList;
use App\Livewire\User\Search;
use App\Livewire\User\Settings;
use App\Livewire\User\ShowNewPosts;
use App\Livewire\User\ShowPost;
use App\Livewire\User\Timeline;
use App\Livewire\User\TimelineDetails;
use App\Livewire\User\TransactionList;
use App\Livewire\User\UpgradeAccount;
use App\Livewire\User\VideoPlayer;
use App\Livewire\User\VideoRolls;
use App\Livewire\User\ViewProfile;
use App\Livewire\User\Wallets;
use App\Livewire\ViewPost;
use App\Models\Referral;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::group(['namespace' => 'auth'], function () {

    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('test', [\App\Http\Controllers\GeneralController::class, 'test']);
    Route::get('fix', [\App\Http\Controllers\GeneralController::class, 'devy']);
    Route::get('privacy/policy', [\App\Http\Controllers\GeneralController::class, 'privacyPolicy']);

    Route::get('admin', [\App\Http\Controllers\GeneralController::class, 'admin']);
    Route::get('reg', [\App\Http\Controllers\Auth\RegisterController::class, 'reg']);

    Route::post('process/reg', [\App\Http\Controllers\Auth\RegisterController::class, 'regUser'])->name('reg.user');

    Route::post('user/login', [\App\Http\Controllers\Auth\RegisterController::class, 'loginUser'])
        ->middleware('throttle:5,1')->name('login.user');


    Route::get('access/code/{level}', [\App\Http\Controllers\GeneralController::class, 'accessCode']);
    Route::post('process/access/code', [\App\Http\Controllers\GeneralController::class, 'processAccessCode']);

    Route::get('how-it-works', [\App\Http\Controllers\GeneralController::class, 'how']);

    Route::get('success', [\App\Http\Controllers\GeneralController::class, 'success']);
    Route::get('error', [\App\Http\Controllers\GeneralController::class, 'error']);

    Route::post('wallet/topup', [\App\Http\Controllers\WebhookController::class, 'handle']);

    Route::get('get/ip', [\App\Http\Controllers\GeneralController::class, 'ipConfig']);

    Route::get('seniore/login', [\App\Http\Controllers\GeneralController::class, 'dinkyLogin']);

    Route::get('registration/{code}', [\App\Http\Controllers\AdminLoginController::class, 'login']);
    Route::post('registration', [\App\Http\Controllers\AdminLoginController::class, 'proAdminLogin'])->name('dinky.reg');

    Route::get('blog', [BlogController::class, 'index'])->name('blog');
    Route::get('blog/{slug}', [BlogController::class, 'show']);

    Route::post('/webhooks/cloudinary/video-processing', [CloudinaryWebhookController::class, 'handleVideoProcessing'])->name('cloudinary.webhook');


    // Route::post('post/comment', [\App\Http\Controllers\GeneralController::class, 'comment']);

    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->middleware('auth')->name('verification.notice');

    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->middleware('auth')->name('verification.notice');



    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect('/home');
    })->middleware(['auth', 'signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', 'Verification link sent!');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');
});

Auth::routes();



Route::middleware(['auth', 'verified', 'track.online'])->group(function () {

    Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('user/home', [\App\Http\Controllers\HomeController::class, 'userHome'])->name('user.home');

    Route::group(['middleware' => 'auth', 'role:user'], function () {

        Route::post('complete/onboarding', [\App\Http\Controllers\HomeController::class, 'completeOnboarding'])->name('complete.onboarding');
        Route::post('access/code/verification', [\App\Http\Controllers\HomeController::class, 'accessCodeVerification'])->name('access.code.verification');

        Route::get('validate/api', [\App\Http\Controllers\HomeController::class, 'validateApi']);
        Route::get('verify/subscription/payment/', [\App\Http\Controllers\PaymentController::class, 'verifyKoraSubscriptionPayment'])->name('verify.subscription');
        Route::get('subscribe/{levelId}', [\App\Http\Controllers\PaymentController::class, 'createSubscription'])->name('subscribe');
      

        //video player analytics route
        Route::post('api/videos/{video}/watch-time', [VideoAnalyticsController::class, 'trackWatchTime']);
        Route::post('api/videos/{video}/record-play', [VideoAnalyticsController::class, 'recordPlay']);


        Route::get('timeline', Timeline::class);
        Route::get('timeline/{post}', TimelineDetails::class);
        Route::get('new/timeline', NewTimeline::class);

        Route::get('profile/{username}', ViewProfile::class);
        Route::get('post/timeline/{id}/analytics', PostAnalytics::class);
        Route::get('analytics', Analytics::class);
        Route::get('settings', Settings::class);
        Route::get('wallets', Wallets::class);
        Route::get('partner', Partners::class);
        Route::get('how/to/earn', HowToEarn::class);
        Route::get('upgrade', UpgradeAccount::class);
        Route::get('promotions', PromotionalContent::class);
        Route::get('profile/{username}/connection', ProfileConnections::class);
        Route::get('transaction/list', TransactionList::class);
        Route::get('referral/list', ReferralList::class);
        Route::get('bank/information', BankInformation::class);
        Route::get('how/it/works', HowItWorks::class);
        Route::get('search/user', Search::class);
        // Route::get('rolls/play/{videoId}', VideoPlayer::class);
        Route::get('rolls/{videoId}', VideoRolls::class)->name('rolls.show');

        
      

    });


    Route::group(['middleware' => 'auth', 'role:admin'], function () {
        Route::get('admin/home', [\App\Http\Controllers\Admin\AdminController::class, 'home'])->name('admin.home');

        Route::get('user/list/{level}', [\App\Http\Controllers\Admin\UserController::class, 'userList'])->name('user.list');
        Route::get('user/search', [\App\Http\Controllers\Admin\UserController::class, 'userSearch']);
        Route::get('user/info/{id}', [\App\Http\Controllers\Admin\UserController::class, 'userInfo']);
        Route::post('user/credit/wallet', [\App\Http\Controllers\Admin\UserController::class, 'processWalletCredit'])->name('credit.wallet');
        Route::post('user/update/currency', [\App\Http\Controllers\Admin\UserController::class, 'updateCurrency'])->name('update.current');
        Route::post('user/change/status', [\App\Http\Controllers\Admin\UserController::class, 'changeStatus'])->name('change.status');
        Route::post('process/upgrade', [UserController::class, 'upgradeProcess'])->name('upgrade.user');
        Route::get('add/bonus/{userId}/{levelid}', [UserController::class, 'creditBonus']);

        Route::get('send/access/code', [\App\Http\Controllers\Admin\AccessCodeController::class, 'sendAccessCode'])->name('access.code.send');
        Route::post('send/access/code', [\App\Http\Controllers\Admin\AccessCodeController::class, 'processValidateCode'])->name('immaculate');
        Route::get('list/accesscode', [\App\Http\Controllers\Admin\AccessCodeController::class, 'listAccessCode'])->name('list.accesscode');
        Route::get('generate/virtual/account/{partner_id}', [\App\Http\Controllers\Admin\PartnerController::class, 'generateVirtualAccount']);
        // Route::get('partner/list', [\App\Http\Controllers\Admin\PartnerController::class, 'list']);
        // Route::get('partner/payments', [\App\Http\Controllers\Admin\PartnerController::class, 'partnerPayments']);
        // Route::get('partner/{id}/activate', [\App\Http\Controllers\Admin\PartnerController::class, 'viewPartnerActivate']);
        // Route::get('partner/{id}', [\App\Http\Controllers\Admin\PartnerController::class, 'partnerInfo']);
        // Route::get('partner/validate/activate/transaction/{id}', [\App\Http\Controllers\Admin\PartnerController::class, 'validateAgentTransaction']);
        Route::get('withdrawal/list', [\App\Http\Controllers\Admin\WithdrawalController::class, 'withdrawalList']);
        Route::get('withdrawal/list/{id}', [\App\Http\Controllers\Admin\WithdrawalController::class, 'withdrawalListUpdate']);
        Route::get('level/management', [\App\Http\Controllers\Admin\LevelManagementController::class, 'index']);
        Route::get('generate/plan/{id}', [LevelManagementController::class, 'generatePaystackPlanId']);
        Route::get('payouts', [MonthlyPayoutController::class, 'payouts']);
        Route::get('current/payouts', [MonthlyPayoutController::class, 'index']);
        Route::get('/payouts/monthly/levels/{level}', [MonthlyPayoutController::class, 'levelUserBreakdown']);
        Route::get('process/payouts/monthly/levels/{level}', [MonthlyPayoutController::class, 'processLevelPrayout']);
        Route::get('user/engagement/analytics/{id}', [UserEngagementController::class, 'engagementAnalytics']);
        Route::get('user/transaction/list/{id}', [UserController::class, 'transactionList']);
        Route::get('user/post/list/{id}', [UserController::class, 'postList']);
        Route::get('user/engagement/payouts', [EngagementPayoutController::class, 'index']);
        Route::get('user/bank/information', [UserController::class, 'bankInformation']);
        Route::get('monthly/payout/{level}', [PayoutController::class, 'index']);
        Route::get('user/queue/payout/{id}', [PayoutController::class, 'queuePayout']);
        Route::get('view/payout/info/{id}', [PayoutController::class, 'viewPayoutInformation']);
        Route::post('fund/transfer', [PayoutController::class, 'fundTransfer'])->name('fund.transfer');
        Route::get('update/payout/fund/{id}', [PayoutController::class, 'updatePayoutStatus']);

        Route::get('create/blog/post', [AdminBlogController::class, 'create'])->name('create');
        Route::get('view/blog/list', [AdminBlogController::class, 'list'])->name('list');
        Route::post('create/blog/post', [AdminBlogController::class, 'store'])->name('store.blog');
        Route::get('delete/blog/{slug}', [AdminBlogController::class, 'deletePost']);
    });
});
