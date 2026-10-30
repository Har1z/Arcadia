<?php

use App\Models\Category;
use App\Models\Machine;
use App\Models\User;

it('redirects guests from the categories page to the login page', function () {
    $this->get('/categories')->assertRedirect('/login');
});

it('lets staff view the category list read-only', function () {
    Category::create(['name' => 'Racing']);

    $this->actingAs(User::factory()->create())
        ->get('/categories')
        ->assertOk()
        ->assertSee('Racing')
        ->assertDontSee('Tambah Kategori');
});

it('lets an admin create a category', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post('/categories', ['name' => 'Racing', 'description' => 'Mesin balapan.'])
        ->assertRedirect(route('categories.index'))
        ->assertSessionHas('success', 'Kategori berhasil ditambahkan.');

    $this->assertDatabaseHas('categories', ['name' => 'Racing']);
});

it('shows an Indonesian validation error when the category name is missing', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->from('/categories/create')
        ->post('/categories', ['name' => ''])
        ->assertRedirect('/categories/create')
        ->assertSessionHasErrors('name', 'Nama kategori wajib diisi.');
});

it('rejects a duplicate category name', function () {
    Category::create(['name' => 'Racing']);

    $this->actingAs(User::factory()->admin()->create())
        ->from('/categories/create')
        ->post('/categories', ['name' => 'Racing'])
        ->assertSessionHasErrors('name', 'Nama kategori sudah digunakan.');
});

it('lets an admin update a category and keep its own name', function () {
    $category = Category::create(['name' => 'Racing']);

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('categories.update', $category), ['name' => 'Racing', 'description' => 'Diperbarui.'])
        ->assertRedirect(route('categories.index'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('categories', ['id' => $category->id, 'description' => 'Diperbarui.']);
});

// SECURITY: every mutating category route is admin-only for staff.
it('forbids staff from all category pages except the index', function (string $uri, string $method) {
    $category = Category::create(['name' => 'Racing']);

    $response = match ($method) {
        'GET' => $this->actingAs(User::factory()->create())->get($uri),
        'POST' => $this->actingAs(User::factory()->create())->post($uri, ['name' => 'Baru']),
        'PUT' => $this->actingAs(User::factory()->create())->put($uri, ['name' => 'Baru']),
        'DELETE' => $this->actingAs(User::factory()->create())->delete($uri),
    };

    $response->assertForbidden();
})->with([
    'create form' => ['/categories/create', 'GET'],
    'store' => ['/categories', 'POST'],
    'edit form' => fn () => ['/categories/'.Category::create(['name' => 'Racing Edit'])->id.'/edit', 'GET'],
    'update' => fn () => ['/categories/'.Category::create(['name' => 'Racing Update'])->id, 'PUT'],
    'destroy' => fn () => ['/categories/'.Category::create(['name' => 'Racing Destroy'])->id, 'DELETE'],
    'show' => fn () => ['/categories/'.Category::create(['name' => 'Racing Show'])->id, 'GET'],
]);

it('lets an admin delete a category that has no machines', function () {
    $category = Category::create(['name' => 'Kosong']);

    $this->actingAs(User::factory()->admin()->create())
        ->delete(route('categories.destroy', $category))
        ->assertRedirect(route('categories.index'))
        ->assertSessionHas('success', 'Kategori berhasil dihapus.');

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

// UX checklist: a category with machines shows a friendly message, not a 500 error.
it('keeps a category that still has machines and shows a friendly error', function () {
    $machine = Machine::factory()->create();
    $category = $machine->category;

    $this->actingAs(User::factory()->admin()->create())
        ->from(route('categories.index'))
        ->delete(route('categories.destroy', $category))
        ->assertRedirect(route('categories.index'))
        ->assertSessionHas('error', 'Kategori masih memiliki mesin dan tidak bisa dihapus.');

    $this->assertDatabaseHas('categories', ['id' => $category->id]);
});

it('searches categories by name', function () {
    Category::create(['name' => 'Racing', 'description' => 'Balapan.']);
    Category::create(['name' => 'Rhythm', 'description' => 'Musik.']);

    $this->actingAs(User::factory()->create())
        ->get('/categories?q=Rhythm')
        ->assertOk()
        ->assertSee('Rhythm')
        ->assertDontSee('Balapan.');
});
