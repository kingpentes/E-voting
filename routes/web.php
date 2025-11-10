<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\OrganizerRegisterController;
use App\Http\Controllers\Auth\VoterRegisterController;
use App\Http\Controllers\Admin\ElectionController;
use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\BlockchainController;
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
        // Default untuk voter atau role lain
        return redirect()->route('dashboard');
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

// Voter Election Routes - Access by invite code
Route::prefix('election')->name('voter.')->group(function () {
    Route::get('/{code}', [VoterElectionController::class, 'show'])->name('election');
    Route::get('/{code}/candidate/{candidateId}', [VoterElectionController::class, 'candidateDetail'])->name('candidate.detail');
    Route::post('/{code}/vote', [VoterElectionController::class, 'vote'])->middleware('auth')->name('vote');
});

Route::get('/dashboard', function () {
    // Satu titik masuk dashboard: penyelenggara/admin diarahkan ke admin dashboard,
    // voter tetap menggunakan tampilan dashboard lama.
    if (Auth::check()) {
        $role = Auth::user()->role;
        if (in_array($role, ['organizer', 'admin'])) {
            return redirect()->route('admin.dashboard');
        }
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Routes - Protected by organizer middleware
Route::prefix('admin')->name('admin.')->middleware(['auth', \App\Http\Middleware\EnsureUserIsOrganizer::class])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        $user = \Illuminate\Support\Facades\Auth::user();
        $election = \App\Models\Election::forOrganizer(\Illuminate\Support\Facades\Auth::id())
            ->with(['candidates', 'votes'])
            ->first();
        
        // Initialize stats
        $stats = [
            'total_voters' => 0,
            'voted' => 0,
            'not_voted' => 0,
            'participation_rate' => 0,
        ];
        
        $candidateStats = collect([]);
        
        if ($election) {
            // Get all voters who joined this election
            $totalVoters = $election->participants()->count();
            
            // Get voters who already voted (distinct voter_id)
            $votedCount = $election->votes()->distinct('voter_id')->count();
            
            // Calculate not voted
            $notVotedCount = $totalVoters - $votedCount;
            
            // Calculate participation rate
            $participationRate = $totalVoters > 0 ? round(($votedCount / $totalVoters) * 100, 1) : 0;
            
            $stats = [
                'total_voters' => $totalVoters,
                'voted' => $votedCount,
                'not_voted' => $notVotedCount,
                'participation_rate' => $participationRate,
            ];
            
            // Get candidate statistics
            $candidates = $election->candidates()
                ->withCount('votes')
                ->orderBy('votes_count', 'desc')
                ->get();
            
            $candidateStats = $candidates->map(function ($candidate) {
                return [
                    'name' => $candidate->name,
                    'votes' => $candidate->votes_count,
                ];
            });
        }

        return view('admin.dashboard', compact('user', 'election', 'stats', 'candidateStats'));
    })->name('dashboard');
    
    // Candidates Resource Routes
    // Custom route MUST be before resource route to avoid being overridden
    Route::get('/candidates/manage', [CandidateController::class, 'index'])->name('candidates.manage');
    Route::resource('candidates', CandidateController::class)->except(['show']);
    
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

    // Blockchain admin
    Route::get('/blockchain', [BlockchainController::class, 'index'])->name('blockchain.index');
    Route::post('/blockchain/deploy', [BlockchainController::class, 'deploy'])->name('blockchain.deploy');
    
    // Election Link Management
    Route::get('/elections/link', function () {
        $elections = \App\Models\Election::forOrganizer(\Illuminate\Support\Facades\Auth::id())
            ->with('candidates')
            ->latest()
            ->get();
        return view('admin.elections.link', compact('elections'));
    })->name('elections.link');
    
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
