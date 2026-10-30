<?php

namespace App\Models;

use Database\Factories\MaintenanceLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// SECURITY: 'user_id' is deliberately NOT fillable — ownership is always
// assigned server-side from the logged-in user, never from request input.
#[Fillable(['machine_id', 'issue', 'action_taken', 'cost', 'status', 'date'])]
class MaintenanceLog extends Model
{
    /** @use HasFactory<MaintenanceLogFactory> */
    use HasFactory;

    public const STATUS_OPEN = 'open';

    public const STATUS_DONE = 'done';

    /**
     * Indonesian labels for every status, used by badges, select
     * options, and the filter dropdown.
     */
    public const STATUS_LABELS = [
        self::STATUS_OPEN => 'Terbuka',
        self::STATUS_DONE => 'Selesai',
    ];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Machine, $this>
     */
    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
