# Panduan Lengkap Pengujian REST API dengan Postman

Repositori ini menyediakan dokumentasi dan panduan langkah demi langkah untuk menguji seluruh 23 endpoint **Sistem Informasi Pencarian Kos** menggunakan Postman.

---

## 1. Persiapan Awal Postman

### A. Konfigurasi Environment Variable
Buat environment baru di Postman bernama `Backend Kos (Local)` dengan variabel berikut:

| Variable | Initial Value | Current Value | Keterangan |
| :--- | :--- | :--- | :--- |
| `base_url` | `https://backend-kos.ddev.site/api/v1` | `https://backend-kos.ddev.site/api/v1` | URL dasar API |
| `token_admin` | *(kosongkan)* | *(diisi setelah login)* | Bearer token Admin |
| `token_pemilik` | *(kosongkan)* | *(diisi setelah login)* | Bearer token Pemilik |
| `token_pencari` | *(kosongkan)* | *(diisi setelah login)* | Bearer token Pencari |
| `kos_id` | `1` | `1` | ID Kos untuk pengujian |
| `kamar_id` | `1` | `1` | ID Kamar untuk pengujian |
| `pemesanan_id`| *(kosongkan)* | *(diisi setelah booking)*| ID Pemesanan aktif |

> [!TIP]
> Anda juga dapat mengimpor langsung file environment [`backend_kos.postman_environment.json`](./backend_kos.postman_environment.json) dan collection [`backend_kos.postman_collection.json`](./backend_kos.postman_collection.json) ke Postman.

### B. Header Wajib
Untuk setiap request di Postman, selalu sertakan Header:
```http
Accept: application/json
Content-Type: application/json
```
Untuk request yang membutuhkan otentikasi, tambahkan pada tab **Authorization**:
* **Type**: `Bearer Token`
* **Token**: `{{token_pencari}}` / `{{token_pemilik}}` / `{{token_admin}}`

### C. Akun Bawaan (Database Seeder)
Jika database baru saja di-reset (`ddev artisan migrate:fresh --seed`), gunakan akun berikut:

* **Admin**: `admin@kos.test` | Password: `password`
* **Pemilik Kos**: `pemilik@kos.test` | Password: `password`
* **Pencari Kos**: `pencari@kos.test` | Password: `password`

---

## 2. Struktur Modul Panduan Pengujian

Untuk panduan per endpoint yang detail beserta contoh payload JSON dan responsenya, buka file sesuai modul berikut:

1. [**01. Autentikasi & Profil Pengguna**](./01_autentikasi.md)
   * Registrasi Pencari & Pemilik
   * Login & Generate Token
   * Ambil Profil (`/me`)
   * Logout (Revoke Token)
2. [**02. Pencarian & Manajemen Kos**](./02_kos_dan_pencarian.md)
   * Katalog Kos Publik & Filter Kompleks (`q`, harga, jenis, fasilitas, ketersediaan)
   * Detail Kos (Eager Loading)
   * Tambah Kos dengan Geocoding Otomatis (Pemilik)
   * Update & Hapus Kos (Ownership Authorization)
3. [**03. Manajemen Unit Kamar**](./03_manajemen_kamar.md)
   * Daftar Kamar per Kos
   * Tambah Kamar Baru (Pemilik Kos)
   * Update Status/Harga Kamar
   * Hapus Unit Kamar
4. [**04. Alur Transaksi Pemesanan (Booking)**](./04_alur_pemesanan.md)
   * Pengajuan Booking Kamar (Pencari)
   * Pengujian Pencegahan Double Booking (`409 Conflict`)
   * Inbox Pemesanan (Pencari vs Pemilik Kos)
   * Persetujuan / Penolakan Booking (Otomatis toggle status kamar)
   * Pembatalan Booking oleh Pencari
5. [**05. Master Fasilitas & Administrasi Pengguna**](./05_master_fasilitas_dan_admin.md)
   * Master Fasilitas Publik & CRUD oleh Admin
   * Pengelolaan Status Akun Pengguna oleh Admin

---

## 3. Urutan Skenario Pengujian (End-to-End Workflow)

Untuk memverifikasi integrasi sistem secara utuh, disarankan mengikuti alur pengujian berikut:

```
[1. Login Akun] ──► Dapatkan token_admin, token_pemilik, token_pencari
       │
[2. Master Fasilitas] ──► Admin menambah fasilitas baru (misal: "Kolam Renang")
       │
[3. Pemilik Tambah Kos] ──► Koordinat alamat terisi otomatis oleh GeocodingService
       │
[4. Pemilik Tambah Kamar] ──► Status kamar awal: "tersedia"
       │
[5. Pencari Cari Kos] ──► Filter berdasarkan lokasi & kamar tersedia
       │
[6. Pencari Buat Booking] ──► Status booking: "menunggu"
       │
[7. Uji Double-Booking] ──► Pencari lain coba pesan kamar yang sama ──► Menghasilkan 409 Conflict
       │
[8. Pemilik Setujui Booking] ──► Status booking jadi "disetujui" & Status kamar otomatis jadi "terisi"
```
