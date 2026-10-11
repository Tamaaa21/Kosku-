# Antigravity Task Execution Plan: Sistem Informasi Pencarian Kos

**Role:** Autonomous Full-Stack AI Engineer  
**Project:** Sistem Informasi Pencarian Kos Terintegrasi Berbasis REST API dengan Layanan Lokasi  
**Target Environment:** Laravel 11 Backend (API Service) + Vue 3 Frontend (Vite SPA Client)  
**Authentication & Security:** Laravel Sanctum + Role-Based Access Control (RBAC)  

---

## 1. System Overview & Boundaries

- **Architecture:** Decoupled Client-Service (`/api/v1` prefix).
- **Actors & Roles:** `pencari`, `pemilik`, `admin`.
- **External Integration:** Nominatim OpenStreetMap Geocoding API (`Http::class`).
- **Core Constraints:**
  - Strict JSON request/response formats.
  - Ownership enforcement (Policy) on `kos` and `kamar` (Forbidden 403 on violations).
  - Database transactions on reservations to prevent double-booking race conditions (409 Conflict).
  - Graceful degradation if external geocoding fails.

---

## 2. Execution Phases & Task Checklist

### Phase 1: Environment Setup & Project Scaffolding
- [ ] Initialize Laravel backend:
  - Command: `composer create-project laravel/laravel backend`
  - Setup API scaffolding & Sanctum: `php artisan install:api`
  - Configure `.env` database parameters and Sanctum stateful domains.
- [ ] Initialize Vue 3 frontend:
  - Command: `npm create vite@latest frontend -- --template vue`
  - Install dependencies: `vue-router`, `pinia`, `axios`, `leaflet`, `@vue-leaflet/vue-leaflet`, `tailwindcss`, `@tailwindcss/forms`.
  - Configure Tailwind CSS in `frontend/tailwind.config.js`.

---

### Phase 2: Database Schema, Models & Seeders
- [ ] Create database migration files with strict constraints:
  - **`users` (alias `pengguna`):** `id`, `name`, `email`, `password`, `role` (enum: `pencari`, `pemilik`, `admin`), `aktif` (boolean, default `true`), timestamps.
  - **`kos`:** `id`, `pemilik_id` (foreign key to `users`), `nama`, `alamat`, `deskripsi`, `jenis` (enum: `putra`, `putri`, `campur`), `harga` (unsigned integer), `latitude` (decimal 10,7, nullable), `longitude` (decimal 10,7, nullable), timestamps.
  - **`kamar`:** `id`, `kos_id` (foreign key to `kos`, cascade on delete), `nama_kamar`, `harga` (unsigned integer), `status` (enum: `tersedia`, `terisi`), timestamps.
  - **`fasilitas`:** `id`, `nama` (unique), `deskripsi` (nullable), timestamps.
  - **`kos_fasilitas`:** `kos_id` (foreign key), `fasilitas_id` (foreign key), primary key composite.
  - **`pemesanan`:** `id`, `pengguna_id` (foreign key to `users`), `kos_id` (foreign key to `kos`), `kamar_id` (foreign key to `kamar`), `tanggal_pemesanan` (date), `status` (enum: `menunggu`, `disetujui`, `ditolak`, `dibatalkan`), timestamps.
- [ ] Configure Eloquent relationships:
  - `User`: `hasMany(Kos::class, 'pemilik_id')`, `hasMany(Pemesanan::class, 'pengguna_id')`.
  - `Kos`: `belongsTo(User::class, 'pemilik_id')`, `hasMany(Kamar::class)`, `belongsToMany(Fasilitas::class, 'kos_fasilitas')`, `hasMany(Pemesanan::class)`.
  - `Kamar`: `belongsTo(Kos::class)`, `hasMany(Pemesanan::class)`.
  - `Fasilitas`: `belongsToMany(Kos::class, 'kos_fasilitas')`.
  - `Pemesanan`: `belongsTo(User::class)`, `belongsTo(Kos::class)`, `belongsTo(Kamar::class)`.
- [ ] Create Database Seeders (`DatabaseSeeder`, `FasilitasSeeder`, `DummyKosSeeder`) for rapid testing.

---

### Phase 3: Middleware, RBAC & Policies
- [ ] Create Role Middleware `EnsureUserRole`:
  - Verify authenticated user role matches route requirement.
  - Return `403 Forbidden` (`{"message": "Akses ditolak untuk peran Anda."}`) when unauthorized.
- [ ] Register middleware alias `'role'` in `bootstrap/app.php`.
- [ ] Create Authorization Policies:
  - `KosPolicy`: `update` and `delete` only allowed if `$user->id === $kos->pemilik_id` (Admin can also delete).
  - `KamarPolicy`: `update` and `delete` only allowed if `$user->id === $kos->pemilik_id`.
  - `PemesananPolicy`: `view` (pencari pembuat / pemilik kos), `updateStatus` (pemilik approve/reject, pencari cancel only if status is `menunggu`).

---

### Phase 4: External Geocoding Service
- [ ] Implement `App\Services\GeocodingService`:
  - External Provider: Nominatim OpenStreetMap (`https://nominatim.openstreetmap.org/search`).
  - Required Header: Unique `User-Agent`.
  - Methods: `geocodeAddress(string $address): ?array` returning `['latitude' => float, 'longitude' => float]`.
  - Implement resilience: Wrap in `try/catch` with 5-second timeout. Fallback to `null` if unreachable so kos creation does not fail.

---

### Phase 5: Backend REST API Endpoints Implementation (`/api/v1`)
- [ ] **Authentication & Profile:**
  - `POST /register` -> `AuthController@register` (Validation, default role: `pencari` / `pemilik`).
  - `POST /login` -> `AuthController@login` (Sanctum plainTextToken).
  - `POST /logout` -> `AuthController@logout` (Revoke current token).
  - `GET /me` -> `AuthController@me`.
- [ ] **Kos Management & Search:**
  - `GET /kos` -> Filterable catalog (`q`, `harga_min`, `harga_max`, `lokasi`, `jenis`, `fasilitas[]`, `tersedia`, `sort`, `page`).
  - `GET /kos/{id}` -> Detail with eager-loaded kamar, fasilitas, and pemilik.
  - `POST /kos` -> [Role: pemilik] Geocoding integration on address, sync fasilitas IDs.
  - `PUT /kos/{id}` -> [Policy: owner] Re-geocode if address modified, update fields.
  - `DELETE /kos/{id}` -> [Policy: owner / admin].
- [ ] **Kamar Management:**
  - `GET /kos/{id}/kamar` -> List rooms of a specific kos.
  - `POST /kos/{id}/kamar` -> [Policy: owner] Add room.
  - `PUT /kamar/{id}` -> [Policy: owner] Update room status/price.
  - `DELETE /kamar/{id}` -> [Policy: owner] Delete room.
- [ ] **Master Fasilitas (Admin):**
  - `GET /fasilitas` -> Public.
  - `POST /fasilitas`, `PUT /fasilitas/{id}`, `DELETE /fasilitas/{id}` -> [Role: admin].
- [ ] **Pemesanan Workflow:**
  - `POST /pemesanan` -> [Role: pencari] Atomic check: room must be `tersedia`, no conflicting pending/approved booking.
  - `GET /pemesanan` -> List own bookings (pencari) or incoming bookings for owned kos (pemilik).
  - `GET /pemesanan/{id}` -> Booking details.
  - `PATCH /pemesanan/{id}/status` -> Update status (Pemilik: `disetujui`/`ditolak`; Pencari: `dibatalkan`). Automatically toggle room availability to `terisi` on approval.
- [ ] **User Administration:**
  - `GET /admin/pengguna` -> [Role: admin]
  - `PATCH /admin/pengguna/{id}/status` -> [Role: admin] Toggle active status.

---

### Phase 6: Web Client (Vue 3 SPA)
- [ ] **Setup HTTP Client & Global State:**
  - Configure `axios` instance with base URL `/api/v1` and request interceptor injecting `Bearer ${token}`.
  - Pinia `useAuthStore` handling user state, token persistence (`localStorage`), login, and logout.
- [ ] **Routing & Route Guards:**
  - Configure `vue-router` with meta fields (`requiresAuth: true`, `roles: ['pemilik', 'admin']`).
  - Implement navigation guard (`router.beforeEach`) checking authentication and authorized roles.
- [ ] **Public Views:**
  - `HomeView.vue`: Search bar and interactive filter drawer.
  - `KosDetailView.vue`: Room availability table, amenities tags, and Leaflet map displaying coordinates.
- [ ] **Role Dashboards:**
  - `Pencari`: Booking status history list with cancel button.
  - `Pemilik`: Kos CRUD, Room management, Booking approval inbox.
  - `Admin`: User account management and amenity master data editor.

---

## 3. Automated Verification & Testing Commands

To verify full implementation conformity with the PRD metrics:

```bash
# 1. Run database migrations and seeders
php artisan migrate:fresh --seed

# 2. Execute Backend Unit & Feature Tests
php artisan test --filter=AuthTest
php artisan test --filter=KosSearchTest
php artisan test --filter=BookingTransactionTest
php artisan test --filter=GeocodingIntegrationTest

# 3. Test API Coverage target (>= 70%)
php artisan test --coverage --min=70

# 4. Frontend Compilation & Linting
cd frontend && npm run build
```

---

## 4. Definition of Done (DoD) Checklist

- [ ] All 23 endpoints implemented under `/api/v1` returning uniform JSON envelopes.
- [ ] Authorization strictly guarded: Unauthorized roles receive 403, missing tokens receive 401.
- [ ] Geocoding service automatically resolves coordinates on Kos creation/update without blocking if external API fails.
- [ ] Concurrency handled on bookings using database transactions preventing double reservations.
- [ ] Web client connects seamlessly with Axios, renders interactive maps with Leaflet, and enforces role access.