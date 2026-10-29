<?php

use App\Models\Category;
use App\Models\IncomeRecord;
use App\Models\Machine;
use App\Models\MaintenanceLog;

it('belongs to a category', function () {
    $machine = Machine::factory()->create();

    expect($machine->category)->toBeInstanceOf(Category::class)
        ->and($machine->category->id)->toBe($machine->category_id);
});

it('has many maintenance logs', function () {
    $machine = Machine::factory()->create();
    $logs = MaintenanceLog::factory()->count(2)->recycle($machine)->create();

    expect($machine->maintenanceLogs)->toHaveCount(2)
        ->each->toBeInstanceOf(MaintenanceLog::class)
        ->and($machine->maintenanceLogs->pluck('id')->sort()->values())
        ->toEqual($logs->pluck('id')->sort()->values());
});

it('has many income records', function () {
    $machine = Machine::factory()->create();
    $records = IncomeRecord::factory()->count(2)->recycle($machine)->create();

    expect($machine->incomeRecords)->toHaveCount(2)
        ->each->toBeInstanceOf(IncomeRecord::class)
        ->and($machine->incomeRecords->pluck('id')->sort()->values())
        ->toEqual($records->pluck('id')->sort()->values());
});
