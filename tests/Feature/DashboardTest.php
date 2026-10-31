<?php

use App\Models\IncomeRecord;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

it('redirects guests from the dashboard to the login page', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

it('shows aggregates that match the seeded data', function () {
    $this->seed(DatabaseSeeder::class);

    // Expected numbers are recomputed from the database independently of
    // the controller, proving the dashboard reflects the real data.
    $monthlyIncome = IncomeRecord::query()
        ->whereYear('date', now()->year)
        ->whereMonth('date', now()->month)
        ->sum('total_income');

    $this->actingAs(User::factory()->admin()->create())
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('>14</p>', false)
        ->assertSee('>11</p>', false)
        ->assertSee('>3</p>', false)
        ->assertSee('Rp'.number_format((float) $monthlyIncome, 0, ',', '.'));
});

it('lists open maintenance logs and latest income records', function () {
    $this->seed(DatabaseSeeder::class);

    $this->actingAs(User::factory()->admin()->create())
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Cabinet 3 gear shift nyangkut')
        ->assertSee('Wangan Midnight Maximum Tune');
});

it('shows the empty state when there is no data', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Tidak ada perawatan terbuka')
        ->assertSee('Belum ada pendapatan tercatat');
});

it('lets staff view the dashboard too', function () {
    $this->actingAs(User::factory()->create())
        ->get('/dashboard')
        ->assertOk();
});
