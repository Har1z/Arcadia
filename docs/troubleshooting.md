# Catatan Troubleshooting

Kumpulan masalah nyata yang ditemui selama pengerjaan — format **Gejala / Penyebab / Solusi**. Bagian ini juga bahan cerita untuk esai deployment.

## 1. Gagal koneksi TLS ke TiDB Cloud di production (Wasmer)

- **Gejala:** Aplikasi live di Wasmer gagal connect ke database dengan error verifikasi sertifikat SSL, padahal di lokal jalan normal.
- **Penyebab:** Runtime PHP di environment deploy tidak membawa CA root yang dipakai TiDB Cloud (ditandatangani ISRG Root X1), sehingga `pdo_mysql` menolak koneksi TLS.
- **Solusi:** Sertifikat ISRG Root X1 dilacak (track) ke dalam repository dan didaftarkan sebagai CA yang dipakai koneksi database. Commit: `76a69a8`.
- **Pelajaran:** Environment production tidak selalu punya bundle CA yang sama dengan mesin development.

## 2. Build deployment gagal karena hook composer dev-only

- **Gejala:** Deploy ke Wasmer gagal pada tahap `composer install` dengan error terkait `boost:update`.
- **Penyebab:** Hook `post-update-cmd` menjalankan perintah milik paket dev (`laravel/boost`) yang tidak terpasang di server karena produksi memakai `composer install --no-dev`.
- **Solusi (bertahap):** Pertama mengatur ulang urutan package discovery (`67a4abb`), namun solusi finalnya menghapus `boost:update` dari `post-update-cmd` karena memang tidak dibutuhkan di production (`0840950`).
- **Pelajaran:** Hook composer yang menyentuh paket dev harus dikondisikan per environment.

## 3. `mysqldump` gagal ke TiDB: `SAVEPOINT sp does not exist`

- **Gejala:** Ekspor `database/arcade.sql` berhenti setelah 2 tabel dengan error `Couldn't execute 'ROLLBACK TO SAVEPOINT sp' (1305)`.
- **Penyebab:** Opsi `--single-transaction` membuat mysqldump memakai `SAVEPOINT`/`ROLLBACK TO SAVEPOINT` antar tabel; TiDB belum mendukung SAVEPOINT.
- **Solusi:** Hilangkan `--single-transaction` (cukup aman karena database pengembangan idle saat ekspor) dan pertahankan `--skip-lock-tables --no-tablespaces --column-statistics=0 --set-gtid-purged=OFF --ssl-mode=REQUIRED`.

## 4. Angka "pendapatan bulan ini" selalu 0

- **Gejala:** Kartu dashboard pendapatan bulan berjalan menampilkan 0 padahal seeder sukses.
- **Penyebab:** Seeder menyimpan pendapatan bulan berjalan dengan `endOfMonth()` — tanggal **masih di masa depan** (mis. 31 Oktober saat tanggal 29), sehingga jatuh di luar rentang `startOfMonth()..now()` query dashboard.
- **Solusi:** `now()->subMonths($n)->endOfMonth()->min(now())` — bulan berjalan dicatat maksimal hari ini. (`database/seeders/IncomeRecordSeeder.php`)

## 5. Seeder gagal: `Column 'user_id' cannot be null`

- **Gejala:** `migrate:fresh --seed` gagal di `IncomeRecordSeeder`; data mesin yang masuk juga tidak konsisten dengan seeder di disk.
- **Penyebab:** Data akun demo diubah (email staff berubah), sementara seeder lain masih mencari id berdasarkan email lama — lookup balik `null` lalu dipaksa jadi `user_id`.
- **Solusi & pelajaran:** Samakan seluruh referensi email antar seeder; satu-satunya sumber konstanta adalah `UserSeeder`. Diverifikasi ulang dengan `DatabaseSeederTest` (jumlah baris per tabel + konsistensi `total_income = play_count × price_per_play`).

## 6. Tes CSRF 419 tidak mungkin dilakukan lewat Pest

- **Gejala:** Rencana menguji "form tanpa token ditolak 419" lewat feature test tidak bisa dipicu — request tanpa `_token` tetap lolos.
- **Penyebab:** Middleware CSRF Laravel (`PreventRequestForgery`) melewati validasi ketika aplikasi berjalan dalam mode unit test (`runningUnitTests()`).
- **Solusi:** Uji otomatis dialihkan menjadi "setiap form mutasi pasti merender field `_token`" (`CsrfProtectionTest`, 17 form) + verifikasi penolakan 419 dilakukan manual di browser (hapus `_token` via devtools → submit).

## 7. `User::create()` tidak membawa default `role`

- **Gejala:** Tes mass-assignment gagal: setelah `User::create()`, atribut `role` di model dalam memori `null`, bukan `staff` (meski kolom DB sudah punya default).
- **Penyebab:** Default database hanya berlaku saat INSERT; instance model tidak otomatis membacanya kembali.
- **Solusi:** Mirror default di model — `protected $attributes = ['role' => self::ROLE_STAFF]` — selaras dengan default kolom. (`app/Models/User.php`)

## 8. Tabel cache/jobs dibuang → wajib ganti cache store

- **Gejala (potensial):** Rate limiter login butuh cache; jika tabel `cache` dihapus tanpa mengganti konfigurasi, throttling akan error.
- **Penyebab & solusi:** Migration bawaan `cache`/`jobs` dihapus supaya skema hanya berisi yang dipakai aplikasi; konfigurasi diubah ke `CACHE_STORE=file` dan `QUEUE_CONNECTION=sync` (terdokumentasi di `.env.example` dan README).
