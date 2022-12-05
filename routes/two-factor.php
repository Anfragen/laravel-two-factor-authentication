<?php

use Anfragen\TwoFactor\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Two Factor Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->controller(TwoFactorController::class)->group(function () {
    Route::middleware(['two-factor.confirmed', 'throttle:6,1'])->group(function () {
        Route::post('/two-factor/resend', 'resend')->name('two-factor.resend');

        Route::post('/two-factor/challenge', 'login')->name('two-factor.login');
    });

    Route::middleware(['two-factor.required', 'password.confirm'])->group(function () {
        Route::post('/two-factor/authentication', 'enable')->name('two-factor.enable');

        Route::post('/two-factor/confirmed-authentication', 'confirm')->name('two-factor.confirm');

        Route::delete('/two-factor/authentication', 'disable')->name('two-factor.disable');

        Route::get('/two-factor/qr-code', 'qrCode')->name('two-factor.qr-code');

        Route::get('/two-factor/secret-key', 'secretKey')->name('two-factor.secret-key');

        Route::get('/two-factor/recovery-codes', 'recoveryCodes')->name('two-factor.recovery-codes');

        Route::post('/two-factor/recovery-codes', 'newRecoveryCodes')->name('two-factor.new-recovery-codes');;
    });
});
