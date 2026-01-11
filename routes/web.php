<?php

use App\Http\Controllers\Admin\LevelManagementController;
use App\Http\Controllers\Admin\MonthlyPayoutController;
use App\Http\Controllers\Admin\UserController;
use App\Livewire\CreateProduct;
use App\Livewire\Level;
use App\Livewire\User\Analytics;
use App\Livewire\User\BankInformation;
use App\Livewire\User\HowItWorks;
use App\Livewire\User\HowToEarn;
use App\Livewire\User\Partners;
use App\Livewire\User\PostAnalytics;
use App\Livewire\User\Posts;
use App\Livewire\User\Profile;
use App\Livewire\User\ProfileConnections;
use App\Livewire\User\PromotionalContent;
use App\Livewire\User\ReferralList;
use App\Livewire\User\Settings;
use App\Livewire\User\ShowNewPosts;
use App\Livewire\User\ShowPost;
use App\Livewire\User\Timeline;
use App\Livewire\User\TransactionList;
use App\Livewire\User\UpgradeAccount;
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

    Route::get('fix', [\App\Http\Controllers\GeneralController::class, 'devy']);
    Route::get('privacy/policy', [\App\Http\Controllers\GeneralController::class, 'privacyPolicy']);

    Route::get('admin', [\App\Http\Controllers\GeneralController::class, 'admin']);
    Route::get('reg', [\App\Http\Controllers\Auth\RegisterController::class, 'reg']);

    Route::post('process/reg', [\App\Http\Controllers\Auth\RegisterController::class, 'regUser'])->name('reg.user');
    Route::post('user/login', [\App\Http\Controllers\Auth\RegisterController::class, 'loginUser'])->name('login.user');


    Route::get('access/code/{level}', [\App\Http\Controllers\GeneralController::class, 'accessCode']);
    Route::post('process/access/code', [\App\Http\Controllers\GeneralController::class, 'processAccessCode']);

    Route::get('how-it-works', [\App\Http\Controllers\GeneralController::class, 'how']);

    Route::get('success', [\App\Http\Controllers\GeneralController::class, 'success']);
    Route::get('error', [\App\Http\Controllers\GeneralController::class, 'error']);

    // Route::get('post/{id}', [\App\Http\Controllers\GeneralController::class, 'showPost']);
    // Route::get('/post/{id}/comments', [\App\Http\Controllers\GeneralController::class, 'loadMoreComments'])->name('post.comments.load_more');

    // Route::get('validate/makintosh', [\App\Http\Controllers\GeneralController::class, 'validateCode']);

    // Route::post('partner', [\App\Http\Controllers\GeneralController::class, 'partner'])->name('partner');
    // Route::get('partners/listed/lots', [\App\Http\Controllers\GeneralController::class, 'viewPartner']);
    // Route::get('activate/{id}', [\App\Http\Controllers\GeneralController::class, 'viewPartnerActivate']);

    // Route::get('view/agent/transaction', [\App\Http\Controllers\TransactionController::class, 'viewAgentTransaction']);
    // Route::get('agent/validate/activate/transaction/{id}', [\App\Http\Controllers\TransactionController::class, 'validateAgentTransaction']);

    // Route::post('validate/slot', [\App\Http\Controllers\TransactionController::class, 'validateSlot'])->name('validate.slot');

    Route::post('wallet/topup', [\App\Http\Controllers\WebhookController::class, 'handle']);

    // Route::get('user/list', [\App\Http\Controllers\GeneralController::class, 'userList']);

    // Route::get('ac/cd', [\App\Http\Controllers\GeneralController::class, 'access']);

    Route::get('get/ip', [\App\Http\Controllers\GeneralController::class, 'ipConfig']);

    Route::get('seniore/login', [\App\Http\Controllers\GeneralController::class, 'dinkyLogin']);

    Route::get('registration/{code}', [\App\Http\Controllers\AdminLoginController::class, 'login']);
    Route::post('registration', [\App\Http\Controllers\AdminLoginController::class, 'proAdminLogin'])->name('dinky.reg');


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



Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('user/home', [\App\Http\Controllers\HomeController::class, 'userHome'])->name('user.home');

    Route::group(['middleware' => 'auth', 'role:user'], function () {

        Route::post('complete/onboarding', [\App\Http\Controllers\HomeController::class, 'completeOnboarding'])->name('complete.onboarding');
        Route::post('access/code/verification', [\App\Http\Controllers\HomeController::class, 'accessCodeVerification'])->name('access.code.verification');

        Route::get('validate/api', [\App\Http\Controllers\HomeController::class, 'validateApi']);
        Route::get('upgrade/api', [\App\Http\Controllers\HomeController::class, 'upgradeApi'])->name('upgrade.api');
        Route::get('subscribe/{levelId}', [\App\Http\Controllers\HomeController::class, 'createSubscription'])->name('subscribe');

        // Route::get('timeline', Timeline::class);

        Route::get('timeline', Posts::class);

        Route::get('profile/{username}', ViewProfile::class);
        // Route::get('show/{query}', ShowPost::class)->name('show');
        Route::get('show/{query}', ShowNewPosts::class);
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
    });


    Route::group(['middleware' => 'auth', 'role:admin'], function () {
        Route::get('admin/home', [\App\Http\Controllers\Admin\AdminController::class, 'home'])->name('admin.home');

        Route::get('user/list/{level}', [\App\Http\Controllers\Admin\UserController::class, 'userList'])->name('user.list');
        Route::get('user/search', [\App\Http\Controllers\Admin\UserController::class, 'userSearch']);
        Route::get('user/info/{id}', [\App\Http\Controllers\Admin\UserController::class, 'userInfo']);
        Route::post('user/credit/wallet', [\App\Http\Controllers\Admin\UserController::class, 'processWalletCredit'])->name('credit.wallet');
        Route::post('user/update/currency', [\App\Http\Controllers\Admin\UserController::class, 'updateCurrency'])->name('update.current');
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
        Route::get('/payouts/monthly/levels/{level}', [MonthlyPayoutController::class, 'levelUserBreakdown']);
    });
});
