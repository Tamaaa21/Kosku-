# Modul 02: Pencarian & Manajemen Kos

Modul ini menguji katalog publik, fitur filter pencarian kompleks, integrasi geocoding koordinat otomatis, serta CRUD kos dengan otorisasi kepemilikan.

---

## 1. Katalog Kos Publik (Semua Data)
* **Method**: `GET`
* **URL**: `{{base_url}}/kos`
* **Auth**: Tidak perlu (Publik)
* **Expected Response** (`200 OK`):
  Array daftar kos beserta relasi `pemilik`, `fasilitas`, `kamar`, dan `kamar_tersedia_count`.

---

## 2. Pengujian Filter & Pencarian Kos

Uji berbagai parameter query pada endpoint `GET {{base_url}}/kos`:

| Skenario | URL Request | Hasil yang Diharapkan |
| :--- | :--- | :--- |
| **Pencarian Nama / Alamat** | `{{base_url}}/kos?q=Melati` | Hanya kos yang mengandung kata "Melati" |
| **Filter Wilayah / Lokasi** | `{{base_url}}/kos?lokasi=Sleman` | Hanya kos dengan alamat di wilayah Sleman |
| **Filter Rentang Harga** | `{{base_url}}/kos?harga_min=700000&harga_max=1000000` | Kos dengan harga antara 700rb s/d 1jt |
| **Filter Kategori Penghuni**| `{{base_url}}/kos?jenis=putri` | Kos bertipe `putri` (`putra`, `campur`) |
| **Filter Fasilitas Spesifik**| `{{base_url}}/kos?fasilitas[]=1&fasilitas[]=2` | Kos yang memiliki fasilitas ID 1 DAN 2 |
| **Filter Kamar Tersedia** | `{{base_url}}/kos?tersedia=1` | Hanya kos yang memiliki sisa kamar `tersedia` |
| **Urutkan Termurah** | `{{base_url}}/kos?sort=harga_asc` | Kos diurutkan dari harga terendah |
| **Urutkan Termahal** | `{{base_url}}/kos?sort=harga_desc` | Kos diurutkan dari harga tertinggi |
| **Paginasi Halaman** | `{{base_url}}/kos?page=1&per_page=5` | Menampilkan 5 kos per halaman |

---

## 3. Detail Kos Lengkap
* **Method**: `GET`
* **URL**: `{{base_url}}/kos/1`
* **Auth**: Tidak perlu
* **Expected Response** (`200 OK`):
  ```json
  {
    "data": {
      "id": 1,
      "pemilik_id": 2,
      "nama": "Kos Melati Harmoni",
      "alamat": "Jl. Kaliurang KM 5, Sleman, D.I. Yogyakarta",
      "deskripsi": "Kos nyaman dekat kampus...",
      "jenis": "putri",
      "harga": 850000,
      "latitude": -7.7612345,
      "longitude": 110.3789012,
      "pemilik": {
        "id": 2,
        "nama": "Pemilik Kos"
      },
      "fasilitas": [
        { "id": 1, "nama": "Wi-Fi" },
        { "id": 2, "nama": "AC" }
      ],
      "kamar": [
        { "id": 1, "nama_kamar": "Kamar A-01", "status": "tersedia" },
        { "id": 2, "nama_kamar": "Kamar A-02", "status": "terisi" }
      ],
      "kamar_tersedia_count": 1
    }
  }
  ```

---

## 4. Tambah Kos Baru (Geocoding Otomatis)
Menambahkan kos baru oleh pemilik. Perhatikan bahwa Anda **tidak perlu mengisi latitude & longitude**, karena backend akan secara otomatis mencari koordinat dari alamat fisik via Nominatim OpenStreetMap API.

* **Method**: `POST`
* **URL**: `{{base_url}}/kos`
* **Auth**: `Bearer Token` (`{{token_pemilik}}`)
* **Body** (`raw` - `JSON`):
  ```json
  {
    "nama": "Kos Gajah Mada Asri",
    "alamat": "Jl. Kaliurang KM 5, Caturtunggal, Depok, Sleman, Yogyakarta",
    "deskripsi": "Kos eksklusif dekat UGM dengan fasilitas lengkap.",
    "jenis": "campur",
    "harga": 1500000,
    "fasilitas_ids": [1, 2, 3]
  }
  ```
* **Expected Response** (`201 Created`):
  Response mengembalikan data kos baru, dengan nilai `latitude` dan `longitude` yang otomatis terisi oleh `GeocodingService`.

---

## 5. Perbarui Properti Kos
Hanya pemilik kos yang bersangkutan atau admin yang boleh memperbarui data kos ini.

* **Method**: `PUT`
* **URL**: `{{base_url}}/kos/{{kos_id}}`
* **Auth**: `Bearer Token` (`{{token_pemilik}}`)
* **Body** (`raw` - `JSON`):
  ```json
  {
    "nama": "Kos Gajah Mada Asri (Renovasi)",
    "harga": 1600000
  }
  ```
* **Expected Response** (`200 OK`): Data kos berhasil diperbarui.

---

## 6. Hapus Properti Kos
* **Method**: `DELETE`
* **URL**: `{{base_url}}/kos/{{kos_id}}`
* **Auth**: `Bearer Token` (`{{token_pemilik}}` atau `{{token_admin}}`)
* **Expected Response** (`200 OK`):
  ```json
  {
    "message": "Data kos berhasil dihapus."
  }
  ```

---

## 7. Pengujian Otorisasi & Hak Akses (Edge Cases)
1. **Pencari mencoba menambah kos**:
   * Gunakan `{{token_pencari}}` pada `POST {{base_url}}/kos`.
   * **Expected Result**: `403 Forbidden` (`{"message": "Akses ditolak untuk peran Anda."}`).
2. **Pemilik lain mencoba mengubah kos yang bukan miliknya**:
   * Login sebagai pemilik baru, lalu panggil `PUT {{base_url}}/kos/1`.
   * **Expected Result**: `403 Forbidden`.
