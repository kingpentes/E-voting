# Sistem Invite Code & Auto-Redirect ke Pemilu

## 📋 Ringkasan Fitur

Sistem ini menghubungkan **access code pemilu** dengan **registrasi voter** sehingga:

1. Voter memasukkan **invite code** saat registrasi
2. Sistem **validasi** apakah code valid dan pemilu sudah dipublikasikan
3. Setelah registrasi berhasil, voter **otomatis login** dan **diarahkan** ke halaman pemilu yang sesuai
4. Voter bisa langsung **melihat kandidat** dan **memberikan suara**

---

## 🔧 Implementasi

### 1. Controller: VoterRegisterController

**File:** `app/Http/Controllers/Auth/VoterRegisterController.php`

**Perubahan:**

```php
// Validasi invite_code sekarang REQUIRED
'invite_code' => ['required', 'string', 'max:8'], // Wajib diisi

// Cek apakah access_code valid
$election = Election::where('access_code', strtoupper($request->invite_code))
    ->where('is_published', true)
    ->first();

if (!$election) {
    return back()->withErrors([
        'invite_code' => 'Kode undangan tidak valid atau pemilu belum dipublikasikan.'
    ])->withInput();
}

// Simpan access_code yang digunakan ke field id_number
'id_number' => strtoupper($request->invite_code),

// Auto-login setelah registrasi
Auth::login($user);

// Redirect ke halaman pemilu yang sesuai
return redirect()->route('voter.election', ['code' => $election->access_code])
    ->with('success', '✓ Registrasi berhasil! Selamat datang di pemilu: ' . $election->title);
```

---

### 2. Controller: VoterElectionController (BARU)

**File:** `app/Http/Controllers/VoterElectionController.php`

**Methods:**

#### a. `show(string $code)` - Tampilkan Halaman Pemilu

-   Cari pemilu berdasarkan `access_code`
-   Load kandidat dengan misi
-   Cek apakah user sudah voting
-   Return view `voter.election`

#### b. `candidateDetail(string $code, int $candidateId)` - Detail Kandidat

-   Tampilkan visi & misi lengkap kandidat
-   Tombol pilih kandidat jika belum voting

#### c. `vote(Request $request, string $code)` - Submit Suara

-   Validasi candidate_id
-   Cek apakah user sudah voting (prevent double vote)
-   Simpan vote ke database
-   Redirect dengan pesan sukses

---

### 3. Routes

**File:** `routes/web.php`

```php
// Voter Election Routes - Access by invite code
Route::prefix('election')->name('voter.')->group(function () {
    Route::get('/{code}', [VoterElectionController::class, 'show'])->name('election');
    Route::get('/{code}/candidate/{candidateId}', [VoterElectionController::class, 'candidateDetail'])->name('candidate.detail');
    Route::post('/{code}/vote', [VoterElectionController::class, 'vote'])->middleware('auth')->name('vote');
});
```

**URL Pattern:**

-   Halaman Pemilu: `/election/{ACCESS_CODE}`
-   Detail Kandidat: `/election/{ACCESS_CODE}/candidate/{ID}`
-   Submit Vote: `POST /election/{ACCESS_CODE}/vote`

---

### 4. Views

#### a. `resources/views/voter/election.blade.php`

Menampilkan:

-   Header pemilu (title, description)
-   Statistik (jumlah kandidat, tanggal mulai/berakhir)
-   Peraturan pemilihan
-   Status voting (sudah vote atau belum)
-   Grid kandidat dengan foto, visi, misi preview
-   Tombol "Detail" dan "Pilih"

**Features:**

-   Responsive design (grid 1-2-3 columns)
-   Card hover effects
-   Success/Error messages
-   Voting disabled setelah user sudah vote

#### b. `resources/views/voter/candidate-detail.blade.php`

Menampilkan:

-   Header gradient dengan foto kandidat
-   Nomor urut
-   Visi lengkap (highlighted box)
-   Misi lengkap (numbered list dengan styling)
-   Tombol "Pilih Kandidat Ini"

---

## 🎯 Alur Lengkap

### Alur 1: Registrasi Voter dengan Invite Code

```
1. Penyelenggara membuat pemilu
   → System generate access_code (contoh: "ABC12345")

2. Penyelenggara publish pemilu
   → is_published = true

3. Penyelenggara share access_code ke voter

4. Voter buka /register/voter

5. Voter isi form registrasi:
   - Invite Code: ABC12345 ✅
   - Name: John Doe
   - Email: john@example.com
   - Password: ********
   - Upload ID Card
   - Capture Face Photo

6. System validasi invite_code:
   ✅ Code valid?
   ✅ Pemilu sudah published?

7. Jika valid:
   → User tersimpan ke database
   → Auto-login
   → Redirect ke /election/ABC12345

8. Jika tidak valid:
   → Error: "Kode undangan tidak valid"
   → Form tidak submit
```

### Alur 2: Voter Memberikan Suara

```
1. Voter sudah login & berada di /election/ABC12345

2. Halaman menampilkan:
   - Informasi pemilu
   - Peraturan
   - List kandidat

3. Voter klik "Detail" pada kandidat
   → Redirect ke /election/ABC12345/candidate/1
   → Tampil visi & misi lengkap

4. Voter klik "Pilih Kandidat Ini"
   → Konfirmasi: "Apakah Anda yakin?"

5. Jika Ya:
   → POST /election/ABC12345/vote
   → System cek: sudah vote?
   → Jika belum: simpan vote
   → Redirect dengan pesan: "Suara berhasil dicatat!"

6. Halaman update:
   → Tombol "Pilih" hilang/disabled
   → Tampil pesan: "Terima kasih! Anda sudah memberikan suara"
```

---

## 🔐 Security Features

### 1. Validasi Access Code

```php
$election = Election::where('access_code', strtoupper($request->invite_code))
    ->where('is_published', true)
    ->first();

if (!$election) {
    return back()->withErrors(['invite_code' => 'Kode tidak valid']);
}
```

### 2. Prevent Double Voting

```php
if (Auth::user()->hasVotedIn($election->id)) {
    return back()->with('error', 'Anda sudah memberikan suara');
}
```

### 3. Multi-Tenancy Protection

```php
// Verify candidate belongs to this election
$candidate = Candidate::where('id', $validated['candidate_id'])
    ->where('election_id', $election->id)
    ->firstOrFail();
```

### 4. Auth Middleware

```php
Route::post('/{code}/vote', [VoterElectionController::class, 'vote'])
    ->middleware('auth')->name('vote');
```

---

## 📊 Database Schema

### Users Table

```sql
id_number VARCHAR(50) UNIQUE -- Menyimpan access_code yang digunakan saat registrasi
```

### Elections Table

```sql
access_code VARCHAR(8) UNIQUE -- Generated otomatis saat create election
is_published BOOLEAN -- Harus true agar voter bisa akses
```

### Votes Table

```sql
id BIGINT PRIMARY KEY
election_id BIGINT FK → elections.id
candidate_id BIGINT FK → candidates.id
voter_id BIGINT FK → users.id
created_at TIMESTAMP

UNIQUE(election_id, voter_id) -- Satu voter hanya bisa vote 1x per election
```

---

## 🧪 Testing

### Test 1: Registrasi dengan Code Valid

```bash
1. Login sebagai Organizer
2. Buat pemilu baru
3. Publish pemilu
4. Copy access_code (misal: XYZ789)
5. Logout
6. Buka /register/voter
7. Isi form dengan invite_code: XYZ789
8. Submit form

✅ Expected:
- Registrasi berhasil
- Auto-login
- Redirect ke /election/XYZ789
- Halaman pemilu tampil dengan kandidat
```

### Test 2: Registrasi dengan Code Invalid

```bash
1. Buka /register/voter
2. Isi form dengan invite_code: INVALID
3. Submit form

✅ Expected:
- Error: "Kode undangan tidak valid"
- Form tidak submit
- User tetap di halaman registrasi
```

### Test 3: Voting Flow

```bash
1. Voter sudah di /election/XYZ789
2. Klik "Detail" pada kandidat #1
3. Baca visi & misi
4. Klik "Pilih Kandidat Ini"
5. Konfirmasi "Ya"

✅ Expected:
- Vote tersimpan ke database
- Redirect ke /election/XYZ789
- Pesan: "Suara berhasil dicatat!"
- Tombol "Pilih" tidak muncul lagi
- Badge "Terima Kasih!" muncul
```

### Test 4: Prevent Double Voting

```bash
1. Voter sudah vote di Test 3
2. Coba akses /election/XYZ789/candidate/2
3. Klik "Pilih Kandidat Ini"

✅ Expected:
- Error: "Anda sudah memberikan suara"
- Vote tidak tersimpan
- Redirect kembali
```

---

## 🎨 UI/UX Features

### Halaman Election

-   **Gradient Header** - Purple & Pink gradient
-   **Stats Cards** - Total kandidat, tanggal mulai/berakhir
-   **Rules Section** - Numbered list dengan icon
-   **Voting Status Badge** - Green badge jika sudah vote
-   **Candidate Cards** - Grid responsive dengan hover effects
-   **Photo Placeholder** - SVG icon jika kandidat belum upload foto

### Halaman Candidate Detail

-   **Gradient Header** - Full-width dengan foto kandidat besar
-   **Nomor Urut Badge** - Floating badge di kiri atas
-   **Visi Section** - Purple background box dengan border accent
-   **Misi Section** - Numbered cards dengan gradient icons
-   **CTA Button** - Full-width gradient button dengan hover animation

### Responsive Design

-   Desktop: 3 columns grid
-   Tablet: 2 columns grid
-   Mobile: 1 column grid

---

## 📁 File Structure

```
app/
├── Http/
│   └── Controllers/
│       ├── Auth/
│       │   └── VoterRegisterController.php (UPDATED)
│       └── VoterElectionController.php (NEW)
│
resources/
└── views/
    └── voter/ (NEW)
        ├── election.blade.php
        └── candidate-detail.blade.php

routes/
└── web.php (UPDATED - added voter routes)
```

---

## 🚀 Next Steps (Optional Enhancements)

1. **Real-time Results** - Tampilkan hasil voting secara real-time jika `show_results_after_vote` enabled
2. **Vote History** - Halaman untuk voter melihat history voting mereka
3. **Election Search** - Halaman untuk voter memasukkan code dan search election
4. **QR Code** - Generate QR code untuk access_code
5. **Email Notification** - Kirim email konfirmasi setelah voting
6. **Vote Receipt** - Generate receipt PDF setelah voting
7. **Anonymous Voting** - Enkripsi ballot untuk anonymous voting
8. **Election Dashboard** - Dashboard untuk voter melihat semua election yang bisa diakses

---

## ✅ Summary

**Fitur yang sudah berhasil diimplementasikan:**

✅ Validasi invite code saat registrasi voter
✅ Auto-redirect ke halaman pemilu setelah registrasi
✅ Halaman election dengan list kandidat (by access code)
✅ Halaman detail kandidat dengan visi & misi lengkap
✅ Sistem voting dengan prevent double vote
✅ Security: Access code validation, auth middleware, multi-tenancy
✅ Responsive UI dengan Tailwind CSS
✅ Success/Error messages dengan flash session

**Access Pattern:**

-   `/election/{ACCESS_CODE}` - Halaman pemilu
-   `/election/{ACCESS_CODE}/candidate/{ID}` - Detail kandidat
-   `POST /election/{ACCESS_CODE}/vote` - Submit vote

**Data Flow:**

```
Organizer Create Election → Generate Access Code → Share to Voter
→ Voter Register with Code → Auto Login → Redirect to Election Page
→ View Candidates → Vote → Success Message → Thank You
```

Sistem siap digunakan! 🎉
