# Modul 01: Autentikasi & Profil Pengguna

Modul ini mencakup endpoint registrasi, login, pengecekan profil, dan logout berbasis Laravel Sanctum.

---

## 1. Registrasi Akun Pencari
* **Method**: `POST`
* **URL**: `{{base_url}}/register`
* **Headers**:
  * `Accept: application/json`
  * `Content-Type: application/json`
* **Body** (`raw` - `JSON`):
  ```json
  {
    "nama": "Andi Pencari",
    "email": "andi.pencari@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "pencari"
  }
  ```
* **Expected Response** (`201 Created`):
  ```json
  {
    "message": "Registrasi berhasil.",
    "user": {
      "id": 4,
      "nama": "Andi Pencari",
      "email": "andi.pencari@example.com",
      "role": "pencari",
      "aktif": true,
      "created_at": "2026-10-11T08:00:00.000000Z",
      "updated_at": "2026-10-11T08:00:00.000000Z"
    },
    "token": "2|abcdef123456..."
  }
  ```

---

## 2. Registrasi Akun Pemilik
* **Method**: `POST`
* **URL**: `{{base_url}}/register`
* **Body** (`raw` - `JSON`):
  ```json
  {
    "nama": "Budi Juragan Kos",
    "email": "budi.juragan@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "pemilik"
  }
  ```
* **Expected Response** (`201 Created`):
  Data user dengan role `pemilik` beserta token akses.

---

## 3. Login Pengguna
Gunakan request ini untuk mendapatkan Bearer Token untuk masing-masing peran.

* **Method**: `POST`
* **URL**: `{{base_url}}/login`
* **Body** (`raw` - `JSON`):

  **Contoh Login Pemilik:**
  ```json
  {
    "email": "pemilik@kos.test",
    "password": "password"
  }
  ```

  **Contoh Login Pencari:**
  ```json
  {
    "email": "pencari@kos.test",
    "password": "password"
  }
  ```

  **Contoh Login Admin:**
  ```json
  {
    "email": "admin@kos.test",
    "password": "password"
  }
  ```

* **Expected Response** (`200 OK`):
  ```json
  {
    "message": "Login berhasil.",
    "user": {
      "id": 2,
      "nama": "Pemilik Kos",
      "email": "pemilik@kos.test",
      "role": "pemilik",
      "aktif": true,
      "created_at": "...",
      "updated_at": "..."
    },
    "token": "3|xyz987654..."
  }
  ```

> [!TIP]
> **Postman Test Script Otomatis**:  
> Salin kode berikut ke tab **Tests** di request login Postman agar token otomatis tersimpan ke environment variable:
> ```javascript
> if (pm.response.code === 200) {
>     const res = pm.response.json();
>     if (res.user.role === 'admin') {
>         pm.environment.set("token_admin", res.token);
>     } else if (res.user.role === 'pemilik') {
>         pm.environment.set("token_pemilik", res.token);
>     } else {
>         pm.environment.set("token_pencari", res.token);
>     }
> }
> ```

---

## 4. Get Profil User Aktif (`/me`)
* **Method**: `GET`
* **URL**: `{{base_url}}/me`
* **Auth**: `Bearer Token` (`{{token_pemilik}}` atau `{{token_pencari}}`)
* **Expected Response** (`200 OK`):
  ```json
  {
    "user": {
      "id": 2,
      "nama": "Pemilik Kos",
      "email": "pemilik@kos.test",
      "role": "pemilik",
      "aktif": true,
      "created_at": "...",
      "updated_at": "..."
    }
  }
  ```

---

## 5. Logout
* **Method**: `POST`
* **URL**: `{{base_url}}/logout`
* **Auth**: `Bearer Token` (`{{token_pemilik}}`)
* **Expected Response** (`200 OK`):
  ```json
  {
    "message": "Logout berhasil."
  }
  ```
* *Catatan:* Setelah logout, jika Anda mencoba memanggil `GET {{base_url}}/me` kembali menggunakan token tersebut, sistem akan menolak dengan `401 Unauthenticated`.
