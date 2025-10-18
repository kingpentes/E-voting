# Admin CRUD Implementation - Complete Guide

## 🎯 Yang Sudah Dibuat

### 1. **Controllers**

#### ✅ ElectionController (Admin/ElectionController.php)

-   `index()` - List semua elections user
-   `create()` - Form create election
-   `store()` - Save election baru
-   `edit($id)` - Form edit election
-   `update($id)` - Update election
-   `destroy($id)` - Delete election
-   `togglePublish($id)` - Publish/unpublish election

**Multi-Tenancy:** ✅ Semua method menggunakan `Election::forOrganizer(Auth::id())`

#### ✅ CandidateController (Admin/CandidateController.php)

-   `index()` - List semua candidates dari elections user
-   `create()` - Form create candidate
-   `store()` - Save candidate baru (dengan foto upload)
-   `edit($id)` - Form edit candidate
-   `update($id)` - Update candidate
-   `destroy($id)` - Delete candidate

**Multi-Tenancy:** ✅ Validasi election ownership sebelum CRUD

### 2. **Middleware**

#### ✅ EnsureUserIsOrganizer

```php
// Protect admin routes, redirect voter ke voter dashboard
if (Auth::user()->role !== 'organizer') {
    return redirect()->route('voter.dashboard')
        ->with('error', 'Halaman admin hanya untuk penyelenggara.');
}
```

### 3. **Routes**

Semua admin routes sekarang protected dengan middleware:

```php
Route::prefix('admin')->middleware(['auth', EnsureUserIsOrganizer::class])
```

**Resource Routes:**

-   `admin.candidates.*` → CandidateController
-   `admin.elections.*` → ElectionController

### 4. **Features Implemented**

#### ✅ Election CRUD

-   Create dengan title, description, dates, rules, settings
-   Update election details
-   Delete election (soft delete)
-   Publish/Unpublish toggle
-   **Link system**: Setiap election punya unique `access_code`

#### ✅ Candidate CRUD

-   Create dengan photo upload, visi, multiple misi
-   Update candidate data
-   Delete candidate (dengan hapus foto)
-   **Validation**: Nomor urut unique per election

#### ✅ Multi-Tenancy

-   Organizer hanya bisa lihat/edit elections mereka sendiri
-   Candidates terikat ke elections milik organizer
-   Voter tidak bisa akses admin routes

---

## 📝 Update View Files

### Update admin/candidates/manage.blade.php

Ganti konten dengan yang menggunakan database:

```blade
<x-admin-layout title="Kelola Kandidat">
    <x-admin-sidebar active="manage" />

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm z-10">
            <div class="px-8 py-6 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Kelola Kandidat</h1>
                    <p class="text-gray-600 mt-1">{{ $candidates->count() }} kandidat dari pemilu Anda</p>
                </div>
                <a href="{{ route('admin.candidates.create') }}"
                   class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl">
                    <span>+ Tambah Kandidat</span>
                </a>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8">
            @if($candidates->isEmpty())
                <div class="bg-white rounded-2xl shadow-md p-12 text-center">
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">Belum Ada Kandidat</h3>
                    <p class="text-gray-500 mb-6">Tambahkan kandidat untuk memulai pemilu</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($candidates as $candidate)
                        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                            <div class="bg-gray-50 px-8 py-4 border-b">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-full flex items-center justify-center font-bold text-2xl">
                                            {{ $candidate->number }}
                                        </div>
                                        <div>
                                            <h2 class="text-2xl font-bold text-gray-900">{{ $candidate->name }}</h2>
                                            <p class="text-gray-500">{{ $candidate->election->title }}</p>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.candidates.edit', $candidate->id) }}"
                                           class="px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.candidates.destroy', $candidate->id) }}" method="POST"
                                              onsubmit="return confirm('Hapus kandidat {{ $candidate->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-5 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="p-8">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="md:col-span-1">
                                        @if($candidate->photo)
                                            <img src="{{ asset('storage/' . $candidate->photo) }}"
                                                 alt="{{ $candidate->name }}"
                                                 class="w-full rounded-xl shadow-lg">
                                        @else
                                            <div class="w-full aspect-square bg-gray-200 rounded-xl flex items-center justify-center">
                                                <span class="text-gray-400">No Photo</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="md:col-span-2">
                                        <div class="mb-6">
                                            <h3 class="text-lg font-bold text-gray-900 mb-2">Visi</h3>
                                            <p class="text-gray-700 bg-blue-50 p-4 rounded-lg">{{ $candidate->visi }}</p>
                                        </div>

                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900 mb-3">Misi</h3>
                                            <ul class="space-y-2">
                                                @foreach($candidate->missions as $mission)
                                                    <li class="flex items-start space-x-3 bg-gray-50 p-3 rounded-lg">
                                                        <span class="flex-shrink-0 w-6 h-6 bg-purple-600 text-white rounded-full flex items-center justify-center text-xs font-bold">
                                                            {{ $loop->iteration }}
                                                        </span>
                                                        <p class="text-gray-700 pt-0.5">{{ $mission->mission }}</p>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </main>
    </div>
</x-admin-layout>
```

### Update admin/elections/manage.blade.php

```blade
<x-admin-layout title="Kelola Pengaturan Pemilu">
    <x-admin-sidebar active="rules" />

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm z-10">
            <div class="px-8 py-6 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Kelola Pengaturan Pemilu</h1>
                    <p class="text-gray-600 mt-1">{{ $elections->count() }} pemilu Anda</p>
                </div>
                <a href="{{ route('admin.elections.create') }}"
                   class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl">
                    + Buat Pemilu Baru
                </a>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8">
            @if($elections->isEmpty())
                <div class="bg-white rounded-2xl shadow-md p-12 text-center">
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">Belum Ada Pemilu</h3>
                    <p class="text-gray-500 mb-6">Buat pemilu pertama Anda</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($elections as $election)
                        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                            <div class="bg-gray-50 px-8 py-4 border-b">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-900">{{ $election->title }}</h2>
                                        <p class="text-gray-500 mt-1">
                                            @if($election->is_published)
                                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                                    ✓ AKTIF
                                                </span>
                                            @else
                                                <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold">
                                                    DRAFT
                                                </span>
                                            @endif
                                            | {{ $election->candidates->count() }} kandidat | {{ $election->votes_count }} suara
                                        </p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <form action="{{ route('admin.elections.toggle-publish', $election->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="px-5 py-2.5 {{ $election->is_published ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white font-semibold rounded-lg">
                                                {{ $election->is_published ? 'Unpublish' : 'Publish' }}
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.elections.edit', $election->id) }}"
                                           class="px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.elections.delete', $election->id) }}" method="POST"
                                              onsubmit="return confirm('Hapus pemilu {{ $election->title }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-5 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="p-8">
                                <!-- Description -->
                                @if($election->description)
                                    <p class="text-gray-700 mb-4">{{ $election->description }}</p>
                                @endif

                                <!-- Dates -->
                                @if($election->start_date)
                                    <div class="mb-4">
                                        <strong>Periode:</strong>
                                        {{ $election->start_date->format('d M Y') }} - {{ $election->end_date->format('d M Y') }}
                                    </div>
                                @endif

                                <!-- Rules -->
                                <div class="mb-4">
                                    <h3 class="font-bold text-gray-900 mb-2">Peraturan:</h3>
                                    <ul class="space-y-1">
                                        @foreach($election->rules as $rule)
                                            <li class="flex items-start space-x-2">
                                                <span class="text-blue-600">•</span>
                                                <span>{{ $rule->rule }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <!-- Settings -->
                                <div>
                                    <h3 class="font-bold text-gray-900 mb-2">Pengaturan:</h3>
                                    <div class="flex flex-wrap gap-2">
                                        @if($election->settings->allow_abstain)
                                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">Izinkan Golput</span>
                                        @endif
                                        @if($election->settings->show_results_after_vote)
                                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Tampilkan Hasil</span>
                                        @endif
                                        @if($election->settings->require_confirmation)
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">Butuh Konfirmasi</span>
                                        @endif
                                        <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">
                                            Max {{ $election->settings->max_votes_per_voter }} suara/voter
                                        </span>
                                    </div>
                                </div>

                                <!-- Access Code -->
                                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                    <strong>Kode Akses:</strong>
                                    <code class="ml-2 px-3 py-1 bg-white border rounded font-mono text-lg">{{ $election->access_code }}</code>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </main>
    </div>
</x-admin-layout>
```

---

## 🚀 Testing

### 1. Start MySQL

```bash
# Buka XAMPP/WAMP dan start MySQL
```

### 2. Run Migrations

```bash
php artisan migrate:fresh
php artisan db:seed  # optional: untuk data sample
```

### 3. Create Storage Link

```bash
php artisan storage:link
```

### 4. Test Alur Lengkap

#### A. Register & Login

```
1. Logout dari akun sebelumnya
2. Register sebagai Organizer
3. Login dengan akun organizer
4. ✅ Redirect ke /admin/dashboard
```

#### B. Create Election

```
1. Klik "Judul & Peraturan" di sidebar
2. Isi form:
   - Judul: Pemilu Test 2025
   - Deskripsi: Pemilu untuk testing
   - Rules: minimal 1
   - Settings: centang sesuai kebutuhan
3. Submit
4. ✅ Redirect ke manage dengan pesan sukses
5. ✅ Election tercatat dengan access_code unik
```

#### C. Create Candidate

```
1. Klik "Kelola Kandidat" di sidebar
2. Klik "Tambah Kandidat Baru"
3. Isi form:
   - Pilih Election
   - Nomor Urut: 1
   - Nama: Test Kandidat
   - Upload Foto
   - Visi & Misi
4. Submit
5. ✅ Redirect ke manage dengan pesan sukses
6. ✅ Foto tersimpan di storage/app/public/candidate-photos
```

#### D. Edit & Delete

```
1. Di manage page, klik Edit
2. Update data kandidat
3. Submit → ✅ Update berhasil
4. Klik Hapus → confirm → ✅ Delete berhasil
5. ✅ Foto lama terhapus dari storage
```

#### E. Publish Election

```
1. Di manage elections, klik "Publish"
2. ✅ Status berubah jadi AKTIF
3. ✅ Access code bisa digunakan voter
```

---

## 🔒 Security Features

### Multi-Tenancy

✅ Organizer A tidak bisa lihat/edit election Organizer B
✅ Semua query filtered by `user_id`
✅ Middleware protect admin routes

### Validation

✅ Nomor urut unique per election
✅ File upload max 2MB
✅ Date validation (end >= start)
✅ Authorization check before edit/delete

### File Handling

✅ Photo upload ke `storage/app/public/candidate-photos`
✅ Delete old photo on update
✅ Delete photo on candidate delete

---

## 📊 Database Queries

### Get Organizer's Elections

```php
Election::forOrganizer(Auth::id())->get()
```

### Get Candidates dari Elections Organizer

```php
$elections = Election::forOrganizer(Auth::id())->pluck('id');
Candidate::whereIn('election_id', $elections)->get()
```

### Check Ownership

```php
$election = Election::forOrganizer(Auth::id())->findOrFail($id);
// Throws 404 jika bukan milik organizer
```

---

## ✅ Checklist Implementation

-   [x] ElectionController dengan CRUD
-   [x] CandidateController dengan CRUD
-   [x] Middleware EnsureUserIsOrganizer
-   [x] Routes dengan middleware protection
-   [x] Multi-tenancy validation
-   [x] File upload handling
-   [x] Access code generation
-   [x] Publish/Unpublish toggle
-   [x] Validation rules
-   [x] Error handling

---

## 🔗 Next Steps

1. Update view files dengan code di atas
2. Test complete CRUD flow
3. Implement voter access dengan access_code
4. Add real-time stats di dashboard
5. Export election results

Semua backend CRUD sudah siap! Tinggal update view files.
