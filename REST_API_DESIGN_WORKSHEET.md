# REST API Design Worksheet: Sistem Informasi Pemesanan Kos

Dokumen ini menyajikan rancangan antarmuka **REST API Design Worksheet** berorientasi *resource-based architecture* yang sesuai dengan standar konvensi RESTful (penggunaan kata benda jamak, pemanfaatan HTTP method semantik, dan bebas dari kata kerja di dalam URI path).

---

## Bagian A - Identifikasi Resource

Berikut adalah 5 resource entitas utama yang diidentifikasi dari skema basis data dan arsitektur backend sistem pemesanan kos:

| No | Resource | Deskripsi |
| :-: | :--- | :--- |
| 1 | `users` *(pengguna)* | Entitas akun pengguna sistem yang mencakup tiga peran utama (*RBAC*): `pencari`, `pemilik`, dan `admin`, beserta status aktivasi akun dan kredensial autentikasi. |
| 2 | `kos` | Properti kos fisik yang disewakan oleh pemilik, menyimpan metadata nama, alamat fisik lengkap, deskripsi, batasan gender penghuni (`putra`, `putri`, `campur`), harga acuan dasar, serta koordinat lokasi spasial (`latitude`, `longitude`). |
| 3 | `rooms` *(kamar)* | Sub-entitas unit kamar spesifik yang dimiliki oleh sebuah properti kos, menyimpan penomoran unit (`nama_kamar`), harga sewa kamar bulanan, dan status ketersediaan saat ini (`tersedia` atau `terisi`). |
| 4 | `facilities` *(fasilitas)* | Master data fasilitas atau amenitas penunjang (misal: Wi-Fi, AC, Kamar Mandi Dalam) yang terhubung ke properti kos melalui relasi *many-to-many* (`kos_facilities`). |
| 5 | `bookings` *(pemesanan)* | Transaksi permohonan sewa kamar oleh pencari kos, mencatat jadwal tanggal mulai sewa serta status progres alur transaksi (`menunggu`, `disetujui`, `ditolak`, `dibatalkan`). |

---

## Bagian B - Rancangan Endpoint

Rancangan endpoint di bawah ini menggunakan prefiks `/api/v1/`, berorientasi kata benda jamak (*plural nouns*), memanfaatkan relasi hierarki (*nested resources*) untuk asosiasi yang terikat kuat, dan tanpa kata kerja di dalam URI:

| No | Method | Endpoint | Fungsi |
| :-: | :---: | :--- | :--- |
| 1 | `POST` | `/api/v1/users` | Mendaftarkan akun pengguna baru ke platform (*registrasi*). |
| 2 | `GET` | `/api/v1/users/me` | Mengambil data profil pengguna yang sedang terautentikasi melalui token. |
| 3 | `PATCH` | `/api/v1/users/{id}` | Mengubah status aktivasi akun pengguna (khusus peran `admin`). |
| 4 | `GET` | `/api/v1/kos` | Mengambil katalog publik daftar kos dengan dukungan filter parameter query string (`q`, `lokasi`, `harga_min`, `harga_max`, `jenis`, `fasilitas`, `tersedia`, `sort`, `page`). |
| 5 | `POST` | `/api/v1/kos` | Membuat entitas properti kos baru (khusus peran `pemilik`), dilengkapi integrasi geocoding koordinat otomatis. |
| 6 | `GET` | `/api/v1/kos/{id}` | Mengambil informasi detail lengkap satu kos beserta data pemilik, daftar fasilitas, dan unit kamar. |
| 7 | `PUT` | `/api/v1/kos/{id}` | Memperbarui data properti kos secara menyeluruh (diotorisasi untuk pemilik kos terkait). |
| 8 | `DELETE` | `/api/v1/kos/{id}` | Menghapus properti kos beserta dependensi data di bawahnya secara berjenjang (*cascade*). |
| 9 | `GET` | `/api/v1/kos/{id}/rooms` | Mengambil daftar seluruh unit kamar yang tersedia pada properti kos tertentu (*nested resource*). |
| 10 | `POST` | `/api/v1/kos/{id}/rooms` | Menambahkan unit kamar baru ke dalam kos tertentu (khusus pemilik kos terkait). |
| 11 | `PUT` | `/api/v1/rooms/{id}` | Memperbarui informasi nomor kamar, harga, atau status unit kamar fisik. |
| 12 | `DELETE` | `/api/v1/rooms/{id}` | Menghapus data unit kamar tertentu. |
| 13 | `GET` | `/api/v1/facilities` | Mengambil daftar seluruh master fasilitas yang tersedia di platform secara publik. |
| 14 | `POST` | `/api/v1/facilities` | Menambahkan item master fasilitas baru (khusus peran `admin`). |
| 15 | `POST` | `/api/v1/bookings` | Mengajukan transaksi pemesanan sewa kamar baru secara atomik (khusus peran `pencari`). |
| 16 | `GET` | `/api/v1/bookings` | Mengambil daftar riwayat pemesanan yang disesuaikan secara otomatis dengan peran pengguna aktif (*scope isolation*). |
| 17 | `GET` | `/api/v1/bookings/{id}` | Mengambil detail spesifik suatu transaksi pemesanan sewa kamar. |
| 18 | `PATCH` | `/api/v1/bookings/{id}` | Memperbarui status pemesanan (`disetujui`, `ditolak`, atau `dibatalkan`) dengan otomatisasi perubahan ketersediaan kamar. |

---

## Bagian C - Contoh Response JSON

Berikut adalah detail representasi response JSON (sukses dan gagal) untuk 3 endpoint representatif:

```
Endpoint 1: GET /api/v1/kos/{id} (Detail Properti Kos)
Endpoint 2: POST /api/v1/bookings (Pengajuan Pemesanan Kamar)
Endpoint 3: POST /api/v1/users (Registrasi Akun Pengguna)
```

---

### 1. Endpoint: `GET /api/v1/kos/{id}`

* **Fungsi**: Menampilkan detail satu properti kos beserta relasi pemilik, fasilitas, dan kamar unitnya.

#### A. Response Sukses (`200 OK`)
```json
{
  "data": {
    "id": 1,
    "pemilik_id": 2,
    "nama": "Kos Melati Harmoni",
    "alamat": "Jl. Kaliurang KM 5, Sleman, D.I. Yogyakarta",
    "deskripsi": "Kos nyaman dekat kampus, lingkungan asri dan tenang.",
    "jenis": "putri",
    "harga": 850000,
    "latitude": -7.7612345,
    "longitude": 110.3789012,
    "created_at": "2026-10-10T05:29:27.000000Z",
    "updated_at": "2026-10-10T05:29:27.000000Z",
    "pemilik": {
      "id": 2,
      "nama": "Pemilik Kos",
      "email": "pemilik@kos.test",
      "role": "pemilik",
      "aktif": true
    },
    "fasilitas": [
      {
        "id": 1,
        "nama": "Wi-Fi",
        "deskripsi": "Koneksi internet nirkabel berkecepatan tinggi."
      },
      {
        "id": 2,
        "nama": "AC",
        "deskripsi": "Air conditioner / pendingin ruangan."
      }
    ],
    "kamar": [
      {
        "id": 1,
        "nama_kamar": "Kamar A-01",
        "harga": 850000,
        "status": "tersedia"
      },
      {
        "id": 2,
        "nama_kamar": "Kamar A-02",
        "harga": 850000,
        "status": "terisi"
      }
    ],
    "kamar_tersedia_count": 1
  }
}
```

#### B. Response Error (`404 Not Found`)
```json
{
  "message": "Resource kos dengan ID 999 tidak ditemukan."
}
```
* **Kondisi Pemicu Error**: Klien mengirimkan request dengan parameter `{id}` kos yang tidak terdaftar atau telah dihapus dari basis data.

---

### 2. Endpoint: `POST /api/v1/bookings`

* **Fungsi**: Pengajuan pemesanan kamar oleh pencari kos secara atomik (*database locking*).

#### A. Response Sukses (`201 Created`)
```json
{
  "message": "Pemesanan berhasil diajukan.",
  "data": {
    "id": 10,
    "pengguna_id": 3,
    "kos_id": 1,
    "kamar_id": 1,
    "tanggal_pemesanan": "2026-11-01",
    "status": "menunggu",
    "created_at": "2026-10-11T09:15:00.000000Z",
    "updated_at": "2026-10-11T09:15:00.000000Z",
    "kamar": {
      "id": 1,
      "nama_kamar": "Kamar A-01",
      "harga": 850000,
      "status": "tersedia"
    },
    "kos": {
      "id": 1,
      "nama": "Kos Melati Harmoni"
    }
  }
}
```

#### B. Response Error 1 (`409 Conflict`)
```json
{
  "message": "Kamar tidak tersedia atau sedang dalam proses pemesanan."
}
```
* **Kondisi Pemicu Error**: Terjadi benturan reservasi (*concurrency double-booking*). Kamar yang diminta saat ini statusnya sudah `terisi` atau terdapat permohonan lain yang statusnya masih aktif (`menunggu`/`disetujui`).

#### C. Response Error 2 (`422 Unprocessable Content`)
```json
{
  "message": "Data yang diberikan tidak valid.",
  "errors": {
    "kamar_id": [
      "Bidang kamar_id wajib diisi."
    ],
    "tanggal_pemesanan": [
      "Tanggal pemesanan harus berupa tanggal setelah atau sama dengan hari ini."
    ]
  }
}
```
* **Kondisi Pemicu Error**: Klien mengirimkan payload yang melanggar aturan validasi bisnis, misalnya kolom `kamar_id` kosong atau `tanggal_pemesanan` menggunakan tanggal di masa lampau.

---

### 3. Endpoint: `POST /api/v1/users`

* **Fungsi**: Pendaftaran pengguna baru (*registrasi akun*).

#### A. Response Sukses (`201 Created`)
```json
{
  "message": "Registrasi berhasil.",
  "user": {
    "id": 4,
    "nama": "Rizky Pratama",
    "email": "rizky.pratama@example.com",
    "role": "pencari",
    "aktif": true,
    "created_at": "2026-10-11T09:20:00.000000Z",
    "updated_at": "2026-10-11T09:20:00.000000Z"
  },
  "token": "4|c8b67f10e4a7819c99df..."
}
```

#### B. Response Error (`422 Unprocessable Content`)
```json
{
  "message": "Data yang diberikan tidak valid.",
  "errors": {
    "email": [
      "Email ini sudah terdaftar di sistem kami."
    ],
    "password": [
      "Konfirmasi kata sandi tidak cocok."
    ]
  }
}
```
* **Kondisi Pemicu Error**: Alamat email yang didaftarkan sudah digunakan oleh akun lain (*unique constraint*), atau field `password` tidak sesuai dengan field `password_confirmation`.
