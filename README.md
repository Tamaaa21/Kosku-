# Kostara &mdash; Platform Pencarian & Sewa Kos Indonesia

<p align="center">
  <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBtAMGcNImL28Wkcksd1pB136MBTXtxAeUZZdLwny2E0TfcGGRkdrMA_JR6pVnktaHUu8Ffua13FdN3HdgZd29vhkeFDWM4WJtQJFMuZndi8o1HB2GXyn8ikhIgK45eAlZmqqiEVYkjMe7_m-Cvw0nxPY6cKLGKCrWp2Ch2dtBIlIqQb5pKjNVS1WaRhbnQPFyDftN7_uvVGXkv3Z_JUnsJpPxReDktVkZYw0eJ1YRW7caBLYvFdhslDw" alt="Kostara Banner" width="100%" style="border-radius: 12px; max-height: 380px; object-fit: cover;" />
</p>

<p align="center">
  <strong>Platform Terintegrasi untuk Pencari Kos dan Mitra Pemilik Kos di Seluruh Indonesia</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11" />
  <img src="https://img.shields.io/badge/PHP-8.4%20%7C%208.5-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.4 / 8.5" />
  <img src="https://img.shields.io/badge/Vue.js-3-4FC08D?style=for-the-badge&logo=vuedotjs&logoColor=white" alt="Vue 3" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS 4" />
  <img src="https://img.shields.io/badge/Vite-8-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite 8" />
  <img src="https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker&logoColor=white" alt="Docker Ready" />
  <img src="https://img.shields.io/badge/Tests-102_Passed-success?style=for-the-badge" alt="102 Tests Passed" />
</p>

---

## 📌 Tentang Kostara

**Kostara** (sebelumnya dikembangkan dengan nama *KosKu*) adalah aplikasi web modern yang menjembatani pencari hunian sewa (mahasiswa, pekerja, keluarga) dengan mitra pemilik kos di seluruh wilayah Indonesia. Dibangun dengan arsitektur decoupled **Laravel 11 REST API** pada sisi backend dan **Vue 3 SPA (Single Page Application)** pada sisi frontend.

Sistem dirancang responsif, cepat, aman, dan mudah digunakan dengan integrasi geocoding OpenStreetMap Nominatim, pencegahan transaksi ganda (*pessimistic locking*), serta komunikasi langsung via WhatsApp.

---

## 🚀 Fitur Unggulan

### 1. 👥 Navigasi Berbasis Peran (*Role-Based Access Control*)
Antarmuka beradaptasi secara otomatis sesuai hak akses pengguna:
- **Pencari Kos & Publik**: Bilah navigasi atas (*top navbar*) dengan menu jelajah, katalog kos, peta interaktif, halaman Tentang Kami (`/about`), dan riwayat pemesanan.
- **Mitra Pemilik Kos**: Bilah samping (*responsive sidebar drawer*) khusus pengelola yang memuat ringkasan performa properti, verifikasi kepemilikan kos, manajemen kamar, dan daftar pemesanan masuk.
- **Administrator**: Panel kontrol sistem untuk pengawasan seluruh transaksi, moderasi kos, manajemen pengguna, dan banner slider.

### 2. 🔍 Pencarian, Filter Radius Geografis & Peta Interaktif
- **Jangkauan Seluruh Indonesia**: Eksplorasi kos di kota-kota besar (Jakarta, Yogyakarta, Bandung, Surabaya, Semarang, Bali, dll.).
- **Filter Radius Jarak (Haversine)**: Parameter query `latitude`, `longitude`, dan `radius` untuk menyaring kos terdekat dari kampus atau lokasi pengguna dengan informasi jarak riil (`jarak_km`).
- **Peta Interaktif Leaflet**: Visualisasi sebaran kos berbasis OpenStreetMap dengan pinpoint akurat dan preview popup.
- **Geocoding Otomatis**: Integrasi Nominatim OpenStreetMap untuk konversi alamat ke koordinat GPS secara otomatis dengan fail-open fallback.

### 3. 🛡️ Proteksi Transaksi Pemesanan (Anti Double-Booking)
- Mekanisme database transaction atomik (`DB::transaction`) dan *pessimistic locking* (`lockForUpdate`) pada saat pemesanan kamar untuk menjamin tidak ada kamar yang dipesan ganda secara bersamaan.
- Validasi durasi sewa fleksibel (harian, mingguan, bulanan) dengan batas maksimum wajar dan pencegahan tanggal masa lalu.

### 4. 📊 Ekspor Rekapitulasi Data (CSV Streaming)
- Fitur ekspor riwayat pemesanan (`GET /api/v1/bookings/export`) untuk pemilik kos dan admin.
- Format streaming CSV ber-UTF-8 BOM yang langsung terbaca rapi pada Microsoft Excel dan aplikasi spreadsheet offline.

### 5. ⚡ Performa & Pengalaman Pengguna (UX)
- **Route Lazy Loading**: Pembagian kode modular (*code splitting*) pada Vue Router yang menurunkan ukuran bundle awal dari 1.2 MB menjadi 237 kB.
- **Halaman Error Terpadu**: Halaman Error 404 (Not Found) dan Error 403 (Forbidden) kustom yang ramah pengguna menggantikan browser pop-up.
- **Halaman Tentang Kami (`/about`)**: Informasi profil platform, visi, misi, dan pilar keunggulan sistem.

---

## 🛠️ Arsitektur & Teknologi

| Bagian | Teknologi | Keterangan |
| :--- | :--- | :--- |
| **Backend Framework** | Laravel 11.35.0 | PHP 8.4 / 8.5 dengan arsitektur RESTful API & Form Requests |
| **Authentication** | Laravel Sanctum | Otentikasi token berbasis Bearer Token yang aman |
| **Database** | MariaDB / MySQL / SQLite | Didukung Eloquent ORM, Migrations, dan Seeders 3NF |
| **Frontend Framework** | Vue 3 (Composition API) | SPA reaktif dengan `<script setup>` |
| **Build Tool** | Vite 8.x | Dynamic chunking, HMR, dan build produksi optimal |
| **State Management** | Pinia | Manajemen sesi login, peran, dan status autentikasi |
| **Styling & Ikon** | Tailwind CSS 4 & Phosphor Icons | Desain antarmuka modern, tipografi Satoshi, dan palet brand |
| **Peta Digital** | Leaflet.js | Visualisasi peta interaktif & geocoding Nominatim |
| **Container & Deploy** | Docker & Compose | Multi-container: PHP 8.4-FPM, Nginx, MariaDB |
| **Automated Testing** | PHPUnit | **102 Automated Feature Tests** (520 Assertions) & Pint Style Formatter |

---

## 👥 Tim Pengembang & Kontributor

| No | Nama | Username GitHub | Peran Utama |
| :---: | :--- | :--- | :--- |
| 1 | **Sigit Pratama** | [`Tamaaa21`](https://github.com/Tamaaa21) | Backend Architect & DevOps Specialist |
| 2 | **Naufal Miftahul Arsyi** | [`Naufal-Arsyi`](https://github.com/Naufal-Arsyi) | Fullstack Lead & UI Architect (Ketua) |
| 3 | **Nurul Salma Afifah** | [`nurulsalmaafifah-oss`](https://github.com/nurulsalmaafifah-oss) | API Designer & QA Specialist |
| 4 | **Putri Areka Sandra** | [`arekaa333-ux`](https://github.com/arekaa333-ux) | Frontend & Data Analytics Specialist |

---

## 📦 Panduan Instalasi & Menjalankan Aplikasi

### Prasyarat Sistem
- **PHP** >= 8.3 (direkomendasikan PHP 8.4) dengan ekstensi `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `curl`.
- **Composer** >= 2.x
- **Node.js** >= 18.x & **npm** >= 9.x
- **Basis Data**: MariaDB / MySQL atau SQLite.

### 1. Clone Repositori
```bash
git clone https://github.com/Tamaaa21/Kosku-.git
cd Kosku-
```

### 2. Setup Backend (Laravel)
```bash
# Salin environment file
cp .env.example .env

# Pasang dependensi PHP
composer install

# Buat encryption key
php artisan key:generate

# Konfigurasi database di file .env, kemudian jalankan migrasi & seeder
php artisan migrate --seed

# Jalankan backend development server (port 8000)
php artisan serve
```

### 3. Setup Frontend (Vue 3 SPA)
```bash
cd frontend

# Pasang dependensi JavaScript
npm install

# Jalankan frontend development server dengan Vite (port 5173)
npm run dev
```

Akses aplikasi pada peramban web di: `http://localhost:5173`

### 4. Menjalankan via Docker & Docker Compose (Opsional)
```bash
# Jalankan seluruh service (PHP-FPM, Nginx, MariaDB)
docker compose up -d --build

# Jalankan migrasi di dalam kontainer app
docker compose exec app php artisan migrate --seed
```

---

## 🧪 Menjalankan Automated Tests & Linting

```bash
# Menjalankan seluruh test suite PHPUnit (102 tests)
php artisan test

# Format kode sesuai standar Laravel Pint
vendor/bin/pint --dirty --format agent

# Build aset frontend produksi
cd frontend && npm run build
```

---

## 📡 Ringkasan Spesifikasi API (`/api/v1`)

Dokumentasi lengkap kontrak OpenAPI 3.1.0 tersedia pada berkas [docs/openapi.yaml](docs/openapi.yaml).

### Autentikasi & Profil
| Method | Endpoint | Deskripsi | Akses |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/v1/auth/register` | Pendaftaran akun pencari/pemilik kos | Publik |
| `POST` | `/api/v1/auth/login` | Masuk akun & penerbitan Bearer Token | Publik |
| `POST` | `/api/v1/auth/logout` | Revoke token autentikasi | Auth |
| `GET` | `/api/v1/auth/me` | Informasi profil pengguna aktif | Auth |
| `PUT` | `/api/v1/me` | Pembaruan data profil dan nomor telepon | Auth |
| `POST` | `/api/v1/me/avatar` | Unggah foto profil pengguna | Auth |
| `PUT` | `/api/v1/me/password` | Ubah kata sandi akun | Auth |

### Properti Kos & Unit Kamar
| Method | Endpoint | Deskripsi | Akses |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/kos` | Daftar kos (mendukung filter `q`, `lokasi`, `harga_min`, `harga_max`, `latitude`, `longitude`, `radius`, `sort`) | Publik |
| `GET` | `/api/v1/kos/{id}` | Detail properti kos, fasilitas, kamar, dan ulasan | Publik |
| `POST` | `/api/v1/kos` | Tambah kos baru dengan auto-geocoding alamat | Pemilik |
| `PUT` | `/api/v1/kos/{id}` | Perbarui data kos | Pemilik / Admin |
| `DELETE` | `/api/v1/kos/{id}` | Hapus properti kos | Pemilik / Admin |
| `GET` | `/api/v1/kos/{id}/rooms` | Daftar kamar pada kos tertentu | Publik |
| `POST` | `/api/v1/kos/{id}/rooms` | Tambah unit kamar kos | Pemilik |
| `PUT` | `/api/v1/rooms/{id}` | Perbarui status dan harga kamar | Pemilik / Admin |
| `DELETE` | `/api/v1/rooms/{id}` | Hapus unit kamar | Pemilik / Admin |

### Alur Reservasi Pemesanan (*Bookings*)
| Method | Endpoint | Deskripsi | Akses |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/v1/bookings` | Pengajuan reservasi kamar (transaksi atomik anti-bentrok) | Pencari |
| `GET` | `/api/v1/bookings` | Daftar riwayat pemesanan pengguna/pemilik | Auth |
| `GET` | `/api/v1/bookings/{id}` | Detail informasi pemesanan | Auth |
| `PATCH` | `/api/v1/bookings/{id}/status` | Persetujuan/penolakan reservasi (`disetujui`, `ditolak`, `dibatalkan`) | Pemilik / Pencari |
| `GET` | `/api/v1/bookings/export` | **Ekspor rekap riwayat pemesanan ke format CSV** | Pemilik / Admin |

---

## 📄 Lisensi

Proyek Kostara dikembangkan untuk keperluan akademik dan kolaboratif. Bebas digunakan dan dimodifikasi sesuai ketentuan repositori.
