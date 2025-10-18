# PENTING: Cara Registrasi Akun Baru

## Masalah

Ketika Anda sudah login dan mencoba akses halaman registrasi, Laravel akan otomatis redirect ke dashboard. Ini adalah fitur keamanan Laravel untuk mencegah user yang sudah login mengakses halaman registrasi.

## Solusi

### ✅ Cara Registrasi Akun Baru:

1. **LOGOUT TERLEBIH DAHULU**

    - Klik tombol **"Keluar"** (merah) di navbar atas
    - Atau akses `/logout` secara manual

2. **Buka Halaman Registrasi**

    - Klik tombol **"Daftar"** di navbar
    - Atau akses langsung ke `/register`

3. **Pilih Tipe Akun**

    - **Penyelenggara (Organizer)** - Untuk membuat dan mengelola pemilu
    - **Pemilih (Voter)** - Untuk memberikan suara

4. **Isi Form Registrasi**

    - Nama lengkap
    - Email (harus unik)
    - Password (minimal 8 karakter)
    - Konfirmasi password

5. **Klik "Sign In"**
    - Setelah registrasi, Anda akan diarahkan ke halaman login
    - Login dengan email dan password yang baru dibuat

## Navbar Baru

Navbar sudah diupdate dengan fitur:

-   ✅ **Avatar user** dengan inisial nama
-   ✅ **Role indicator** (Penyelenggara/Pemilih/Admin)
-   ✅ **Dashboard link** sesuai role
-   ✅ **Tombol Keluar** yang jelas (warna merah)
-   ✅ **Notifikasi** untuk registrasi berhasil

## Testing

### Test Registrasi Organizer:

```
1. Logout dari akun sekarang
2. Daftar sebagai Organizer
   - Email: organizer@test.com
   - Password: password123
3. Login dengan akun baru
4. Akan diarahkan ke Admin Dashboard
```

### Test Registrasi Voter:

```
1. Logout dari akun sekarang
2. Daftar sebagai Voter
   - Email: voter@test.com
   - Password: password123
3. Login dengan akun baru
4. Akan diarahkan ke Voter Dashboard
```

## Alur Lengkap

```
┌─────────────────────┐
│   Sudah Login?      │
│   (Ada navbar user) │
└──────────┬──────────┘
           │
           ▼
    ┌──────────────┐
    │  Klik KELUAR │
    └──────┬───────┘
           │
           ▼
    ┌──────────────┐
    │  Klik DAFTAR │
    └──────┬───────┘
           │
           ▼
    ┌──────────────────┐
    │ Pilih Tipe Akun  │
    │ Organizer/Voter  │
    └──────┬───────────┘
           │
           ▼
    ┌──────────────────┐
    │  Isi Form & Send │
    └──────┬───────────┘
           │
           ▼
    ┌──────────────────┐
    │ Halaman LOGIN    │
    │ (Pesan: Sukses!) │
    └──────┬───────────┘
           │
           ▼
    ┌──────────────────┐
    │  Login & Masuk   │
    │  ke Dashboard    │
    └──────────────────┘
```

## Middleware Explanation

Laravel menggunakan middleware `guest` pada route registrasi/login yang bekerja seperti ini:

```php
Route::middleware('guest')->group(function () {
    Route::get('register', ...);  // Hanya bisa diakses jika BELUM login
    Route::post('register', ...);
    Route::get('login', ...);
    Route::post('login', ...);
});
```

**Middleware `guest`:**

-   ✅ Mengizinkan: User yang BELUM login
-   ❌ Menolak: User yang SUDAH login (redirect ke dashboard)

Ini adalah **fitur keamanan** Laravel untuk mencegah:

-   User membuat akun ganda saat sudah login
-   Confusion tentang akun mana yang aktif
-   Security issues

## Quick Commands

```bash
# Check current logged in user
php artisan tinker
>>> Auth::user()

# Clear all sessions (jika perlu)
php artisan session:clear
```

---

**INGAT:** Selalu **LOGOUT** dulu sebelum registrasi akun baru!
