# Spesifikasi Basis Data & Skema Migrasi (DATABASE_SCHEMA.md)

**Proyek:** Sistem Informasi Pencarian Kos Terintegrasi Berbasis REST API dengan Layanan Lokasi  
**Target RDBMS:** MySQL 8.x / MariaDB 10.x  
**Framework:** Laravel (Eloquent ORM)  
**Encoding / Collation:** `utf8mb4` / `utf8mb4_unicode_ci`

---

## 1. Diagram Relasi Entitas (ERD Konseptual)

```
 [ users / pengguna ]
       │ 1
       │
       ├─────────────── (1:N) ───────────────┐
       │                                      │
       ▼ N (pemilik_id)                       ▼ N (pengguna_id)
    [ kos ] ◄── (1:N) ──┐                  [ pemesanan ]
       │ 1              │                     ▲
       │                │                     │
       ├─ (1:N) ─► [ kamar ] ─── (1:N) ───────┤
       │              1                       │ (kamar_id)
       ▼ M                                    │
 [ kos_fasilitas ] (Pivot M:N)                │ (kos_id)
       ▲ N                                    │
       │                                      │
 [ fasilitas ] ───────────────────────────────┘
```

### Ringkasan Relasi Kardinalitas:
1. `pengguna` (role: pemilik) $\rightarrow$ `kos`: **One-to-Many (1:N)**. Satu pemilik dapat memiliki banyak kos.
2. `kos` $\rightarrow$ `kamar`: **One-to-Many (1:N)**. Satu kos memiliki banyak kamar. Jika kos dihapus, kamar ikut terhapus (*Cascade*).
3. `kos` $\leftrightarrow$ `fasilitas`: **Many-to-Many (M:N)** via pivot table `kos_fasilitas`.
4. `pengguna` (role: pencari) $\rightarrow$ `pemesanan`: **One-to-Many (1:N)**. Satu pencari dapat mengajukan banyak pemesanan.
5. `kamar` $\rightarrow$ `pemesanan`: **One-to-Many (1:N)**. Satu unit kamar dapat tercatat dalam beberapa pemesanan historis.

---

## 2. Definisi Detail Tabel & Kolom

### 2.1. Tabel: `pengguna` (users)
Menyimpan akun terdaftar untuk ketiga role (Pencari, Pemilik, Admin).

| Kolom | Tipe Data | Nullable | Default | Keterangan & Aturan Validasi |
| :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto Increment | Primary Key |
| `nama` | `VARCHAR(150)` | No | - | Nama lengkap pengguna |
| `email` | `VARCHAR(191)` | No | - | Email unik untuk login |
| `password` | `VARCHAR(255)` | No | - | Hasil hash (Bcrypt / Argon2id) |
| `role` | `ENUM('pencari', 'pemilik', 'admin')` | No | `'pencari'` | Hak akses RBAC |
| `aktif` | `BOOLEAN` | No | `true` | Status aktivasi akun (dikelola Admin) |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | Waktu pendaftaran |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | Waktu pembaruan data |

* **Index & Constraints:**
  * `PRIMARY KEY (id)`
  * `UNIQUE KEY users_email_unique (email)`
  * `INDEX users_role_index (role)`

---

### 2.2. Tabel: `kos`
Menyimpan data properti kos, harga terendah sebagai patokan, serta koordinat geocoding.

| Kolom | Tipe Data | Nullable | Default | Keterangan & Aturan Validasi |
| :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto Increment | Primary Key |
| `pemilik_id` | `BIGINT UNSIGNED` | No | - | FK merujuk ke `pengguna.id` |
| `nama` | `VARCHAR(150)` | No | - | Nama properti kos |
| `alamat` | `TEXT` | No | - | Alamat fisik lengkap untuk geocoding |
| `deskripsi` | `TEXT` | Yes | `NULL` | Ulasan fasilitas dan lingkungan |
| `jenis` | `ENUM('putra', 'putri', 'campur')` | No | - | Kategori penghuni |
| `harga` | `INT UNSIGNED` | No | - | Harga acuan per bulan (untuk filter awal) |
| `latitude` | `DECIMAL(10, 7)` | Yes | `NULL` | Koordinat lintang dari Geocoding API |
| `longitude` | `DECIMAL(10, 7)` | Yes | `NULL` | Koordinat bujur dari Geocoding API |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | Timestamps |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | Timestamps |

* **Index & Constraints:**
  * `PRIMARY KEY (id)`
  * `FOREIGN KEY (pemilik_id) REFERENCES pengguna(id) ON DELETE CASCADE`
  * `INDEX kos_jenis_harga_index (jenis, harga)` (Mempercepat query filter PRD FR-11)
  * `FULLTEXT INDEX kos_pencarian_fulltext (nama, alamat)` (Untuk parameter query `q`)

---

### 2.3. Tabel: `kamar`
Menyimpan unit kamar di dalam suatu kos beserta ketersediaannya.

| Kolom | Tipe Data | Nullable | Default | Keterangan & Aturan Validasi |
| :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto Increment | Primary Key |
| `kos_id` | `BIGINT UNSIGNED` | No | - | FK merujuk ke `kos.id` |
| `nama_kamar` | `VARCHAR(50)` | No | - | Nomor/label kamar (misal: "Kamar A-01") |
| `harga` | `INT UNSIGNED` | No | - | Harga sewa per bulan unit spesifik |
| `status` | `ENUM('tersedia', 'terisi')` | No | `'tersedia'` | Ketersediaan kamar |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | Timestamps |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | Timestamps |

* **Index & Constraints:**
  * `PRIMARY KEY (id)`
  * `FOREIGN KEY (kos_id) REFERENCES kos(id) ON DELETE CASCADE`
  * `INDEX kamar_status_index (status)`

---

### 2.4. Tabel: `fasilitas`
Master data fasilitas yang tersedia di platform.

| Kolom | Tipe Data | Nullable | Default | Keterangan & Aturan Validasi |
| :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto Increment | Primary Key |
| `nama` | `VARCHAR(100)` | No | - | Nama fasilitas (misal: Wi-Fi, AC) |
| `deskripsi` | `TEXT` | Yes | `NULL` | Keterangan tambahan |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | Timestamps |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | Timestamps |

* **Index & Constraints:**
  * `PRIMARY KEY (id)`
  * `UNIQUE KEY fasilitas_nama_unique (nama)`

---

### 2.5. Tabel Pivot: `kos_fasilitas`
Tabel relasi many-to-many antara `kos` dan `fasilitas`.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| `kos_id` | `BIGINT UNSIGNED` | No | - | FK merujuk ke `kos.id` |
| `fasilitas_id` | `BIGINT UNSIGNED` | No | - | FK merujuk ke `fasilitas.id` |

* **Index & Constraints:**
  * `PRIMARY KEY (kos_id, fasilitas_id)`
  * `FOREIGN KEY (kos_id) REFERENCES kos(id) ON DELETE CASCADE`
  * `FOREIGN KEY (fasilitas_id) REFERENCES fasilitas(id) ON DELETE CASCADE`

---

### 2.6. Tabel: `pemesanan`
Mencatat transaksi pengajuan sewa kamar oleh calon penghuni.

| Kolom | Tipe Data | Nullable | Default | Keterangan & Aturan Validasi |
| :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto Increment | Primary Key |
| `pengguna_id` | `BIGINT UNSIGNED` | No | - | FK pemohon (merujuk `pengguna.id`) |
| `kos_id` | `BIGINT UNSIGNED` | No | - | FK merujuk `kos.id` |
| `kamar_id` | `BIGINT UNSIGNED` | No | - | FK merujuk `kamar.id` |
| `tanggal_pemesanan` | `DATE` | No | - | Tanggal rencana mulai ngekos |
| `status` | `ENUM('menunggu', 'disetujui', 'ditolak', 'dibatalkan')` | No | `'menunggu'` | Status alur sewa |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | Timestamps |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | Timestamps |

* **Index & Constraints:**
  * `PRIMARY KEY (id)`
  * `FOREIGN KEY (pengguna_id) REFERENCES pengguna(id) ON DELETE RESTRICT`
  * `FOREIGN KEY (kos_id) REFERENCES kos(id) ON DELETE CASCADE`
  * `FOREIGN KEY (kamar_id) REFERENCES kamar(id) ON DELETE RESTRICT`
  * `INDEX pemesanan_status_index (status)`

---

## 3. Template Skrip Migrasi Laravel

Berikut implementasi kode migrasi Laravel yang siap dipasang ke dalam repositori:

### 3.1. Migrasi Tabel Pengguna
File: `database/migrations/0001_01_01_000000_create_users_table.php`
```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('email', 191)->unique();
            $table->string('password');
            $table->enum('role', ['pencari', 'pemilik', 'admin'])->default('pencari')->index();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
```

### 3.2. Migrasi Tabel Kos
File: `database/migrations/2026_10_05_000001_create_kos_table.php`
```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemilik_id')->constrained('pengguna')->cascadeOnDelete();
            $table->string('nama', 150);
            $table->text('alamat');
            $table->text('deskripsi')->nullable();
            $table->enum('jenis', ['putra', 'putri', 'campur']);
            $table->unsignedInteger('harga');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->index(['jenis', 'harga']);
            $table->fullText(['nama', 'alamat']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kos');
    }
};
```

### 3.3. Migrasi Tabel Kamar
File: `database/migrations/2026_10_05_000002_create_kamar_table.php`
```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kamar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kos_id')->constrained('kos')->cascadeOnDelete();
            $table->string('nama_kamar', 50);
            $table->unsignedInteger('harga');
            $table->enum('status', ['tersedia', 'terisi'])->default('tersedia')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kamar');
    }
};
```

### 3.4. Migrasi Tabel Fasilitas & Pivot Kos Fasilitas
File: `database/migrations/2026_10_05_000003_create_fasilitas_table.php`
```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fasilitas', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->unique();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('kos_fasilitas', function (Blueprint $table) {
            $table->foreignId('kos_id')->constrained('kos')->cascadeOnDelete();
            $table->foreignId('fasilitas_id')->constrained('fasilitas')->cascadeOnDelete();
            $table->primary(['kos_id', 'fasilitas_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kos_fasilitas');
        Schema::dropIfExists('fasilitas');
    }
};
```

### 3.5. Migrasi Tabel Pemesanan
File: `database/migrations/2026_10_05_000004_create_pemesanan_table.php`
```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pemesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->restrictOnDelete();
            $table->foreignId('kos_id')->constrained('kos')->cascadeOnDelete();
            $table->foreignId('kamar_id')->constrained('kamar')->restrictOnDelete();
            $table->date('tanggal_pemesanan');
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'dibatalkan'])->default('menunggu')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemesanan');
    }
};
```

---

## 4. Konfigurasi Relasi Model Eloquent

Untuk memastikan controller dan resource serializer dapat mengambil data dengan efisien (*eager loading* tanpa masalah N+1 query):

### Model `Pengguna` (App\Models\Pengguna)
```php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengguna extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'pengguna';
    protected $guarded = ['id'];
    protected $hidden = ['password'];

    public function kos(): HasMany
    {
        return $this->hasMany(Kos::class, 'pemilik_id');
    }

    public function pemesanan(): HasMany
    {
        return $this->hasMany(Pemesanan::class, 'pengguna_id');
    }
}
```

### Model `Kos` (App\Models\Kos)
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kos extends Model
{
    protected $table = 'kos';
    protected $guarded = ['id'];

    public function pemilik(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pemilik_id');
    }

    public function kamar(): HasMany
    {
        return $this->hasMany(Kamar::class, 'kos_id');
    }

    public function fasilitas(): BelongsToMany
    {
        return $this->belongsToMany(Fasilitas::class, 'kos_fasilitas', 'kos_id', 'fasilitas_id');
    }

    public function pemesanan(): HasMany
    {
        return $this->hasMany(Pemesanan::class, 'kos_id');
    }
}
```

### Model `Kamar` (App\Models\Kamar)
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kamar extends Model
{
    protected $table = 'kamar';
    protected $guarded = ['id'];

    public function kos(): BelongsTo
    {
        return $this->belongsTo(Kos::class, 'kos_id');
    }

    public function pemesanan(): HasMany
    {
        return $this->hasMany(Pemesanan::class, 'kamar_id');
    }
}
```

### Model `Fasilitas` (App\Models\Fasilitas)
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Fasilitas extends Model
{
    protected $table = 'fasilitas';
    protected $guarded = ['id'];

    public function kos(): BelongsToMany
    {
        return $this->belongsToMany(Kos::class, 'kos_fasilitas', 'fasilitas_id', 'kos_id');
    }
}
```

### Model `Pemesanan` (App\Models\Pemesanan)
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pemesanan extends Model
{
    protected $table = 'pemesanan';
    protected $guarded = ['id'];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function kos(): BelongsTo
    {
        return $this->belongsTo(Kos::class, 'kos_id');
    }

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }
}
```