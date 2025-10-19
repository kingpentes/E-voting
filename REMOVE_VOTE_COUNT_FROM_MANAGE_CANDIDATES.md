# Hapus Perolehan Suara dari Kelola Kandidat

## Ringkasan Perubahan

Menghapus informasi perolehan suara dari halaman "Kelola Kandidat" agar data voting hanya ditampilkan di Dashboard. Ini membuat halaman Kelola Kandidat lebih fokus pada pengelolaan kandidat (CRUD) tanpa statistik voting.

## Alasan Perubahan

1. **Separation of Concerns**: Halaman Kelola Kandidat fokus pada manajemen kandidat (tambah, edit, hapus)
2. **Dashboard as Single Source**: Dashboard menjadi satu-satunya tempat untuk melihat statistik voting
3. **Performance**: Mengurangi query `withCount('votes')` yang tidak diperlukan
4. **Cleaner UI**: Interface lebih bersih tanpa informasi yang redundant

## Perubahan yang Dilakukan

### 1. View: `resources/views/admin/candidates/manage.blade.php`

#### Hapus Badge Suara di Header

**Sebelum:**

```blade
<div class="flex items-center space-x-4 mt-2">
    <div class="flex items-center space-x-2">
        <svg class="w-5 h-5 text-purple-600">...</svg>
        <span class="text-sm font-semibold text-purple-600">{{ $candidate->votes_count }} Suara</span>
    </div>
    <div class="flex items-center space-x-2">
        <svg class="w-5 h-5 text-blue-600">...</svg>
        <span class="text-sm font-semibold text-blue-600">{{ $candidate->missions->count() }} Misi</span>
    </div>
</div>
```

**Sesudah:**

```blade
<div class="flex items-center space-x-4 mt-2">
    <div class="flex items-center space-x-2">
        <svg class="w-5 h-5 text-blue-600">...</svg>
        <span class="text-sm font-semibold text-blue-600">{{ $candidate->missions->count() }} Misi</span>
    </div>
</div>
```

#### Hapus Card Statistik Voting

**Sebelum:**

```blade
<div class="p-8">
    <!-- Vote Statistics -->
    @php
        $totalVotes = $candidate->election->votes()->count();
        $percentage = $totalVotes > 0 ? round(($candidate->votes_count / $totalVotes) * 100, 1) : 0;
    @endphp

    <div class="mb-6 p-4 bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200 rounded-xl">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-semibold text-gray-700">Perolehan Suara</span>
            <span class="text-2xl font-bold text-purple-600">{{ $candidate->votes_count }} / {{ $totalVotes }}</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 h-4 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
        </div>
        <div class="mt-2 text-right">
            <span class="text-lg font-bold text-purple-700">{{ $percentage }}%</span>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-6">
        <!-- Content -->
    </div>
</div>
```

**Sesudah:**

```blade
<div class="p-8">
    <div class="grid grid-cols-3 gap-6">
        <!-- Content -->
    </div>
</div>
```

### 2. Controller: `app/Http/Controllers/Admin/CandidateController.php`

#### Hapus withCount('votes')

**Sebelum:**

```php
public function index()
{
    $elections = Election::forOrganizer(Auth::id())->pluck('id');
    $candidates = Candidate::whereIn('election_id', $elections)
        ->with(['election', 'missions'])
        ->withCount('votes') // Add vote count
        ->orderBy('election_id')
        ->orderBy('number')
        ->get();

    return view('admin.candidates.manage', compact('candidates'));
}
```

**Sesudah:**

```php
public function index()
{
    $elections = Election::forOrganizer(Auth::id())->pluck('id');
    $candidates = Candidate::whereIn('election_id', $elections)
        ->with(['election', 'missions'])
        ->orderBy('election_id')
        ->orderBy('number')
        ->get();

    return view('admin.candidates.manage', compact('candidates'));
}
```

## Dampak Perubahan

### ✅ Keuntungan

1. **Lebih Cepat**: Tidak perlu query `COUNT` votes untuk setiap kandidat
2. **Lebih Fokus**: Halaman Kelola Kandidat fokus pada manajemen data kandidat
3. **Konsistensi**: Statistik voting hanya ada di satu tempat (Dashboard)
4. **UI Lebih Bersih**: Menghapus card statistik yang besar dari setiap kandidat

### 📊 Data Voting Tetap Tersedia

-   Dashboard menampilkan statistik lengkap per kandidat
-   Dashboard menampilkan grafik bar dan pie chart
-   Dashboard menampilkan total pemilih, sudah voting, belum voting

## Halaman yang Terpengaruh

1. ✅ `/admin/candidates/manage` - Halaman Kelola Kandidat (dihapus statistik voting)
2. ✅ `/admin/dashboard` - Dashboard Admin (tetap menampilkan statistik voting lengkap)

## Testing

1. Login sebagai organizer
2. Akses `/admin/candidates/manage`
3. Pastikan:
    - ✅ Tidak ada badge "X Suara" di header kandidat
    - ✅ Tidak ada card "Perolehan Suara" dengan progress bar
    - ✅ Hanya menampilkan: foto, nomor urut, nama, visi, misi, dan jumlah misi
    - ✅ Button Edit dan Hapus tetap berfungsi normal
4. Akses `/admin/dashboard`
5. Pastikan:
    - ✅ Statistik voting tetap ditampilkan dengan benar
    - ✅ Grafik kandidat menampilkan perolehan suara

## File yang Dimodifikasi

1. `resources/views/admin/candidates/manage.blade.php`
    - Hapus badge suara di header
    - Hapus card statistik voting lengkap
2. `app/Http/Controllers/Admin/CandidateController.php`
    - Hapus `withCount('votes')` untuk optimasi query

## Catatan

-   Perolehan suara **hanya** ditampilkan di Dashboard
-   Halaman Kelola Kandidat tetap menampilkan jumlah misi per kandidat
-   Tidak ada perubahan pada fungsi CRUD kandidat
-   Query lebih efisien karena tidak perlu count votes
