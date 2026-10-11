# Modul 03: Manajemen Unit Kamar

Modul ini menguji pengelolaan unit kamar di dalam suatu kos, status ketersediaan kamar, dan otorisasi pemilik.

---

## 1. Daftar Kamar untuk Kos Tertentu
* **Method**: `GET`
* **URL**: `{{base_url}}/kos/1/kamar`
* **Auth**: Tidak perlu (Publik)
* **Expected Response** (`200 OK`):
  ```json
  {
    "data": [
      {
        "id": 1,
        "kos_id": 1,
        "nama_kamar": "Kamar A-01",
        "harga": 850000,
        "status": "tersedia",
        "created_at": "...",
        "updated_at": "..."
      },
      {
        "id": 2,
        "kos_id": 1,
        "nama_kamar": "Kamar A-02",
        "harga": 850000,
        "status": "terisi",
        "created_at": "...",
        "updated_at": "..."
      }
    ]
  }
  ```

---

## 2. Tambah Kamar Baru
Hanya pemilik dari kos yang bersangkutan yang diizinkan menambahkan kamar.

* **Method**: `POST`
* **URL**: `{{base_url}}/kos/1/kamar`
* **Auth**: `Bearer Token` (`{{token_pemilik}}`)
* **Body** (`raw` - `JSON`):
  ```json
  {
    "nama_kamar": "Kamar B-01",
    "harga": 900000,
    "status": "tersedia"
  }
  ```
* **Expected Response** (`201 Created`):
  ```json
  {
    "message": "Kamar berhasil ditambahkan.",
    "data": {
      "id": 3,
      "kos_id": 1,
      "nama_kamar": "Kamar B-01",
      "harga": 900000,
      "status": "tersedia",
      "created_at": "...",
      "updated_at": "..."
    }
  }
  ```

---

## 3. Perbarui Informasi / Status Kamar
Pemilik dapat mengubah nama kamar, harga sewa, maupun manual toggle status (`tersedia` / `terisi`).

* **Method**: `PUT`
* **URL**: `{{base_url}}/kamar/3`
* **Auth**: `Bearer Token` (`{{token_pemilik}}`)
* **Body** (`raw` - `JSON`):
  ```json
  {
    "nama_kamar": "Kamar B-01 (VIP)",
    "harga": 950000,
    "status": "tersedia"
  }
  ```
* **Expected Response** (`200 OK`):
  ```json
  {
    "message": "Data kamar berhasil diperbarui.",
    "data": {
      "id": 3,
      "kos_id": 1,
      "nama_kamar": "Kamar B-01 (VIP)",
      "harga": 950000,
      "status": "tersedia",
      "created_at": "...",
      "updated_at": "..."
    }
  }
  ```

---

## 4. Hapus Unit Kamar
* **Method**: `DELETE`
* **URL**: `{{base_url}}/kamar/3`
* **Auth**: `Bearer Token` (`{{token_pemilik}}` atau `{{token_admin}}`)
* **Expected Response** (`200 OK`):
  ```json
  {
    "message": "Kamar berhasil dihapus."
  }
  ```

---

## 5. Pengujian Skenario Otorisasi Kamar
* **User lain mencoba mengedit kamar**:
  Login dengan akun pemilik lain yang tidak memiliki kos tersebut, lalu panggil `PUT {{base_url}}/kamar/1`.
  * **Expected Result**: `403 Forbidden`.
