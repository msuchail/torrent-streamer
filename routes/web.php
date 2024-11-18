<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\LogoutController;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Passwords\Confirm;
use App\Livewire\Auth\Passwords\Email;
use App\Livewire\Auth\Passwords\Reset;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\Verify;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('guest')->group(function () {
    Route::get('login', Login::class)
        ->name('login');

    Route::get('register', Register::class)
        ->name('register');
});

Route::get('password/reset', Email::class)
    ->name('password.request');

Route::get('password/reset/{token}', Reset::class)
    ->name('password.reset');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/', \App\Livewire\Home::class)->name('home');


    Route::name('movie.')->prefix('movies')->group(function () {
        Route::get('/', \App\Livewire\Movie\Index::class)->name('index');
        Route::get('/{movie}', \App\Livewire\Movie\Show::class)->name('show');
    });

    Route::name('serie.')->prefix('series')->group(function () {
        Route::get('/', \App\Livewire\Serie\Index::class)->name('index');
        Route::get('/{serie}', \App\Livewire\Serie\Show::class)->name('show');
    });



    Route::prefix("video/{video}")->controller(\App\Http\Controllers\VideoController::class)->name('video.')->group(function () {
        Route::get('master.m3u8', 'master')->name('master');
        Route::get('video/{segment}', 'video')->name('video');
        Route::get('audio/{piste}/{segment}', 'audio')->name('audio');
        Route::get('srt/{piste}', 'subtitle')->name('subtitle');
    });

    Route::get('test', \App\Http\Controllers\TestController::class)->middleware(['admin']);
    Route::get('inactive', \App\Livewire\Inactive::class)->name('inactive');

    Route::get('email/verify', Verify::class)
        ->middleware('throttle:6,1')
        ->name('verification.notice');

    Route::get('password/confirm', Confirm::class)
        ->name('password.confirm');
});

Route::middleware('auth')->group(function () {
    Route::get('email/verify/{id}/{hash}', EmailVerificationController::class)
        ->middleware('signed')
        ->name('verification.verify');

    Route::get('logout', LogoutController::class)
        ->name('logout');
});

