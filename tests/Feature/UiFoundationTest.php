<?php

use App\Models\User;

it('redirects the home page to the dashboard', function () {
    $this->actingAs(User::factory()->create())->get('/')->assertRedirect('/dashboard');
});

it('renders the dashboard inside the app layout', function () {
    $response = $this->actingAs(User::factory()->create())->get('/dashboard');

    $response->assertOk()
        ->assertSee('Arcadia', false)
        ->assertSee('id="sidebar"', false)
        ->assertSee('data-theme-toggle', false)
        ->assertSee('Total Mesin', false);
});

it('renders the component showcase', function () {
    $this->get('/samples/ui')
        ->assertOk()
        ->assertSee('Aktif', false)
        ->assertSee('Perawatan', false)
        ->assertSee('Keadaan Kosong', false);
});

it('puts the theme init script before the stylesheet so the page never flashes', function () {
    $html = $this->actingAs(User::factory()->create())->get('/dashboard')->getContent();

    $themeInit = mb_strpos($html, 'document.documentElement.dataset.theme');
    $stylesheet = mb_strpos($html, 'rel="stylesheet"');

    expect($themeInit)->toBeInt()
        ->and($stylesheet)->toBeInt()
        ->and($themeInit)->toBeLessThan($stylesheet);
});
