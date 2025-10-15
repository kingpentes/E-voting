<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.register-choice');
});

// Registration Routes
Route::get('/register', function () {
    return view('auth.register-choice');
})->name('register');

Route::get('/register/organizer', function () {
    return view('auth.register-organizer');
})->name('register.organizer');

Route::get('/register/voter', function () {
    return view('auth.register-voter');
})->name('register.voter');

// Registration Form Handlers (you'll need to create these controllers later)
Route::post('/register/organizer', function () {
    // Handle organizer registration
    return redirect()->route('dashboard');
})->name('register.organizer.store');

Route::post('/register/voter', function () {
    // Handle voter registration with ID and face verification
    return redirect()->route('dashboard');
})->name('register.voter.store');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
