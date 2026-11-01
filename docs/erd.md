# ERD — Entity Relationship Diagram

Sumber kebenaran: file migration di `database/migrations/`. Semua foreign key dideklarasikan eksplisit dengan aturan hapus (`restrict` / `cascade`).

## Diagram

```mermaid
erDiagram
    USERS ||--o{ MAINTENANCE_LOGS : "mencatat"
    USERS ||--o{ INCOME_RECORDS : "mencatat"
    CATEGORIES ||--o{ MACHINES : "mengelompokkan"
    MACHINES ||--o{ MAINTENANCE_LOGS : "memiliki log"
    MACHINES ||--o{ INCOME_RECORDS : "menghasilkan"

    USERS {
        bigint id PK
        varchar name
        varchar email UK
        timestamp email_verified_at "nullable"
        varchar password "hash bcrypt"
        varchar role "admin|staff, default staff"
        varchar remember_token
        timestamps created_updated
    }

    CATEGORIES {
        bigint id PK
        varchar name UK
        text description "nullable"
        timestamps created_updated
    }

    MACHINES {
        bigint id PK
        bigint category_id FK "RESTRICT on delete"
        varchar name
        varchar serial_number UK
        varchar location
        date purchase_date
        decimal purchase_price "12,2"
        decimal price_per_play "10,2"
        varchar status "active|maintenance|broken|retired"
        timestamps created_updated
    }

    MAINTENANCE_LOGS {
        bigint id PK
        bigint machine_id FK "CASCADE on delete"
        bigint user_id FK "RESTRICT on delete"
        varchar issue
        text action_taken "nullable"
        decimal cost "12,2 default 0"
        varchar status "open|done"
        date date
        timestamps created_updated
    }

    INCOME_RECORDS {
        bigint id PK
        bigint machine_id FK "CASCADE on delete"
        bigint user_id FK "RESTRICT on delete"
        date date
        int play_count "unsigned"
        decimal total_income "12,2"
        timestamps created_updated
    }
```

> Tabel framework pendukung: `sessions` (driver session database), `password_reset_tokens`, dan `migrations` — tidak berelasi dengan entitas bisnis.

## Penjelasan Relasi

| Relasi | Kardinalitas | Aturan hapus | Alasan |
|---|---|---|---|
| `categories` → `machines` | one-to-many | **RESTRICT** | Kategori yang masih dipakai mesin tidak boleh dihapus; controller menangkap `QueryException` dan menampilkan pesan ramah (lihat `CategoryController::destroy`). |
| `machines` → `maintenance_logs` | one-to-many | **CASCADE** | Riwayat perawatan melekat pada mesin; mesin dihapus → riwayatnya ikut terhapus. |
| `machines` → `income_records` | one-to-many | **CASCADE** | Sama seperti log perawatan: catatan pendapatan tidak bermakna tanpa mesinnya. |
| `users` → `maintenance_logs` / `income_records` | one-to-many | **RESTRICT** | Jejak data siapa yang mencatat harus terjaga; user yang masih punya catatan tidak bisa dihapus (`UserController::destroy` menampilkan pesan error ramah). |

## Memanggil Relasi via Eloquent

```php
// Dari sisi "one" ke "many"
$category->machines;                    // App\Models\Category::machines()
$machine->maintenanceLogs;              // App\Models\Machine::maintenanceLogs()
$user->incomeRecords;

// Dari sisi "many" ke "one"
$maintenanceLog->machine->name;         // App\Models\MaintenanceLog::machine()
$incomeRecord->user->name;

// Eager loading untuk mencegah N+1 query (dipakai di semua index controller)
Machine::with('category')->paginate(10);
IncomeRecord::with(['machine', 'user'])->get();
```
