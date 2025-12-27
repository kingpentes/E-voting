<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\OrganizerRegisterController;
use App\Http\Controllers\Auth\VoterRegisterController;
use App\Http\Controllers\Admin\ElectionController;
use App\Http\Controllers\Admin\CandidateController;

use App\Http\Controllers\VoterElectionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    // Jika sudah login, arahkan sesuai peran agar tidak kembali ke halaman register
    if (Auth::check()) {
        $user = Auth::user();
        if (in_array($user->role, ['organizer', 'admin'])) {
            return redirect()->route('admin.dashboard');
        }
        
        // Voter: cek apakah sudah verifikasi
        if ($user->isVoter()) {
            // Jika belum verifikasi, arahkan ke verification
            if ($user->needsVerification()) {
                return redirect()->route('voter.verification');
            }
            
            // Jika sudah verified dan punya elections, arahkan ke election-selection
            $hasElections = method_exists($user, 'participatingElections')
                ? $user->participatingElections()->where('is_published', true)->exists()
                : false;

            if ($hasElections) {
                return redirect()->route('voter.verification'); // Will show election-selection
            }
            
            // Verified tapi belum ada election, tetap ke verification page
            return redirect()->route('voter.verification');
        }
    }

    // Guest melihat pilihan register/login
    return view('auth.register-choice');
});

// Registration Routes (use Breeze default /register; custom choices are under /register/organizer and /register/voter)

Route::get('/register/organizer', [OrganizerRegisterController::class, 'create'])
    ->middleware('guest')
    ->name('register.organizer');

Route::get('/register/voter', [VoterRegisterController::class, 'create'])
    ->middleware('guest')
    ->name('register.voter');

Route::post('/register/organizer', [OrganizerRegisterController::class, 'store'])
    ->middleware('guest')
    ->name('register.organizer.store');

Route::post('/register/voter', [VoterRegisterController::class, 'store'])
    ->middleware('guest')
    ->name('register.voter.store');

// Voter Verification Routes
Route::middleware(['auth'])->prefix('voter')->name('voter.')->group(function () {
    Route::get('/verification', [\App\Http\Controllers\VoterVerificationController::class, 'show'])->name('verification');
    Route::post('/verification', [\App\Http\Controllers\VoterVerificationController::class, 'store'])->name('verification.store');
});

// Voter Election Routes - Access by invite code
Route::prefix('election')->name('voter.')->group(function () {
    Route::get('/{code}', [VoterElectionController::class, 'show'])->name('election');
    Route::get('/{code}/candidate/{candidateId}', [VoterElectionController::class, 'candidateDetail'])->name('candidate.detail');
    Route::post('/{code}/vote', [VoterElectionController::class, 'vote'])->middleware('auth')->name('vote');
});

Route::get('/dashboard', function () {
    // Satu titik masuk dashboard: organizer/admin -> admin dashboard; voter -> verification atau election-selection
    $user = Auth::user();
    if (in_array($user->role, ['organizer', 'admin'])) {
        return redirect()->route('admin.dashboard');
    }

    // Voter: prioritaskan verification dulu
    if ($user->isVoter()) {
        // Belum verifikasi atau masih pending/rejected
        if ($user->needsVerification()) {
            return redirect()->route('voter.verification');
        }
        
        // Sudah verified, cek apakah punya elections
        $hasElections = method_exists($user, 'participatingElections')
            ? $user->participatingElections()->where('is_published', true)->exists()
            : false;

        if ($hasElections) {
            // Punya elections, redirect ke verification (akan show election-selection)
            return redirect()->route('voter.verification');
        }
        
        // Verified tapi belum ada election, ke verification page
        return redirect()->route('voter.verification');
    }
    
    // Fallback
    return redirect()->route('voter.verification');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Routes - Protected by organizer middleware
Route::prefix('admin')->name('admin.')->middleware(['auth', \App\Http\Middleware\EnsureUserIsOrganizer::class])->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Candidates Resource Routes
    // Custom route MUST be before resource route to avoid being overridden
    Route::get('/candidates/manage', [CandidateController::class, 'index'])->name('candidates.manage');
    Route::resource('candidates', CandidateController::class)->except(['show']);
    
    // Voter Approval Routes (must be before /voters/{id} to avoid route conflict)
    Route::get('/voters/approval', [\App\Http\Controllers\Admin\VoterApprovalController::class, 'index'])->name('voters.approval');
    Route::post('/voters/{id}/approve', [\App\Http\Controllers\Admin\VoterApprovalController::class, 'approve'])->name('voters.approve');
    Route::post('/voters/{id}/reject', [\App\Http\Controllers\Admin\VoterApprovalController::class, 'reject'])->name('voters.reject');
    
    // Voters Routes
    Route::get('/voters', [\App\Http\Controllers\Admin\VoterController::class, 'index'])->name('voters.index');
    Route::get('/voters/{id}', [\App\Http\Controllers\Admin\VoterController::class, 'show'])->name('voters.show');
    
    // Elections Resource Routes
    Route::get('/elections', [ElectionController::class, 'index'])->name('elections.manage');
    Route::get('/elections/create', [ElectionController::class, 'create'])->name('elections.create');
    Route::post('/elections', [ElectionController::class, 'store'])->name('elections.store');
    Route::get('/elections/{id}/edit', [ElectionController::class, 'edit'])->name('elections.edit');
    Route::put('/elections/{id}', [ElectionController::class, 'update'])->name('elections.update');
    Route::delete('/elections/{id}', [ElectionController::class, 'destroy'])->name('elections.delete');
    Route::post('/elections/{id}/toggle-publish', [ElectionController::class, 'togglePublish'])->name('elections.toggle-publish');
    Route::post('/elections/{id}/close', [ElectionController::class, 'closeElection'])->name('elections.close');
    Route::post('/elections/{id}/deploy-contract', [ElectionController::class, 'deployContract'])->name('elections.deploy-contract');
    Route::get('/elections/sync-status', [ElectionController::class, 'syncStatus'])->name('elections.sync-status');
    
    // Election Link Management
    Route::get('/elections/link', function () {
        $elections = \App\Models\Election::forOrganizer(\Illuminate\Support\Facades\Auth::id())
            ->with('candidates')
            ->latest()
            ->get();
        return view('admin.elections.link', compact('elections'));
    })->name('elections.link');
    
    // Gmail OAuth Callback (for artisan gmail:auth command)
    Route::get('/gmail/callback', function () {
        $code = request()->get('code');
        
        if (!$code) {
            return response()->json([
                'error' => 'No authorization code received',
                'message' => 'Please try again with php artisan gmail:auth'
            ], 400);
        }
        
        return view('admin.gmail-callback', ['code' => $code]);
    })->name('gmail.callback');
    
    // Old route aliases for backward compatibility
    Route::get('/elections/rules/manage', [ElectionController::class, 'index'])->name('elections.rules.manage');
    Route::get('/elections/rules', [ElectionController::class, 'create'])->name('elections.rules.create');
    Route::post('/elections/rules/store', [ElectionController::class, 'store'])->name('elections.rules.store');
    Route::get('/elections/rules/edit/{id}', [ElectionController::class, 'edit'])->name('elections.rules.edit');
    Route::put('/elections/rules/update/{id}', [ElectionController::class, 'update'])->name('elections.rules.update');
    Route::delete('/elections/rules/delete/{id}', [ElectionController::class, 'destroy'])->name('elections.rules.delete');
});

// Redirect /voter ke home (voter harus akses via access code)
Route::get('/voter', function () {
    return redirect('/')->with('info', 'Silakan masukkan kode akses pemilu untuk melanjutkan.');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
