---
name: KosKu
description: Platform Pencarian & Sewa Kos Terintegrasi Indonesia
colors:
  primary: "#0B478B"
  on-primary: "#ffffff"
  primary-container: "#0D52A0"
  on-primary-container: "#ffffff"
  accent: "#FF9900"
  on-accent: "#ffffff"
  accent-container: "#FFF4E5"
  secondary: "#475569"
  secondary-container: "#f1f5f9"
  tertiary: "#FF9900"
  on-tertiary: "#ffffff"
  tertiary-container: "#FF9900"
  tertiary-fixed: "#FFF4E5"
  surface: "#ffffff"
  surface-container-lowest: "#ffffff"
  surface-container-low: "#f8fafc"
  surface-container: "#f1f5f9"
  surface-container-high: "#e2e8f0"
  on-surface: "#0f172a"
  on-surface-variant: "#475569"
  outline: "#cbd5e1"
  outline-variant: "#e2e8f0"
  error: "#dc2626"
  whatsapp-green: "#16a34a"
typography:
  display:
    fontFamily: "Satoshi, sans-serif"
    fontSize: "clamp(2rem, 5vw, 3rem)"
    fontWeight: 700
    lineHeight: 1.15
    letterSpacing: "-0.02em"
  headline:
    fontFamily: "Satoshi, sans-serif"
    fontSize: "1.5rem"
    fontWeight: 700
    lineHeight: 1.25
    letterSpacing: "-0.01em"
  title:
    fontFamily: "Satoshi, sans-serif"
    fontSize: "1.125rem"
    fontWeight: 600
    lineHeight: 1.35
  body:
    fontFamily: "Satoshi, sans-serif"
    fontSize: "0.875rem"
    fontWeight: 400
    lineHeight: 1.5
  label:
    fontFamily: "Satoshi, sans-serif"
    fontSize: "0.75rem"
    fontWeight: 500
    lineHeight: 1.4
rounded:
  sm: "4px"
  md: "8px"
  lg: "12px"
  xl: "16px"
  full: "9999px"
spacing:
  xs: "4px"
  sm: "8px"
  md: "16px"
  lg: "24px"
  xl: "32px"
components:
  button-primary:
    backgroundColor: "{colors.primary-container}"
    textColor: "{colors.on-primary}"
    rounded: "{rounded.md}"
    padding: "12px 24px"
  button-primary-hover:
    backgroundColor: "{colors.primary}"
  button-accent:
    backgroundColor: "{colors.accent}"
    textColor: "{colors.on-accent}"
    rounded: "{rounded.md}"
    padding: "10px 20px"
  button-whatsapp:
    backgroundColor: "{colors.whatsapp-green}"
    textColor: "{colors.on-primary}"
    rounded: "{rounded.lg}"
    padding: "10px 16px"
  card-surface:
    backgroundColor: "{colors.surface-container-lowest}"
    textColor: "{colors.on-surface}"
    rounded: "{rounded.xl}"
    padding: "16px"
---

# Design System: KosKu

## Overview

**Creative North Star: "The Nusantara Living Compass"**

KosKu dirancang sebagai pemandu arah hunian yang jernih, tenang, dan tepercaya di Indonesia. Identitas visualnya berakar langsung dari logo resmi KosKu, memadukan ketegasan **Deep Blue (#0B478B)** sebagai lambang kestabilan dan keamanan hunian, dengan kehangatan **Kuning Ke-orangan (#FF9900)** sebagai aksen ramah khas nusantara.

Setiap permukaan antarmuka memprioritaskan keterbacaan data properti, kepastian lokasi peta, dan kemudahan akses komunikasi langsung antara pencari kos dan pemilik.

**Key Characteristics:**
- **Harmonisasi Identitas Logo**: Warna Deep Blue `#0B478B` sebagai jangkar navigasi dan tombol aksi utama, dipadukan dengan aksen Kuning Ke-orangan `#FF9900` untuk sorotan rating, promo, dan status aktif.
- **Layered Tonal Surfaces**: Tingkatan kontras yang sejuk dan bersih (`#f8fafc`, `#ffffff`) tanpa bergantung pada bayangan pekat.
- **Direct Contact Affordance**: Jalur tindakan langsung (WhatsApp 1-klik dan navigasi Google Maps) yang selalu mudah ditemukan.
- **Adaptive Role Navigation**: Bilah atas publik yang luas untuk pencari berpadu harmonis dengan bilah laci samping yang efisien bagi pemilik kos.
- **Ergonomic Tactility**: Tombol dan target sentuh berukuran mantap (minimal 44px) yang nyaman dioperasikan dengan satu tangan di ponsel.

## Colors

Palet warna KosKu dibangun dari warna brand logo resmi KosKu dan palet netral Slate:

### Primary (Brand Logo)
- **KosKu Deep Blue** (`#0B478B`): Digunakan untuk identitas merek utama, tombol utama, tautan aktif, border fokus, dan status prioritas tinggi. Menghadirkan rasio kontras 9.17:1 di atas putih (melampaui WCAG AAA).
- **Interactive Deep Blue Container** (`#0D52A0`): Wadah interaktif untuk tombol submit/pencarian dengan feedback hover mendalam (`#083569`).
- **Primary Fixed Soft Tint** (`#e0edff`): Latar belakang sorotan lembut untuk tag aktif dan badge properti.

### Accent & Tertiary (Brand Logo)
- **KosKu Warm Yellow-Orange** (`#FF9900`): Aksen hangat khas logo KosKu untuk sorotan rating bintang, badge pilihan/top-rated, notifikasi perhatian, dan status sewa aktif.
- **Accent Container** (`#FFF4E5`): Latar belakang badge halus berpadu kontras tinggi dengan teks oranye gelap (`#9A3412`).

### Secondary & Neutrals
- **Slate Support** (`#475569`): Teks keterangan sekunder, ikon netral, dan label pembantu.
- **Ice Slate Canvas** (`#f8fafc`): Warna dasar latar belakang aplikasi; tenang, bersih, dan nyaman di mata.
- **Pure Canvas White** (`#ffffff`): Latar belakang kartu unit, modal dialog, dan dropdown navigasi.
- **Midnight Slate Ink** (`#0f172a`): Warna teks primer dengan rasio kontras tajam di atas latar belakang terang.
- **WhatsApp Action Green** (`#16a34a`): Aksen fungsional khusus untuk tombol komunikasi WhatsApp instan.

## Typography

**Display Font:** Satoshi (Fontshare, 400/500/700/900)  
**Body Font:** Satoshi (Fontshare, 400/500/700/900)  
**Icon Library:** Phosphor Icons (`@phosphor-icons/vue`)

**Character:** Tipografi geometris humanis Satoshi memberikan kejelasan tinggi pada angka tarif sewa, nama jalan, dan spesifikasi kamar pada seluruh ukuran layar.

### Hierarchy
- **Display** (Bold 700, clamp(2rem, 5vw, 3rem), line-height 1.15, tracking -0.02em): Digunakan untuk judul hero utama beranda pencarian.
- **Headline** (Bold 700, 1.5rem / 24px, line-height 1.25, tracking -0.01em): Digunakan untuk nama kos pada halaman detail dan tajuk bagian dashboard pengelola.
- **Title** (Semi-bold 600, 1.125rem / 18px, line-height 1.35): Digunakan untuk judul kartu listing, judul tab, dan nama nomor kamar kos.
- **Body** (Regular 400, 0.875rem / 14px, line-height 1.5): Digunakan untuk teks deskripsi properti, aturan kos, dan riwayat pesanan (maksimal 70ch).
- **Label** (Medium 500 & Semi-bold 600, 0.75rem / 12px, line-height 1.4): Digunakan untuk badge status kamar, tag gender (Putra/Putri/Campur), dan label form.

### Named Rules
**The Legibility-Over-Style Rule.** Seluruh nominal tarif bulanan dan status ketersediaan wajib ditulis dengan bobot minimal Semi-bold (600) untuk memastikan angka sewa terbaca jelas tanpa keraguan.

## Layout

KosKu mengadopsi struktur kisi adaptif mobile-first dengan kontainer terpusat untuk publik dan tata letak kanvas terpisah untuk dashboard operasional:

- **Max Container Width**: 1280px (`max-w-7xl`) dengan margin lateral 24px (`px-margin` / 1.5rem) pada desktop, dan 16px pada ponsel.
- **Rhythm & Spacing Scale**:
  - `space-xs` (4px): Jarak mikro antara ikon dan teks pendamping.
  - `space-sm` (8px): Jarak antar butir tag fasilitas dan padding internal badge.
  - `space-md` (16px): Padding internal kartu standar dan celah antar kolom form.
  - `space-lg` (24px): Jarak vertikal antar bagian dan celah kisi kartu listing.
  - `space-xl` (32px+): Pemisah seksi hero dan ruang bernapas halaman.
- **Responsive Layout**:
  - Tampilan publik menggunakan Top Navbar tetap (tinggi 64px / `h-16`) dengan backdrop blur.
  - Tampilan pemilik dan admin mengadopsi responsive drawer sidebar (lebar 260px desktop, off-canvas drawer di mobile).

## Elevation & Depth

Sistem mengutamakan **Tonal Layering** di atas bayangan tebal. Kedalaman dibentuk melalui tingkatan luminansi permukaan Material 3 (`surface` `#f8f9ff` $\rightarrow$ `surface-container-low` `#eff4ff` $\rightarrow$ `surface-container-lowest` `#ffffff`), dikombinasikan dengan garis tepi tipis (border 1px `border-slate-100` atau `border-surface-container-high/40`).

### Shadow Vocabulary
- **Ambient Low** (`box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05)`): Bayangan bawaan pada kartu kos dan kartu unit kamar saat diam (*rest*).
- **Interactive Lift** (`box-shadow: 0 10px 25px -5px rgba(0, 74, 198, 0.1), 0 8px 10px -6px rgba(0, 74, 198, 0.05)`): Bayangan melayang saat kartu disentuh atau kursor diarahkan (*hover state*).
- **Floating Overlays** (`box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.04)`): Digunakan untuk Floating Search Bar di hero dan tombol bantuan WhatsApp mengambang.

### Named Rules
**The Flat-By-Default Rule.** Kartu informasi dan panel data beristirahat secara datar dengan diferensiasi warna latar. Bayangan hanya diaktifkan untuk elemen yang benar-benar mengambang (floating bar, dropdown, sticky navbar, tombol CS).

## Shapes

- **Corner Strategy**:
  - **Pill (Full Rounded)** (`rounded-full` / 9999px): Digunakan untuk chip status jenis kos (Putri/Putra/Campur), badge verifikasi, rating bintang, dan tag harga mengambang.
  - **Large Curves (16px)** (`rounded-2xl` / 1rem): Digunakan untuk kontainer kartu properti, bingkai galeri foto utama, dan panel tab informasi.
  - **Medium Curves (12px)** (`rounded-xl` / 0.75rem): Digunakan untuk tombol WhatsApp, tombol booking, dan dropdown notifikasi.
  - **Standard Radius (8px)** (`rounded-lg` / 0.5rem): Digunakan untuk kolom input pencarian, thumbnail galeri, dan tombol filter cepat.

## Components

### Buttons
- **Shape:** Rounded medium (8px untuk filter/pencarian, 12px untuk CTA utama WhatsApp dan Pesan).
- **Primary Button:** Latar belakang Maritime Cobalt (`#2563eb` $\rightarrow$ `#004ac6`), teks putih, padding 12px 24px, font Semi-bold 14px.
- **WhatsApp Direct Action:** Latar belakang Emerald (`#16a34a` hover `#15803d`), teks putih, padding 10px 16px, memuat ikon chat 18px.
- **Hover & Active:** Efek transisi halus 150ms dengan sedikit kompresi sentuhan (`active:scale-[0.98]`).

### Chips & Badges
- **Tipe Kos (Putra / Putri / Campur):** Pill melingkar (`rounded-full`), teks 11px Semi-bold, kontras warna lembut (misal: aksen rose untuk putri, indigo untuk putra, emerald untuk campur).
- **Badge Verifikasi:** Latar belakang putih dengan backdrop blur, ikon centang terverifikasi, teks hijau zamrud 11px.
- **Rating Star:** Latar belakang putih dengan ikon bintang kuning emas amber (`#f59e0b`), teks skor angka tebal.

### Cards & Property Containers
- **Corner Style:** Radius melengkung 16px (`rounded-2xl`).
- **Background:** Pure Canvas White (`#ffffff`).
- **Border:** Garis halus 1px slate (`border-slate-100`).
- **Aspect Ratio:** Galeri foto 16:9 (`aspect-video`) dengan `overflow-hidden` dan pelapisan badge mengambang di sudut atas.

### Inputs & Search Fields
- **Style:** Latar belakang penampung sejuk (`#eff4ff`), radius 8px, tanpa garis batas keras saat pasif.
- **Focus:** Berubah ke warna putih bersih (`#ffffff`) dengan ring fokus halus (`ring-2 ring-primary/20`).

### Navigation
- **Public Topbar:** Ketinggian 64px, latar belakang putih semi-transparan dengan `backdrop-blur-md`, tautan navigasi dengan indikator teks tebal saat rute aktif.
- **Owner & Admin Sidebar:** Lebar tetap 260px pada desktop, bilah menu bertumpuk dengan penanda visual warna latar biru muda pada item terpilih.

## Do's and Don'ts

### Do:
- **Do** sediakan tombol akses cepat WhatsApp langsung pada setiap kartu kos dan halaman detail kamar.
- **Do** cantumkan alamat lengkap dan koordinat GPS tombol navigasi Google Maps untuk memudahkan pencari survei rute.
- **Do** pastikan target sentuh tombol minimal 44x44px pada mode ponsel.
- **Do** bedakan warna badge tipe kos (Putra, Putri, Campur) agar calon penyewa tidak salah memilih hunian.

### Don't:
- **Don't** menutupi foto kamar dengan badge iklan atau stiker diskon berukuran raksasa.
- **Don't** menggunakan teks abu-abu yang terlalu pucat di bawah kontras rasio WCAG AA (4.5:1 untuk teks biasa).
- **Don't** menyembunyikan nomor kontak pemilik kos di balik lebih dari dua langkah klik dialog yang membingungkan.
- **Don't** memaksakan tata letak horizontal pada layar ponsel kecil yang menyebabkan scrolling menyamping tanpa sengaja.
