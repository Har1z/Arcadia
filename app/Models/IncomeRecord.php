<?php

namespace App\Models;

use Database\Factories\IncomeRecordFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// SECURITY: 'user_id' is deliberately NOT fillable — ownership is always
// assigned server-side from the logged-in user, never from request input.
#[Fillable(['machine_id', 'date', 'play_count', 'total_income'])]
class IncomeRecord extends Model
{
    /** @use HasFactory<IncomeRecordFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'play_count' => 'integer',
            'total_income' => 'decimal:2',
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
