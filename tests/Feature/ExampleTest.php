<?php

// The home page redirects to the dashboard (see routes/web.php).
test('the application redirects the home page to the dashboard', function () {
    $this->get('/')->assertRedirect('/dashboard');
});
