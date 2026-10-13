# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

- **Pencari Kos**: Mahasiswa, perantau, pekerja, dan pasangan/keluarga di berbagai kota di Indonesia. Mencari hunian kos yang aman, sesuai anggaran, memiliki lokasi jelas, serta ingin berkomunikasi langsung dengan pemilik tanpa birokrasi berbelit.
- **Mitra Pemilik Kos**: Pengelola dan pemilik properti sewa yang membutuhkan platform terstruktur untuk menampilkan listing, memantau okupansi kamar secara real-time, dan memproses permohonan reservasi dengan cepat.
- **Administrator**: Pengawas platform yang mengawasi validitas listing, moderasi data pengguna, dan kelancaran transaksi sewa.

## Product Purpose

KosKu adalah platform pencarian dan penyewaan kos modern di Indonesia yang menjembatani pencari kos dengan mitra pemilik kos secara transparan, cepat, dan terpercaya. Kesuksesan produk didefinisikan oleh kemudahan pencari dalam menemukan kamar ideal serta efisiensi pemilik dalam mengelola dan menyewakan propertinya.

## Positioning

Kombinasi eksplorasi visual interaktif (peta Leaflet & rute Google Maps turn-by-turn) dengan jalur komunikasi instan WhatsApp 1-klik yang dipersonalisasi (pesan otomatis memuat nama kos, tipe kamar, dan status ketersediaan), didukung antarmuka berbasis peran (*role-based adapted layout*) yang ringan dan responsif.

## Operating Context

- Digunakan melalui web browser modern pada perangkat mobile/smartphone (terutama saat survei langsung di lokasi kos) dan desktop (terutama untuk administrasi inventori kamar oleh pemilik kos).
- Terhubung langsung dengan aplikasi WhatsApp dan Google Maps pada perangkat pengguna.

## Capabilities and Constraints

- **Arsitektur**: Decoupled RESTful API (Laravel 11, PHP 8.3, Sanctum Bearer Token) dan Frontend SPA (Vue 3 Composition API, Vite 5, Tailwind CSS 3, Pinia).
- **Otentikasi & Peran**: Role-Based Access Control (Publik/Pencari, Pemilik Kos, Administrator).
- **Fitur Utama**:
  - Katalog dan filter kos multi-parameter (harga, tipe putra/putri/campur, fasilitas, kota).
  - Visualisasi peta interaktif dengan clustering dan navigasi GPS.
  - Alur reservasi sewa (Menunggu, Disetujui, Ditolak) disertai notifikasi dinamis.
  - Manajemen unit kamar dan fasilitas kos bagi pemilik.
  - Widget bantuan terpadu (Floating Customer Service).

## Brand Commitments

- **Nama**: KosKu (Platform Pencarian & Sewa Kos Indonesia).
- **Tone & Voice**: Hangat, ramah, jelas, komunikatif, dan terpercaya.
- **Identitas Visual**: Palet utama bernuansa Emerald / Teal yang segar dan bersih, visualisasi kartu properti terstruktur, dan tata letak yang mengutamakan kemudahan navigasi di layar ponsel maupun desktop.

## Evidence on Hand

- Repositori frontend aktif di [`frontend/`](file:///home/pratama/Downloads/project/backend-kos/frontend) (Vue 3, Tailwind CSS).
- Dokumentasi teknis & spesifikasi: [`README.md`](file:///home/pratama/Downloads/project/backend-kos/README.md), [`plan.md`](file:///home/pratama/Downloads/project/backend-kos/plan.md), [`REST_API_DESIGN_WORKSHEET.md`](file:///home/pratama/Downloads/project/backend-kos/REST_API_DESIGN_WORKSHEET.md), dan [`database_schema_migration_specification.md`](file:///home/pratama/Downloads/project/backend-kos/database_schema_migration_specification.md).
- Folder aset antarmuka referensi: `ui admin/` dan `ui pemilik/` (Mockup Stitch KosKu Boarding Platform UI/UX).

## Product Principles

1. **Direct & Frictionless Contact**: Menghubungkan calon penyewa dan pemilik sedekat mungkin melalui integrasi WhatsApp instan dan navigasi peta langsung.
2. **Context-Aware Presentation**: Layout otomatis menyesuaikan peran pengguna (topbar eksplorasi untuk pencari vs responsive drawer sidebar untuk pemilik).
3. **Spatial & Pricing Clarity**: Informasi lokasi, foto, fasilitas, dan biaya ditampilkan secara transparan dan akurat tanpa biaya tersembunyi.
4. **Mobile-First Responsiveness**: Menjamin pengalaman gesit, ringan, dan mudah dioperasikan dengan satu tangan saat pengguna berada di jalan.

## Accessibility & Inclusion

- Target sentuh tombol dan elemen interaktif minimal 44x44px untuk kenyamanan sentuhan di ponsel.
- Kontras warna teks dan latar belakang yang memenuhi standar keterbacaan tinggi di berbagai kondisi pencahayaan layar.
- Indikator status (kamar tersedia, terisi, status pesanan) dilengkapi label teks jelas, tidak hanya bergantung pada warna semata.
