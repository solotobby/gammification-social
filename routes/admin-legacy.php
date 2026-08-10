<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Legacy admin URL redirects (Phase 1 paths → Phase 2 /admin/* routes)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('user/list/{level?}', function (?string $level = 'all') {
        return redirect()->route('admin.users.index', $level !== 'all' ? ['level' => $level] : []);
    });
    Route::get('user/search', fn () => redirect()->route('admin.users.search', request()->query()));
    Route::redirect('user/info/{user}', '/admin/users/{user}', 301);
    Route::redirect('user/transaction/list/{user}', '/admin/users/{user}/transactions', 301);
    Route::redirect('user/post/list/{user}', '/admin/users/{user}/posts', 301);
    Route::redirect('user/engagement/analytics/{user}', '/admin/users/{user}/engagement', 301);
    Route::redirect('user/bank/information', '/admin/bank-accounts', 301);
    Route::redirect('withdrawal/list', '/admin/withdrawals', 301);
    Route::redirect('level/management', '/admin/levels', 301);
    Route::redirect('payouts', '/admin/payouts/pro-rata', 301);
    Route::redirect('current/payouts', '/admin/payouts/current', 301);
    Route::redirect('payouts/monthly/levels/{level}', '/admin/payouts/monthly/{level}/users', 301);
    Route::redirect('monthly/payout/{level}', '/admin/payouts/levels/{level}', 301);
    Route::redirect('view/payout/info/{id}', '/admin/payouts/details/{id}', 301);
    Route::redirect('user/engagement/payouts', '/admin/payouts/engagement', 301);
    Route::redirect('list/accesscode', '/admin/access-codes', 301);
    Route::redirect('send/access/code', '/admin/access-codes/send', 301);
    Route::redirect('view/blog/list', '/admin/blog', 301);
    Route::redirect('create/blog/post', '/admin/blog/create', 301);
    Route::redirect('currency/list', '/admin/currencies', 301);
    Route::redirect('trend/management', '/admin/home', 301);
    Route::post('send/access/code', [\App\Http\Controllers\Admin\AccessCodeController::class, 'processValidateCode'])->name('immaculate');
    Route::post('user/credit/wallet', [\App\Http\Controllers\Admin\UserController::class, 'processWalletCredit'])->name('credit.wallet');
    Route::post('user/update/currency', [\App\Http\Controllers\Admin\UserController::class, 'updateCurrency'])->name('update.current');
    Route::post('user/change/status', [\App\Http\Controllers\Admin\UserController::class, 'changeStatus'])->name('change.status');
    Route::post('process/upgrade', [\App\Http\Controllers\Admin\UserController::class, 'upgradeProcess'])->name('upgrade.user');
    Route::post('add/bonus/{userId}/{levelid}', [\App\Http\Controllers\Admin\UserController::class, 'creditBonus'])->name('admin.users.bonus.legacy');
    Route::post('fund/transfer', [\App\Http\Controllers\Admin\PayoutController::class, 'fundTransfer'])->name('fund.transfer');
    Route::post('user/queue/payout/{id}', [\App\Http\Controllers\Admin\PayoutController::class, 'queuePayout']);
    Route::post('update/payout/fund/{id}', [\App\Http\Controllers\Admin\PayoutController::class, 'updatePayoutStatus']);
    Route::post('withdrawal/list/{id}', [\App\Http\Controllers\Admin\WithdrawalController::class, 'withdrawalListUpdate']);
    Route::post('generate/plan/{id}', [\App\Http\Controllers\Admin\LevelManagementController::class, 'generatePaystackPlanId']);
    Route::post('process/payouts/monthly/levels/{level}', [\App\Http\Controllers\Admin\MonthlyPayoutController::class, 'processLevelPrayout']);
    Route::post('currency/status/{id}', [\App\Http\Controllers\Admin\CurrencyController::class, 'changeStatus']);
    Route::post('trend/toggle/status/{id}', [\App\Http\Controllers\Admin\TrendController::class, 'toggleStatus']);
    Route::delete('delete/blog/{slug}', [\App\Http\Controllers\Admin\BlogController::class, 'deletePost']);
    Route::put('currency/update/{id}', [\App\Http\Controllers\Admin\CurrencyController::class, 'update']);
    Route::post('create/blog/post', [\App\Http\Controllers\Admin\BlogController::class, 'store']);
    Route::post('trend/store', [\App\Http\Controllers\Admin\TrendController::class, 'store']);
    Route::redirect('admin/verify/flutterwave/charge/admin', '/admin/verify/flutterwave/charge', 301);
});
