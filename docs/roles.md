# Matriks Hak Akses

Sumber: `routes/web.php` + `app/Policies/*.php` + Form Request `authorize()`.
Diverifikasi otomatis oleh Pest: `AuthTest`, `MachineCrudTest`, `CategoryCrudTest`, `MaintenanceCrudTest`, `IncomeCrudTest`, `UserCrudTest`.

## Ringkasan

| Kemampuan | Guest | Staff | Admin |
|---|:-:|:-:|:-:|
| Login / register | ✅ | — | — |
| Melihat dashboard | ❌ | ✅ | ✅ |
| Melihat daftar & detail mesin | ❌ | ✅ | ✅ |
| Menambah / menghapus mesin | ❌ | ❌ | ✅ |
| Mengubah data lengkap mesin | ❌ | ❌ | ✅ |
| Mengubah **status mesin saja** | ❌ | ✅ | ✅ |
| Melihat daftar kategori | ❌ | ✅ | ✅ |
| Mengelola kategori (tambah/ubah/hapus) | ❌ | ❌ | ✅ |
| Mencatat & melihat log perawatan sendiri | ❌ | ✅ | ✅ |
| Mengelola semua log perawatan | ❌ | ❌ | ✅ |
| Mencatat & melihat pendapatan sendiri | ❌ | ✅ | ✅ |
| Mengelola semua pendapatan | ❌ | ❌ | ✅ |
| CRUD pengguna | ❌ | ❌ | ✅ |

## Detail per Route

| Method & Path | Guest | Staff | Admin |
|---|:-:|:-:|:-:|
| `GET /` | → `/login` | → `/dashboard` | → `/dashboard` |
| `GET/POST /register` | halaman/proses | redirect `/dashboard` | redirect `/dashboard` |
| `GET /login` | halaman | redirect `/dashboard` | redirect `/dashboard` |
| `POST /login` (throttle 5x/menit) | proses | redirect | redirect |
| `POST /logout` | → `/login` | keluar | keluar |
| `GET /dashboard` | 302 login | ✅ | ✅ |
| `GET /machines`, `GET /machines/{id}` | 302 login | ✅ | ✅ |
| `GET/POST /machines/create` | 302 login | **403** | ✅ |
| `GET/PUT /machines/{id}/edit` | 302 login | ✅ status saja¹ | ✅ |
| `DELETE /machines/{id}` | 302 login | **403** | ✅ |
| `GET /categories` | 302 login | ✅ (hanya baca) | ✅ |
| `POST /categories` dan route kelola lainnya | 302 login | **403** | ✅ |
| `maintenance.*` (semua action) | 302 login | ✅ milik sendiri² | ✅ semua data |
| `income.*` (semua action) | 302 login | ✅ milik sendiri² | ✅ semua data |
| `users.*` (tanpa show) | 302 login | **403** | ✅ |

¹ **Aturan khusus mesin**: staff boleh membuka form edit tetapi hanya field `status` yang dikirim; `UpdateMachineRequest` menolak field lain dari non-admin (`prohibits`) dan controller hanya membaca `status` untuk non-admin — proteksi ganda.
² **Aturan kepemilikan**: ditegakkan oleh Policy (`MaintenanceLogPolicy`, `IncomeRecordPolicy`) — staff yang menekan URL langsung milik user lain menerima **403**; daftar (index) juga sudah difilter per user sehingga data orang lain tidak pernah tampil.

## Catatan Keamanan Tambahan

- `user_id` pada log perawatan & pendapatan **selalu** diambil dari session (`$request->user()->id`), bukan dari input form — payload `user_id` palsu diabaikan (diuji di `MaintenanceCrudTest`).
- Register publik selalu membuat role `staff`; kolom `role` tidak masuk `$fillable` sehingga tidak bisa di-inject lewat mass assignment (diuji di `AuthTest` dan `UserTest`).
- Menghapus kategori yang masih dipakai mesin ditolak database (FK restrict) dan ditangani jadi pesan ramah, bukan error 500.
- Menghapus user yang masih memiliki catatan perawatan/pendapatan juga ditolak dengan pesan ramah.
