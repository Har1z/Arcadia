<?php

namespace App\Models;

use Database\Factories\MachineFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['category_id', 'name', 'serial_number', 'location', 'purchase_date', 'purchase_price', 'price_per_play', 'status'])]
class Machine extends Model
{
    /** @use HasFactory<MachineFactory> */
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_MAINTENANCE = 'maintenance';

    public const STATUS_BROKEN = 'broken';

    public const STATUS_RETIRED = 'retired';

    /**
     * Indonesian labels for every status, used by badges, select
     * options, and the filter dropdown.
     */
    public const STATUS_LABELS = [
        self::STATUS_ACTIVE => 'Aktif',
        self::STATUS_MAINTENANCE => 'Perawatan',
        self::STATUS_BROKEN => 'Rusak',
        self::STATUS_RETIRED => 'Ditarik',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'purchase_price' => 'decimal:2',
            'price_per_play' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<MaintenanceLog, $this>
     */
    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    /**
     * @return HasMany<IncomeRecord, $this>
     */
    public function incomeRecords(): HasMany
    {
        return $this->hasMany(IncomeRecord::class);
    }
}
