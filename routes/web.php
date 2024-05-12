<?php

use App\Livewire\CreateProduct;
use App\Livewire\User\Timeline;
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
    Route::post('process/reg', [\App\Http\Controllers\Auth\RegisterController::class, 'regUser'])->name('reg.user');
    Route::get('access/code', [\App\Http\Controllers\GeneralController::class, 'accessCode']);
    Route::post('process/access/code', [\App\Http\Controllers\GeneralController::class, 'processAccessCode']);
    Route::get('validate/api', [\App\Http\Controllers\GeneralController::class, 'validateApi']);
    Route::get('success', [\App\Http\Controllers\GeneralController::class, 'success']);
    Route::get('error', [\App\Http\Controllers\GeneralController::class, 'error']);
});
Auth::routes();
Route::middleware(['auth'])->group(function () {

    
    Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('user/home', [\App\Http\Controllers\HomeController::class, 'userHome'])->name('user.home');
    Route::get('admin/home', [\App\Http\Controllers\HomeController::class, 'adminHome'])->name('admin.home');

    Route::get('timeline', Timeline::class);
});



// Route::get('/', function () {
//     return view('welcome');
// });



// Auth::routes();
// // Route::get('create/product', [])
// Route::get('timeline', Timeline::class);
// // Route::post('product', Timeline::class);
// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
