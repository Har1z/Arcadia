<?php

use App\Models\Category;
use App\Models\Machine;
use App\Models\User;

/**
 * A complete machine payload for create/update requests.
 */
function machinePayload(array $overrides = []): array
{
    return array_merge([
        'category_id' => Category::factory()->create()->id,
        'name' => 'Wangan Midnight Maximum Tune',
        'serial_number' => 'ARC-9001',
        'location' => 'Lantai 1 - Zona A',
        'purchase_date' => '2024-01-15',
        'purchase_price' => 133_600_000,
        'price_per_play' => 16_000,
        'status' => Machine::STATUS_ACTIVE,
    ], $overrides);
}

it('redirects guests from the machines page to the login page', function () {
    $this->get('/machines')->assertRedirect('/login');
});

it('lists machines for authenticated staff', function () {
    Machine::factory()->create(['name' => 'Initial D Arcade Stage']);

    $this->actingAs(User::factory()->create())
        ->get('/machines')
        ->assertOk()
        ->assertSee('Initial D Arcade Stage')
        ->assertDontSee('Tambah Mesin');
});

it('lets an admin create a machine', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post('/machines', machinePayload())
        ->assertRedirect(route('machines.index'))
        ->assertSessionHas('success', 'Mesin berhasil ditambahkan.');

    $this->assertDatabaseHas('machines', ['serial_number' => 'ARC-9001', 'name' => 'Wangan Midnight Maximum Tune']);
});

it('shows an Indonesian validation error when the machine name is missing', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->from('/machines/create')
        ->post('/machines', machinePayload(['name' => null]))
        ->assertRedirect('/machines/create')
        ->assertSessionHasErrors('name', 'Nama mesin wajib diisi.');
});

it('rejects a machine with a duplicate serial number', function () {
    Machine::factory()->create(['serial_number' => 'ARC-DUP']);

    $this->actingAs(User::factory()->admin()->create())
        ->from('/machines/create')
        ->post('/machines', machinePayload(['serial_number' => 'ARC-DUP']))
        ->assertSessionHasErrors('serial_number', 'Nomor seri sudah digunakan mesin lain.');
});

// SECURITY: staff must never be able to create or delete machines.
it('forbids staff from opening the create machine form', function () {
    $this->actingAs(User::factory()->create())
        ->get('/machines/create')
        ->assertForbidden();
});

it('forbids staff from creating a machine', function () {
    $this->actingAs(User::factory()->create())
        ->post('/machines', machinePayload())
        ->assertForbidden();
});

it('forbids staff from deleting a machine', function () {
    $machine = Machine::factory()->create();

    $this->actingAs(User::factory()->create())
        ->delete(route('machines.destroy', $machine))
        ->assertForbidden();

    $this->assertDatabaseHas('machines', ['id' => $machine->id]);
});

it('lets staff update only the machine status', function () {
    $machine = Machine::factory()->create();

    $this->actingAs(User::factory()->create())
        ->put(route('machines.update', $machine), ['status' => Machine::STATUS_BROKEN])
        ->assertRedirect(route('machines.index'))
        ->assertSessionHas('success', 'Mesin berhasil diperbarui.');

    $this->assertDatabaseHas('machines', [
        'id' => $machine->id,
        'status' => Machine::STATUS_BROKEN,
        'name' => $machine->name,
    ]);
});

// SECURITY: a staff payload that touches other fields is rejected, not silently ignored.
it('rejects staff updates that change fields other than status', function () {
    $machine = Machine::factory()->create();

    $this->actingAs(User::factory()->create())
        ->put(route('machines.update', $machine), [
            'status' => Machine::STATUS_ACTIVE,
            'name' => 'Nama Baru Bukan Milikmu',
        ])
        ->assertSessionHasErrors('name', 'Anda hanya boleh mengubah status mesin.');

    $this->assertDatabaseHas('machines', ['id' => $machine->id, 'name' => $machine->name]);
});

it('lets an admin update all machine fields', function () {
    $machine = Machine::factory()->create();
    $payload = machinePayload(['name' => 'Judul Baru']);

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('machines.update', $machine), $payload)
        ->assertRedirect(route('machines.index'));

    $this->assertDatabaseHas('machines', ['id' => $machine->id, 'name' => 'Judul Baru']);
});

it('lets an admin keep the machine serial number when updating', function () {
    $machine = Machine::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('machines.update', $machine), machinePayload([
            'category_id' => $machine->category_id,
            'serial_number' => $machine->serial_number,
            'name' => $machine->name,
            'location' => $machine->location,
            'purchase_date' => $machine->purchase_date->format('Y-m-d'),
            'purchase_price' => (int) $machine->purchase_price,
            'price_per_play' => (int) $machine->price_per_play,
            'status' => $machine->status,
        ]))
        ->assertRedirect(route('machines.index'))
        ->assertSessionHasNoErrors();
});

it('searches machines by name', function () {
    Machine::factory()->create(['name' => 'Wangan Midnight Maximum Tune']);
    Machine::factory()->create(['name' => 'Taiko no Tatsujin']);

    $this->actingAs(User::factory()->create())
        ->get('/machines?q=Wangan')
        ->assertOk()
        ->assertSee('Wangan Midnight Maximum Tune')
        ->assertDontSee('Taiko no Tatsujin');
});

// SECURITY: SQL-like search input is bound as a parameter and treated as text.
it('treats SQL-like search input as plain text', function () {
    Machine::factory()->create(['name' => 'Wangan Midnight Maximum Tune']);

    $this->actingAs(User::factory()->create())
        ->get("/machines?q='".urlencode(' OR 1=1 --'))
        ->assertOk()
        ->assertDontSee('Wangan Midnight Maximum Tune')
        ->assertSee('Belum ada mesin');
});

it('filters machines by status', function () {
    Machine::factory()->create(['name' => 'Mesin Aktif Satu']);
    Machine::factory()->broken()->create(['name' => 'Mesin Rusak Satu']);

    $this->actingAs(User::factory()->create())
        ->get('/machines?status='.Machine::STATUS_BROKEN)
        ->assertOk()
        ->assertSee('Mesin Rusak Satu')
        ->assertDontSee('Mesin Aktif Satu');
});

it('rejects an invalid status filter', function () {
    $this->actingAs(User::factory()->create())
        ->from('/machines')
        ->get('/machines?status=nonsense')
        ->assertRedirect('/machines')
        ->assertSessionHasErrors('status');
});

it('paginates machines with search and filter kept in the query string', function () {
    Machine::factory()->count(15)->create();

    $response = $this->actingAs(User::factory()->create())
        ->get('/machines?q=Mesin')
        ->assertOk();

    $response->assertSee('Menampilkan 1–10 dari 15 data');
    $response->assertSee('page=2');

    $this->actingAs(User::factory()->create())
        ->get('/machines?q=Mesin&page=2')
        ->assertOk()
        ->assertSee('Menampilkan 11–15 dari 15 data');
});

it('shows an empty state when no machines exist', function () {
    $this->actingAs(User::factory()->create())
        ->get('/machines')
        ->assertOk()
        ->assertSee('Belum ada mesin');
});
