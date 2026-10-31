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

it('puts the theme init script before the stylesheet so the page never flashes', function () {
    $html = $this->actingAs(User::factory()->create())->get('/dashboard')->getContent();

    $themeInit = mb_strpos($html, 'document.documentElement.dataset.theme');
    $stylesheet = mb_strpos($html, 'rel="stylesheet"');

    expect($themeInit)->toBeInt()
        ->and($stylesheet)->toBeInt()
        ->and($themeInit)->toBeLessThan($stylesheet);
});

it('renders the maimai loading overlay in both layouts', function () {
    // Guest page first: actingAs() below would otherwise redirect /login.
    $guestHtml = $this->get('/login')->getContent();
    $appHtml = $this->actingAs(User::factory()->create())->get('/dashboard')->getContent();

    foreach ([$appHtml, $guestHtml] as $html) {
        expect($html)
            ->toContain('id="loading-overlay"')
            ->toContain('maimai-loader')
            // Arcade layout: lane 1 top-right, lane 8 top-left.
            ->toContain('22.5deg')
            ->toContain('337.5deg')
            ->toContain('Memuat');

        expect(substr_count($html, 'maimai-note'))->toBe(8)
            ->and(substr_count($html, 'maimai-lane'))->toBe(8)
            // The judgment ring carries no buttons anymore.
            ->and(substr_count($html, 'maimai-button'))->toBe(0);
    }
});
