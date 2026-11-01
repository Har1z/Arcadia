# Struktur Direktori (MVC)

```
arcadia/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # C — terima request, panggil model, kembalikan view/redirect
│   │   │   ├── AuthController.php        # login, register, logout (manual, tanpa starter kit)
│   │   │   ├── DashboardController.php   # ringkasan statistik
│   │   │   ├── CategoryController.php
│   │   │   ├── MachineController.php
│   │   │   ├── MaintenanceController.php
│   │   │   ├── IncomeController.php
│   │   │   └── UserController.php        # CRUD pengguna (admin)
│   │   ├── Middleware/
│   │   │   └── RoleMiddleware.php        # alias "role" — proteksi route admin
│   │   └── Requests/            # validasi terpusat (Form Requests)
│   │       ├── StoreMachineRequest.php, UpdateMachineRequest.php, ...
│   │       └── StoreCategoryRequest.php, StoreMaintenanceLogRequest.php, ...
│   ├── Models/                  # M — Eloquent: relasi, cast, guard mass-assignment
│   │   ├── User.php             # role tidak fillable + isAdmin()
│   │   ├── Category.php, Machine.php
│   │   └── MaintenanceLog.php, IncomeRecord.php
│   ├── Policies/                # aturan kepemilikan data (staff hanya data sendiri)
│   │   ├── MaintenanceLogPolicy.php
│   │   └── IncomeRecordPolicy.php
│   └── Providers/
│       └── AppServiceProvider.php         # rate limiter login (throttle:login)
│
├── database/
│   ├── migrations/              # skema tabel + foreign key (restrict/cascade)
│   ├── factories/               # data palsu untuk testing (Pest)
│   ├── seeders/                 # data demo deterministik (akun, mesin, pendapatan)
│   └── arcade.sql               # dump siap import (struktur + data seeder)
│
├── resources/
│   ├── css/app.css              # design token (terang/gelap) + Tailwind 4 @theme inline
│   ├── js/app.js                # toggle tema, drawer sidebar, modal hapus (vanilla JS)
│   └── views/                   # V — Blade
│       ├── layouts/             # app (sidebar+topbar) dan guest (kartu tengah)
│       ├── partials/            # _flash, _pagination, _theme-toggle, _confirm-delete, _loading
│       ├── auth/                # login, register
│       ├── machines/ categories/ maintenance/ income/ users/
│       ├── errors/              # halaman error bertema 403/404/419/500/503
│       └── dashboard.blade.php
│
├── routes/web.php               # definisi semua route + middleware per grup
├── tests/Feature/               # Pest — auth, CRUD, keamanan (CSRF/XSS/injection), error page
├── docs/                        # dokumentasi proyek (folder ini)
└── public/                      # entry index.php + aset build Vite (css/js terkompilasi)
```

## Pola yang Dipakai (konsisten di seluruh modul)

1. **Route** → resource controller + middleware (`auth`, `role:admin`).
2. **Validasi** → Form Request terpisah dengan pesan error bahasa Indonesia.
3. **Query** → Eloquent + eager loading (`with()`) untuk mencegah N+1; pencarian memakai binding parameter LIKE.
4. **View** → Blade dengan `{{ }}` (escaping otomatis), komponen badge status, flash message via session.
5. **Tes** → satu file Pest per modul + file khusus keamanan (`CsrfProtectionTest`, `XssProtectionTest`, `ErrorPagesTest`).
