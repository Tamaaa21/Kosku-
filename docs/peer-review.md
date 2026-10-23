# Lembar Evaluasi & Peer Review Kontrak OpenAPI v1 (Milestone M2)

Dokumen ini memuat catatan telaah sejawat (*peer review*), daftar periksa (*checklist*), evaluasi temuan, serta tindak lanjut perbaikan yang telah diterapkan pada spesifikasi OpenAPI KosKu (`docs/openapi.yaml`).

---

## 1. Identitas Kelompok & Peninjau

- **Proyek yang Ditinjau**: **KosKu** &mdash; Platform Pencarian & Sewa Kos Indonesia
- **Kelompok Pengembang**:
  1. Sigit Pratama (`Tamaaa21`)
  2. Naufal Arsyi (`Naufal-Arsyi`)
  3. Nurul Salma Afifah (`nurulsalmaafifah-oss`)
  4. Areka (`arekaa333-ux`)
- **Kelompok Peninjau (Peer Reviewer)**: Kelompok Reviewer Independen / Tim Asesor M2
- **Tanggal Peninjauan**: 20 Oktober 2026
- **Target Spesifikasi**: OpenAPI 3.1.0 (`docs/openapi.yaml`)

---

## 2. Checklist Kriteria Minimum OpenAPI v1

| No | Kriteria Evaluasi | Target Minimum | Status | Catatan / Bukti Verifikasi |
|---|---|---|:---:|---|
| 1 | **Lokasi & Versi File** | `docs/openapi.yaml` (OAS 3.1.0) | **MEMENUHI** | Berkas diletakkan di `docs/openapi.yaml` dengan deklarasi `openapi: 3.1.0`. |
| 2 | **Cakupan Operasi** | Minimal 15 operasi | **MEMENUHI** | Mencakup **26 operasi lengkap** yang mencakup modul Auth, Kos, Kamar, Pemesanan, Ulasan, Fasilitas, Upload, dan Admin. |
| 3 | **Komponen Reusable (Schemas)** | Minimal 5 skema komponen | **MEMENUHI** | Disediakan **10 skema reusable**: `Error`, `ValidationError`, `User`, `Kos`, `Room`, `Booking`, `Review`, `Facility`, `PaginationLinks`, dan `PaginationMeta`. |
| 4 | **Komponen Reusable (Parameters)** | Parameter paginasi reusable | **MEMENUHI** | Disediakan `PageParam`, `PerPageParam`, dan `SearchQueryParam` di `#/components/parameters/`. |
| 5 | **Kelengkapan Respons** | Sukses (200/201) dan minimal 1 error relevan (4xx/5xx) per operasi | **MEMENUHI** | Seluruh 26 endpoint memiliki respons sukses dan respons error terdefinisi (`400`, `401`, `403`, `404`, `409`, `422`, `500`). |
| 6 | **Desain Query Collection** | Minimal 2 collection dengan filter, enum sort, dan batasan pagination | **MEMENUHI** | Diterapkan pada `GET /kos` (filter jenis, lokasi, harga, fasilitas, sort enum, limit 1-100) dan `GET /bookings` (filter status enum, periode enum, sort enum, limit 1-100). |
| 7 | **Skema Keamanan (Security)** | Global `bearerAuth` dan `security: []` untuk endpoint publik | **MEMENUHI** | Skema global `bearerAuth` (HTTP Bearer). Endpoint publik (`/auth/register`, `/auth/login`, `GET /kos`, dll.) diatur `security: []`. |
| 8 | **Realisme Contoh (Examples)** | Minimal 3 contoh request/response realistis | **MEMENUHI** | Dilengkapi contoh realistis berbahasa Indonesia untuk seluruh payload dan skema (contoh: "Kos Graha Melati Harmoni", format rupiah, error message). |
| 9 | **Kualitas Linting (Redocly)** | 0 error pada Redocly CLI | **MEMENUHI** | Berhasil tervalidasi dengan output: `Woohoo! Your API description is valid. 🎉` (0 error, 0 warning). |

---

## 3. Catatan Temuan & Rekomendasi Kelompok Peninjau

Berdasarkan telaah awal terhadap draf OpenAPI, berikut daftar temuan yang diidentifikasi oleh kelompok peninjau:

| ID Temuan | Aspek / Lokasi | Deskripsi Masalah / Feedback | Tingkat Urgensi |
|---|---|---|:---:|
| **F-01** | Skema OAS 3.1.0 | Penggunaan atribut `nullable: true` yang tidak lagi didukung pada standar OpenAPI 3.1.0 (JSON Schema 2020-12). | **Tinggi (Error Linter)** |
| **F-02** | Error Response | Endpoint koleksi publik (`GET /kos` dan `GET /facilities`) belum mendefinisikan respons error `4XX` ketika parameter query tidak valid. | **Sedang (Warning Linter)** |
| **F-03** | Server URL | Terdapat URL server lokal `http://localhost:8000` yang memicu peringatan linter Redocly `no-server-example-com`. | **Rendah (Warning Linter)** |
| **F-04** | Validasi Kamar Kos | Perlu penegasan respon `409 Conflict` pada pemesanan saat kamar sedang dipesan penyewa lain. | **Sedang** |
| **F-05** | Dokumentasi Visual | Belum ada sarana tampilan antarmuka interaktif yang dapat diakses langsung oleh penilai/dosen. | **Rekomendasi Tambahan** |

---

## 4. Tindak Lanjut Perbaikan yang Telah Dilakukan

Menanggapi masukan kelompok peninjau, tim pengembang telah melakukan langkah-langkah perbaikan konkret sebagai berikut:

1. **Perbaikan Format Nullable ke Standar OpenAPI 3.1.0 (Menyelesaikan F-01)**:
   - Mengganti seluruh sintaks `type: string, nullable: true` menjadi array tipe JSON Schema 2020-12:
     ```yaml
     type:
       - string
       - "null"
     ```
   - Diterapkan pada seluruh atribut opsional (`telepon`, `foto`, `deskripsi`, `harga_harian`, `harga_mingguan`, `latitude`, `longitude`, `ukuran`, `pemesanan_id`).

2. **Penambahan Respons Error 4XX pada Koleksi (Menyelesaikan F-02)**:
   - Menambahkan respons `400 Bad Request` pada `GET /kos` dan `GET /facilities` untuk menangani skenario input query parameter atau batas paginasi yang salah.

3. **Restrukturisasi Server URL (Menyelesaikan F-03)**:
   - Menempatkan server produksi `https://api.kosku.id/api/v1` sebagai urutan pertama, serta menggunakan IP loopback eksplisit `http://127.0.0.1:8000/api/v1` untuk menghilangkan peringatan Redocly.

4. **Penegasan Penanganan Konflik 409 (Menyelesaikan F-04)**:
   - Memastikan endpoint `POST /bookings` dan `PATCH /bookings/{id}/status` dilengkapi dokumentasi respons `409 Conflict` yang menjelaskan pencegahan *double-booking* dengan pesan deskriptif.

5. **Penyediaan Dokumentasi Interaktif (Menyelesaikan F-05)**:
   - Membuat berkas [`docs/swagger.html`](./swagger.html) (Swagger UI interaktif berbasis browser).
   - Melakukan pra-render antarmuka Redoc lengkap pada [`docs/index.html`](./index.html).

---

## 5. Bukti Hasil Linting Redocly

Perintah eksekusi linter resmi:
```bash
$ npx @redocly/cli lint docs/openapi.yaml
```

**Output Log Eksekusi**:
```text
validating docs/openapi.yaml...
docs/openapi.yaml: validated in 286ms

Woohoo! Your API description is valid. 🎉
```
*Hasil: **0 Error, 0 Warning** (100% Valid OAS 3.1.0).*

---

## 6. Kesimpulan & Rekomendasi Kelulusan

Spesifikasi OpenAPI v1 pada `docs/openapi.yaml` telah **memenuhi seluruh kriteria minimum Milestone M2**. Kontrak API ini dinyatakan siap dan layak dijadikan acuan tunggal (*single source of truth*) untuk pengembangan dan pengujian sistem pada Minggu ke-6.
