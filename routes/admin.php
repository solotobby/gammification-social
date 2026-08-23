<?php

use App\Http\Controllers\Admin\AccessCodeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AcademyController as AdminAcademyController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\HelpCenterController as AdminHelpCenterController;
use App\Http\Controllers\Admin\BookmarkAnalyticsController;
use App\Http\Controllers\Admin\CommunityController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\Admin\LevelManagementController;
use App\Http\Controllers\Admin\MonthlyPayoutController;
use App\Http\Controllers\Admin\OutreachController;
use App\Http\Controllers\Admin\PayoutController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\PostReportController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserEngagementController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\AdminLoginController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Shared: admin + staff
        Route::get('home', [AdminController::class, 'home'])->name('home');
        Route::post('logout', [AdminLoginController::class, 'logout'])->name('logout');

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('search', [UserController::class, 'userSearch'])->name('search');
            Route::get('{user}/transactions', [UserController::class, 'transactionList'])->name('transactions');
            Route::get('{user}/posts', [UserController::class, 'postList'])->name('posts');
            Route::get('{user}/engagement', [UserEngagementController::class, 'engagementAnalytics'])->name('engagement');
            Route::get('{user}', [UserController::class, 'userInfo'])->name('show');
            Route::get('/', [UserController::class, 'userList'])->name('index');
            Route::post('currency', [UserController::class, 'updateCurrency'])->name('currency.update');
            Route::post('status', [UserController::class, 'changeStatus'])->name('status.update');
        });

        Route::prefix('communities')->name('communities.')->group(function () {
            Route::get('/', [CommunityController::class, 'index'])->name('index');
            Route::get('{community}', [CommunityController::class, 'show'])->name('show');
            Route::post('{community}/currency', [CommunityController::class, 'updateCurrency'])->name('currency.update');
            Route::post('{community}/archive', [CommunityController::class, 'archive'])->name('archive');
            Route::post('{community}/unarchive', [CommunityController::class, 'unarchive'])->name('unarchive');
            Route::post('{community}/ban-member', [CommunityController::class, 'banMember'])->name('ban-member');
            Route::post('{community}/unban-member', [CommunityController::class, 'unbanMember'])->name('unban-member');
            Route::post('{community}/remove-member', [CommunityController::class, 'removeMember'])->name('remove-member');
            Route::delete('{community}/posts/{post}', [CommunityController::class, 'destroyPost'])->name('posts.destroy');
            Route::delete('{community}', [CommunityController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('posts')->name('posts.')->group(function () {
            Route::get('/', [PostController::class, 'index'])->name('index');
            Route::get('{post}', [PostController::class, 'show'])->name('show');
            Route::post('{post}/hide', [PostController::class, 'hide'])->name('hide');
            Route::post('{post}/unhide', [PostController::class, 'unhide'])->name('unhide');
            Route::delete('{post}', [PostController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [PostReportController::class, 'index'])->name('index');
            Route::get('posts/{post}', [PostReportController::class, 'show'])->name('show');
            Route::post('{report}/dismiss', [PostReportController::class, 'dismiss'])->name('dismiss');
            Route::post('posts/{post}/dismiss-all', [PostReportController::class, 'dismissAll'])->name('dismiss-all');
            Route::post('posts/{post}/hide', [PostReportController::class, 'hidePost'])->name('hide-post');
            Route::delete('posts/{post}', [PostReportController::class, 'destroyPost'])->name('destroy-post');
            Route::post('posts/{post}/author', [PostReportController::class, 'actionAuthor'])->name('action-author');
        });

        Route::prefix('feedback')->name('feedback.')->group(function () {
            Route::get('/', [FeedbackController::class, 'index'])->name('index');
            Route::get('{feedback}', [FeedbackController::class, 'show'])->name('show');
            Route::post('{feedback}/reply', [FeedbackController::class, 'reply'])->name('reply');
            Route::put('{feedback}', [FeedbackController::class, 'update'])->name('update');
        });

        Route::prefix('videos')->name('videos.')->group(function () {
            Route::get('/', [VideoController::class, 'index'])->name('index');
            Route::get('{video}', [VideoController::class, 'show'])->name('show');
            Route::post('{video}/mark-failed', [VideoController::class, 'markFailed'])->name('mark-failed');
            Route::post('{video}/mark-completed', [VideoController::class, 'markCompleted'])->name('mark-completed');
            Route::post('{video}/hide', [VideoController::class, 'hide'])->name('hide');
            Route::delete('{video}', [VideoController::class, 'destroy'])->name('destroy');
        });

        Route::get('bookmarks', [BookmarkAnalyticsController::class, 'index'])->name('bookmarks.index');
        Route::get('outreach', [OutreachController::class, 'index'])->name('outreach.index');

        Route::prefix('blog')->name('blog.')->group(function () {
            Route::get('/', [AdminBlogController::class, 'list'])->name('index');
            Route::get('create', [AdminBlogController::class, 'create'])->name('create');
            Route::post('/', [AdminBlogController::class, 'store'])->name('store');
            Route::get('{slug}/edit', [AdminBlogController::class, 'edit'])->name('edit');
            Route::put('{slug}', [AdminBlogController::class, 'update'])->name('update');
            Route::delete('{slug}', [AdminBlogController::class, 'deletePost'])->name('delete');
        });

        // Admin-only: finance, payouts, CMS extras, staff invites
        Route::middleware('admin.only')->group(function () {
            Route::post('users/{user}/bonus/{level}', [UserController::class, 'creditBonus'])->name('users.bonus');
            Route::post('users/upgrade', [UserController::class, 'upgradeProcess'])->name('users.upgrade');
            Route::post('users/wallet/credit', [UserController::class, 'processWalletCredit'])->name('users.wallet.credit');

            Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
            Route::get('bank-accounts', [UserController::class, 'bankInformation'])->name('bank-accounts.index');
            Route::get('finance', [FinanceController::class, 'index'])->name('finance.index');

            Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
                Route::get('/', [WithdrawalController::class, 'withdrawalList'])->name('index');
                Route::post('{withdrawal}/mark-paid', [WithdrawalController::class, 'withdrawalListUpdate'])->name('update');
            });

            Route::prefix('levels')->name('levels.')->group(function () {
                Route::get('/', [LevelManagementController::class, 'index'])->name('index');
                Route::post('{level}/paystack-plan', [LevelManagementController::class, 'generatePaystackPlanId'])->name('generate-plan');
            });

            Route::prefix('payouts')->name('payouts.')->group(function () {
                Route::get('pro-rata', [MonthlyPayoutController::class, 'payouts'])->name('pro-rata');
                Route::get('current', [MonthlyPayoutController::class, 'index'])->name('current');
                Route::get('engagement', fn () => redirect()->route('admin.payouts.levels.show', 'Basic'))->name('engagement');
                Route::get('monthly/{level}/users', [MonthlyPayoutController::class, 'levelUserBreakdown'])->name('monthly.users');
                Route::post('monthly/{level}/process', [MonthlyPayoutController::class, 'processLevelPrayout'])->name('process-level');
                Route::get('levels/{level}', [PayoutController::class, 'index'])->name('levels.show');
                Route::post('queue/{engagementStat}', [PayoutController::class, 'queuePayout'])->name('queue');
                Route::get('details/{engagementStat}', [PayoutController::class, 'viewPayoutInformation'])->name('show');
                Route::post('mark-paid/{payout}', [PayoutController::class, 'updatePayoutStatus'])->name('mark-paid');
                Route::post('fund-transfer', [PayoutController::class, 'fundTransfer'])->name('fund-transfer');
            });

            Route::prefix('access-codes')->name('access-codes.')->group(function () {
                Route::get('/', [AccessCodeController::class, 'listAccessCode'])->name('index');
                Route::get('send', [AccessCodeController::class, 'sendAccessCode'])->name('send');
                Route::post('send', [AccessCodeController::class, 'processValidateCode'])->name('store');
            });

            Route::prefix('academy')->name('academy.')->group(function () {
                Route::get('/', [AdminAcademyController::class, 'list'])->name('index');
                Route::get('create', [AdminAcademyController::class, 'create'])->name('create');
                Route::post('/', [AdminAcademyController::class, 'store'])->name('store');
                Route::delete('{slug}', [AdminAcademyController::class, 'delete'])->name('delete');
            });

            Route::prefix('help')->name('help.')->group(function () {
                Route::get('/', [AdminHelpCenterController::class, 'list'])->name('index');
                Route::get('create', [AdminHelpCenterController::class, 'create'])->name('create');
                Route::post('/', [AdminHelpCenterController::class, 'store'])->name('store');
                Route::delete('{slug}', [AdminHelpCenterController::class, 'delete'])->name('delete');
            });

            Route::prefix('currencies')->name('currencies.')->group(function () {
                Route::get('/', [CurrencyController::class, 'index'])->name('index');
                Route::post('{currency}/status', [CurrencyController::class, 'changeStatus'])->name('status');
                Route::put('{currency}', [CurrencyController::class, 'update'])->name('update');
            });

            Route::prefix('staff')->name('staff.')->group(function () {
                Route::get('/', [StaffController::class, 'index'])->name('index');
                Route::post('invite', [StaffController::class, 'invite'])->name('invite');
                Route::delete('invites/{invite}', [StaffController::class, 'revokeInvite'])->name('invites.revoke');
                Route::delete('{user}', [StaffController::class, 'remove'])->name('remove');
            });

            Route::get('subscribe/test/{levelId}', [AdminController::class, 'testSubscription'])->name('test.subscribe');
            Route::get('verify/flutterwave/charge', [AdminController::class, 'verifyFlutterwaveAdminCharge'])->name('verify.flutterwave.charge');
        });
    });
