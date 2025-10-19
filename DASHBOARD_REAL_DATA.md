# Dashboard Real Data Integration

## Ringkasan Perubahan

Dashboard admin telah diupdate untuk menampilkan data statistik yang sebenarnya dari database, menggantikan data dummy/hardcoded sebelumnya.

## Perubahan yang Dilakukan

### 1. Route Dashboard (`routes/web.php`)

**Sebelum:**

-   Menghitung statistik dari semua elections
-   Menggunakan data aggregate: total_elections, active_elections, total_candidates, total_votes

**Sesudah:**

-   Mengambil election milik organizer yang sedang login
-   Menghitung statistik voter yang sebenarnya:
    -   `total_voters`: Total pemilih yang join election (dari pivot table election_user)
    -   `voted`: Jumlah pemilih yang sudah voting (distinct voter_id dari votes table)
    -   `not_voted`: Selisih antara total_voters dan voted
    -   `participation_rate`: Persentase tingkat partisipasi
-   Mengambil statistik kandidat dengan vote count dari database
-   Mengurutkan kandidat berdasarkan jumlah suara (descending)

### 2. View Dashboard (`resources/views/admin/dashboard.blade.php`)

#### Card Statistik

**Card 1 - Total Pemilih:**

-   Sebelum: Hardcoded "2,847"
-   Sesudah: `{{ number_format($stats['total_voters']) }}`

**Card 2 - Sudah Memilih:**

-   Sebelum: Hardcoded "1,453" dengan "51%"
-   Sesudah: `{{ number_format($stats['voted']) }}` dengan persentase dinamis

**Card 3 - Belum Memilih:**

-   Sebelum: Hardcoded "1,394" dengan "49%"
-   Sesudah: `{{ number_format($stats['not_voted']) }}` dengan persentase dinamis

**Card 4 - Tidak Memilih:**

-   Sebelum: "Tingkat Partisipasi" dengan hardcoded "51%"
-   Sesudah: "Tidak Memilih" menampilkan jumlah yang belum voting + tingkat partisipasi

#### Grafik Kandidat

**Sebelum:**

-   Labels: Hardcoded ['Kandidat A', 'Kandidat B', 'Kandidat C', 'Kandidat D']
-   Data: Hardcoded [542, 438, 325, 148]
-   Legend: Hardcoded 4 kandidat dengan data static

**Sesudah:**

-   Labels: Dinamis dari `$candidateStats->map(c => c.name)`
-   Data: Dinamis dari `$candidateStats->map(c => c.votes)`
-   Legend: Loop @foreach untuk menampilkan semua kandidat dengan warna dinamis
-   Support hingga 8 kandidat dengan warna berbeda
-   Fallback: Jika belum ada kandidat, tampilkan "Belum ada data"

### 3. Teknologi yang Digunakan

-   **Eloquent ORM**: Query builder untuk mengambil data
-   **Relationships**:
    -   `election->participants()`: Many-to-Many untuk voter
    -   `election->votes()`: One-to-Many untuk voting records
    -   `candidate->votes()`: One-to-Many untuk vote count
-   **Aggregation**: `withCount()`, `distinct()`, `count()`
-   **Chart.js**: Visualisasi data kandidat dalam bentuk bar dan pie chart

## Struktur Data

### Stats Array

```php
[
    'total_voters' => int,      // Total pemilih terdaftar
    'voted' => int,             // Jumlah yang sudah voting
    'not_voted' => int,         // Jumlah yang belum voting
    'participation_rate' => float, // Persentase partisipasi (0-100)
]
```

### Candidate Stats Collection

```php
[
    [
        'name' => string,       // Nama kandidat
        'votes' => int,         // Jumlah suara
    ],
    // ... kandidat lainnya
]
```

## Fitur Tambahan

1. **Number Formatting**: Menggunakan `number_format()` untuk format angka (1,234)
2. **Dynamic Percentage**: Menghitung persentase secara otomatis dari data
3. **Responsive Colors**: Array warna untuk mendukung banyak kandidat
4. **Empty State**: Menampilkan pesan "Belum ada data" jika tidak ada kandidat
5. **Sorted Data**: Kandidat diurutkan berdasarkan perolehan suara terbanyak

## Testing

1. Login sebagai organizer
2. Akses `/admin/dashboard`
3. Pastikan statistik menampilkan data yang benar:
    - Total Pemilih = jumlah user yang join election
    - Sudah Memilih = jumlah user yang sudah vote
    - Belum Memilih = Total - Sudah
    - Grafik menampilkan semua kandidat dengan vote count yang benar

## Catatan Penting

-   Dashboard hanya menampilkan data dari **election pertama** milik organizer (karena sistem 1 user = 1 election)
-   Jika organizer belum membuat election, semua statistik akan menampilkan 0
-   Vote count kandidat dihitung secara real-time dari database
-   Partisipasi rate dihitung sebagai: (voted / total_voters) \* 100%

## File yang Dimodifikasi

1. `routes/web.php` - Route dashboard logic
2. `resources/views/admin/dashboard.blade.php` - View template

## Dependencies

-   Model: `Election`, `Candidate`, `Vote`, `User`
-   Relationships: `participants()`, `votes()`, `candidates()`
-   Laravel Collection methods: `map()`, `count()`, `isNotEmpty()`
