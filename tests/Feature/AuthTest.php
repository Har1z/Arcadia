<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    // Throwaway route to prove the role:admin middleware boundary.
    Route::get('/__admin-only', fn () => 'ok')->middleware(['auth', 'role:admin']);
});

it('redirects guests from the dashboard to the login page', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

it('redirects authenticated users away from the login page', function () {
    $this->actingAs(User::factory()->create())
        ->get('/login')
        ->assertRedirect(route('dashboard'));
});

it('renders the login form for guests', function () {
    $this->get('/login')->assertOk()->assertSee('Masuk');
});

it('renders the registration form for guests', function () {
    $this->get('/register')->assertOk()->assertSee('Daftar Akun');
});

it('authenticates a user with valid credentials', function () {
    $user = User::factory()->create(['password' => 'rahasia123']);

    $this->post('/login', ['email' => $user->email, 'password' => 'rahasia123'])
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

it('shows a generic error message for a wrong password', function () {
    $user = User::factory()->create();

    $this->from('/login')
        ->post('/login', ['email' => $user->email, 'password' => 'salah-banget'])
        ->assertRedirect('/login')
        ->assertSessionHasErrors('email', 'Email atau password salah.');

    $this->assertGuest();
});

// SECURITY: brute-force protection — 6th attempt within a minute is rejected.
it('limits login attempts to five per minute', function () {
    $user = User::factory()->create();

    foreach (range(1, 5) as $attempt) {
        $this->post('/login', ['email' => $user->email, 'password' => 'salah'])
            ->assertSessionHasErrors('email');
    }

    $this->post('/login', ['email' => $user->email, 'password' => 'salah'])
        ->assertStatus(429);
});

it('registers a new account as staff and signs them in', function () {
    $this->post('/register', [
        'name' => 'Karyawan Baru',
        'email' => 'karyawan@example.test',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => 'karyawan@example.test',
        'role' => User::ROLE_STAFF,
    ]);
});

// SECURITY: a crafted registration payload must not be able to pick a role.
it('ignores a role sent with the registration payload', function () {
    $this->post('/register', [
        'name' => 'Attacker',
        'email' => 'attacker@example.test',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
        'role' => User::ROLE_ADMIN,
    ])->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('users', [
        'email' => 'attacker@example.test',
        'role' => User::ROLE_STAFF,
    ]);
});

it('rejects registration with an already used email', function () {
    User::factory()->create(['email' => 'taken@example.test']);

    $this->from('/register')
        ->post('/register', [
            'name' => 'Duplikat',
            'email' => 'taken@example.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])
        ->assertRedirect('/register')
        ->assertSessionHasErrors('email', 'Email sudah terdaftar.');

    $this->assertGuest();
});

it('ends the session on logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/logout')
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

it('redirects guests who try to log out to the login page', function () {
    $this->post('/logout')->assertRedirect('/login');
});

it('forbids staff from admin-only routes', function () {
    $this->actingAs(User::factory()->create())
        ->get('/__admin-only')
        ->assertForbidden();
});

it('allows admins into admin-only routes', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/__admin-only')
        ->assertOk();
});
