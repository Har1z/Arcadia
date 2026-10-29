<?php

use App\Models\Machine;
use App\Models\MaintenanceLog;
use App\Models\User;

it('belongs to a machine and a user', function () {
    $log = MaintenanceLog::factory()->create();

    expect($log->machine)->toBeInstanceOf(Machine::class)
        ->and($log->machine->id)->toBe($log->machine_id)
        ->and($log->user)->toBeInstanceOf(User::class)
        ->and($log->user->id)->toBe($log->user_id);
});
