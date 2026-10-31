<?php

use App\Models\Category;
use App\Models\Machine;
use App\Models\MaintenanceLog;
use App\Models\User;

// Payload from the manual test checklist: must be stored and shown as
// plain text, never as executable markup.
const XSS_PAYLOAD = '<script>alert(1)</script>';

it('escapes a script tag in the machine name', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post('/machines', [
            'category_id' => Category::factory()->create()->id,
            'name' => XSS_PAYLOAD,
            'serial_number' => 'ARC-XSS',
            'location' => 'Lantai 1 - Zona A',
            'purchase_date' => '2024-01-15',
            'purchase_price' => 133_600_000,
            'price_per_play' => 16_000,
            'status' => Machine::STATUS_ACTIVE,
        ])
        ->assertRedirect(route('machines.index'));

    $machine = Machine::where('serial_number', 'ARC-XSS')->firstOrFail();

    $this->actingAs($admin)->get('/machines')
        ->assertOk()
        ->assertSee(XSS_PAYLOAD)             // escaped needle: &lt;script&gt;...
        ->assertDontSee(XSS_PAYLOAD, false); // raw markup must be absent

    $this->actingAs($admin)->get(route('machines.show', $machine))
        ->assertOk()
        ->assertSee(XSS_PAYLOAD)
        ->assertDontSee(XSS_PAYLOAD, false);
});

it('escapes a script tag in a maintenance issue', function () {
    $staff = User::factory()->create();

    $this->actingAs($staff)
        ->post('/maintenance', [
            'machine_id' => Machine::factory()->create()->id,
            'issue' => XSS_PAYLOAD,
            'action_taken' => null,
            'cost' => 0,
            'status' => MaintenanceLog::STATUS_OPEN,
            'date' => '2026-10-15',
        ])
        ->assertRedirect(route('maintenance.index'));

    $this->actingAs($staff)->get('/maintenance')
        ->assertOk()
        ->assertSee(XSS_PAYLOAD)
        ->assertDontSee(XSS_PAYLOAD, false);
});

it('escapes a script tag in the logged in user name', function () {
    $user = User::factory()->create(['name' => XSS_PAYLOAD]);

    // The topbar renders the user name on every page of the app layout.
    $this->actingAs($user)->get('/dashboard')
        ->assertOk()
        ->assertSee(XSS_PAYLOAD)
        ->assertDontSee(XSS_PAYLOAD, false);
});
