# Modul 04: Alur Transaksi Pemesanan (Booking)

Modul ini menguji siklus pemesanan sewa kamar kos secara transaksional, pencegahan *race condition* / *double booking*, serta perubahan otomatis status ketersediaan kamar.

---

## 1. Pengajuan Pemesanan Kamar (Pencari)
Calon penghuni mengajukan sewa untuk unit kamar yang berstatus `tersedia`.

* **Method**: `POST`
* **URL**: `{{base_url}}/pemesanan`
* **Auth**: `Bearer Token` (`{{token_pencari}}`)
* **Body** (`raw` - `JSON`):
  ```json
  {
    "kamar_id": 1,
    "tanggal_pemesanan": "2026-11-01"
  }
  ```
* **Expected Response** (`201 Created`):
  ```json
  {
    "message": "Pemesanan berhasil diajukan.",
    "data": {
      "id": 2,
      "pengguna_id": 3,
      "kos_id": 1,
      "kamar_id": 1,
      "tanggal_pemesanan": "2026-11-01",
      "status": "menunggu",
      "created_at": "...",
      "updated_at": "...",
      "pengguna": {
        "id": 3,
        "nama": "Pencari Kos"
      },
      "kos": {
        "id": 1,
        "nama": "Kos Melati Harmoni"
      },
      "kamar": {
        "id": 1,
        "nama_kamar": "Kamar A-01",
        "status": "tersedia"
      }
    }
  }
  ```

> [!NOTE]
> Simpan nilai `id` pemesanan di atas ke variabel `pemesanan_id` di Postman Anda.

---

## 2. Pengujian Pencegahan Double Booking (`409 Conflict`)
Untuk membuktikan bahwa sistem aman dari *race condition*:
1. Tanpa menyetujui pemesanan sebelumnya, login sebagai pencari lain atau kirim ulang request `POST {{base_url}}/pemesanan` dengan `kamar_id` yang sama (kamar 1).
2. **Expected Response** (`409 Conflict`):
   ```json
   {
     "message": "Kamar tidak tersedia atau sedang dalam proses pemesanan."
   }
   ```
   *Sistem secara atomik mengunci baris database (`lockForUpdate`) dan menolak pengajuan ganda.*

---

## 3. Melihat Daftar Pemesanan
Endpoint ini secara cerdas memfilter data berdasarkan peran pengguna yang sedang login:
* **Pencari**: Hanya melihat riwayat pengajuan pemesanan milik pribadinya.
* **Pemilik Kos**: Melihat daftar pengajuan pemesanan yang masuk untuk kos-kos miliknya.
* **Admin**: Melihat semua pemesanan di platform.

* **Method**: `GET`
* **URL**: `{{base_url}}/pemesanan`
* **Auth**: `Bearer Token` (`{{token_pemilik}}` atau `{{token_pencari}}`)
* **Parameter Opsional**: `?status=menunggu` (atau `disetujui`, `ditolak`, `dibatalkan`)
* **Expected Response** (`200 OK`):
  Paginasi daftar pemesanan lengkap dengan objek pemesan, kos, dan kamar.

---

## 4. Melihat Detail Pemesanan
* **Method**: `GET`
* **URL**: `{{base_url}}/pemesanan/{{pemesanan_id}}`
* **Auth**: `Bearer Token` (`{{token_pencari}}` atau `{{token_pemilik}}`)
* **Expected Response** (`200 OK`): Objek pemesanan spesifik.

---

## 5. Pemilik Menyetujui Pemesanan (Approve)
Saat pemilik menyetujui, sistem secara otomatis memperbarui status unit kamar terkait menjadi `terisi`.

* **Method**: `PATCH`
* **URL**: `{{base_url}}/pemesanan/{{pemesanan_id}}/status`
* **Auth**: `Bearer Token` (`{{token_pemilik}}`)
* **Body** (`raw` - `JSON`):
  ```json
  {
    "status": "disetujui"
  }
  ```
* **Expected Response** (`200 OK`):
  ```json
  {
    "message": "Status pemesanan berhasil diperbarui menjadi disetujui.",
    "data": {
      "id": 2,
      "status": "disetujui",
      "kamar": {
        "id": 1,
        "nama_kamar": "Kamar A-01",
        "status": "terisi"
      }
    }
  }
  ```
* *Verifikasi:* Cek detail kamar via `GET {{base_url}}/kos/1/kamar`. Kamar 1 sekarang berstatus `terisi`.

---

## 6. Pemilik Menolak Pemesanan (Reject)
* **Method**: `PATCH`
* **URL**: `{{base_url}}/pemesanan/{{pemesanan_id}}/status`
* **Auth**: `Bearer Token` (`{{token_pemilik}}`)
* **Body** (`raw` - `JSON`):
  ```json
  {
    "status": "ditolak"
  }
  ```
* **Expected Response** (`200 OK`): Status pemesanan berubah menjadi `ditolak`.

---

## 7. Pencari Membatalkan Pemesanan (Cancel)
* **Skenario Sukses**:
  Jika status pemesanan masih `menunggu`, pencari dapat membatalkannya:
  * **Method**: `PATCH`
  * **URL**: `{{base_url}}/pemesanan/{{pemesanan_id}}/status`
  * **Auth**: `Bearer Token` (`{{token_pencari}}`)
  * **Body** (`raw` - `JSON`):
    ```json
    {
      "status": "dibatalkan"
    }
    ```
  * **Expected Response** (`200 OK`).

* **Skenario Ditolak (Pelanggaran Aturan)**:
  Jika pemesanan **sudah disetujui** oleh pemilik, pencari mencoba membatalkannya:
  * **Expected Response** (`403 Forbidden`).
