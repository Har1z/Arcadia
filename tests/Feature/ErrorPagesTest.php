<?php

use App\Models\User;

it('shows the themed 404 page for unknown urls', function () {
    $this->get('/halaman-tidak-ada')
        ->assertNotFound()
        ->assertSee('404')
        ->assertSee('Halaman Tidak Ditemukan')
        ->assertSee('Kembali ke Beranda');
});

it('shows the themed 403 page when access is denied', function () {
    $this->actingAs(User::factory()->create())
        ->get('/users')
        ->assertForbidden()
        ->assertSee('403')
        ->assertSee('Akses Ditolak');
});

it('renders a themed 419 page', function () {
    expect(view('errors.419')->render())
        ->toContain('419')
        ->toContain('Sesi Berakhir');
});

it('renders a themed 500 page', function () {
    expect(view('errors.500')->render())
        ->toContain('500')
        ->toContain('Terjadi Kesalahan');
});

it('renders a themed 503 page', function () {
    expect(view('errors.503')->render())
        ->toContain('503')
        ->toContain('Sedang Dalam Pemeliharaan');
});

// Dark mode must stay consistent on error pages too: the same theme init
// script runs before the stylesheet.
it('keeps the theme init script on the 404 page', function () {
    $html = $this->get('/halaman-tidak-ada')->getContent();

    $themeInit = mb_strpos($html, 'document.documentElement.dataset.theme');
    $stylesheet = mb_strpos($html, 'rel="stylesheet"');

    expect($themeInit)->toBeInt()
        ->and($stylesheet)->toBeInt()
        ->and($themeInit)->toBeLessThan($stylesheet);
});
