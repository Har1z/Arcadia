<?php

use App\Models\User;

// The home page sends guests to login and authenticated users to the dashboard
// (see routes/web.php).
test('the application redirects guests from the home page to the login page', function () {
    $this->get('/')->assertRedirect('/login');
});

test('the application redirects authenticated users from the home page to the dashboard', function () {
    $this->actingAs(User::factory()->create())->get('/')->assertRedirect('/dashboard');
});
