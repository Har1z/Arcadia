<?php

use App\Models\Machine;
use App\Models\MaintenanceLog;
use App\Models\User;

/**
 * A complete maintenance log payload for create/update requests.
 */
function maintenancePayload(array $overrides = []): array
{
    return array_merge([
        'machine_id' => Machine::factory()->create()->id,
        'issue' => 'Tombol player 1 tidak merespons',
        'action_taken' => 'Ganti sakelar tombol dan uji ulang',
        'cost' => 150_000,
        'status' => MaintenanceLog::STATUS_OPEN,
        'date' => '2026-10-15',
    ], $overrides);
}

it('redirects guests from the maintenance page to the login page', function () {
    $this->get('/maintenance')->assertRedirect('/login');
});

it('lists only the staff member own maintenance logs', function () {
    $me = User::factory()->create();
    $other = User::factory()->create();

    MaintenanceLog::factory()->for($me)->create(['issue' => 'Masalah Punyaku']);
    MaintenanceLog::factory()->for($other)->create(['issue' => 'Masalah Orang Lain']);

    $this->actingAs($me)
        ->get('/maintenance')
        ->assertOk()
        ->assertSee('Masalah Punyaku')
        ->assertDontSee('Masalah Orang Lain');
});

it('lists every maintenance log for an admin', function () {
    $other = User::factory()->create();

    MaintenanceLog::factory()->for($other)->create(['issue' => 'Masalah Orang Lain']);

    $this->actingAs(User::factory()->admin()->create())
        ->get('/maintenance')
        ->assertOk()
        ->assertSee('Masalah Orang Lain');
});

// SECURITY: even a crafted payload with another user's id must not be able
// to assign the record to someone else — user_id comes from the session.
it('forces the logged in user as the owner of a new maintenance log', function () {
    $me = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($me)
        ->post('/maintenance', maintenancePayload(['user_id' => $other->id]))
        ->assertRedirect(route('maintenance.index'))
        ->assertSessionHas('success', 'Log perawatan berhasil ditambahkan.');

    $this->assertDatabaseHas('maintenance_logs', [
        'issue' => 'Tombol player 1 tidak merespons',
        'user_id' => $me->id,
    ]);
});

it('shows an Indonesian validation error when the issue is missing', function () {
    $this->actingAs(User::factory()->create())
        ->from('/maintenance/create')
        ->post('/maintenance', maintenancePayload(['issue' => null]))
        ->assertRedirect('/maintenance/create')
        ->assertSessionHasErrors('issue', 'Masalah wajib diisi.');
});

it('rejects an unknown machine on a maintenance log', function () {
    $this->actingAs(User::factory()->create())
        ->from('/maintenance/create')
        ->post('/maintenance', maintenancePayload(['machine_id' => 999999]))
        ->assertSessionHasErrors('machine_id', 'Mesin tidak ditemukan.');
});

it('searches maintenance logs by issue', function () {
    $me = User::factory()->create();
    MaintenanceLog::factory()->for($me)->create(['issue' => 'Gas cabinet macet']);
    MaintenanceLog::factory()->for($me)->create(['issue' => 'Layar berkedip']);

    $this->actingAs($me)
        ->get('/maintenance?q=cabinet')
        ->assertOk()
        ->assertSee('Gas cabinet macet')
        ->assertDontSee('Layar berkedip');
});

// SECURITY: SQL-like search input is bound as a parameter and treated as text.
it('treats SQL-like search input as plain text', function () {
    $me = User::factory()->create();
    MaintenanceLog::factory()->for($me)->create(['issue' => 'Gas cabinet macet']);

    $this->actingAs($me)
        ->get("/maintenance?q='".urlencode(' OR 1=1 --'))
        ->assertOk()
        ->assertDontSee('Gas cabinet macet')
        ->assertSee('Belum ada log perawatan');
});

it('filters maintenance logs by status', function () {
    $me = User::factory()->create();
    MaintenanceLog::factory()->for($me)->create(['issue' => 'Log Terbuka', 'status' => MaintenanceLog::STATUS_OPEN]);
    MaintenanceLog::factory()->for($me)->done()->create(['issue' => 'Log Selesai']);

    $this->actingAs($me)
        ->get('/maintenance?status='.MaintenanceLog::STATUS_DONE)
        ->assertOk()
        ->assertSee('Log Selesai')
        ->assertDontSee('Log Terbuka');
});

it('rejects an invalid maintenance status filter', function () {
    $this->actingAs(User::factory()->create())
        ->from('/maintenance')
        ->get('/maintenance?status=nonsense')
        ->assertRedirect('/maintenance')
        ->assertSessionHasErrors('status');
});

it('paginates maintenance logs with search kept in the query string', function () {
    $me = User::factory()->create();
    MaintenanceLog::factory()->count(15)->for($me)->create(['issue' => 'Masalah Umum']);

    $response = $this->actingAs($me)
        ->get('/maintenance')
        ->assertOk();

    $response->assertSee('Menampilkan 1–10 dari 15 data')
        ->assertSee('page=2');

    $this->actingAs($me)
        ->get('/maintenance?page=2')
        ->assertOk()
        ->assertSee('Menampilkan 11–15 dari 15 data');
});

it('shows an empty state when no maintenance logs exist', function () {
    $this->actingAs(User::factory()->create())
        ->get('/maintenance')
        ->assertOk()
        ->assertSee('Belum ada log perawatan');
});

it('lets staff edit their own maintenance log', function () {
    $me = User::factory()->create();
    $log = MaintenanceLog::factory()->for($me)->create();

    $this->actingAs($me)
        ->put(route('maintenance.update', $log), maintenancePayload([
            'machine_id' => $log->machine_id,
            'issue' => 'Masalah Baru Setelah Diperbaiki',
        ]))
        ->assertRedirect(route('maintenance.index'))
        ->assertSessionHas('success', 'Log perawatan berhasil diperbarui.');

    $this->assertDatabaseHas('maintenance_logs', ['id' => $log->id, 'issue' => 'Masalah Baru Setelah Diperbaiki']);
});

it('lets staff delete their own maintenance log', function () {
    $me = User::factory()->create();
    $log = MaintenanceLog::factory()->for($me)->create();

    $this->actingAs($me)
        ->delete(route('maintenance.destroy', $log))
        ->assertSessionHas('success', 'Log perawatan berhasil dihapus.');

    $this->assertDatabaseMissing('maintenance_logs', ['id' => $log->id]);
});

// SECURITY: staff must never see, edit, or delete another user's logs,
// even by hitting the URL directly.
it('forbids staff from viewing another user maintenance log', function () {
    $other = User::factory()->create();
    $log = MaintenanceLog::factory()->for($other)->create();

    $this->actingAs(User::factory()->create())
        ->get(route('maintenance.show', $log))
        ->assertForbidden();
});

it('forbids staff from editing another user maintenance log', function () {
    $other = User::factory()->create();
    $log = MaintenanceLog::factory()->for($other)->create();

    $this->actingAs(User::factory()->create())
        ->get(route('maintenance.edit', $log))
        ->assertForbidden();

    $this->actingAs(User::factory()->create())
        ->put(route('maintenance.update', $log), maintenancePayload(['machine_id' => $log->machine_id]))
        ->assertForbidden();

    $this->assertDatabaseHas('maintenance_logs', ['id' => $log->id, 'issue' => $log->issue]);
});

it('forbids staff from deleting another user maintenance log', function () {
    $other = User::factory()->create();
    $log = MaintenanceLog::factory()->for($other)->create();

    $this->actingAs(User::factory()->create())
        ->delete(route('maintenance.destroy', $log))
        ->assertForbidden();

    $this->assertDatabaseHas('maintenance_logs', ['id' => $log->id]);
});

it('lets an admin edit and delete any maintenance log', function () {
    $other = User::factory()->create();
    $log = MaintenanceLog::factory()->for($other)->create();

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('maintenance.update', $log), maintenancePayload([
            'machine_id' => $log->machine_id,
            'issue' => 'Diperbarui Oleh Admin',
        ]))
        ->assertRedirect(route('maintenance.index'));

    $this->assertDatabaseHas('maintenance_logs', ['id' => $log->id, 'issue' => 'Diperbarui Oleh Admin']);

    $this->actingAs(User::factory()->admin()->create())
        ->delete(route('maintenance.destroy', $log))
        ->assertSessionHas('success', 'Log perawatan berhasil dihapus.');

    $this->assertDatabaseMissing('maintenance_logs', ['id' => $log->id]);
});
