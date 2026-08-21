<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\EngagementPayoutController;
use App\Http\Controllers\Admin\LevelManagementController;
use App\Http\Controllers\Admin\MonthlyPayoutController;
use App\Http\Controllers\Admin\PayoutController;
use App\Http\Controllers\Admin\TrendController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserEngagementController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\AcademyController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\HelpCenterController;
use App\Http\Controllers\CloudinaryWebhookController;
use App\Http\Controllers\FlutterwaveWebhookController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KorapayWebhookController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RollsController;
use App\Http\Controllers\RollsWatchController;
use App\Http\Controllers\VideoAnalyticsController;
use App\Http\Controllers\WebhookController;
use App\Livewire\CreateProduct;
use App\Livewire\Level;
use App\Livewire\User\Analytics;
use App\Livewire\User\BankInformation;
use App\Livewire\User\Blog;
use App\Livewire\User\BookmarkedPosts;
use App\Livewire\User\Community;
use App\Livewire\User\CommunityDetails;
use App\Livewire\User\DashboardTimeline;
use App\Livewire\User\EarningList;
use App\Livewire\User\FeedbackForm;
use App\Livewire\User\FeedbackThread;
use App\Livewire\User\Hashtag;
use App\Livewire\User\HowItWorks;
use App\Livewire\User\HowToEarn;
use App\Livewire\User\Messages;
use App\Livewire\User\NewTimeline;
use App\Livewire\User\Payout;
use App\Livewire\User\PostAnalytics;
use App\Livewire\User\Posts;
use App\Livewire\User\Profile;
use App\Livewire\User\ProfileConnections;
use App\Livewire\User\PromotionalContent;
use App\Livewire\User\ReferralList;
use App\Livewire\User\Rolls;
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

    // Route::get('/', function () {
    //     return view('welcome');
    // });

    Route::get('/', [GeneralController::class, 'landingpage']);

    Route::get('test', [\App\Http\Controllers\GeneralController::class, 'test']);
    Route::get('fix', [\App\Http\Controllers\GeneralController::class, 'devy']);
    Route::get('privacy/policy', [\App\Http\Controllers\GeneralController::class, 'privacyPolicy']);
    Route::get('terms/conditions', [\App\Http\Controllers\GeneralController::class, 'terms']);
    Route::get('how-it-works', [\App\Http\Controllers\GeneralController::class, 'how']);
    Route::get('features', [\App\Http\Controllers\GeneralController::class, 'features'])->name('features');
    Route::get('creators', [\App\Http\Controllers\GeneralController::class, 'creators'])->name('creators');
    Route::get('communities', [\App\Http\Controllers\GeneralController::class, 'communities'])->name('communities');
    Route::get('earn', [\App\Http\Controllers\GeneralController::class, 'earn'])->name('earn');
    Route::get('ai', [\App\Http\Controllers\GeneralController::class, 'ai'])->name('ai');
    Route::get('faq', fn () => redirect()->route('help', [], 301))->name('faq');
    Route::get('help', [HelpCenterController::class, 'index'])->name('help');
    Route::get('help/{slug}', [HelpCenterController::class, 'show'])->name('help.show');

    Route::get('about', [\App\Http\Controllers\GeneralController::class, 'about']);
    Route::get('contact', [\App\Http\Controllers\GeneralController::class, 'contact']);
    Route::post('contact', [\App\Http\Controllers\GeneralController::class, 'submitContact'])
        ->middleware('throttle:6,1')
        ->name('contact.submit');
    // Route::get('blog', [\App\Http\Controllers\GeneralController::class, 'blog']);
    // Route::get('blog/{slug}', [\App\Http\Controllers\GeneralController::class, 'showBlogPost']);
    Route::get('top-earners', [\App\Http\Controllers\GeneralController::class, 'topEarners']);

    Route::get('reg', [\App\Http\Controllers\Auth\RegisterController::class, 'reg']);

    Route::post('process/reg', [\App\Http\Controllers\Auth\RegisterController::class, 'regUser'])->name('reg.user');

    Route::post('user/login', [\App\Http\Controllers\Auth\RegisterController::class, 'loginUser'])
        ->middleware('throttle:login')->name('login.user');

    // Fresh CSRF token for long-lived auth forms (login/register).
    Route::get('csrf-token', function () {
        return response()->json(['token' => csrf_token()]);
    })->middleware('throttle:120,1')->name('csrf.token');


    Route::get('access/code/{level}', [\App\Http\Controllers\GeneralController::class, 'accessCode']);
    Route::post('process/access/code', [\App\Http\Controllers\GeneralController::class, 'processAccessCode']);


    Route::get('/c/{community:slug}', [\App\Http\Controllers\GeneralController::class, 'communityPublic'])->name('community.public');
    Route::get('/c/{community:slug}/join', [\App\Http\Controllers\GeneralController::class, 'communityAuthIntent'])->name('community.auth');

    // Public standalone roll — watch one video without logging in
    Route::get('/v/{video}', [RollsController::class, 'publicShow'])->name('rolls.public');

    Route::get('success', [\App\Http\Controllers\GeneralController::class, 'success']);
    Route::get('error', [\App\Http\Controllers\GeneralController::class, 'error']);

    Route::post('wallet/topup', [\App\Http\Controllers\WebhookController::class, 'handle']);

    Route::get('get/ip', [\App\Http\Controllers\GeneralController::class, 'ipConfig']);

    Route::middleware(['admin.gate', 'throttle:6,1'])->group(function () {
        Route::get('seniore/login', [AdminLoginController::class, 'createGate'])->name('admin.gate.create');
        Route::get('registration/{code}', [AdminLoginController::class, 'showLoginForm'])->name('admin.gate.show');
    });

    Route::post('registration', [AdminLoginController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('dinky.reg');

    Route::get('blog', [BlogController::class, 'index'])->name('blog');
    Route::get('blog/{slug}', [BlogController::class, 'show']);

    Route::get('academy', [AcademyController::class, 'index'])->name('academy');
    Route::get('academy/{slug}', [AcademyController::class, 'show'])->name('academy.show');

    Route::get('top/earners',  [\App\Http\Controllers\GeneralController::class, 'topEarners']);

    Route::post('/webhooks/cloudinary/video-processing', [CloudinaryWebhookController::class, 'handleVideoProcessing'])->name('cloudinary.webhook');
    Route::post('flutterwave/webhook', [WebhookController::class, 'flutterwave'])->name('flutterwave.webhook');
    Route::post('korapay/webhook', [WebhookController::class, 'korapay'])->name('korapay.webhook');


    // routes/api.php (no CSRF by default)
    // Route::post('/webhooks/korapay', [KorapayWebhookController::class])->name('webhooks.korapay');
    // Route::post('/webhooks/flutterwave', [FlutterwaveWebhookController::class])->name('webhooks.flutterwave');

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



Route::middleware([
    'auth',
    'verified',
    'track.online'
])->group(function () {

    Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('user/home', [\App\Http\Controllers\HomeController::class, 'userHome'])->name('user.home');

    Route::group(['middleware' => 'auth', 'role:user'], function () {

        Route::post('complete/onboarding', [\App\Http\Controllers\HomeController::class, 'completeOnboarding'])->name('complete.onboarding');
        Route::post('access/code/verification', [\App\Http\Controllers\HomeController::class, 'accessCodeVerification'])->name('access.code.verification');

        Route::get('validate/api', [\App\Http\Controllers\HomeController::class, 'validateApi']);
        Route::get('verify/subscription/payment/', [\App\Http\Controllers\PaymentController::class, 'verifyKoraSubscriptionPayment'])->name('verify.subscription');
        Route::get('verify/fluterwave/charge', [PaymentController::class, 'verifyFlutterwaveCharge'])->name('verify.flutterwave.charge');
        Route::get('subscribe/{levelId}', [\App\Http\Controllers\PaymentController::class, 'createSubscription'])->name('subscribe');
        Route::get('payg-subscribe/{levelId}', [\App\Http\Controllers\PaymentController::class, 'createPaygSubscription'])->name('payg-subscribe');
        Route::get('verify/community/subscription/payment/', [\App\Http\Controllers\PaymentController::class, 'verifyKoraCommunitySubscriptionPayment'])->name('verify.korapay.community.subscription');

        Route::get('community/payment/{communityId}', [PaymentController::class, 'paidCommunityPayment'])->name('community.payment');
        Route::get('community/invite/{token}', [\App\Http\Controllers\CommunityInviteController::class, 'accept'])->name('community.invite.accept');

        //video player analytics route
        Route::post('api/videos/{video}/watch-time', [VideoAnalyticsController::class, 'trackWatchTime']);
        Route::post('api/videos/{video}/record-play', [VideoAnalyticsController::class, 'recordPlay']);
        Route::post('api/rolls/watch', [RollsWatchController::class, 'store'])->name('api.rolls.watch');


        Route::get('timeline', Timeline::class);
        Route::get('timeline/{post}', TimelineDetails::class)->name('timeline.show');
        Route::get('bookmarks', BookmarkedPosts::class)->name('bookmarks');
        Route::get('messages', Messages::class)->name('messages');
        Route::get('new-timeline', NewTimeline::class);
        Route::get('dashboard-timeline', DashboardTimeline::class);

        Route::get('profile/{username}', ViewProfile::class);
        Route::get('post/timeline/{id}/analytics', PostAnalytics::class);
        Route::get('analytics', Analytics::class);
        Route::get('settings', Settings::class);
        Route::get('feedback', FeedbackForm::class)->name('feedback');
        Route::get('feedback/{feedback}', FeedbackThread::class)->name('feedback.show');
        Route::get('wallets', Wallets::class);
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
        Route::get('hashtag/{tag}', Hashtag::class);


        //VIDEO PLAYER ROUTE
        Route::get('rolls', [RollsController::class, 'random'])->name('rolls.random');
        Route::get('rolls/{video}', Rolls::class)->name('rolls.show');
        // Route::get('earners/list', EarningList::class);
        Route::get('earner/list', EarningList::class);
        Route::get('user/blog', Blog::class);
        Route::get('user/payouts', Payout::class);

        //community
        Route::get('community', Community::class)->name('community');
        Route::get('community/{community}', CommunityDetails::class)->name('community.show');
    });

    // Route::get('rolls/{video}', VideoRolls::class);



});


