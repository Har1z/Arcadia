<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// SECURITY: 'role' is deliberately NOT fillable so it can never be set through
// mass assignment (e.g. a crafted registration form sending role=admin).
// Registration always assigns 'staff' explicitly in the controller.
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_STAFF = 'staff';

    /**
     * Mirror of the database default so a freshly created (unsaved or
     * just-created) user already reports itself as staff.
     *
     * @var array<string, string>
     */
    protected $attributes = [
        'role' => self::ROLE_STAFF,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // SECURITY: role checks go through this helper, never a loose string compare
    // scattered across controllers.
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
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
