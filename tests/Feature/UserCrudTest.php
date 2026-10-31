<?php

use App\Models\IncomeRecord;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('redirects guests from the users page to the login page', function () {
    $this->get('/users')->assertRedirect('/login');
});

// SECURITY: every user route is admin-only.
it('forbids staff from every user route', function (string $uri, string $method) {
    $target = User::factory()->create();

    $response = match ($method) {
        'GET' => $this->actingAs(User::factory()->create())->get($uri),
        'POST' => $this->actingAs(User::factory()->create())->post($uri, [
            'name' => 'Baru', 'email' => 'baru@example.test',
            'password' => 'password', 'password_confirmation' => 'password',
            'role' => 'staff',
        ]),
        'PUT' => $this->actingAs(User::factory()->create())->put($uri, [
            'name' => 'Baru', 'email' => 'baru@example.test', 'role' => 'staff',
        ]),
        'DELETE' => $this->actingAs(User::factory()->create())->delete($uri),
    };

    $response->assertForbidden();
})->with([
    'index' => ['/users', 'GET'],
    'create form' => ['/users/create', 'GET'],
    'store' => ['/users', 'POST'],
    'edit form' => fn () => ['/users/'.User::factory()->create()->id.'/edit', 'GET'],
    'update' => fn () => ['/users/'.User::factory()->create()->id, 'PUT'],
    'destroy' => fn () => ['/users/'.User::factory()->create()->id, 'DELETE'],
]);

it('lets an admin list users with their roles', function () {
    $admin = User::factory()->admin()->create();
    $staff = User::factory()->create(['name' => 'Rani Staff']);

    $this->actingAs($admin)
        ->get('/users')
        ->assertOk()
        ->assertSee($admin->email)
        ->assertSee($staff->email)
        ->assertSee('Admin')
        ->assertSee('Staff');
});

it('lets an admin create a staff user with a hashed password', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->from('/users/create')
        ->post('/users', [
            'name' => 'Rani',
            'email' => 'rani@example.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => 'staff',
        ])
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('success', 'Pengguna berhasil ditambahkan.');

    $user = User::where('email', 'rani@example.test')->first();

    expect($user)->not->toBeNull()
        ->and($user->role)->toBe('staff')
        ->and(Hash::check('rahasia123', $user->password))->toBeTrue();
});

it('lets an admin create another admin', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post('/users', [
            'name' => 'Admin Dua',
            'email' => 'admin2@example.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => 'admin',
        ])
        ->assertSessionHasNoErrors();

    expect(User::where('email', 'admin2@example.test')->first()?->isAdmin())->toBeTrue();
});

it('shows Indonesian validation errors for invalid user input', function () {
    User::factory()->create(['email' => 'taken@example.test']);

    $this->actingAs(User::factory()->admin()->create())
        ->from('/users/create')
        ->post('/users', [
            'name' => '',
            'email' => 'taken@example.test',
            'password' => 'abc',
            'password_confirmation' => 'beda',
            'role' => 'superadmin',
        ])
        ->assertRedirect('/users/create')
        ->assertSessionHasErrors([
            'name' => 'Nama wajib diisi.',
            'email' => 'Email sudah digunakan pengguna lain.',
            'password' => 'Password minimal 8 karakter.',
            'role' => 'Role tidak valid.',
        ]);
});

it('lets an admin update a user and keep the password when blank', function () {
    $user = User::factory()->create(['password' => 'lama12345']);

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('users.update', $user), [
            'name' => 'Nama Baru',
            'email' => $user->email,
            'password' => '',
            'password_confirmation' => '',
            'role' => 'admin',
        ])
        ->assertRedirect(route('users.index'))
        ->assertSessionHasNoErrors();

    $user->refresh();

    expect($user->name)->toBe('Nama Baru')
        ->and($user->role)->toBe('admin')
        ->and(Hash::check('lama12345', $user->password))->toBeTrue();
});

it('rejects an update to an email owned by another user', function () {
    $other = User::factory()->create(['email' => 'milik@example.test']);
    $user = User::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->from(route('users.edit', $user))
        ->put(route('users.update', $user), [
            'name' => $user->name,
            'email' => $other->email,
            'role' => 'staff',
        ])
        ->assertRedirect(route('users.edit', $user))
        ->assertSessionHasErrors('email', 'Email sudah digunakan pengguna lain.');
});

// SECURITY: an admin deleting their own account would lock them out.
it('prevents an admin from deleting their own account', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->from(route('users.index'))
        ->delete(route('users.destroy', $admin))
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('error', 'Anda tidak bisa menghapus akun Anda sendiri.');

    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});

// UX checklist: a user with records shows a friendly message, not a 500 error.
it('keeps a user who still has income records and shows a friendly error', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->has(IncomeRecord::factory())->create();

    $this->actingAs($admin)
        ->from(route('users.index'))
        ->delete(route('users.destroy', $user))
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('error', 'Pengguna masih memiliki catatan perawatan atau pendapatan dan tidak bisa dihapus.');

    $this->assertDatabaseHas('users', ['id' => $user->id]);
});

it('lets an admin delete a user without records', function () {
    $user = User::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->delete(route('users.destroy', $user))
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('success', 'Pengguna berhasil dihapus.');

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

it('searches users by name and filters by role', function () {
    User::factory()->create(['name' => 'Rani Pencarian', 'email' => 'rani-p@example.test']);
    User::factory()->create(['name' => 'Joko Lain', 'email' => 'joko@example.test']);

    $this->actingAs(User::factory()->admin()->create())
        ->get('/users?q=Rani+Pencarian')
        ->assertOk()
        ->assertSee('rani-p@example.test')
        ->assertDontSee('joko@example.test');

    $this->actingAs(User::factory()->admin()->create())
        ->get('/users?role=admin')
        ->assertOk()
        ->assertSee('Admin');
});
