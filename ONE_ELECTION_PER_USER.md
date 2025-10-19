# Sistem 1 Pemilu Per User & Validasi Kandidat untuk Publish

## 📋 Perubahan yang Dibuat

### 1. **Batasan 1 Pemilu Per Organizer**

Setiap organizer hanya dapat membuat **1 pemilu** untuk mencegah spam dan menjaga simplicity sistem.

#### Implementasi di Controller

**File:** `app/Http/Controllers/Admin/ElectionController.php`

##### Method `create()`

```php
public function create()
{
    // Check if user already has an election
    $existingElection = Election::forOrganizer(Auth::id())->first();

    if ($existingElection) {
        return redirect()->route('admin.elections.manage')
            ->with('error', '✗ Anda sudah memiliki pemilu. Setiap organizer hanya dapat membuat 1 pemilu.');
    }

    return view('admin.elections.rules');
}
```

##### Method `store()`

```php
public function store(Request $request)
{
    // Check if user already has an election
    $existingElection = Election::forOrganizer(Auth::id())->first();

    if ($existingElection) {
        return redirect()->route('admin.elections.manage')
            ->with('error', '✗ Anda sudah memiliki pemilu. Setiap organizer hanya dapat membuat 1 pemilu.');
    }

    // ... rest of the code
}
```

#### Implementasi di View

**File:** `resources/views/admin/elections/manage.blade.php`

Tombol "Buat Pemilu Baru" diganti dengan status "Limit 1 Pemilu Tercapai" jika user sudah memiliki pemilu:

```blade
@if($elections->isEmpty())
    <a href="{{ route('admin.elections.create') }}"
       class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all">
        <span>+ Buat Pemilu Baru</span>
    </a>
@else
    <div class="px-6 py-3 bg-gray-300 text-gray-500 font-bold rounded-xl cursor-not-allowed" title="Anda sudah memiliki pemilu">
        <span>Limit 1 Pemilu Tercapai</span>
    </div>
@endif
```

---

### 2. **Validasi Kandidat Sebelum Publish**

Pemilu **tidak dapat di-publish** jika belum ada kandidat minimal 1 orang.

#### Implementasi di Controller

**File:** `app/Http/Controllers/Admin/ElectionController.php`

##### Method `togglePublish()`

```php
public function togglePublish(string $id)
{
    $election = Election::forOrganizer(Auth::id())->findOrFail($id);

    // Check if election has candidates before publishing
    if (!$election->is_published && $election->candidates()->count() === 0) {
        return redirect()->back()
            ->with('error', '✗ Tidak dapat mempublish pemilu tanpa kandidat. Tambahkan kandidat terlebih dahulu.');
    }

    $election->update([
        'is_published' => !$election->is_published,
        'status' => $election->is_published ? 'draft' : 'active',
    ]);

    $status = $election->is_published ? 'dipublish' : 'draft';
    return redirect()->back()
        ->with('success', "✓ Pemilu berhasil $status!");
}
```

#### Implementasi di View

**File:** `resources/views/admin/elections/manage.blade.php`

Tombol Publish dinonaktifkan jika tidak ada kandidat:

```blade
@if(!$election->is_published && $election->candidates_count === 0)
    <button disabled class="px-5 py-2.5 bg-gray-300 text-gray-600 font-semibold rounded-lg cursor-not-allowed"
            title="Tambahkan kandidat terlebih dahulu untuk publish">
        Publish (Perlu Kandidat)
    </button>
@else
    <form action="{{ route('admin.elections.toggle-publish', $election->id) }}" method="POST">
        @csrf
        <button type="submit" class="px-5 py-2.5 {{ $election->is_published ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white font-semibold rounded-lg transition-colors">
            {{ $election->is_published ? 'Unpublish' : 'Publish' }}
        </button>
    </form>
@endif
```

Peringatan visual di jumlah kandidat:

```blade
<span class="text-gray-700 {{ $election->candidates_count === 0 ? 'text-red-600 font-bold' : '' }}">
    {{ $election->candidates_count }} Kandidat
    @if($election->candidates_count === 0 && !$election->is_published)
        <span class="text-xs">(⚠️ Diperlukan untuk publish)</span>
    @endif
</span>
```

---

## 🔍 Cara Kerja

### Flow User Membuat Pemilu

1. **Organizer Login** → Dashboard
2. **Klik "Buat Pemilu Baru"** (hanya muncul jika belum ada pemilu)
3. **Isi Form Pemilu** → Simpan (Status: DRAFT)
4. **Tombol "Buat Pemilu Baru" Hilang** → Diganti dengan "Limit 1 Pemilu Tercapai"
5. **Tambah Kandidat** → Minimal 1 kandidat
6. **Tombol "Publish" Aktif** → Klik untuk publish
7. **Status Berubah: DRAFT → AKTIF**

### Validasi yang Terjadi

#### Saat Membuat Pemilu Baru

```php
✅ Cek: Apakah user sudah punya pemilu?
   ├── Tidak → Lanjut buat pemilu
   └── Ya → Redirect dengan error "Anda sudah memiliki pemilu"
```

#### Saat Publish Pemilu

```php
✅ Cek: Apakah pemilu punya kandidat?
   ├── Ya → Lanjut publish
   └── Tidak → Redirect dengan error "Tidak dapat mempublish tanpa kandidat"
```

---

## 🎯 Manfaat

### 1. Prevent Spam Elections

-   Organizer tidak bisa membuat pemilu berkali-kali
-   Database tetap clean
-   Fokus ke 1 pemilu yang berkualitas

### 2. Data Integrity

-   Pemilu yang di-publish pasti punya kandidat
-   Voter tidak akan menemukan pemilu kosong
-   Proses voting selalu valid

### 3. Better UX

-   UI menampilkan status dengan jelas
-   Tombol dinonaktifkan jika kondisi tidak terpenuhi
-   Pesan error yang informatif

---

## 🧪 Testing

### Test Case 1: Batasan 1 Pemilu

**Langkah:**

1. Login sebagai organizer
2. Buat pemilu pertama → ✅ Berhasil
3. Coba akses halaman buat pemilu lagi → ❌ Redirect dengan error
4. Coba POST form buat pemilu via API → ❌ Redirect dengan error

**Expected Result:**

-   Tombol "Buat Pemilu Baru" hilang setelah ada 1 pemilu
-   Error message: "✗ Anda sudah memiliki pemilu. Setiap organizer hanya dapat membuat 1 pemilu."

---

### Test Case 2: Publish Tanpa Kandidat

**Langkah:**

1. Buat pemilu (status DRAFT)
2. Jangan tambah kandidat
3. Klik tombol "Publish" → ❌ Tombol disabled
4. Coba POST publish via form → ❌ Redirect dengan error

**Expected Result:**

-   Tombol Publish disabled dengan tooltip
-   Jumlah kandidat ditampilkan merah dengan warning
-   Error message: "✗ Tidak dapat mempublish pemilu tanpa kandidat. Tambahkan kandidat terlebih dahulu."

---

### Test Case 3: Publish Dengan Kandidat

**Langkah:**

1. Buat pemilu (status DRAFT)
2. Tambah minimal 1 kandidat
3. Klik tombol "Publish" → ✅ Berhasil

**Expected Result:**

-   Status berubah dari DRAFT → AKTIF
-   Badge berubah dari gray → green
-   Access code bisa digunakan voter
-   Success message: "✓ Pemilu berhasil dipublish!"

---

## 📝 Catatan Tambahan

### Jika Ingin Menghapus Batasan 1 Pemilu

Jika di masa depan ingin mengizinkan organizer membuat lebih dari 1 pemilu, hapus validasi di:

-   `ElectionController::create()` - Hapus pengecekan `$existingElection`
-   `ElectionController::store()` - Hapus pengecekan `$existingElection`
-   `manage.blade.php` - Kembalikan tombol "Buat Pemilu Baru" tanpa kondisi

### Jika Ingin Mengubah Batas Jumlah Pemilu

Ganti logika dari:

```php
$existingElection = Election::forOrganizer(Auth::id())->first();
```

Menjadi:

```php
$electionCount = Election::forOrganizer(Auth::id())->count();
$maxElections = 3; // Contoh: maksimal 3 pemilu

if ($electionCount >= $maxElections) {
    return redirect()->route('admin.elections.manage')
        ->with('error', "✗ Anda sudah mencapai batas maksimal $maxElections pemilu.");
}
```

---

## ✅ Checklist Implementation

-   [x] Validasi 1 pemilu per user di `create()`
-   [x] Validasi 1 pemilu per user di `store()`
-   [x] Update UI tombol "Buat Pemilu Baru"
-   [x] Validasi kandidat sebelum publish di `togglePublish()`
-   [x] Disable tombol Publish jika tidak ada kandidat
-   [x] Peringatan visual di jumlah kandidat
-   [x] Error messages yang informatif
-   [x] Dokumentasi lengkap

---

**Status:** ✅ **SELESAI**
