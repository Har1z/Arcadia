<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// SECURITY: a crafted request payload must not be able to create an admin.
it('ignores a role sent through mass assignment', function () {
    $user = User::create([
        'name' => 'Attacker',
        'email' => 'attacker@example.test',
        'password' => Hash::make('password'),
        'role' => User::ROLE_ADMIN,
    ]);

    expect($user->role)->toBe(User::ROLE_STAFF)
        ->and($user->fresh()->role)->toBe(User::ROLE_STAFF);
});

it('recognizes admins via isAdmin', function () {
    $admin = User::factory()->admin()->create();
    $staff = User::factory()->create();

    expect($admin->isAdmin())->toBeTrue()
        ->and($staff->isAdmin())->toBeFalse();
});
