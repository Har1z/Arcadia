<?php

use App\Models\IncomeRecord;
use App\Models\Machine;
use App\Models\User;

it('belongs to a machine and a user', function () {
    $record = IncomeRecord::factory()->create();

    expect($record->machine)->toBeInstanceOf(Machine::class)
        ->and($record->machine->id)->toBe($record->machine_id)
        ->and($record->user)->toBeInstanceOf(User::class)
        ->and($record->user->id)->toBe($record->user_id);
});
