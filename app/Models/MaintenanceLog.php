<?php

namespace App\Models;

use Database\Factories\MaintenanceLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['machine_id', 'user_id', 'issue', 'action_taken', 'cost', 'status', 'date'])]
class MaintenanceLog extends Model
{
    /** @use HasFactory<MaintenanceLogFactory> */
    use HasFactory;

    public const STATUS_OPEN = 'open';

    public const STATUS_DONE = 'done';

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
