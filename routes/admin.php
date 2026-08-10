<?php

use App\Http\Controllers\Admin\AccessCodeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\LevelManagementController;
use App\Http\Controllers\Admin\MonthlyPayoutController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\PayoutController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserEngagementController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\AdminLoginController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('home', [AdminController::class, 'home'])->name('home');
        Route::post('logout', [AdminLoginController::class, 'logout'])->name('logout');

        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('search', [UserController::class, 'userSearch'])->name('search');
            Route::get('{user}/transactions', [UserController::class, 'transactionList'])->name('transactions');
            Route::get('{user}/posts', [UserController::class, 'postList'])->name('posts');
            Route::get('{user}/engagement', [UserEngagementController::class, 'engagementAnalytics'])->name('engagement');
            Route::post('{user}/bonus/{level}', [UserController::class, 'creditBonus'])->name('bonus');
            Route::get('{user}', [UserController::class, 'userInfo'])->name('show');
            Route::get('/', [UserController::class, 'userList'])->name('index');
            Route::post('upgrade', [UserController::class, 'upgradeProcess'])->name('upgrade');
            Route::post('currency', [UserController::class, 'updateCurrency'])->name('currency.update');
            Route::post('status', [UserController::class, 'changeStatus'])->name('status.update');
            Route::post('wallet/credit', [UserController::class, 'processWalletCredit'])->name('wallet.credit');
        });

        Route::get('bank-accounts', [UserController::class, 'bankInformation'])->name('bank-accounts.index');

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

        Route::post('partners/{partner}/virtual-account', [PartnerController::class, 'generateVirtualAccount'])->name('partners.virtual-account');

        Route::prefix('blog')->name('blog.')->group(function () {
            Route::get('/', [AdminBlogController::class, 'list'])->name('index');
            Route::get('create', [AdminBlogController::class, 'create'])->name('create');
            Route::post('/', [AdminBlogController::class, 'store'])->name('store');
            Route::delete('{slug}', [AdminBlogController::class, 'deletePost'])->name('delete');
        });

        Route::prefix('currencies')->name('currencies.')->group(function () {
            Route::get('/', [CurrencyController::class, 'index'])->name('index');
            Route::post('{currency}/status', [CurrencyController::class, 'changeStatus'])->name('status');
            Route::put('{currency}', [CurrencyController::class, 'update'])->name('update');
        });

        Route::get('subscribe/test/{levelId}', [AdminController::class, 'testSubscription'])->name('test.subscribe');
        Route::get('verify/flutterwave/charge', [AdminController::class, 'verifyFlutterwaveAdminCharge'])->name('verify.flutterwave.charge');
    });
