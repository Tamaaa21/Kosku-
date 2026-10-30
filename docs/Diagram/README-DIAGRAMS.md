# Katalog Diagram Arsitektur & UML KosKu

Dokumentasi diagram perancangan sistem berbasis **PlantUML** untuk platform KosKu (Sistem Informasi Pencarian & Sewa Kos).

Semua file diagram berada di dalam direktori `docs/` dengan ekstensi `.puml`.

---

## 1. Use Case Diagram (Modular)

| Nama File | Lingkup Diagram | Deskripsi |
| :--- | :--- | :--- |
| [`usecase-global.puml`](file:///home/pratama/Downloads/project/backend-kos/docs/usecase-global.puml) | **Global (Bird's Eye View)** | Menampilkan seluruh batas sistem, 4 aktor, dan subsistem tingkat tinggi dalam 1 halaman ringkas. Cocok untuk pengantar arsitektur. |
| [`usecase-pencari.puml`](file:///home/pratama/Downloads/project/backend-kos/docs/usecase-pencari.puml) | **Pencari Kos & Pengunjung** | Rincian fitur eksplorasi katalog, peta Leaflet (GPS), WhatsApp 1-klik, pengajuan sewa, dan ulasan properti. |
| [`usecase-pemilik.puml`](file:///home/pratama/Downloads/project/backend-kos/docs/usecase-pemilik.puml) | **Mitra Pemilik Kos** | Rincian pengajuan verifikasi KTP/legalitas, kelola listing kos, foto galeri, stok/tarif kamar, dan konfirmasi reservasi sewa. |
| [`usecase-admin.puml`](file:///home/pratama/Downloads/project/backend-kos/docs/usecase-admin.puml) | **Administrator** | Rincian moderasi berkas verifikasi pemilik, moderasi kos, kontrol akun pengguna, master fasilitas, hero banner, dan statistik. |

---

## 2. Activity Diagram (Alur Proses Bisnis)

| Nama File | Proses Bisnis | Deskripsi Alur |
| :--- | :--- | :--- |
| [`activity-reservasi.puml`](file:///home/pratama/Downloads/project/backend-kos/docs/activity-reservasi.puml) | **Reservasi Sewa Kamar** | Alur dari pencari memilih kamar, pengecekan ketersediaan sistem, hingga persetujuan/penolakan oleh pemilik kos. |
| [`activity-verifikasi-pemilik.puml`](file:///home/pratama/Downloads/project/backend-kos/docs/activity-verifikasi-pemilik.puml) | **Verifikasi Legalitas Pemilik** | Alur pemilik mengunggah berkas KTP/properti, masuk antrean moderasi, hingga diaudit dan disetujui admin. |
| [`activity-pencarian-kos.puml`](file:///home/pratama/Downloads/project/backend-kos/docs/activity-pencarian-kos.puml) | **Pencarian & Filter Kos** | Alur input multi-filter, eksekusi query sistem, rendering kartu katalog dan pin marker pada peta GPS. |

---

## 3. Sequence Diagram (Skenario Interaksi Sistem)

| Nama File | Skenario Sinyal & Objek | Komponen Terlibat |
| :--- | :--- | :--- |
| [`sequence-auth-login.puml`](file:///home/pratama/Downloads/project/backend-kos/docs/sequence-auth-login.puml) | **Autentikasi & Login** | `LoginModal.vue` $\to$ `authStore` $\to$ `AuthController` $\to$ `Pengguna` (Token Sanctum) $\to$ MySQL. |
| [`sequence-reservasi.puml`](file:///home/pratama/Downloads/project/backend-kos/docs/sequence-reservasi.puml) | **Pengajuan Sewa Kamar (PlantUML)** | `BookingModal.vue` $\to$ `bookingStore` $\to$ `BookingController` $\to$ Cek Stok Kamar $\to$ Simpan Pemesanan. |
| [`sequence-reservasi.html`](file:///home/pratama/Downloads/project/backend-kos/docs/sequence-reservasi.html) | **Pengajuan Sewa Kamar (Archify Interaktif HTML)** | Versi web interaktif mandiri (*standalone HTML*), dilengkapi animasi trace alur pesan, mode gelap/terang, dan kartu ringkasan arsitektur. |
| [`sequence-konfirmasi-reservasi.puml`](file:///home/pratama/Downloads/project/backend-kos/docs/sequence-konfirmasi-reservasi.puml) | **Approval / Rejection Sewa** | `OwnerBookings.vue` $\to$ Middleware `verified_owner` $\to$ Update Status Pemesanan & Pengurangan Stok Kamar. |

---

## 4. Diagram Struktur Data Statis (1 Diagram Terpusat)

| Nama File | Jenis Diagram | Deskripsi |
| :--- | :--- | :--- |
| [`class-diagram.puml`](file:///home/pratama/Downloads/project/backend-kos/docs/class-diagram.puml) | **Class Diagram (UML)** | Struktur model domain Eloquent bertingkat (*Layered Packages*) dengan navigasi orthogonal (`Pengguna`, `Kos`, `Kamar`, `Pemesanan`, `Ulasan`, `Fasilitas`, `HeroBanner`). |
| [`erd-diagram.puml`](file:///home/pratama/Downloads/project/backend-kos/docs/erd-diagram.puml) | **Entity Relationship Diagram (ERD)** | Skema fisik basis data MySQL dengan notasi **Crow's Foot** lengkap dengan tipe data presisi, *Primary Key*, *Foreign Key*, dan *Unique Constraints*. |

---

## Cara Render / Pratinjau Diagram PlantUML

1. **Draw.io**:
   - Buka [app.diagrams.net](https://app.diagrams.net/)
   - Menu **Arrange** $\to$ **Insert** $\to$ **Advanced** $\to$ **PlantUML**
   - Tempel kode `.puml` dan klik **Insert**.
2. **VS Code**:
   - Pasang ekstensi **PlantUML** (Jebbs).
   - Buka file `.puml`, tekan shortcut `Alt + D` untuk melihat pratinjau langsung.
3. **PlantText Online**:
   - Buka [planttext.com](https://www.planttext.com/)
   - Salin isi file `.puml` untuk mengekspor ke format SVG, PNG, atau PDF.
