<?php

use App\Livewire\CreateProduct;
use App\Livewire\User\Analytics;
use App\Livewire\User\Partners;
use App\Livewire\User\PostAnalytics;
use App\Livewire\User\Profile;
use App\Livewire\User\Settings;
use App\Livewire\User\ShowPost;
use App\Livewire\User\Timeline;
use App\Livewire\User\ViewProfile;
use App\Livewire\User\Wallets;
use App\Livewire\ViewPost;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
    Route::get('reg', [\App\Http\Controllers\Auth\RegisterController::class, 'reg']);

    Route::post('process/reg', [\App\Http\Controllers\Auth\RegisterController::class, 'regUser'])->name('reg.user');
    Route::get('access/code/{level}', [\App\Http\Controllers\GeneralController::class, 'accessCode']);
    Route::post('process/access/code', [\App\Http\Controllers\GeneralController::class, 'processAccessCode']);
   
    Route::get('success', [\App\Http\Controllers\GeneralController::class, 'success']);
    Route::get('error', [\App\Http\Controllers\GeneralController::class, 'error']);

    Route::get('post/{id}', [\App\Http\Controllers\GeneralController::class, 'showPost']);
    Route::get('/post/{id}/comments', [\App\Http\Controllers\GeneralController::class, 'loadMoreComments'])->name('post.comments.load_more');

    Route::get('validate/makintosh', [\App\Http\Controllers\GeneralController::class, 'validateCode']);
    Route::post('villa', [\App\Http\Controllers\GeneralController::class, 'processValidateCode'])->name('immaculate');

    Route::post('partner', [\App\Http\Controllers\GeneralController::class, 'partner'])->name('partner');
    Route::get('partners/listed/lots', [\App\Http\Controllers\GeneralController::class, 'viewPartner']);
    Route::get('activate/{id}', [\App\Http\Controllers\GeneralController::class, 'viewPartnerActivate']);
    
    Route::get('view/agent/transaction', [\App\Http\Controllers\TransactionController::class, 'viewAgentTransaction']);
    Route::get('agent/validate/activate/transaction/{id}', [\App\Http\Controllers\TransactionController::class, 'validateAgentTransaction']);

    Route::post('validate/slot', [\App\Http\Controllers\TransactionController::class, 'validateSlot'])->name('validate.slot');

    Route::post('account/charge', [\App\Http\Controllers\WebhookController::class, 'handle']);

    Route::get('user/list', [\App\Http\Controllers\GeneralController::class, 'userList']);
});

Auth::routes();
Route::middleware(['auth'])->group(function () {

    Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('user/home', [\App\Http\Controllers\HomeController::class, 'userHome'])->name('user.home');
    Route::get('admin/home', [\App\Http\Controllers\HomeController::class, 'adminHome'])->name('admin.home');
    Route::get('complete/onboarding', [\App\Http\Controllers\HomeController::class, 'completeOnboarding'])->name('complete.onboarding');
    Route::get('validate/api', [\App\Http\Controllers\HomeController::class, 'validateApi']);
    Route::get('post/{username}/{id}', ViewPost::class);

    Route::get('timeline', Timeline::class);
   
    Route::get('profile/{id}', ViewProfile::class);
    Route::get('show/{query}', ShowPost::class)->name('show');
    Route::get('post/timeline/{id}/analytics', PostAnalytics::class);
    Route::get('analytics', Analytics::class);
    Route::get('settings', Settings::class);
    Route::get('wallets', Wallets::class);
    Route::get('partner', Partners::class);
});
