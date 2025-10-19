# Daftar Voter di Admin Panel

## 📋 Fitur yang Ditambahkan

Halaman **Daftar Voter** di admin panel untuk memudahkan organizer memantau dan mengelola pemilih yang terdaftar.

---

## 🎯 Fitur Utama

### 1. **Halaman Index Voters**

**URL:** `/admin/voters`

**Fitur:**

-   ✅ Tabel daftar semua voter
-   ✅ Statistik real-time (Total Voter, Peserta Pemilu, Sudah Voting, Belum Voting)
-   ✅ Search & Filter functionality
-   ✅ Status voting indicator
-   ✅ Pagination
-   ✅ Foto profil voter

**Kolom Tabel:**

-   Voter (Nama & Email dengan foto)
-   Organisasi
-   ID Number (NIK/NIM)
-   Status Vote (Sudah Vote / Belum Vote / Bukan Peserta)
-   Total Vote
-   Terdaftar (Tanggal & Waktu)
-   Aksi (Link ke Detail)

---

### 2. **Halaman Detail Voter**

**URL:** `/admin/voters/{id}`

**Fitur:**

-   ✅ Profil lengkap voter
-   ✅ Informasi personal (ID Number, Organisasi, dll)
-   ✅ Status di pemilu organizer
-   ✅ Daftar pemilu yang diikuti
-   ✅ Riwayat voting lengkap
-   ✅ Vote hash untuk transparansi

**Informasi yang Ditampilkan:**

1. **Profile Card:**

    - Foto profil
    - Nama lengkap
    - Email
    - Total vote count
    - Role badge

2. **Personal Info:**

    - ID Number
    - Organisasi
    - Tanggal registrasi
    - Status di pemilu organizer

3. **Participating Elections:**

    - List pemilu yang diikuti
    - Access code yang digunakan
    - Tanggal bergabung
    - Status voting per pemilu

4. **Voting History:**
    - Pemilu yang sudah di-vote
    - Kandidat yang dipilih (atau abstain)
    - Waktu voting
    - Vote hash

---

## 📊 Statistik Dashboard

**4 Card Statistik di Header:**

1. **Total Voter** (Blue Card)

    - Jumlah semua voter di sistem

2. **Peserta Pemilu** (Green Card)

    - Jumlah voter yang join pemilu organizer

3. **Sudah Voting** (Purple Card)

    - Jumlah voter yang sudah memberikan suara

4. **Belum Voting** (Orange Card)
    - Jumlah voter yang belum voting

---

## 🔍 Search & Filter

### Search

Cari voter berdasarkan:

-   Nama
-   Email
-   ID Number
-   Organisasi

### Filter

-   **Semua Voter:** Tampilkan semua voter di sistem
-   **Peserta Pemilu Saya:** Hanya tampilkan voter yang join pemilu organizer

---

## 🎨 UI/UX Features

### Status Badges

-   **Sudah Vote:** Green badge dengan checkmark
-   **Belum Vote:** Yellow badge dengan clock icon
-   **Bukan Peserta:** Gray badge

### Visual Indicators

-   Foto profil voter (atau initial jika tidak ada foto)
-   Gradient background untuk header
-   Hover effects pada table rows
-   Responsive design (mobile-friendly)

---

## 📁 File Structure

### Controller

```
app/Http/Controllers/Admin/VoterController.php
├── index()  → List voters with stats & filters
└── show()   → Voter detail with full history
```

### Views

```
resources/views/admin/voters/
├── index.blade.php  → Voters list page
└── show.blade.php   → Voter detail page
```

### Routes

```php
Route::get('/voters', [VoterController::class, 'index'])->name('voters.index');
Route::get('/voters/{id}', [VoterController::class, 'show'])->name('voters.show');
```

### Sidebar Menu

```blade
<a href="{{ route('admin.voters.index') }}">
    Daftar Voter
</a>
```

---

## 🔧 Implementasi Detail

### VoterController.php

#### Method `index()`

```php
public function index(Request $request)
{
    // Get organizer's election
    $election = Election::forOrganizer(Auth::id())->first();

    // Query voters with vote count
    $query = User::where('role', 'voter')
        ->withCount('votes')
        ->orderBy('created_at', 'desc');

    // Filter participants only
    if ($request->get('filter') === 'participants') {
        $query->whereHas('participatingElections', function($q) use ($election) {
            $q->where('election_id', $election->id);
        });
    }

    // Search functionality
    if ($request->filled('search')) {
        $search = $request->get('search');
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('id_number', 'like', "%{$search}%")
              ->orWhere('organization', 'like', "%{$search}%");
        });
    }

    $voters = $query->paginate(20);

    // Calculate statistics
    $stats = [
        'total_voters' => User::where('role', 'voter')->count(),
        'participants' => $election ? $election->participants()->count() : 0,
        'voted' => $election ? $election->votes()->distinct('voter_id')->count() : 0,
        'not_voted' => ...,
    ];

    return view('admin.voters.index', compact('voters', 'election', 'stats'));
}
```

#### Method `show()`

```php
public function show(string $id)
{
    $voter = User::where('role', 'voter')
        ->with(['participatingElections', 'votes.election', 'votes.candidate'])
        ->findOrFail($id);

    $election = Election::forOrganizer(Auth::id())->first();

    return view('admin.voters.show', compact('voter', 'election'));
}
```

---

## 🧪 Testing

### Test Halaman Index

1. **Akses Halaman:**

    ```
    /admin/voters
    ```

2. **Verifikasi:**

    - ✅ Tampil statistik 4 card
    - ✅ Tabel voter muncul
    - ✅ Search box berfungsi
    - ✅ Filter dropdown berfungsi
    - ✅ Pagination berfungsi

3. **Test Search:**

    - Cari nama voter → Harus filter hasil
    - Cari email → Harus filter hasil
    - Cari ID number → Harus filter hasil

4. **Test Filter:**
    - Filter "Peserta Pemilu Saya" → Hanya tampil voter yang join pemilu organizer
    - Filter "Semua Voter" → Tampil semua voter

---

### Test Halaman Detail

1. **Akses Detail:**

    ```
    /admin/voters/{id}
    ```

2. **Verifikasi:**

    - ✅ Foto profil tampil
    - ✅ Informasi personal lengkap
    - ✅ Status voting benar
    - ✅ List pemilu yang diikuti tampil
    - ✅ Riwayat voting lengkap

3. **Test Tombol Kembali:**
    - Klik tombol "Kembali" → Redirect ke `/admin/voters`

---

## 💡 Use Cases

### Use Case 1: Monitoring Partisipasi

**Scenario:** Organizer ingin tahu berapa voter yang sudah voting

**Steps:**

1. Buka `/admin/voters`
2. Lihat card "Sudah Voting" (warna ungu)
3. Lihat card "Belum Voting" (warna orange)
4. Persentase partisipasi bisa dihitung manual

**Result:** Organizer dapat memantau partisipasi secara real-time

---

### Use Case 2: Verifikasi Voter

**Scenario:** Organizer ingin verifikasi voter tertentu

**Steps:**

1. Buka `/admin/voters`
2. Cari nama voter di search box
3. Klik "Detail" pada voter
4. Verifikasi foto & ID number
5. Cek riwayat voting

**Result:** Organizer dapat memverifikasi identitas dan aktivitas voter

---

### Use Case 3: Follow-up Voter Belum Vote

**Scenario:** Organizer ingin follow-up voter yang belum voting

**Steps:**

1. Buka `/admin/voters`
2. Filter "Peserta Pemilu Saya"
3. Lihat kolom "Status Vote"
4. Identifikasi voter dengan badge "Belum Vote" (kuning)
5. Klik "Detail" untuk lihat info kontak
6. Follow-up via email

**Result:** Organizer dapat meningkatkan partisipasi voting

---

## 🔒 Security

### Authorization

-   ✅ Route protected dengan middleware `EnsureUserIsOrganizer`
-   ✅ Hanya organizer yang dapat akses
-   ✅ Voter tidak dapat akses admin panel

### Privacy

-   ⚠️ **Vote tetap anonymous:** Organizer TIDAK dapat melihat pilihan spesifik voter
-   ✅ Hanya bisa lihat status "Sudah Vote" atau "Belum Vote"
-   ✅ Vote hash ditampilkan untuk transparansi (tapi tidak bisa di-reverse)

---

## 📈 Future Improvements

### Planned Features

1. **Export Data:**

    - Export voter list ke Excel/CSV
    - Export voting statistics

2. **Email Notification:**

    - Kirim email reminder ke voter yang belum voting
    - Bulk email feature

3. **Advanced Filters:**

    - Filter by organization
    - Filter by registration date
    - Sort by various columns

4. **Analytics:**
    - Voting timeline chart
    - Participation rate graph
    - Demographic analysis

---

## ✅ Checklist Implementation

-   [x] VoterController dengan index & show methods
-   [x] Routes untuk voters
-   [x] View index.blade.php dengan tabel & statistik
-   [x] View show.blade.php dengan detail lengkap
-   [x] Menu "Daftar Voter" di sidebar
-   [x] Search functionality
-   [x] Filter functionality
-   [x] Pagination
-   [x] Status badges
-   [x] Responsive design
-   [x] Error handling
-   [x] Documentation

---

**Status:** ✅ **SELESAI**

**Tested On:** {{ now()->format('d F Y') }}
