<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Google OAuth
    Route::get('auth/google', [GoogleController::class, 'redirect'])
        ->name('auth.google');
    Route::get('auth/google/callback', [GoogleController::class, 'callback'])
        ->name('auth.google.callback');
    
    // Override generic register: show role choice page instead of default registration form
    Route::get('register', function () {
        return view('auth.register-choice');
    })->name('register');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Forgot Password with OTP via Gmail
    Route::get('forgot-password', [ForgotPasswordController::class, 'showForgotForm'])
        ->name('password.request');

    Route::post('forgot-password/send-otp', [ForgotPasswordController::class, 'sendOTP'])
        ->name('password.send-otp');

    Route::get('verify-otp', [ForgotPasswordController::class, 'showVerifyForm'])
        ->name('password.verify.form');

    Route::post('verify-otp', [ForgotPasswordController::class, 'verifyOTP'])
        ->name('password.verify-otp');

    Route::get('reset-password', [ForgotPasswordController::class, 'showResetForm'])
        ->name('password.reset.form');

    Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword'])
        ->name('password.update');

    Route::post('resend-otp', [ForgotPasswordController::class, 'resendOTP'])
        ->name('password.resend-otp');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
