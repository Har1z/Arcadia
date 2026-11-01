# Alur Permintaan (Request Lifecycle)

Dua alur utama yang dipakai sebagai bahan jawaban esai: **(1)** siklus tambah data (MVC lengkap) dan **(2)** autentikasi & session.

## 1. Siklus MVC — Contoh: Tambah Mesin (`POST /machines`)

```mermaid
flowchart TD
    A["Browser: form Tambah Mesin<br/>dikirim (POST + _token CSRF)"] --> B["public/index.php<br/>bootstrap aplikasi Laravel"]
    B --> C["Middleware stack<br/>1. ValidateCsrfToken → token cocok?<br/>2. auth → sudah login?<br/>3. role:admin → admin?"]
    C -->|token salah / tamu / staff| X["419 / 302 ke /login / 403"]
    C -->|lolos| D["Form Request: StoreMachineRequest<br/>validasi + authorize()"]
    D -->|validasi gagal| E["302 redirect balik ke form<br/>+ errors di session + old input"]
    E --> A
    D -->|validasi lolos| F["Controller: MachineController@store<br/>hanya $request->validated()"]
    F --> G["Model: Machine::create()<br/>Eloquent INSERT ke MySQL"]
    G --> H["302 redirect ke /machines<br/>+ flash success di session"]
    H --> I["View: machines/index.blade.php<br/>{{ }} escaping, badge status, pagination"]
    I --> A
```

Peran setiap lapisan (bukti di kode):

| Lapisan | File | Tanggung jawab |
|---|---|---|
| Routing | `routes/web.php` | `Route::resource('machines', ...)->only(['create','store','destroy'])->middleware('role:admin')` |
| Middleware | `app/Http/Middleware/RoleMiddleware.php` | tolak non-admin dengan 403 |
| Validasi | `app/Http/Requests/StoreMachineRequest.php` | aturan + pesan error bahasa Indonesia |
| Controller | `app/Http/Controllers/MachineController.php` (`store`) | `Machine::create($request->validated())` — tidak pernah `$request->all()` |
| Model | `app/Models/Machine.php` | `$fillable` (mass-assignment guard), relasi, cast `decimal:2` / `date` |
| View | `resources/views/machines/index.blade.php` | tabel + pencarian + badge status + pagination |

## 2. Autentikasi & Session — Login (`POST /login`)

```mermaid
flowchart TD
    A["Form login (POST /login)"] --> B["Middleware throttle:login<br/>maks 5 percobaan/menit per email+IP"]
    B -->|percobaan ke-6| X["429 Too Many Requests"]
    B --> C["AuthController::login<br/>Auth::attempt(email, password)"]
    C -->|gagal| D["302 balik ke /login<br/>+ error generik 'Email atau password salah.'"]
    C -->|berhasil| E["session()->regenerate()<br/>anti session-fixation"]
    E --> F["302 ke /dashboard<br/>session id baru tersimpan di tabel sessions"]
    F --> G["Request berikutnya:<br/>cookie session → auth middleware → halaman terbuka"]
```

Poin keamanan pada alur ini:

- **Session driver `database`** — data session disimpan di tabel `sessions`, bukan file; `SESSION_DRIVER=database` (lihat `.env.example`).
- **Session fixation** — `session()->regenerate()` saat login; logout memakai `invalidate()` + `regenerateToken()`.
- **Password** — diverifikasi via `Hash`/bcrypt (`Auth::attempt`), tidak pernah dibandingkan sebagai string.
- **Kontrol akses berlapis** — `auth` middleware (sudah login?) → `role` middleware (admin?) → Policy (pemilik data?) → Form Request `authorize()`. Lihat [`roles.md`](roles.md) untuk matriks lengkap.
