<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kostara API Playground & Tester</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#017E91',
                        'primary-hover': '#016877',
                        secondary: '#E68202',
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
                    }
                }
            }
        }
    </script>
    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: rgba(15, 23, 42, 0.6); }
        ::-webkit-scrollbar-thumb { background: rgba(51, 65, 85, 0.8); border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(71, 85, 105, 1); }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col font-sans select-text">

    <!-- Top Bar Header -->
    <header class="bg-slate-950/95 border-b border-slate-800 sticky top-0 z-40 backdrop-blur-md px-4 lg:px-6 py-3">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="/" class="flex items-center gap-2 group">
                    <span class="text-xl font-extrabold tracking-tight">
                        <span class="text-primary">Kos</span><span class="text-secondary">tara</span>
                    </span>
                </a>
                <div class="h-5 w-px bg-slate-700 hidden sm:block"></div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 text-xs font-bold bg-primary/20 text-sky-400 border border-primary/40 rounded-md">
                        API TEST SUITE v1
                    </span>
                    <span class="text-xs text-slate-400 hidden lg:inline">
                        Standalone REST API Interactive Testing Console
                    </span>
                </div>
            </div>

            <!-- Global Environment & Auth Tools -->
            <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs">
                <!-- Base URL -->
                <div class="flex items-center bg-slate-900 border border-slate-700/80 rounded-lg px-2.5 py-1.5 gap-2">
                    <span class="text-slate-400 font-semibold">Base URL:</span>
                    <input
                        id="baseUrlInput"
                        type="text"
                        class="bg-transparent text-slate-200 outline-none w-48 sm:w-60 text-xs font-mono"
                        value="/api/v1"
                    />
                </div>

                <!-- Auth Status -->
                <div id="authStatusBadge" class="px-2.5 py-1.5 rounded-lg bg-slate-800 text-slate-400 border border-slate-700 font-mono">
                    Guest (No Auth)
                </div>

                <!-- Quick Login Buttons -->
                <div class="flex items-center gap-1">
                    <button onclick="quickLogin('admin@kos.test', 'password')" class="px-2.5 py-1.5 bg-primary/20 text-sky-400 border border-primary/40 hover:bg-primary hover:text-white active:scale-95 rounded-lg font-bold text-[11px] transition cursor-pointer" title="Login Admin">
                        Admin
                    </button>
                    <button onclick="quickLogin('pemilik@kos.test', 'password')" class="px-2.5 py-1.5 bg-amber-500/20 text-amber-300 border border-amber-500/40 hover:bg-amber-500 hover:text-white active:scale-95 rounded-lg font-bold text-[11px] transition cursor-pointer" title="Login Pemilik">
                        Pemilik
                    </button>
                    <button onclick="quickLogin('pencari@kos.test', 'password')" class="px-2.5 py-1.5 bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-500 hover:text-white active:scale-95 rounded-lg font-bold text-[11px] transition cursor-pointer" title="Login Pencari">
                        Pencari
                    </button>
                    <button onclick="clearToken()" class="px-2.5 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 rounded-lg font-bold text-[11px] transition cursor-pointer" title="Clear Token">
                        Clear Token
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Workspace Layout -->
    <div class="flex-1 max-w-7xl w-full mx-auto p-4 lg:p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 min-h-0">
        <!-- Sidebar Endpoints List (lg:col-span-4) -->
        <aside class="lg:col-span-4 flex flex-col bg-slate-950/70 border border-slate-800 rounded-2xl overflow-hidden max-h-[88vh]">
            <div class="p-3.5 border-b border-slate-800 space-y-2.5 bg-slate-950/90">
                <input
                    id="endpointSearchInput"
                    type="text"
                    oninput="filterEndpoints()"
                    placeholder="Cari endpoint atau URL..."
                    class="w-full bg-slate-900 border border-slate-700/80 rounded-xl px-3 py-1.5 text-xs text-slate-200 placeholder:text-slate-500 outline-none focus:border-primary transition"
                />
                <div class="flex flex-wrap items-center gap-1">
                    <button onclick="setMethodFilter('ALL')" class="method-btn px-2 py-0.5 rounded text-[10px] font-bold tracking-wider transition bg-primary text-white cursor-pointer" data-method="ALL">ALL</button>
                    <button onclick="setMethodFilter('GET')" class="method-btn px-2 py-0.5 rounded text-[10px] font-bold tracking-wider transition bg-slate-900 text-slate-400 hover:bg-slate-800 cursor-pointer" data-method="GET">GET</button>
                    <button onclick="setMethodFilter('POST')" class="method-btn px-2 py-0.5 rounded text-[10px] font-bold tracking-wider transition bg-slate-900 text-slate-400 hover:bg-slate-800 cursor-pointer" data-method="POST">POST</button>
                    <button onclick="setMethodFilter('PUT')" class="method-btn px-2 py-0.5 rounded text-[10px] font-bold tracking-wider transition bg-slate-900 text-slate-400 hover:bg-slate-800 cursor-pointer" data-method="PUT">PUT</button>
                    <button onclick="setMethodFilter('PATCH')" class="method-btn px-2 py-0.5 rounded text-[10px] font-bold tracking-wider transition bg-slate-900 text-slate-400 hover:bg-slate-800 cursor-pointer" data-method="PATCH">PATCH</button>
                    <button onclick="setMethodFilter('DELETE')" class="method-btn px-2 py-0.5 rounded text-[10px] font-bold tracking-wider transition bg-slate-900 text-slate-400 hover:bg-slate-800 cursor-pointer" data-method="DELETE">DELETE</button>
                </div>
            </div>

            <!-- List of Endpoints Container -->
            <div id="endpointsListContainer" class="flex-1 overflow-y-auto divide-y divide-slate-800/60 p-1.5 space-y-1">
                <!-- Injected via JavaScript -->
            </div>

            <div class="p-2.5 border-t border-slate-800 bg-slate-950 text-[11px] text-slate-500 flex items-center justify-between">
                <span id="endpointCounterText">49 Endpoint Tersedia</span>
                <a href="/" class="text-primary hover:underline">Beranda Kostara</a>
            </div>
        </aside>

        <!-- Right Main: Request & Response Console (lg:col-span-8) -->
        <main class="lg:col-span-8 flex flex-col gap-5 min-h-0">
            <!-- Request Builder -->
            <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-4 sm:p-5 flex flex-col gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-800">
                    <div>
                        <div class="flex items-center gap-2">
                            <span id="currentMethodBadge" class="px-2 py-0.5 rounded text-xs font-black uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                POST
                            </span>
                            <h2 id="currentEndpointName" class="text-base font-bold text-slate-100">
                                Pilih Endpoint
                            </h2>
                        </div>
                        <p id="currentEndpointDesc" class="text-xs text-slate-400 mt-1">
                            Deskripsi endpoint akan ditampilkan di sini.
                        </p>
                    </div>

                    <span id="currentAuthBadge" class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                        Public
                    </span>
                </div>

                <!-- URL Bar -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                    <div class="flex-1 flex items-center bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs font-mono overflow-hidden focus-within:border-primary transition">
                        <span id="urlPrefixSpan" class="text-slate-500 select-none mr-1">/api/v1</span>
                        <input
                            id="endpointPathInput"
                            type="text"
                            class="flex-1 bg-transparent text-slate-100 outline-none font-mono"
                            value="/auth/login"
                        />
                    </div>

                    <button
                        id="sendBtn"
                        onclick="executeRequest()"
                        class="px-5 py-2 bg-primary hover:bg-primary-hover active:scale-95 text-white text-xs font-bold rounded-xl flex items-center justify-center gap-2 transition cursor-pointer shadow-md"
                    >
                        <span>Kirim Request</span>
                    </button>

                    <button
                        onclick="copyCurl()"
                        class="px-3 py-2 bg-slate-900 hover:bg-slate-800 active:scale-95 text-slate-300 border border-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer"
                        title="Salin cURL"
                    >
                        cURL
                    </button>
                </div>

                <!-- Configuration Tabs -->
                <div>
                    <div class="flex items-center gap-2 border-b border-slate-800 text-xs font-semibold">
                        <button onclick="switchReqTab('body')" id="reqTabBodyBtn" class="pb-2 px-3 text-primary border-b-2 border-primary font-bold cursor-pointer">
                            Request Body (JSON)
                        </button>
                        <button onclick="switchReqTab('headers')" id="reqTabHeadersBtn" class="pb-2 px-3 text-slate-400 hover:text-slate-200 cursor-pointer">
                            Headers
                        </button>
                        <button onclick="switchReqTab('docs')" id="reqTabDocsBtn" class="pb-2 px-3 text-slate-400 hover:text-slate-200 cursor-pointer">
                            Panduan
                        </button>
                    </div>

                    <div class="pt-3">
                        <div id="reqTabBodyContent">
                            <textarea
                                id="requestBodyInput"
                                rows="8"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-xs font-mono text-slate-200 outline-none focus:border-primary transition resize-y"
                                placeholder='{"key": "value"}'
                            ></textarea>
                        </div>
                        <div id="reqTabHeadersContent" class="hidden space-y-2">
                            <textarea
                                id="requestHeadersInput"
                                rows="5"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-xs font-mono text-slate-200 outline-none focus:border-primary transition"
                            >Accept: application/json
Content-Type: application/json</textarea>
                            <p class="text-[11px] text-slate-500">Satu header per baris (Format: Key: Value). Token otorisasi disisipkan otomatis jika aktif.</p>
                        </div>
                        <div id="reqTabDocsContent" class="hidden space-y-2 text-xs text-slate-300">
                            <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-800">
                                <div class="font-bold text-slate-200">Catatan:</div>
                                <p id="endpointDocNotes" class="text-slate-400 mt-1">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Response Panel -->
            <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-4 sm:p-5 flex flex-col gap-3">
                <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-slate-400">Response:</span>
                        <span id="responseStatusBadge" class="px-2.5 py-0.5 rounded-full text-xs font-black tracking-wide bg-slate-800 text-slate-400">
                            Belum Ada Request
                        </span>
                    </div>

                    <div class="flex items-center gap-3 text-xs text-slate-400 font-mono">
                        <span id="responseTimeBadge">- ms</span>
                        <span id="responseSizeBadge">-</span>
                        <button onclick="copyResponse()" class="px-2 py-1 bg-slate-900 hover:bg-slate-800 rounded text-slate-300 text-xs cursor-pointer">
                            Salin JSON
                        </button>
                    </div>
                </div>

                <div class="relative bg-slate-900 border border-slate-800 rounded-xl p-3 max-h-[420px] overflow-y-auto font-mono text-xs text-slate-200">
                    <pre id="responseBodyPre" class="whitespace-pre-wrap leading-relaxed">// Tekan tombol "Kirim Request" untuk melihat respons API secara langsung...</pre>
                </div>
            </div>
        </main>
    </div>

    <!-- Script Application -->
    <script>
        const endpoints = [
            // 1. Auth & Akun
            { id: 'auth-register', category: 'Autentikasi & Akun', name: 'Pendaftaran Akun Baru', method: 'POST', path: '/auth/register', auth: 'public', description: 'Mendaftarkan akun baru (pencari / pemilik kos).', body: JSON.stringify({ nama: 'Budi Santoso', email: 'budi.santoso@example.com', telepon: '081234567899', role: 'pencari', password: 'password123', password_confirmation: 'password123' }, null, 2), notes: 'Role: pencari atau pemilik.' },
            { id: 'auth-login', category: 'Autentikasi & Akun', name: 'Login Pengguna', method: 'POST', path: '/auth/login', auth: 'public', description: 'Login email & password untuk memperoleh Bearer token.', body: JSON.stringify({ email: 'admin@kos.test', password: 'password' }, null, 2), notes: 'Default: admin@kos.test / password' },
            { id: 'auth-forgot-password', category: 'Autentikasi & Akun', name: 'Lupa Password', method: 'POST', path: '/auth/forgot-password', auth: 'public', description: 'Mengirimkan tautan reset kata sandi ke email.', body: JSON.stringify({ email: 'pencari@kos.test' }, null, 2), notes: 'Email harus terdaftar.' },
            { id: 'auth-reset-password', category: 'Autentikasi & Akun', name: 'Reset Password', method: 'POST', path: '/auth/reset-password', auth: 'public', description: 'Mereset kata sandi menggunakan token email.', body: JSON.stringify({ token: 'contoh_token', email: 'pencari@kos.test', password: 'passwordBaru123', password_confirmation: 'passwordBaru123' }, null, 2), notes: 'Memerlukan token reset valid.' },
            { id: 'auth-me', category: 'Autentikasi & Akun', name: 'Profil Saya (Me)', method: 'GET', path: '/auth/me', auth: 'auth', description: 'Mengambil data profil pengguna yang sedang login.', body: '', notes: 'Memerlukan Bearer token.' },
            { id: 'auth-update-profile', category: 'Autentikasi & Akun', name: 'Perbarui Data Profil', method: 'PUT', path: '/me', auth: 'auth', description: 'Mengubah nama dan nomor telepon profil saat ini.', body: JSON.stringify({ nama: 'Budi Santoso Diperbarui', telepon: '081299887766' }, null, 2), notes: 'Format telepon 10-15 digit.' },
            { id: 'auth-update-password', category: 'Autentikasi & Akun', name: 'Ganti Kata Sandi', method: 'PUT', path: '/me/password', auth: 'auth', description: 'Mengubah kata sandi akun.', body: JSON.stringify({ current_password: 'password', password: 'passwordBaru123', password_confirmation: 'passwordBaru123' }, null, 2), notes: 'Password lama harus tepat.' },
            { id: 'auth-logout', category: 'Autentikasi & Akun', name: 'Logout', method: 'POST', path: '/auth/logout', auth: 'auth', description: 'Mencabut Bearer token pengguna saat ini.', body: '', notes: 'Token dihapus dari database.' },

            // 2. Katalog Kos
            { id: 'kos-index', category: 'Katalog Kos', name: 'Daftar Kos (Pencarian & Filter)', method: 'GET', path: '/kos?sort=terbaru&per_page=10&page=1', auth: 'public', description: 'Daftar kos dengan filter kota, harga, tipe, dan radius.', body: '', notes: 'Mendukung query: search, kota, min_price, max_price, latitude, longitude, radius, sort.' },
            { id: 'kos-show', category: 'Katalog Kos', name: 'Detail Kos', method: 'GET', path: '/kos/1', auth: 'public', description: 'Informasi lengkap kos, pemilik, kamar, dan fasilitas.', body: '', notes: 'Ganti /1 dengan ID kos.' },
            { id: 'kos-store', category: 'Katalog Kos', name: 'Tambah Kos Baru', method: 'POST', path: '/kos', auth: 'pemilik terverifikasi', description: 'Membuat properti kos baru.', body: JSON.stringify({ nama: 'Kos Harmoni Baru', alamat: 'Jl. Gejayan No. 45', deskripsi: 'Kos nyaman dekat kampus.', jenis: 'campur', harga: 1200000, fasilitas_ids: [1, 2] }, null, 2), notes: 'Khusus Pemilik terverifikasi.' },
            { id: 'kos-update', category: 'Katalog Kos', name: 'Perbarui Kos', method: 'PUT', path: '/kos/1', auth: 'pemilik / admin', description: 'Mengubah informasi kos.', body: JSON.stringify({ nama: 'Kos Harmoni Renovasi', harga: 1300000 }, null, 2), notes: 'Ganti /1 dengan ID kos.' },
            { id: 'kos-destroy', category: 'Katalog Kos', name: 'Hapus Kos', method: 'DELETE', path: '/kos/1', auth: 'pemilik / admin', description: 'Menghapus kos beserta kamarnya.', body: '', notes: 'Tindakan permanen.' },

            // 3. Kamar Kos
            { id: 'rooms-index', category: 'Kamar Kos', name: 'Daftar Kamar per Kos', method: 'GET', path: '/kos/1/rooms', auth: 'public', description: 'Mengambil daftar unit/tipe kamar di suatu kos.', body: '', notes: 'Ganti /1/rooms dengan ID kos.' },
            { id: 'rooms-store', category: 'Kamar Kos', name: 'Tambah Kamar Baru', method: 'POST', path: '/kos/1/rooms', auth: 'pemilik terverifikasi', description: 'Menambahkan kamar ke dalam kos.', body: JSON.stringify({ nama_kamar: 'Kamar Deluxe 101', harga: 1250000, status: 'tersedia', ukuran: '3x4 m', fasilitas_kamar: ['AC', 'Kamar Mandi Dalam'] }, null, 2), notes: 'Khusus Pemilik terverifikasi.' },
            { id: 'rooms-update', category: 'Kamar Kos', name: 'Perbarui Kamar', method: 'PUT', path: '/rooms/1', auth: 'pemilik / admin', description: 'Mengubah data kamar.', body: JSON.stringify({ nama_kamar: 'Kamar Deluxe A1', harga: 1300000, status: 'tersedia' }, null, 2), notes: 'Path /rooms/{id}.' },
            { id: 'rooms-destroy', category: 'Kamar Kos', name: 'Hapus Kamar', method: 'DELETE', path: '/rooms/1', auth: 'pemilik / admin', description: 'Menghapus data kamar.', body: '', notes: 'Path /rooms/{id}.' },

            // 4. Pemesanan / Booking
            { id: 'bookings-store', category: 'Pemesanan / Booking', name: 'Pengajuan Pemesanan Kamar', method: 'POST', path: '/bookings', auth: 'pencari', description: 'Mengajukan sewa kamar kos dengan proteksi pessimistic locking.', body: JSON.stringify({ kamar_id: 1, tanggal_pemesanan: new Date().toISOString().split('T')[0], durasi_tipe: 'bulanan', durasi_jumlah: 1 }, null, 2), notes: 'Khusus akun Pencari.' },
            { id: 'bookings-index', category: 'Pemesanan / Booking', name: 'Daftar Riwayat Pemesanan', method: 'GET', path: '/bookings?page=1&per_page=10', auth: 'auth', description: 'Melihat riwayat pesanan sewa.', body: '', notes: 'Otomatis difilter sesuai role pengguna.' },
            { id: 'bookings-show', category: 'Pemesanan / Booking', name: 'Detail Pemesanan', method: 'GET', path: '/bookings/1', auth: 'auth', description: 'Detail lengkap pemesanan kamar.', body: '', notes: 'Ganti /1 dengan ID pesanan.' },
            { id: 'bookings-status', category: 'Pemesanan / Booking', name: 'Ubah Status Pemesanan', method: 'PATCH', path: '/bookings/1/status', auth: 'pemilik / admin', description: 'Menyetujui atau menolak pesanan.', body: JSON.stringify({ status: 'disetujui' }, null, 2), notes: 'Status: disetujui, ditolak, dibatalkan.' },
            { id: 'bookings-export', category: 'Pemesanan / Booking', name: 'Ekspor Pemesanan (CSV)', method: 'GET', path: '/bookings/export', auth: 'pemilik / admin', description: 'Streaming file CSV riwayat pemesanan ber-UTF-8 BOM.', body: '', notes: 'Menghasilkan file .csv streaming.' },

            // 5. Ulasan & Rating
            { id: 'reviews-index', category: 'Ulasan & Rating', name: 'Daftar Ulasan per Kos', method: 'GET', path: '/kos/1/reviews?per_page=10&page=1', auth: 'public', description: 'Testimoni dan rating suatu kos.', body: '', notes: 'Ganti /1 dengan ID kos.' },
            { id: 'reviews-eligibility', category: 'Ulasan & Rating', name: 'Cek Kelayakan Ulasan', method: 'GET', path: '/kos/1/reviews/eligibility', auth: 'pencari', description: 'Cek apakah pencari berhak mengulas kos.', body: '', notes: 'Harus memiliki pesanan disetujui.' },
            { id: 'reviews-store', category: 'Ulasan & Rating', name: 'Kirim Ulasan', method: 'POST', path: '/kos/1/reviews', auth: 'pencari', description: 'Mengirim rating bintang (1-5) dan komentar.', body: JSON.stringify({ rating: 5, komentar: 'Tempat kos sangat bersih, aman, dan nyaman!' }, null, 2), notes: 'Rating 1-5.' },

            // 6. Fasilitas Properti
            { id: 'facilities-index', category: 'Fasilitas Properti', name: 'Daftar Semua Fasilitas', method: 'GET', path: '/facilities', auth: 'public', description: 'Mengambil master fasilitas properti.', body: '', notes: 'Menyertakan jumlah kos per fasilitas.' },
            { id: 'facilities-store', category: 'Fasilitas Properti', name: 'Tambah Fasilitas', method: 'POST', path: '/facilities', auth: 'pemilik / admin', description: 'Menambahkan fasilitas baru.', body: JSON.stringify({ nama: 'Dapur Komunal Lengkap', deskripsi: 'Dilengkapi kompor dan kulkas.' }, null, 2), notes: 'Nama fasilitas unik.' },
            { id: 'facilities-update', category: 'Fasilitas Properti', name: 'Perbarui Fasilitas (Admin)', method: 'PUT', path: '/facilities/1', auth: 'admin', description: 'Mengubah data fasilitas.', body: JSON.stringify({ nama: 'WiFi Super Cepat 100Mbps', deskripsi: 'Akses internet tanpa batas.' }, null, 2), notes: 'Khusus Admin.' },
            { id: 'facilities-destroy', category: 'Fasilitas Properti', name: 'Hapus Fasilitas (Admin)', method: 'DELETE', path: '/facilities/1', auth: 'admin', description: 'Menghapus fasilitas dari sistem.', body: '', notes: 'Khusus Admin.' },

            // 7. Verifikasi Pemilik
            { id: 'verification-show', category: 'Verifikasi Pemilik', name: 'Status Verifikasi Pemilik', method: 'GET', path: '/pemilik/verification', auth: 'pemilik', description: 'Mengecek status verifikasi KTP pemilik.', body: '', notes: 'Status: unverified, pending, verified, rejected.' },
            { id: 'verification-submit', category: 'Verifikasi Pemilik', name: 'Kirim Dokumen Verifikasi', method: 'POST', path: '/pemilik/verification', auth: 'pemilik', description: 'Submit NIK & dokumen kepemilikan.', body: JSON.stringify({ nik: '3201123456780005', foto_ktp: 'https://images.unsplash.com/photo-1544717305-2782549b5136?w=800', bukti_properti: 'https://images.unsplash.com/photo-1582407947304-fd86f028f716?w=800', foto_properti: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800', catatan_verifikasi: 'Pengajuan kos Gejayan' }, null, 2), notes: 'NIK 16-20 digit.' },

            // 8. Hero Banner Slider
            { id: 'banners-public', category: 'Hero Banner Slider', name: 'Banner Publik (Beranda)', method: 'GET', path: '/hero-banners', auth: 'public', description: 'Banner slider aktif untuk beranda.', body: '', notes: 'Diurutkan berdasarkan urutan ascending.' },
            { id: 'banners-admin-index', category: 'Hero Banner Slider', name: 'Daftar Semua Banner (Admin)', method: 'GET', path: '/admin/hero-banners', auth: 'admin', description: 'Daftar banner untuk admin.', body: '', notes: 'Khusus Admin.' },
            { id: 'banners-admin-store', category: 'Hero Banner Slider', name: 'Tambah Banner (Admin)', method: 'POST', path: '/admin/hero-banners', auth: 'admin', description: 'Menambahkan banner slider baru.', body: JSON.stringify({ judul: 'Promo Spesial Kost Eksklusif', subjudul: 'Dapatkan diskon sewa bulan pertama.', gambar_url: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=1200', urutan: 1, is_active: true }, null, 2), notes: 'gambar_url wajib.' },
            { id: 'banners-admin-show', category: 'Hero Banner Slider', name: 'Detail Banner (Admin)', method: 'GET', path: '/admin/hero-banners/1', auth: 'admin', description: 'Melihat detail data banner.', body: '', notes: 'Ganti /1 dengan ID banner.' },
            { id: 'banners-admin-update', category: 'Hero Banner Slider', name: 'Perbarui Banner (Admin)', method: 'PUT', path: '/admin/hero-banners/1', auth: 'admin', description: 'Mengubah judul, teks, atau gambar banner.', body: JSON.stringify({ judul: 'Hunian Modern Indonesia', gambar_url: 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=1200', urutan: 1, is_active: true }, null, 2), notes: 'Khusus Admin.' },
            { id: 'banners-admin-status', category: 'Hero Banner Slider', name: 'Ubah Status Banner (Admin)', method: 'PATCH', path: '/admin/hero-banners/1/status', auth: 'admin', description: 'Mengaktifkan atau menonaktifkan banner.', body: JSON.stringify({ is_active: true }, null, 2), notes: 'is_active: boolean.' },
            { id: 'banners-admin-destroy', category: 'Hero Banner Slider', name: 'Hapus Banner (Admin)', method: 'DELETE', path: '/admin/hero-banners/1', auth: 'admin', description: 'Menghapus banner slider.', body: '', notes: 'Khusus Admin.' },

            // 9. Admin Panel & Moderasi
            { id: 'admin-stats', category: 'Panel Admin & Moderasi', name: 'Statistik Ringkasan Sistem (Admin)', method: 'GET', path: '/admin/stats', auth: 'admin', description: 'Ringkasan metrik users, kos, dan pemesanan.', body: '', notes: 'Khusus Admin.' },
            { id: 'admin-users-index', category: 'Panel Admin & Moderasi', name: 'Daftar Pengguna (Admin)', method: 'GET', path: '/admin/users?page=1&per_page=10', auth: 'admin', description: 'Manajemen pengguna platform.', body: '', notes: 'Filter: role, aktif, q, sort.' },
            { id: 'admin-users-show', category: 'Panel Admin & Moderasi', name: 'Detail Pengguna (Admin)', method: 'GET', path: '/admin/users/1', auth: 'admin', description: 'Melihat profil salah satu akun user.', body: '', notes: 'Ganti /1 dengan ID user.' },
            { id: 'admin-users-status', category: 'Panel Admin & Moderasi', name: 'Ubah Status User (Admin)', method: 'PATCH', path: '/admin/users/2/status', auth: 'admin', description: 'Mengaktifkan / memblokir akun user.', body: JSON.stringify({ aktif: true }, null, 2), notes: 'aktif: true/false.' },
            { id: 'admin-verifications-index', category: 'Panel Admin & Moderasi', name: 'Antrean Verifikasi Pemilik', method: 'GET', path: '/admin/verifications?page=1', auth: 'admin', description: 'Daftar dokumen verifikasi pemilik kos.', body: '', notes: 'Khusus Admin.' },
            { id: 'admin-verifications-review', category: 'Panel Admin & Moderasi', name: 'Putusan Verifikasi (Admin)', method: 'PATCH', path: '/admin/verifications/2/status', auth: 'admin', description: 'Menyetujui (verified) atau menolak (rejected) kepemilikan kos.', body: JSON.stringify({ status: 'verified', alasan_penolakan: null }, null, 2), notes: 'status: verified | rejected.' },
            { id: 'admin-kos-index', category: 'Panel Admin & Moderasi', name: 'Daftar Moderasi Kos (Admin)', method: 'GET', path: '/admin/kos?page=1&per_page=10', auth: 'admin', description: 'Seluruh listing kos untuk moderasi.', body: '', notes: 'Khusus Admin.' },
            { id: 'admin-kos-destroy', category: 'Panel Admin & Moderasi', name: 'Hapus Kos Moderasi (Admin)', method: 'DELETE', path: '/admin/kos/1', auth: 'admin', description: 'Menghapus kos yang melanggar aturan.', body: '', notes: 'Khusus Admin.' },
        ];

        let currentFilterMethod = 'ALL';
        let selectedEndpoint = endpoints[1]; // default login
        let authToken = localStorage.getItem('token') || '';

        function renderAuthStatus() {
            const el = document.getElementById('authStatusBadge');
            if (authToken) {
                el.className = 'px-2.5 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 font-mono text-xs font-bold';
                el.innerText = 'Token Active (Bearer)';
            } else {
                el.className = 'px-2.5 py-1.5 rounded-lg bg-slate-800 text-slate-400 border border-slate-700 font-mono text-xs';
                el.innerText = 'Guest (No Auth)';
            }
        }

        function clearToken() {
            authToken = '';
            localStorage.removeItem('token');
            renderAuthStatus();
            alert('Token dihapus.');
        }

        async function quickLogin(email, password) {
            const baseUrl = document.getElementById('baseUrlInput').value.replace(/\/$/, '');
            try {
                const res = await fetch(`${baseUrl}/auth/login`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });
                const data = await res.json();
                if (data.token) {
                    authToken = data.token;
                    localStorage.setItem('token', data.token);
                    renderAuthStatus();
                    alert(`Login berhasil sebagai: ${data.user?.nama || email}`);
                } else {
                    alert('Login gagal: ' + (data.message || 'Token tidak ditemukan.'));
                }
            } catch (err) {
                alert('Gagal login: ' + err.message);
            }
        }

        function methodClass(m) {
            switch(m) {
                case 'GET': return 'bg-sky-500/20 text-sky-400 border border-sky-500/30';
                case 'POST': return 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30';
                case 'PUT': return 'bg-amber-500/20 text-amber-400 border border-amber-500/30';
                case 'PATCH': return 'bg-purple-500/20 text-purple-400 border border-purple-500/30';
                case 'DELETE': return 'bg-rose-500/20 text-rose-400 border border-rose-500/30';
                default: return 'bg-slate-800 text-slate-300';
            }
        }

        function renderEndpoints() {
            const container = document.getElementById('endpointsListContainer');
            const search = document.getElementById('endpointSearchInput').value.toLowerCase().trim();
            container.innerHTML = '';

            const filtered = endpoints.filter(ep => {
                if (currentFilterMethod !== 'ALL' && ep.method !== currentFilterMethod) return false;
                if (search && !ep.name.toLowerCase().includes(search) && !ep.path.toLowerCase().includes(search) && !ep.category.toLowerCase().includes(search)) return false;
                return true;
            });

            document.getElementById('endpointCounterText').innerText = `${filtered.length} dari ${endpoints.length} Endpoint`;

            filtered.forEach(ep => {
                const btn = document.createElement('button');
                const isSelected = selectedEndpoint && selectedEndpoint.id === ep.id;
                btn.className = `w-full text-left p-2.5 rounded-xl transition flex flex-col gap-1 cursor-pointer select-none ${isSelected ? 'bg-slate-800 border border-primary/50' : 'hover:bg-slate-900 border border-transparent'}`;
                btn.onclick = () => selectEndpoint(ep);

                btn.innerHTML = `
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold uppercase ${methodClass(ep.method)}">${ep.method}</span>
                        <span class="text-[10px] text-slate-400 font-medium truncate">${ep.category}</span>
                    </div>
                    <div class="text-xs font-semibold text-slate-200 truncate">${ep.name}</div>
                    <div class="flex items-center justify-between text-[11px] font-mono text-slate-400 truncate">
                        <span class="truncate">${ep.path}</span>
                        <span class="text-[10px] text-amber-400/90 ml-1 shrink-0">${ep.auth !== 'public' ? '🔒 ' + ep.auth : ''}</span>
                    </div>
                `;
                container.appendChild(btn);
            });
        }

        function setMethodFilter(m) {
            currentFilterMethod = m;
            document.querySelectorAll('.method-btn').forEach(b => {
                if (b.dataset.method === m) {
                    b.className = 'method-btn px-2 py-0.5 rounded text-[10px] font-bold tracking-wider transition bg-primary text-white cursor-pointer';
                } else {
                    b.className = 'method-btn px-2 py-0.5 rounded text-[10px] font-bold tracking-wider transition bg-slate-900 text-slate-400 hover:bg-slate-800 cursor-pointer';
                }
            });
            renderEndpoints();
        }

        function filterEndpoints() {
            renderEndpoints();
        }

        function selectEndpoint(ep) {
            selectedEndpoint = ep;
            document.getElementById('currentMethodBadge').className = `px-2 py-0.5 rounded text-xs font-black uppercase ${methodClass(ep.method)}`;
            document.getElementById('currentMethodBadge').innerText = ep.method;
            document.getElementById('currentEndpointName').innerText = ep.name;
            document.getElementById('currentEndpointDesc').innerText = ep.description;
            document.getElementById('endpointPathInput').value = ep.path;
            document.getElementById('requestBodyInput').value = ep.body || '';
            document.getElementById('endpointDocNotes').innerText = ep.notes || '-';

            const authBadge = document.getElementById('currentAuthBadge');
            if (ep.auth === 'public') {
                authBadge.className = 'shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30';
                authBadge.innerText = 'Publik (Tanpa Token)';
            } else {
                authBadge.className = 'shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30';
                authBadge.innerText = 'Auth: ' + ep.auth;
            }

            renderEndpoints();
        }

        function switchReqTab(tab) {
            ['body', 'headers', 'docs'].forEach(t => {
                document.getElementById(`reqTab${t.charAt(0).toUpperCase() + t.slice(1)}Content`).classList.add('hidden');
                document.getElementById(`reqTab${t.charAt(0).toUpperCase() + t.slice(1)}Btn`).className = 'pb-2 px-3 text-slate-400 hover:text-slate-200 cursor-pointer';
            });
            document.getElementById(`reqTab${tab.charAt(0).toUpperCase() + tab.slice(1)}Content`).classList.remove('hidden');
            document.getElementById(`reqTab${tab.charAt(0).toUpperCase() + tab.slice(1)}Btn`).className = 'pb-2 px-3 text-primary border-b-2 border-primary font-bold cursor-pointer';
        }

        async function executeRequest() {
            if (!selectedEndpoint) return;
            const baseUrl = document.getElementById('baseUrlInput').value.replace(/\/$/, '');
            const path = document.getElementById('endpointPathInput').value;
            const fullUrl = `${baseUrl}${path}`;
            const sendBtn = document.getElementById('sendBtn');
            sendBtn.innerHTML = '<span>Mengirim...</span>';
            sendBtn.disabled = true;

            const headers = { 'Accept': 'application/json' };
            if (['POST', 'PUT', 'PATCH'].includes(selectedEndpoint.method)) {
                headers['Content-Type'] = 'application/json';
            }
            if (authToken) {
                headers['Authorization'] = `Bearer ${authToken}`;
            }

            // Custom headers
            const rawCustomHeaders = document.getElementById('requestHeadersInput').value.split('\n');
            rawCustomHeaders.forEach(line => {
                const parts = line.split(':');
                if (parts.length >= 2) {
                    headers[parts[0].trim()] = parts.slice(1).join(':').trim();
                }
            });

            const options = {
                method: selectedEndpoint.method,
                headers
            };

            const bodyVal = document.getElementById('requestBodyInput').value.trim();
            if (['POST', 'PUT', 'PATCH'].includes(selectedEndpoint.method) && bodyVal) {
                options.body = bodyVal;
            }

            const startTime = performance.now();
            try {
                const res = await fetch(fullUrl, options);
                const duration = Math.round(performance.now() - startTime);
                document.getElementById('responseTimeBadge').innerText = `${duration} ms`;

                let resData;
                const contentType = res.headers.get('content-type') || '';
                if (contentType.includes('application/json')) {
                    resData = await res.json();
                    document.getElementById('responseBodyPre').innerText = JSON.stringify(resData, null, 2);
                    document.getElementById('responseSizeBadge').innerText = `${(new Blob([JSON.stringify(resData)]).size / 1024).toFixed(1)} KB`;
                } else if (contentType.includes('text/csv')) {
                    const text = await res.text();
                    document.getElementById('responseBodyPre').innerText = text;
                    document.getElementById('responseSizeBadge').innerText = `${(new Blob([text]).size / 1024).toFixed(1)} KB`;
                } else {
                    const text = await res.text();
                    document.getElementById('responseBodyPre').innerText = text;
                    document.getElementById('responseSizeBadge').innerText = `${(new Blob([text]).size / 1024).toFixed(1)} KB`;
                }

                const statusBadge = document.getElementById('responseStatusBadge');
                statusBadge.innerText = `${res.status} ${res.statusText || ''}`;
                if (res.status >= 200 && res.status < 300) {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-xs font-black tracking-wide bg-emerald-500/20 text-emerald-400 border border-emerald-500/30';
                } else if (res.status >= 400 && res.status < 500) {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-xs font-black tracking-wide bg-amber-500/20 text-amber-400 border border-amber-500/30';
                } else {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-xs font-black tracking-wide bg-rose-500/20 text-rose-400 border border-rose-500/30';
                }
            } catch (err) {
                const duration = Math.round(performance.now() - startTime);
                document.getElementById('responseTimeBadge').innerText = `${duration} ms`;
                document.getElementById('responseSizeBadge').innerText = '0 B';
                document.getElementById('responseStatusBadge').className = 'px-2.5 py-0.5 rounded-full text-xs font-black tracking-wide bg-rose-500/20 text-rose-400 border border-rose-500/30';
                document.getElementById('responseStatusBadge').innerText = 'Network Error';
                document.getElementById('responseBodyPre').innerText = JSON.stringify({ error: err.message, help: 'Periksa koneksi jaringan atau URL Base API.' }, null, 2);
            } finally {
                sendBtn.innerHTML = '<span>Kirim Request</span>';
                sendBtn.disabled = false;
            }
        }

        function copyCurl() {
            const baseUrl = document.getElementById('baseUrlInput').value.replace(/\/$/, '');
            const path = document.getElementById('endpointPathInput').value;
            let curl = `curl -X ${selectedEndpoint.method} "${baseUrl}${path}"`;
            if (authToken) {
                curl += ` \\\n  -H "Authorization: Bearer ${authToken}"`;
            }
            curl += ` \\\n  -H "Accept: application/json"`;
            const bodyVal = document.getElementById('requestBodyInput').value.trim();
            if (['POST', 'PUT', 'PATCH'].includes(selectedEndpoint.method) && bodyVal) {
                curl += ` \\\n  -H "Content-Type: application/json"`;
                curl += ` \\\n  -d '${bodyVal.replace(/'/g, `'\\''`)}'`;
            }
            navigator.clipboard.writeText(curl);
            alert('cURL command disalin ke clipboard!');
        }

        function copyResponse() {
            const text = document.getElementById('responseBodyPre').innerText;
            navigator.clipboard.writeText(text);
            alert('JSON respons disalin!');
        }

        // Initialize on load
        window.addEventListener('DOMContentLoaded', () => {
            renderAuthStatus();
            renderEndpoints();
            selectEndpoint(endpoints[1]);
        });
    </script>
</body>
</html>
