#!/usr/bin/env sh
#
# Init script untuk serversideup/php — dijalankan otomatis oleh s6
# sebelum nginx & php-fpm start. Letakkan di /etc/entrypoint.d/.
#
set -e

cd /var/www/html

# Volume app-database menimpa folder database/ → file sqlite dari image
# hilang pada first boot. Buat ulang sebelum migration automation jalan.
mkdir -p database
if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
    chown www-data:www-data database/database.sqlite
    chmod 664 database/database.sqlite
fi

# Generate APP_KEY kalau belum ada (idempotent — aman dijalankan tiap boot)
if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    php artisan key:generate --force --no-interaction || true
fi

# Pastikan storage symlink tersedia
php artisan storage:link --force || true

# Cache config/route/view untuk performa production
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true
php artisan event:cache || true
