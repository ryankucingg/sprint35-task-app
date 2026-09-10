# MrCatz Laravel Template

Starter template Laravel lengkap dengan admin panel (Livewire), frontend public, dark/light mode, CRUD datatable, manajemen user & pengaturan, serta export PDF/Excel. Siap pakai untuk projek baru — setup cepat dengan satu perintah.

## Tech Stack

- **Laravel 13** — Backend framework (PHP 8.3+)
- **Livewire 4** — Backend/admin interactive components
- **Tailwind CSS 4** — Utility-first CSS
- **DaisyUI 5** — Component library (dark/light theme)
- **Vite 8** — Build tool & HMR
- **Maatwebsite Excel** — Export Excel (styled, zebra-striped)
- **Barryvdh DomPDF** — Export PDF
- **mrcatz/datatable** — Custom datatable component library

## Fitur Bawaan

### Frontend (Public)
- Halaman landing page (configurable via database settings)
- Navbar responsive dengan dark/light mode toggle
- Footer dengan info kontak & social media (dinamis dari database)

### Backend / Admin Panel
- Login dengan username & password
- Role-based access control: `super-admin` dan `admin`
- **Dashboard** — Halaman utama admin
- **Manajemen User** (khusus super-admin) — CRUD user, assign role, cek username realtime
- **Pengaturan Aplikasi** (khusus super-admin) — Kelola nama aplikasi, kontak, social media langsung dari admin panel
- **Profil** — Edit nama, username, email, upload avatar, ubah password
- Sidebar collapsible — di desktop menyempit jadi rel ikon (label diganti tooltip), di mobile tetap drawer biasa
- Dark/light mode toggle
- Base class datatable (`MrCatzDataTablesComponent`) dengan:
  - Search (simple & advanced relevance scoring)
  - Filter dropdown
  - Sorting per kolom
  - Pagination
  - Export PDF & Excel (semua data / sesuai filter, dengan styling profesional)
- Reusable UI components: `input`, `select`, `textarea`, `file-input`, `toggle`, `chooser`, `breadcrumbs`, `navigation-item`
- Toast notification system
- Breadcrumb navigation

### Keamanan
- Rate limiting pada login (5x/menit)
- Security headers middleware (X-Frame-Options, X-Content-Type-Options, HSTS, dll)
- Session encryption (database-backed)
- XSS protection pada datatable search
- Authorization: admin tidak bisa mengelola super-admin
- Audit logging (login, logout, CRUD user, perubahan settings)

## Cara Pakai

### Quick Setup (Recommended)

```bash
git clone https://gitlab.com/ryankucingg/mrcatz-laravel-template.git nama-projek-baru
cd nama-projek-baru
rm -rf .git
git init
```

Buat database MySQL terlebih dahulu, lalu edit `.env` untuk konfigurasi database:

```env
DB_DATABASE=nama_database_kamu
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan setup otomatis (install dependencies, generate key, migrate, build assets):

```bash
composer run-script setup
```

### Manual Setup

Jika ingin setup manual langkah per langkah:

#### 1. Install Dependencies

```bash
composer install
npm install
```

#### 2. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` — sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_kamu
DB_USERNAME=root
DB_PASSWORD=
```

> **Catatan:** Konfigurasi identitas aplikasi (nama, kontak, social media) sekarang dikelola melalui **Admin Panel > Pengaturan**, bukan lagi dari `.env`.

#### 3. Setup Database

```bash
php artisan migrate:fresh --seed
```

Migration akan otomatis membuat default settings (nama app, kontak, social media) dan akun default.

#### 4. Build Assets

```bash
npm run build
```

#### 5. Jalankan

```bash
php artisan serve
```

### Development Mode

Untuk development dengan hot-reload (menjalankan Laravel server, queue worker, log viewer, dan Vite HMR secara bersamaan):

```bash
composer run dev
```

## Akun Default

| Role | Username | Password |
|---|---|---|
| Super Admin | `super-admin` | `masukhaja` |
| Admin | `admin` | `masukhaja` |

> **Penting:** Segera ubah password default setelah deploy ke production.

## Pengaturan Aplikasi

Setelah login sebagai super-admin, buka menu **Pengaturan** untuk mengkonfigurasi:

| Kategori | Setting |
|---|---|
| **Identitas** | Nama aplikasi, nama singkat, subtitle, deskripsi, motto |
| **Kontak** | Email, telepon, alamat, domain email |
| **Social Media** | Facebook, Instagram, YouTube, TikTok |

Perubahan setting langsung berlaku di frontend (navbar, footer, dll) tanpa perlu edit file atau restart server.

## Struktur Projek

```
app/
├── Exports/                  # Export class (Excel, styled)
├── Http/
│   ├── Controllers/
│   │   ├── Auth/             # LoginController
│   │   └── Frontend/         # HomeController (halaman publik)
│   └── Middleware/            # Admin, SuperAdmin, SecurityHeaders
├── Livewire/Admin/           # Livewire components (backend)
│   ├── Dashboard/            # Halaman dashboard
│   ├── User/                 # CRUD User Management
│   ├── Setting/              # Pengaturan Aplikasi
│   └── Profile/              # Profil & Avatar
├── Models/                   # User, Setting
├── Providers/                # SiteSettingServiceProvider (load settings dari DB)
└── MrCatzClass/              # Base classes datatable & CRUD

config/
├── site.php                  # Konfigurasi site (dinamis dari database)
└── mrcatz.php                # Konfigurasi datatable (locale, icon, warna export)

resources/views/
├── auth/                     # Halaman login
├── components/
│   ├── layouts/              # Admin layout & sidebar
│   └── ui/                   # Reusable UI components
├── exports/                  # Template PDF & Excel
├── frontend/                 # Halaman publik
│   ├── layouts/
│   ├── partials/
│   └── home.blade.php
└── livewire/admin/           # Views untuk Livewire admin
```

## Role & Hak Akses

| Fitur | Super Admin | Admin |
|---|:---:|:---:|
| Dashboard | ✓ | ✓ |
| Edit Profil & Avatar | ✓ | ✓ |
| Manajemen User | ✓ | ✗ |
| Pengaturan Aplikasi | ✓ | ✗ |
| Kelola Super Admin | ✓ | ✗ |
| Hapus Akun Sendiri | ✗ | ✗ |

## Menambahkan CRUD Baru

### 1. Buat Livewire Components

```bash
mkdir -p app/Livewire/Admin/NamaModule
```

Buat `NamaModulePage.php` (extends `MrCatzComponent`) dan `NamaModuleTable.php` (extends `MrCatzDataTablesComponent`).

### 2. Buat Views

```bash
mkdir -p resources/views/livewire/admin/nama-module
```

Buat `nama-module-page.blade.php`, `nama-module_form.blade.php`, `nama-module_nav.blade.php`.

### 3. Tambahkan Route

```php
// routes/web.php di dalam group admin
Route::get('/nama-module', NamaModulePage::class)->name('nama-module');
```

### 4. Tambahkan ke Sidebar

Edit `resources/views/components/layouts/admin_sidebar.blade.php`, tambahkan `@include` untuk nav module baru.

## Konfigurasi Tema Warna

Edit `resources/css/app.css` untuk mengubah warna tema:

- `mrcatz-light` — Tema terang
- `mrcatz-dark` — Tema gelap
- `@theme` — Warna custom (polri-navy, polri-gold, dll)

## License

MIT
