<?php

use App\Models\IncomeRecord;
use App\Models\Machine;
use App\Models\User;

/**
 * A complete income record payload for create/update requests.
 */
function incomePayload(array $overrides = []): array
{
    return array_merge([
        'machine_id' => Machine::factory()->create()->id,
        'date' => '2026-10-15',
        'play_count' => 120,
        'total_income' => 1_920_000,
    ], $overrides);
}

it('redirects guests from the income page to the login page', function () {
    $this->get('/income')->assertRedirect('/login');
});

it('lists only the staff member own income records', function () {
    $me = User::factory()->create(['name' => 'Pemilik Catatan']);
    $other = User::factory()->create(['name' => 'Orang Lain Banget']);

    IncomeRecord::factory()->for($me)->create();
    IncomeRecord::factory()->for($other)->create();

    $this->actingAs($me)
        ->get('/income')
        ->assertOk()
        ->assertSee('Pemilik Catatan')
        ->assertDontSee('Orang Lain Banget');
});

it('lists every income record for an admin', function () {
    $other = User::factory()->create(['name' => 'Orang Lain Banget']);
    IncomeRecord::factory()->for($other)->create();

    $this->actingAs(User::factory()->admin()->create())
        ->get('/income')
        ->assertOk()
        ->assertSee('Orang Lain Banget');
});

// SECURITY: even a crafted payload with another user's id must not be able
// to assign the record to someone else — user_id comes from the session.
it('forces the logged in user as the owner of a new income record', function () {
    $me = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($me)
        ->post('/income', incomePayload(['user_id' => $other->id]))
        ->assertRedirect(route('income.index'))
        ->assertSessionHas('success', 'Catatan pendapatan berhasil ditambahkan.');

    $this->assertDatabaseHas('income_records', [
        'play_count' => 120,
        'user_id' => $me->id,
    ]);
});

it('shows an Indonesian validation error when the play count is missing', function () {
    $this->actingAs(User::factory()->create())
        ->from('/income/create')
        ->post('/income', incomePayload(['play_count' => null]))
        ->assertRedirect('/income/create')
        ->assertSessionHasErrors('play_count', 'Jumlah permainan wajib diisi.');
});

it('rejects an income record with a play count below one', function () {
    $this->actingAs(User::factory()->create())
        ->from('/income/create')
        ->post('/income', incomePayload(['play_count' => 0]))
        ->assertSessionHasErrors('play_count', 'Jumlah permainan minimal 1.');
});

it('searches income records by machine name', function () {
    $me = User::factory()->create();
    $wangan = Machine::factory()->create(['name' => 'Wangan Midnight Maximum Tune']);
    $taiko = Machine::factory()->create(['name' => 'Taiko no Tatsujin']);

    IncomeRecord::factory()->for($me)->for($wangan, 'machine')->create();
    IncomeRecord::factory()->for($me)->for($taiko, 'machine')->create();

    $this->actingAs($me)
        ->get('/income?q=Wangan')
        ->assertOk()
        ->assertSee('Wangan Midnight Maximum Tune')
        ->assertDontSee('Taiko no Tatsujin');
});

// SECURITY: SQL-like search input is bound as a parameter and treated as text.
it('treats SQL-like search input as plain text', function () {
    $me = User::factory()->create();
    $machine = Machine::factory()->create(['name' => 'Wangan Midnight Maximum Tune']);
    IncomeRecord::factory()->for($me)->for($machine, 'machine')->create();

    $this->actingAs($me)
        ->get("/income?q='".urlencode(' OR 1=1 --'))
        ->assertOk()
        ->assertDontSee('Wangan Midnight Maximum Tune')
        ->assertSee('Belum ada catatan pendapatan');
});

it('paginates income records', function () {
    $me = User::factory()->create();
    IncomeRecord::factory()->count(15)->for($me)->create();

    $response = $this->actingAs($me)
        ->get('/income')
        ->assertOk();

    $response->assertSee('Menampilkan 1–10 dari 15 data')
        ->assertSee('page=2');

    $this->actingAs($me)
        ->get('/income?page=2')
        ->assertOk()
        ->assertSee('Menampilkan 11–15 dari 15 data');
});

it('shows an empty state when no income records exist', function () {
    $this->actingAs(User::factory()->create())
        ->get('/income')
        ->assertOk()
        ->assertSee('Belum ada catatan pendapatan');
});

it('lets staff edit their own income record', function () {
    $me = User::factory()->create();
    $record = IncomeRecord::factory()->for($me)->create();

    $this->actingAs($me)
        ->put(route('income.update', $record), incomePayload([
            'machine_id' => $record->machine_id,
            'play_count' => 200,
        ]))
        ->assertRedirect(route('income.index'))
        ->assertSessionHas('success', 'Catatan pendapatan berhasil diperbarui.');

    $this->assertDatabaseHas('income_records', ['id' => $record->id, 'play_count' => 200]);
});

it('lets staff delete their own income record', function () {
    $me = User::factory()->create();
    $record = IncomeRecord::factory()->for($me)->create();

    $this->actingAs($me)
        ->delete(route('income.destroy', $record))
        ->assertSessionHas('success', 'Catatan pendapatan berhasil dihapus.');

    $this->assertDatabaseMissing('income_records', ['id' => $record->id]);
});

// SECURITY: staff must never see, edit, or delete another user's records,
// even by hitting the URL directly.
it('forbids staff from viewing another user income record', function () {
    $other = User::factory()->create();
    $record = IncomeRecord::factory()->for($other)->create();

    $this->actingAs(User::factory()->create())
        ->get(route('income.show', $record))
        ->assertForbidden();
});

it('forbids staff from editing another user income record', function () {
    $other = User::factory()->create();
    $record = IncomeRecord::factory()->for($other)->create();

    $this->actingAs(User::factory()->create())
        ->get(route('income.edit', $record))
        ->assertForbidden();

    $this->actingAs(User::factory()->create())
        ->put(route('income.update', $record), incomePayload(['machine_id' => $record->machine_id]))
        ->assertForbidden();

    $this->assertDatabaseHas('income_records', ['id' => $record->id, 'play_count' => $record->play_count]);
});

it('forbids staff from deleting another user income record', function () {
    $other = User::factory()->create();
    $record = IncomeRecord::factory()->for($other)->create();

    $this->actingAs(User::factory()->create())
        ->delete(route('income.destroy', $record))
        ->assertForbidden();

    $this->assertDatabaseHas('income_records', ['id' => $record->id]);
});

it('lets an admin edit and delete any income record', function () {
    $other = User::factory()->create();
    $record = IncomeRecord::factory()->for($other)->create();

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('income.update', $record), incomePayload([
            'machine_id' => $record->machine_id,
            'play_count' => 300,
        ]))
        ->assertRedirect(route('income.index'));

    $this->assertDatabaseHas('income_records', ['id' => $record->id, 'play_count' => 300]);

    $this->actingAs(User::factory()->admin()->create())
        ->delete(route('income.destroy', $record))
        ->assertSessionHas('success', 'Catatan pendapatan berhasil dihapus.');

    $this->assertDatabaseMissing('income_records', ['id' => $record->id]);
});
