# Modul 05: Master Fasilitas & Administrasi Pengguna

Modul ini menguji master data fasilitas serta administrasi akun pengguna yang dikelola khusus oleh Administrator.

---

## 1. Master Data Fasilitas

### A. Melihat Seluruh Fasilitas (Publik)
* **Method**: `GET`
* **URL**: `{{base_url}}/fasilitas`
* **Auth**: Tidak perlu (Publik)
* **Expected Response** (`200 OK`):
  Array daftar fasilitas (contoh: Wi-Fi, AC, Kamar Mandi Dalam, Kasur, Lemari, dll).

### B. Tambah Fasilitas Baru (Admin Only)
* **Method**: `POST`
* **URL**: `{{base_url}}/fasilitas`
* **Auth**: `Bearer Token` (`{{token_admin}}`)
* **Body** (`raw` - `JSON`):
  ```json
  {
    "nama": "Water Heater",
    "deskripsi": "Pemanas air mandi bertenaga listrik atau gas."
  }
  ```
* **Expected Response** (`201 Created`):
  ```json
  {
    "message": "Fasilitas berhasil ditambahkan.",
    "data": {
      "id": 8,
      "nama": "Water Heater",
      "deskripsi": "Pemanas air mandi bertenaga listrik atau gas."
    }
  }
  ```

### C. Perbarui Fasilitas (Admin Only)
* **Method**: `PUT`
* **URL**: `{{base_url}}/fasilitas/8`
* **Auth**: `Bearer Token` (`{{token_admin}}`)
* **Body** (`raw` - `JSON`):
  ```json
  {
    "nama": "Water Heater (Gas/Listrik)"
  }
  ```
* **Expected Response** (`200 OK`).

### D. Hapus Fasilitas (Admin Only)
* **Method**: `DELETE`
* **URL**: `{{base_url}}/fasilitas/8`
* **Auth**: `Bearer Token` (`{{token_admin}}`)
* **Expected Response** (`200 OK`):
  ```json
  {
    "message": "Fasilitas berhasil dihapus."
  }
  ```

---

## 2. Administrasi Pengguna (Admin Only)

### A. Melihat Daftar Semua Pengguna
* **Method**: `GET`
* **URL**: `{{base_url}}/admin/pengguna`
* **Auth**: `Bearer Token` (`{{token_admin}}`)
* **Parameter Opsional**:
  * `?role=pemilik` (atau `pencari`, `admin`)
  * `?aktif=1` (atau `0`)
  * `?q=Budi` (pencarian nama/email)
* **Expected Response** (`200 OK`):
  Paginasi seluruh data akun pengguna beserta status aktifnya.

### B. Menonaktifkan / Mengaktifkan Akun Pengguna
Admin dapat memblokir/menonaktifkan akun yang melanggar aturan platform.

* **Method**: `PATCH`
* **URL**: `{{base_url}}/admin/pengguna/3/status`
* **Auth**: `Bearer Token` (`{{token_admin}}`)
* **Body** (`raw` - `JSON`):
  ```json
  {
    "aktif": false
  }
  ```
* **Expected Response** (`200 OK`):
  ```json
  {
    "message": "Status akun pengguna berhasil diperbarui.",
    "data": {
      "id": 3,
      "nama": "Pencari Kos",
      "email": "pencari@kos.test",
      "role": "pencari",
      "aktif": false
    }
  }
  ```

### C. Verifikasi Pemblokiran Akun
Setelah dinonaktifkan di atas:
1. Coba panggil `POST {{base_url}}/login` menggunakan kredensial akun user ID 3 (`pencari@kos.test`).
2. **Expected Response** (`403 Forbidden`):
   ```json
   {
     "message": "Akun Anda dinonaktifkan oleh administrator."
   }
   ```
3. Kembalikan status akun ke aktif (`"aktif": true`) via `PATCH {{base_url}}/admin/pengguna/3/status` untuk mengaktifkan kembali.
