<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\OrganizerRegisterController;
use App\Http\Controllers\Auth\VoterRegisterController;
use App\Http\Controllers\Admin\ElectionController;
use App\Http\Controllers\Admin\CandidateController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.register-choice');
});

// Registration Routes
Route::get('/register', function () {
    return view('auth.register-choice');
})->name('register');

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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Routes - Protected by organizer middleware
Route::prefix('admin')->name('admin.')->middleware(['auth', \App\Http\Middleware\EnsureUserIsOrganizer::class])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        $user = \Illuminate\Support\Facades\Auth::user();
        $elections = \App\Models\Election::forOrganizer(\Illuminate\Support\Facades\Auth::id())
            ->withCount(['candidates', 'votes'])
            ->latest()
            ->get();
        
        $stats = [
            'total_elections' => $elections->count(),
            'active_elections' => $elections->where('status', 'active')->count(),
            'total_candidates' => $elections->sum('candidates_count'),
            'total_votes' => $elections->sum('votes_count'),
        ];

        return view('admin.dashboard', compact('user', 'elections', 'stats'));
    })->name('dashboard');
    
    // Candidates Resource Routes
    // Custom route MUST be before resource route to avoid being overridden
    Route::get('/candidates/manage', [CandidateController::class, 'index'])->name('candidates.manage');
    Route::resource('candidates', CandidateController::class)->except(['show']);
    
    // Elections Resource Routes
    Route::get('/elections', [ElectionController::class, 'index'])->name('elections.manage');
    Route::get('/elections/create', [ElectionController::class, 'create'])->name('elections.create');
    Route::post('/elections', [ElectionController::class, 'store'])->name('elections.store');
    Route::get('/elections/{id}/edit', [ElectionController::class, 'edit'])->name('elections.edit');
    Route::put('/elections/{id}', [ElectionController::class, 'update'])->name('elections.update');
    Route::delete('/elections/{id}', [ElectionController::class, 'destroy'])->name('elections.delete');
    Route::post('/elections/{id}/toggle-publish', [ElectionController::class, 'togglePublish'])->name('elections.toggle-publish');
    
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

// Public Voter Dashboard (no middleware for now)
Route::get('/voter', function () {
    $election = [
        'title' => 'E-Voting',
        'description' => 'Sistem Pemilihan Elektronik untuk memilih pemimpin masa depan dengan transparan, aman, dan demokratis'
    ];

    $candidates = [
        [
            'id' => 1,
            'number' => 1,
            'name' => 'Dr. Ahmad Santoso',
            'photo' => 'https://i.pravatar.cc/256?img=12',
            'visi' => 'Mewujudkan kepemimpinan yang humanis, efektif, dan berintegritas.',
            'misi' => [
                'Meningkatkan program pengembangan karakter siswa',
                'Memperkuat kolaborasi antar organisasi siswa',
                'Transparansi dalam setiap pengambilan keputusan',
            ],
        ],
        [
            'id' => 2,
            'number' => 2,
            'name' => 'Prof. Dr. Siti Nurhaliza, M.Pd.',
            'photo' => 'https://i.pravatar.cc/256?img=47',
            'visi' => 'Mewujudkan institusi pendidikan berkelas dunia.',
            'misi' => [
                'Meningkatkan kualitas pembelajaran melalui digitalisasi',
                'Mendorong budaya riset dan publikasi ilmiah',
                'Memperkuat jaringan alumni dan stakeholder',
                'Mengembangkan program pengabdian masyarakat berdampak',
            ],
        ],
        [
            'id' => 3,
            'number' => 3,
            'name' => 'Ir. Budi Pratama',
            'photo' => 'https://i.pravatar.cc/256?img=15',
            'visi' => 'Membangun budaya kerja yang disiplin dan berprestasi.',
            'misi' => [
                'Optimalisasi fasilitas dan sumber daya',
                'Program efisiensi dan tata kelola modern',
                'Kompetisi akademik dan non-akademik rutin',
            ],
        ],
        [
            'id' => 4,
            'number' => 4,
            'name' => 'Dr. Maya Kusuma',
            'photo' => 'https://i.pravatar.cc/256?img=49',
            'visi' => 'Menciptakan ekosistem belajar yang inklusif dan kolaboratif.',
            'misi' => [
                'Pelatihan kepemimpinan bagi siswa',
                'Kegiatan kolaborasi lintas jurusan',
                'Program literasi dan numerasi berkelanjutan',
            ],
        ],
        [
            'id' => 5,
            'number' => 5,
            'name' => 'Prof. Rahmat Hidayat',
            'photo' => 'https://i.pravatar.cc/256?img=13',
            'visi' => 'Mendorong lahirnya inovasi dari siswa untuk masyarakat.',
            'misi' => [
                'Inkubasi proyek riset siswa',
                'Kemitraan dengan industri dan komunitas',
                'Akselerasi kompetensi abad 21',
            ],
        ],
        
    ];

    return view('elections.voter-dashboard', compact('election', 'candidates'));
})->name('voter.dashboard');

// Voter Candidate Detail Route
Route::get('/voter/candidate/{id}', function ($id) {
    $candidates = [
        [
            'id' => 1,
            'number' => 1,
            'name' => 'Dr. Ahmad aSantoso',
            'photo' => 'https://i.pravatar.cc/256?img=12',
            'visi' => 'Mewujudkan kepemimpinan yang humanis, efektif, dan berintegritas.',
            'misi' => [
                'Meningkatkan program pengembangan karakter siswa',
                'Memperkuat kolaborasi antar organisasi siswa',
                'Transparansi dalam setiap pengambilan keputusan',
            ],
        ],
        [
            'id' => 2,
            'number' => 2,
            'name' => 'Prof. Dr. Siti Nurhaliza, M.Pd.',
            'photo' => 'https://i.pravatar.cc/256?img=47',
            'visi' => 'Mewujudkan institusi pendidikan berkelas dunia.',
            'misi' => [
                'Meningkatkan kualitas pembelajaran melalui digitalisasi',
                'Mendorong budaya riset dan publikasi ilmiah',
                'Memperkuat jaringan alumni dan stakeholder',
                'Mengembangkan program pengabdian masyarakat berdampak',
            ],
        ],
        [
            'id' => 3,
            'number' => 3,
            'name' => 'Ir. Budi Pratama',
            'photo' => 'https://i.pravatar.cc/256?img=15',
            'visi' => 'Membangun budaya kerja yang disiplin dan berprestasi.',
            'misi' => [
                'Optimalisasi fasilitas dan sumber daya',
                'Program efisiensi dan tata kelola modern',
                'Kompetisi akademik dan non-akademik rutin',
            ],
        ],
        [
            'id' => 4,
            'number' => 4,
            'name' => 'Dr. Maya Kusuma',
            'photo' => 'https://i.pravatar.cc/256?img=49',
            'visi' => 'Menciptakan ekosistem belajar yang inklusif dan kolaboratif.',
            'misi' => [
                'Pelatihan kepemimpinan bagi siswa',
                'Kegiatan kolaborasi lintas jurusan',
                'Program literasi dan numerasi berkelanjutan',
            ],
        ],
        [
            'id' => 5,
            'number' => 5,
            'name' => 'Prof. Rahmat Hidayat',
            'photo' => 'https://i.pravatar.cc/256?img=13',
            'visi' => 'Mendorong lahirnya inovasi dari siswa untuk masyarakat.',
            'misi' => [
                'Inkubasi proyek riset siswa',
                'Kemitraan dengan industri dan komunitas',
                'Akselerasi kompetensi abad 21',
            ],
        ],
        [
            'id' => 6,
            'number' => 6,
            'name' => 'Dr. Dewi Lestari',
            'photo' => 'https://i.pravatar.cc/256?img=32',
            'visi' => 'Mewujudkan lingkungan belajar yang sehat dan ramah.',
            'misi' => [
                'Program kesehatan mental dan fisik siswa',
                'Gerakan sekolah hijau dan ramah lingkungan',
                'Peningkatan layanan konseling',
            ],
        ],
    ];

    $candidate = collect($candidates)->firstWhere('id', $id);
    
    if (!$candidate) {
        abort(404);
    }

    return view('elections.candidate-detail', compact('candidate'));
})->name('voter.candidate');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
