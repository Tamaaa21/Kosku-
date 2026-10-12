#!/bin/sh
set -e

echo "==> [KosKu] Menjalankan inisialisasi kontainer..."

# 1. Menunggu database siap
if [ -n "$DB_HOST" ]; then
    echo "==> Menunggu database di $DB_HOST:${DB_PORT:-3306}..."
    max_retries=30
    retry_count=0
    until php -r "try { new PDO('mysql:host='.getenv('DB_HOST').';port='.(getenv('DB_PORT') ?: '3306'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0); } catch (\Throwable \$e) { exit(1); }" 2>/dev/null; do
        retry_count=$((retry_count + 1))
        if [ "$retry_count" -ge "$max_retries" ]; then
            echo "==> [ERROR] Gagal terhubung ke database setelah $max_retries percobaan."
            break
        fi
        sleep 2
    done
    echo "==> [OK] Koneksi database berhasil!"
fi

# 2. Cek atau generate APP_KEY jika belum ada
if [ -z "$APP_KEY" ]; then
    echo "==> APP_KEY tidak ditemukan, membuat application key baru..."
    php artisan key:generate --force
fi

# 3. Pastikan symlink storage publik tersedia
php artisan storage:link --no-interaction 2>/dev/null || true

# 4. Pastikan permission folder storage dan cache tetap aman
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# 5. Jalankan migrasi database otomatis jika diaktifkan
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "==> Menjalankan migrasi database..."
    php artisan migrate --force

    # Jalankan seeder jika diaktifkan (misal pada deployment pertama)
    if [ "${RUN_SEEDER:-true}" = "true" ]; then
        echo "==> Menjalankan seeder database bawaan (Pengguna, Fasilitas, Kos, Kamar)..."
        php artisan db:seed --force
    fi
fi

# 6. Cache konfigurasi & route pada mode production
if [ "${APP_ENV:-production}" = "production" ]; then
    echo "==> Mengoptimalkan cache Laravel untuk production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

echo "==> [KosKu] Inisialisasi selesai. Menjalankan perintah utama: $@"
exec "$@"
