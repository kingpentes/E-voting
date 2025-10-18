# Database Schema - E-Voting System

## Overview

Sistem E-Voting dengan multi-tenancy di mana setiap penyelenggara (organizer) memiliki pemilu mereka sendiri yang terisolasi dari penyelenggara lain.

## Struktur Database

### 1. Users Table

Tabel pengguna untuk menyimpan data organizer, voter, dan admin.

```sql
- id (PK)
- name
- email (unique)
- password
- role (enum: 'organizer', 'voter', 'admin')
- id_number (unique, nullable) - NIK/NIM untuk voter
- face_photo (nullable) - Path foto wajah untuk verifikasi
- organization (nullable) - Institusi/Organisasi
- email_verified_at
- remember_token
- timestamps
```

**Relationships:**

-   Has many `elections` (untuk organizer)
-   Has many `votes` (untuk voter)

---

### 2. Elections Table

Tabel pemilu yang dibuat oleh organizer.

```sql
- id (PK)
- user_id (FK → users.id) - ID Organizer
- title - Judul pemilu
- description (nullable) - Deskripsi pemilu
- start_date (nullable) - Tanggal mulai
- end_date (nullable) - Tanggal berakhir
- start_time (nullable) - Jam mulai
- end_time (nullable) - Jam berakhir
- status (enum: 'draft', 'active', 'completed') - Status pemilu
- is_published (boolean) - Apakah sudah dipublish
- access_code (unique) - Kode akses untuk voter
- timestamps
- deleted_at (soft delete)
```

**Relationships:**

-   Belongs to `user` (organizer)
-   Has many `candidates`
-   Has many `rules`
-   Has one `settings`
-   Has many `votes`

**Key Features:**

-   Setiap election terikat ke satu organizer (user_id)
-   Access code auto-generate saat pembuatan
-   Soft delete untuk history

---

### 3. Election Rules Table

Tabel aturan/peraturan pemilu.

```sql
- id (PK)
- election_id (FK → elections.id)
- rule (text) - Isi aturan
- order - Urutan aturan
- timestamps
```

**Relationships:**

-   Belongs to `election`

---

### 4. Election Settings Table

Tabel pengaturan pemilu.

```sql
- id (PK)
- election_id (FK → elections.id)
- allow_abstain (boolean) - Izinkan golput
- show_results_after_vote (boolean) - Tampilkan hasil setelah vote
- require_confirmation (boolean) - Butuh konfirmasi sebelum submit
- allow_vote_change (boolean) - Izinkan ubah pilihan
- max_votes_per_voter (integer) - Maksimal pilih berapa kandidat
- timestamps
```

**Relationships:**

-   Belongs to `election`

---

### 5. Candidates Table

Tabel kandidat dalam pemilu.

```sql
- id (PK)
- election_id (FK → elections.id)
- number - Nomor urut kandidat
- name - Nama kandidat
- photo (nullable) - Path foto kandidat
- visi (text) - Visi kandidat
- vote_count (integer, default: 0) - Cache jumlah suara
- timestamps
- deleted_at (soft delete)
- UNIQUE(election_id, number) - Nomor urut unik per pemilu
```

**Relationships:**

-   Belongs to `election`
-   Has many `missions`
-   Has many `votes`

**Key Features:**

-   Nomor urut unik per pemilu
-   Vote count untuk optimasi query
-   Soft delete

---

### 6. Candidate Missions Table

Tabel misi kandidat.

```sql
- id (PK)
- candidate_id (FK → candidates.id)
- mission (text) - Isi misi
- order - Urutan misi
- timestamps
```

**Relationships:**

-   Belongs to `candidate`

---

### 7. Votes Table

Tabel suara pemilih.

```sql
- id (PK)
- election_id (FK → elections.id)
- voter_id (FK → users.id)
- candidate_id (FK → candidates.id, nullable) - Null untuk golput
- vote_hash (unique) - Hash untuk verifikasi
- ip_address (nullable)
- user_agent (nullable)
- voted_at (timestamp)
- timestamps
- UNIQUE(election_id, voter_id) - Satu voter satu suara per pemilu
```

**Relationships:**

-   Belongs to `election`
-   Belongs to `voter` (user)
-   Belongs to `candidate` (nullable untuk abstain)

**Key Features:**

-   Vote hash untuk verifikasi anonim
-   Satu voter hanya bisa vote sekali per pemilu
-   IP dan user agent untuk audit trail

---

## Multi-Tenancy Implementation

### Isolation Strategy

Setiap organizer hanya bisa mengakses pemilu mereka sendiri melalui:

1. **Model Scope:**

```php
// Di Election Model
public function scopeForOrganizer($query, $userId)
{
    return $query->where('user_id', $userId);
}

// Penggunaan:
$myElections = Election::forOrganizer(auth()->id())->get();
```

2. **Route Middleware:**

```php
// Middleware untuk memastikan organizer hanya akses election mereka
if ($election->user_id !== auth()->id()) {
    abort(403, 'Unauthorized');
}
```

3. **Database Constraint:**

-   Foreign key `user_id` di table `elections` memastikan ownership
-   Semua data terkait (candidates, rules, settings) terhubung via `election_id`

### Data Access Flow

**Organizer 1:**

```
User (ID: 1)
  └── Elections (user_id = 1)
        ├── Candidates
        ├── Rules
        ├── Settings
        └── Votes
```

**Organizer 2:**

```
User (ID: 2)
  └── Elections (user_id = 2)
        ├── Candidates
        ├── Rules
        ├── Settings
        └── Votes
```

**Voter:**

-   Dapat mengakses election dengan `access_code`
-   Dapat vote di multiple elections (dari organizer berbeda)
-   Tidak bisa melihat management interface

---

## Migration Files

File migration yang sudah dibuat:

1. `2025_01_18_000001_create_elections_table.php`
2. `2025_01_18_000002_create_election_rules_table.php`
3. `2025_01_18_000003_create_election_settings_table.php`
4. `2025_01_18_000004_create_candidates_table.php`
5. `2025_01_18_000005_create_candidate_missions_table.php`
6. `2025_01_18_000006_create_votes_table.php`
7. `2025_01_18_000007_add_role_to_users_table.php`

## Models Created

1. `Election.php` - Model pemilu dengan scopes dan helpers
2. `ElectionRule.php` - Model aturan pemilu
3. `ElectionSetting.php` - Model pengaturan pemilu
4. `Candidate.php` - Model kandidat dengan vote tracking
5. `CandidateMission.php` - Model misi kandidat
6. `Vote.php` - Model suara dengan hash generation
7. `User.php` - Updated dengan role system

## Seeder

`ElectionSeeder.php` - Membuat data sample:

-   2 Organizer
-   2 Elections (1 active, 1 draft)
-   Multiple candidates dengan visi/misi
-   Rules dan settings
-   5 voters

---

## Setup Instructions

### 1. Pastikan MySQL berjalan

Jalankan XAMPP/WAMP atau MySQL service:

```bash
# Windows - Buka XAMPP Control Panel dan start MySQL
```

### 2. Create Database

```sql
CREATE DATABASE evoting;
```

### 3. Run Migrations

```bash
php artisan migrate:fresh
```

### 4. Run Seeder (Optional)

```bash
php artisan db:seed
```

### 5. Test Login

-   Organizer 1: `organizer1@example.com` / `password`
-   Organizer 2: `organizer2@example.com` / `password`
-   Voter: `voter1@example.com` / `password`

---

## Security Features

1. **Access Control:**

    - Organizer hanya bisa CRUD election mereka sendiri
    - Voter hanya bisa vote di published elections
    - Vote hash untuk anonymous verification

2. **Data Integrity:**

    - Foreign key constraints
    - Unique constraints (election_id + voter_id, election_id + number)
    - Soft deletes untuk audit trail

3. **Vote Privacy:**
    - Vote hash tidak terkait langsung dengan voter identity
    - IP dan user agent tersimpan untuk audit tapi tidak ditampilkan

---

## Query Examples

### Get Organizer's Elections

```php
$elections = Election::forOrganizer(auth()->id())
    ->with(['candidates', 'rules', 'settings'])
    ->get();
```

### Get Active Elections for Voter

```php
$elections = Election::published()
    ->active()
    ->whereDate('start_date', '<=', now())
    ->whereDate('end_date', '>=', now())
    ->get();
```

### Check if Voter Already Voted

```php
$hasVoted = auth()->user()->hasVotedIn($electionId);
```

### Get Election Results

```php
$election = Election::with(['candidates.votes'])->find($id);
$results = $election->candidates->map(function($candidate) {
    return [
        'name' => $candidate->name,
        'votes' => $candidate->vote_count,
        'percentage' => $candidate->vote_percentage,
    ];
});
```

---

## Next Steps

1. Implement Authentication (Laravel Breeze/Jetstream)
2. Create Controllers untuk CRUD operations
3. Add Middleware untuk role-based access
4. Implement voting system dengan verification
5. Create dashboard untuk organizer
6. Add real-time results dengan broadcasting
