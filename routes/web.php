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

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    Route::get('/candidates/create', function () {
        return view('admin.candidates.create');
    })->name('candidates.create');
    
    Route::post('/candidates/store', function () {
        // Handle candidate store logic here
        return redirect()->route('admin.dashboard')->with('success', 'Kandidat berhasil ditambahkan!');
    })->name('candidates.store');
    
    Route::get('/candidates/manage', function () {
        return view('admin.candidates.manage');
    })->name('candidates.manage');
    
    Route::get('/candidates/edit/{id}', function ($id) {
        // In real app, fetch candidate data from database
        return view('admin.candidates.edit', compact('id'));
    })->name('candidates.edit');
    
    Route::put('/candidates/update/{id}', function ($id) {
        // Handle candidate update logic here
        return redirect()->route('admin.candidates.manage')->with('success', 'Kandidat berhasil diupdate!');
    })->name('candidates.update');
    
    Route::delete('/candidates/delete/{id}', function ($id) {
        // Handle candidate delete logic here
        return redirect()->route('admin.candidates.manage')->with('success', 'Kandidat berhasil dihapus!');
    })->name('candidates.delete');
    
    Route::get('/elections/link', function () {
        return view('admin.elections.link');
    })->name('elections.link');
    
    Route::get('/elections/rules/manage', function () {
        return view('admin.elections.manage');
    })->name('elections.rules.manage');
    
    Route::get('/elections/rules', function () {
        return view('admin.elections.rules');
    })->name('elections.rules.create');
    
    Route::get('/elections/rules/edit/{id}', function ($id) {
        return view('admin.elections.edit', compact('id'));
    })->name('elections.rules.edit');
    
    Route::post('/elections/rules/store', function () {
        // Handle rules store logic here
        return redirect()->route('admin.elections.rules.manage')->with('success', 'Pengaturan berhasil dibuat!');
    })->name('elections.rules.store');
    
    Route::put('/elections/rules/update/{id}', function ($id) {
        // Handle rules update logic here
        return redirect()->route('admin.elections.rules.manage')->with('success', 'Pengaturan berhasil diupdate!');
    })->name('elections.rules.update');
    
    Route::delete('/elections/rules/delete/{id}', function ($id) {
        // Handle rules delete logic here
        return redirect()->route('admin.elections.rules.manage')->with('success', 'Pengaturan berhasil dihapus!');
    })->name('elections.rules.delete');
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
