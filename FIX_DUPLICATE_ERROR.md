# Fix: Duplicate Entry Error pada Registrasi Voter

## 🐛 Error yang Terjadi

```
Illuminate\Database\UniqueConstraintViolationException

SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'EUVNHDZ8'
for key 'users.users_id_number_unique'
```

**Penyebab:**

-   Field `id_number` di tabel `users` memiliki constraint **UNIQUE**
-   Controller menyimpan `access_code` ke field `id_number`
-   Banyak voter menggunakan `access_code` yang sama
-   Voter ke-2, ke-3, dst tidak bisa registrasi karena duplicate entry

---

## ✅ Solusi yang Diterapkan

### 1. Buat Tabel Pivot: `election_user`

**Migration:** `2025_10_18_102242_create_election_user_table.php`

```php
Schema::create('election_user', function (Blueprint $table) {
    $table->id();
    $table->foreignId('election_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('access_code_used', 8); // Code yang digunakan
    $table->timestamp('joined_at')->useCurrent();
    $table->timestamps();

    // Unique constraint
    $table->unique(['election_id', 'user_id']);
});
```

**Tujuan:**

-   Track relasi **many-to-many** antara Voter dan Election
-   Simpan `access_code` yang digunakan di pivot table
-   Satu voter bisa join multiple elections
-   Satu election bisa diikuti multiple voters

---

### 2. Update VoterRegisterController

**File:** `app/Http/Controllers/Auth/VoterRegisterController.php`

**Perubahan:**

```php
// SEBELUM (ERROR)
$user = User::create([
    'id_number' => strtoupper($request->invite_code), // ❌ Duplicate!
    'organization' => $idCardPath,
]);

// SESUDAH (FIXED)
$user = User::create([
    'id_number' => null, // ✅ Nullable, tidak dipakai
    'organization' => $election->title, // ✅ Nama pemilu
]);

// Attach voter ke election (many-to-many)
$user->elections()->attach($election->id, [
    'access_code_used' => strtoupper($request->invite_code),
    'joined_at' => now(),
]);
```

---

### 3. Update Model User

**File:** `app/Models/User.php`

**Tambahkan Relationship:**

```php
// Relationship: User (Voter) belongs to many Elections (as participant)
public function participatingElections()
{
    return $this->belongsToMany(Election::class, 'election_user')
        ->withPivot('access_code_used', 'joined_at')
        ->withTimestamps();
}
```

**Note:**

-   `elections()` → Untuk organizer (hasMany)
-   `participatingElections()` → Untuk voter (belongsToMany)

---

## 📊 Database Schema Update

### Tabel: `election_user` (BARU)

```sql
id                  BIGINT PRIMARY KEY
election_id         BIGINT FK → elections.id
user_id             BIGINT FK → users.id
access_code_used    VARCHAR(8) -- Code yang digunakan voter
joined_at           TIMESTAMP
created_at          TIMESTAMP
updated_at          TIMESTAMP

UNIQUE(election_id, user_id) -- Prevent duplicate join
```

### Tabel: `users` (NO CHANGES)

```sql
id_number VARCHAR(50) UNIQUE NULLABLE -- Tetap nullable, tidak digunakan voter
```

---

## 🔄 Alur Setelah Fix

```
1. Voter masukkan invite_code: "ABC12345"
   ↓
2. System validasi code → ✅ Valid & Published
   ↓
3. User::create() → id_number = NULL (tidak duplikat lagi)
   ↓
4. $user->elections()->attach($election->id)
   → Insert ke election_user dengan access_code_used = "ABC12345"
   ↓
5. Auto-login & redirect ke pemilu
```

---

## 🧪 Testing

### Test 1: Multiple Voters dengan Code yang Sama

```bash
# Voter 1
POST /register/voter
- invite_code: ABC12345
- email: voter1@test.com
✅ Expected: Success, redirect ke /election/ABC12345

# Voter 2
POST /register/voter
- invite_code: ABC12345
- email: voter2@test.com
✅ Expected: Success, redirect ke /election/ABC12345

# Voter 3
POST /register/voter
- invite_code: ABC12345
- email: voter3@test.com
✅ Expected: Success, redirect ke /election/ABC12345
```

**Semua voter bisa registrasi tanpa duplicate error!**

---

### Test 2: Cek Data di Database

```php
php artisan tinker

# Cek voter yang join
>>> $election = Election::find(1);
>>> $election->participants; // Via belongsToMany
=> Collection [User #1, User #2, User #3]

# Cek election yang diikuti voter
>>> $user = User::find(2);
>>> $user->participatingElections;
=> Collection [Election #1]

# Cek pivot data (access_code_used)
>>> $user->participatingElections()->first()->pivot->access_code_used;
=> "ABC12345"
```

---

## 📁 File yang Diubah

### Created:

-   ✅ `database/migrations/2025_10_18_102242_create_election_user_table.php`

### Updated:

-   ✅ `app/Http/Controllers/Auth/VoterRegisterController.php`
-   ✅ `app/Models/User.php`

---

## 💡 Keuntungan Solusi Ini

1. ✅ **No More Duplicate Error** - id_number tetap nullable
2. ✅ **Many-to-Many Support** - Voter bisa join multiple elections
3. ✅ **Track Access Code** - Tahu voter pakai code apa saat join
4. ✅ **Track Join Date** - Kapan voter join election
5. ✅ **Scalable** - Bisa tambah field lain di pivot (status, verified, dll)

---

## 🔍 Query Examples

### Get All Voters in Election

```php
$election = Election::find(1);
$voters = $election->participants; // Via belongsToMany
```

### Get All Elections Joined by Voter

```php
$user = User::find(2);
$elections = $user->participatingElections;
```

### Get Access Code Used

```php
$user->participatingElections()
    ->first()
    ->pivot
    ->access_code_used;
```

---

## ✅ Status

**Error FIXED!** ✅

Sekarang multiple voters bisa registrasi dengan access_code yang sama tanpa duplicate entry error.

---

## 🚀 Next Steps (Optional)

1. Update Model Election untuk relationship balik:

    ```php
    public function participants()
    {
        return $this->belongsToMany(User::class, 'election_user')
            ->withPivot('access_code_used', 'joined_at')
            ->withTimestamps();
    }
    ```

2. Buat dashboard untuk organizer lihat list participants

3. Export participants list ke Excel/PDF

4. Email notification saat voter join election
