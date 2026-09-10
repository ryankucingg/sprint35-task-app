# Sprint35 Task App

Aplikasi manajemen tugas berbasis web: setiap pengguna mencatat dan memantau tugasnya sendiri (prioritas, status, tenggat waktu), sementara admin memantau seluruh tugas pengguna, membuatkan tugas untuk mereka, serta mengelola kategori dan akun pengguna.

## Tech Stack

- **Laravel 13** — backend framework (PHP 8.3+)
- **Livewire 4** — komponen interaktif backend/admin
- **Tailwind CSS 4** — utility-first CSS
- **DaisyUI 5** — component library (tema terang/gelap)
- **mrcatz/datatable** — datatable untuk CRUD & daftar data (search, filter, sort, export PDF/Excel)
- **ApexCharts** — grafik pada dashboard (distribusi status, prioritas, ketepatan tenggat), dimuat via CDN
- **MySQL** — database
- **Vite** — build tool aset frontend

## Cara Menjalankan

### 1. Install Dependency

```bash
composer install
npm install
```

### 2. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`, sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sprint35_taskapp
DB_USERNAME=root
DB_PASSWORD=
```

Buat database-nya terlebih dahulu (migrasi tidak membuat database otomatis):

```sql
CREATE DATABASE sprint35_taskapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Migrasi & Seed Database

```bash
php artisan migrate:fresh --seed
```

### 4. Build Aset Frontend

```bash
npm run build
```

### 5. Jalankan Aplikasi

```bash
php artisan serve
```

Aplikasi bisa diakses di `http://localhost:8000`. Halaman utama langsung mengarah ke login.

## Tema Default

Tema awal aplikasi diatur melalui `.env`:

```env
# Pilihan: light / dark / system (mengikuti sistem operasi)
DEFAULT_THEME=light
```

Perubahan hanya berlaku bagi pengunjung yang belum memilih tema sendiri — pilihan tema personal yang tersimpan di browser selalu diutamakan.

## Akun Pengujian

| Username | Password | Role | Hak Akses |
|---|---|---|---|
| `admin` | `password123` | Admin | Melihat semua tugas, membuatkan tugas untuk user, edit/hapus tugas siapa pun, kelola kategori & user |
| `user-1` | `password123` | User | Mengelola tugas miliknya sendiri |
| `user-2` | `password123` | User | Mengelola tugas miliknya sendiri |

> Untuk menguji pembatasan akses: login dengan dua akun user berbeda, lalu pastikan satu akun tidak dapat membuka, mengubah, atau menghapus tugas milik akun lainnya.
