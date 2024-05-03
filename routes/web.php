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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
// Route::get('create/product', [])
Route::get('timeline', Timeline::class);
// Route::post('product', Timeline::class);
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
