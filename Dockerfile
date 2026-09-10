# ============================================
# Stage 1: Composer dependencies
# ============================================
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-interaction \
    --no-scripts \
    --no-autoloader \
    --no-dev \
    --prefer-dist \
    --ignore-platform-reqs

COPY . .

# Empty sqlite file so any boot-time Schema::hasTable() / config reads that
# expect the DB file to exist don't crash during `package:discover`, which
# runs via post-autoload-dump. The real DB ships in a mounted volume at
# runtime, not in the image.
RUN mkdir -p database && touch database/database.sqlite

RUN composer dump-autoload --optimize --no-dev

# ============================================
# Stage 2: Node.js build assets
# ============================================
FROM node:22-alpine AS assets

# libc6-compat is required for native bindings on musl/alpine
# (lightningcss & @tailwindcss/oxide).
RUN apk add --no-cache libc6-compat

WORKDIR /app

COPY package.json package-lock.json* ./
RUN npm ci

# Tailwind v4's `@source` directives in resources/css/app.css scan blade
# files under vendor/mrcatz and vendor/laravel/framework (pagination views).
# Without the PHP vendor tree present here, any class that only appears
# inside the datatable package won't be compiled into the final bundle,
# and the live demo table will render unstyled.
COPY --from=vendor /app/vendor/mrcatz /app/vendor/mrcatz
COPY --from=vendor /app/vendor/laravel/framework/src/Illuminate/Pagination \
                   /app/vendor/laravel/framework/src/Illuminate/Pagination

COPY . .
RUN npm run build

# ============================================
# Stage 3: ServerSideUp FrankenPHP + Laravel Octane
# ============================================
FROM serversideup/php:8.4-frankenphp

# Install PHP extensions the docs site + mrcatz/datatable need that aren't
# in the base image (intl, gd, bcmath, exif).
USER root
RUN install-php-extensions \
    intl \
    gd \
    bcmath \
    exif

# Pre-init script: dijalankan s6 sebelum AUTORUN_LARAVEL_MIGRATION,
# memastikan sqlite file ada di volume yang baru di-mount.
COPY docker/entrypoint.sh /etc/entrypoint.d/05-mrcatz-init.sh
RUN chmod +x /etc/entrypoint.d/05-mrcatz-init.sh
USER www-data

# Copy application source, then vendor & built assets from the earlier
# stages so we don't ship composer / node / their caches into the runtime.
COPY --chown=www-data:www-data . /var/www/html
COPY --chown=www-data:www-data --from=vendor /app/vendor /var/www/html/vendor
COPY --chown=www-data:www-data --from=assets /app/public/build /var/www/html/public/build

