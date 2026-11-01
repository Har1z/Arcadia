# Arcadia — Sistem Manajemen Aset Mesin Arcade

Aplikasi web untuk mengelola inventaris mesin arcade: kategori, mesin, log perawatan, dan catatan pendapatan per mesin — dengan dua peran pengguna (**admin** dan **staff**), ringkasan dashboard, pencarian
- **Aplikasi live:** <https://arcadia.wasmer.app/>
- **Dibangun dengan:** Laravel 13, PHP 8.3, MySQL (TiDB Cloud di production), Blade, Tailwind CSS 4, Vite, vanilla JavaScript

## Kebutuhan Sistem

| Perangkat | Versi |
|---|---|
| PHP | 8.3+ (ekstensi: `pdo_mysql`, `mbstring`, `openssl`) |
| Composer | 2.x |
| Node.js + npm | 18+ / 9+ |
| MySQL | 8.0+ (atau kompatibel seperti TiDB / MariaDB) |

## Akun Demo

Akun berikut dibuat oleh seeder (`php artisan migrate:fresh --seed`) dan tersedia juga di aplikasi live:

| Nama | Email | Password | Role |
|---|---|---|---|
| admin | `admin@gmail.com` | `admin` | admin |
| Antonio Wisnu | `staff1@gmail.com` | `staff` | staff |
| Dodo Wiyaja | `staff2@gmail.com` | `staff` | staff |

## Instalasi Lokal

```bash
# 1. Clone dan install dependensi
git clone <url-repo-ini> arcadia && cd arcadia
composer install
npm install

# 2. Konfigurasi environment
cp .env.example .env
php artisan key:generate
#    lalu sesuaikan DB_* di .env dengan MySQL lokal Anda

# 3. Siapkan database (pilih SALAH SATU):

#    Opsi A — migrasi + seeder (disarankan):
php artisan migrate:fresh --seed

#    Opsi B — import dump siap pakai (struktur + data contoh):
mysql -u root -p arcadia < database/arcade.sql

# 4. Build aset frontend
npm run build

# 5. Jalankan aplikasi
php artisan serve
#    buka http://localhost:8000
```

> Saat development, `npm run dev` menjalankan Vite dengan hot reload.
> Jika memakai dump (Opsi B), buat dulu database kosong `arcadia` (`CREATE DATABASE arcadia;`) sebelum import.

## Menjalankan Tes

Proyek menggunakan Pest (135 test / 465 assertion):

```bash
vendor/bin/pest          # seluruh suite
vendor/bin/pest --compact
```

## Struktur & Dokumentasi

Dokumentasi lengkap ada di folder [`docs/`](docs):

| Dokumen | Isi |
|---|---|
| [`docs/erd.md`](docs/erd.md) | ERD (Mermaid) beserta relasi antar tabel |
| [`docs/flow.md`](docs/flow.md) | Diagram alur permintaan (MVC request lifecycle) |
| [`docs/structure.md`](docs/structure.md) | Struktur direktori MVC aplikasi |
| [`docs/roles.md`](docs/roles.md) | Matriks hak akses per route × peran |
| [`docs/troubleshooting.md`](docs/troubleshooting.md) | Catatan masalah dan solusinya |

## Deployment

Aplikasi dideploy di [Wasmer](https://wasmer.io) dengan database MySQL terkelola di TiDB Cloud. Konfigurasi sensitif (`APP_KEY`, kredensial `DB_*`) hanya disimpan sebagai environment variable di server, tidak pernah di repository. Lihat [`docs/troubleshooting.md`](docs/troubleshooting.md) untuk catatan proses deployment.
