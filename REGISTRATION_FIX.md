# Perbaikan Role & Redirect Registrasi

## Masalah yang Diperbaiki

### 1. **Role Selalu Menjadi `voter`**

**Masalah:** Ketika registrasi sebagai organizer, role tetap jadi `voter`
**Penyebab:** `RegisteredUserController` tidak membedakan tipe registrasi
**Solusi:** Membuat 2 controller terpisah:

-   `OrganizerRegisterController` - Set role = `'organizer'`
-   `VoterRegisterController` - Set role = `'voter'`

### 2. **Redirect ke `/dashboard` Setelah Login**

**Masalah:** Setelah login, diarahkan ke `/dashboard` yang tidak ada
**Penyebab:** Route default tidak sesuai dengan role
**Solusi:** Update `AuthenticatedSessionController` untuk redirect berdasarkan role

---

## File yang Diubah

### 1. Controller Baru

#### `OrganizerRegisterController.php`

```php
public function store(Request $request): RedirectResponse
{
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'organizer', // ✅ Set role sebagai organizer
        'organization' => $request->organization,
    ]);

    return redirect()->route('login')
        ->with('status', '✓ Registrasi sebagai Penyelenggara berhasil!');
}
```

#### `VoterRegisterController.php`

```php
public function store(Request $request): RedirectResponse
{
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'voter', // ✅ Set role sebagai voter
        'id_number' => $request->id_number,
        'organization' => $request->organization,
        'face_photo' => $facePhotoPath,
    ]);

    return redirect()->route('login')
        ->with('status', '✓ Registrasi sebagai Pemilih berhasil!');
}
```

### 2. Routes Updated

**File:** `routes/web.php`

```php
use App\Http\Controllers\Auth\OrganizerRegisterController;
use App\Http\Controllers\Auth\VoterRegisterController;

// Registration Routes dengan Controller
Route::get('/register/organizer', [OrganizerRegisterController::class, 'create'])
    ->middleware('guest')
    ->name('register.organizer');

Route::post('/register/organizer', [OrganizerRegisterController::class, 'store'])
    ->middleware('guest')
    ->name('register.organizer.store');

Route::get('/register/voter', [VoterRegisterController::class, 'create'])
    ->middleware('guest')
    ->name('register.voter');

Route::post('/register/voter', [VoterRegisterController::class, 'store'])
    ->middleware('guest')
    ->name('register.voter.store');
```

### 3. Login Redirect Fixed

**File:** `AuthenticatedSessionController.php`

```php
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    $user = Auth::user();

    // ✅ Redirect berdasarkan role
    if ($user->role === 'organizer') {
        return redirect()->intended(route('admin.dashboard', absolute: false));
    } elseif ($user->role === 'voter') {
        return redirect()->intended(route('voter.dashboard', absolute: false));
    }

    return redirect()->intended(route('dashboard', absolute: false));
}
```

---

## Alur Registrasi Sekarang

### Organizer Registration Flow:

```
1. Pilih "Daftar sebagai Penyelenggara"
   ↓
2. Isi form:
   - Nama Lengkap
   - Email
   - Organization Name ✅
   - Phone Number
   - Password
   ↓
3. Submit → OrganizerRegisterController
   ↓
4. User dibuat dengan role='organizer' ✅
   ↓
5. Redirect ke /login dengan pesan sukses
   ↓
6. Login → Redirect ke /admin/dashboard ✅
```

### Voter Registration Flow:

```
1. Pilih "Daftar sebagai Pemilih"
   ↓
2. Isi form:
   - Nama Lengkap
   - Email
   - ID Number (NIK/NIM) ✅
   - Organization (optional)
   - Face Photo (optional)
   - Password
   ↓
3. Submit → VoterRegisterController
   ↓
4. User dibuat dengan role='voter' ✅
   ↓
5. Redirect ke /login dengan pesan sukses
   ↓
6. Login → Redirect ke /voter ✅
```

---

## Testing

### Test 1: Registrasi Organizer

```bash
1. Logout dari akun sekarang
2. Buka /register
3. Klik "Daftar sebagai Penyelenggara"
4. Isi form:
   - Name: Test Organizer
   - Email: organizer@test.com
   - Organization: Test Organization
   - Phone: 08123456789
   - Password: password123
5. Submit
6. Cek pesan sukses di halaman login
7. Login dengan email & password
8. ✅ Harus redirect ke /admin/dashboard
9. ✅ Navbar harus tampil "👔 Penyelenggara"
```

### Test 2: Registrasi Voter

```bash
1. Logout dari akun sekarang
2. Buka /register
3. Klik "Daftar sebagai Pemilih"
4. Isi form:
   - Name: Test Voter
   - Email: voter@test.com
   - ID Number: 1234567890
   - Organization: Test School
   - Password: password123
5. Submit
6. Cek pesan sukses di halaman login
7. Login dengan email & password
8. ✅ Harus redirect ke /voter
9. ✅ Navbar harus tampil "🗳️ Pemilih"
```

### Verifikasi Database

```bash
php artisan tinker

# Check organizer
>>> User::where('email', 'organizer@test.com')->first()->role
=> "organizer" ✅

# Check voter
>>> User::where('email', 'voter@test.com')->first()->role
=> "voter" ✅
```

---

## Validation Rules

### Organizer Registration

-   ✅ `name` - required, string, max:255
-   ✅ `email` - required, unique, valid email
-   ✅ `password` - required, confirmed, min:8
-   ✅ `organization` - optional, string, max:255

### Voter Registration

-   ✅ `name` - required, string, max:255
-   ✅ `email` - required, unique, valid email
-   ✅ `password` - required, confirmed, min:8
-   ✅ `id_number` - required, unique, max:50
-   ✅ `organization` - optional, string, max:255
-   ✅ `face_photo` - optional, image, max:2MB

---

## Error Handling

### Duplicate Email

```
Error: "The email has already been taken."
Solution: Gunakan email yang berbeda
```

### Duplicate ID Number (Voter)

```
Error: "The id number has already been taken."
Solution: Gunakan ID number yang berbeda
```

### Password Mismatch

```
Error: "The password confirmation does not match."
Solution: Pastikan password dan confirm password sama
```

### File Too Large (Face Photo)

```
Error: "The face photo must not be greater than 2048 kilobytes."
Solution: Compress foto atau gunakan foto yang lebih kecil
```

---

## Summary

✅ **Role sistem sudah benar:**

-   Registrasi Organizer → role = `'organizer'`
-   Registrasi Voter → role = `'voter'`

✅ **Redirect sudah benar:**

-   Organizer login → `/admin/dashboard`
-   Voter login → `/voter`

✅ **Validation lengkap:**

-   Email unique
-   ID number unique (untuk voter)
-   Password confirmation
-   File upload handling (face photo)

✅ **User experience:**

-   Pesan sukses setelah registrasi
-   Navbar menampilkan role dengan icon
-   Tombol logout yang jelas
