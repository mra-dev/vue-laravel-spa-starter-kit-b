<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\VerifyPhoneController;
use Illuminate\Support\Facades\Route;


Route::group([ 'middleware' => ['guest', 'throttle:auth'] ], function () {

    Route::group([ 'prefix' => '/login', 'name' => 'login.' ], function () {
        Route::post('validate', [AuthenticatedSessionController::class, 'validateLogin'])
            ->name('validate');

        Route::post('/', [AuthenticatedSessionController::class, 'passwordLogin'])
            ->name('password');
    });

    Route::group([ 'prefix' => '/register', 'name' => 'register.' ], function () {
        Route::post('validate', [RegisteredUserController::class, 'validateLogin'])
            ->name('validate');

        Route::post('/', [RegisteredUserController::class, 'store'])
            ->name('index');
    });

    Route::group([ 'prefix' => '/verify', 'name' => 'verify.' ], function () {
        Route::post('/{type}/send', [VerifyPhoneController::class, 'send'])
            ->name('send');
        Route::post('/{type}', [VerifyPhoneController::class, 'verify'])
            ->name('verify');
    });

});

//Route::post('/register', [RegisteredUserController::class, 'store'])
//                ->middleware('guest')
//                ->name('register');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
                ->middleware('guest')
                ->name('password.email');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
                ->middleware('guest')
                ->name('password.update');

Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
                ->middleware(['auth', 'signed', 'throttle:6,1'])
                ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware(['auth', 'throttle:6,1'])
                ->name('verification.send');

Route::post('/logout', [AuthenticatedSessionController::class, 'logOut'])
                ->middleware('auth')
                ->name('logout');
