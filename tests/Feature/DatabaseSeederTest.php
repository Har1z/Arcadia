<?php

use App\Models\Category;
use App\Models\IncomeRecord;
use App\Models\Machine;
use App\Models\MaintenanceLog;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Hash;

it('seeds the demo admin account', function () {
    $this->seed(DatabaseSeeder::class);

    $admin = User::where('email', 'admin@gmail.com')->first();

    expect($admin)->not->toBeNull()
        ->and($admin->name)->toBe('admin')
        ->and($admin->role)->toBe(User::ROLE_ADMIN)
        ->and(Hash::check('admin', $admin->password))->toBeTrue();
});

it('seeds the expected number of records for every table', function () {
    $this->seed(DatabaseSeeder::class);

    expect(User::count())->toBe(3)
        ->and(Category::count())->toBe(7)
        ->and(Machine::count())->toBe(14)
        ->and(MaintenanceLog::count())->toBe(10)
        ->and(IncomeRecord::count())->toBe(55);
});

it('spreads income records across several months', function () {
    $this->seed(DatabaseSeeder::class);

    $distinctMonths = IncomeRecord::get(['date'])
        ->map(fn (IncomeRecord $record) => $record->date->format('Y-m'))
        ->unique();

    expect($distinctMonths)->toHaveCount(4);
});

it('keeps income consistent with play count and price per play', function () {
    $this->seed(DatabaseSeeder::class);

    IncomeRecord::with('machine')->chunk(20, function ($records) {
        foreach ($records as $record) {
            expect((float) $record->total_income)
                ->toBe((float) $record->play_count * (float) $record->machine->price_per_play);
        }
    });
});
