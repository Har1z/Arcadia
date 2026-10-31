<?php

use App\Models\Category;
use App\Models\IncomeRecord;
use App\Models\Machine;
use App\Models\MaintenanceLog;
use App\Models\User;
use Tests\TestCase;

/**
 * Visit every mutating form page and report which URIs are missing the
 * hidden CSRF token field, so a failure names the offending form.
 *
 * @param  TestCase  $test  Bound Pest test case ($this in the test)
 * @param  array<int, string>  $uris
 * @return array<int, string>
 */
function assertFormsCarryCsrfToken(TestCase $test, array $uris): array
{
    $missing = [];

    foreach ($uris as $uri) {
        $response = $test->get($uri);

        if ($response->status() !== 200 || ! str_contains($response->getContent(), 'name="_token"')) {
            $missing[] = $uri;
        }
    }

    return $missing;
}

// SECURITY: Laravel's CSRF middleware rejects requests whose _token is
// missing/mismatched with a 419 (verified manually in the browser because
// the middleware skips validation while running tests). The automated
// guard is that every mutating form actually carries a hidden token field.
it('renders a csrf token field on the guest forms', function (string $uri) {
    $this->get($uri)
        ->assertOk()
        ->assertSee('name="_token"', false);
})->with([
    'login page' => ['/login'],
    'register page' => ['/register'],
]);

it('renders a csrf token field on every admin form', function () {
    $machine = Machine::factory()->create();
    $category = Category::factory()->create();
    $user = User::factory()->create();

    $this->actingAs(User::factory()->admin()->create());

    $missing = assertFormsCarryCsrfToken($this, [
        '/machines/create',
        "/machines/{$machine->id}/edit",
        '/categories/create',
        "/categories/{$category->id}/edit",
        '/users/create',
        "/users/{$user->id}/edit",
        '/dashboard',
    ]);

    expect($missing)->toBe([]);
});

it('renders a csrf token field on every staff form', function () {
    $staff = User::factory()->create();
    $log = MaintenanceLog::factory()->for($staff)->create();
    $income = IncomeRecord::factory()->for($staff)->create();

    $this->actingAs($staff);

    $missing = assertFormsCarryCsrfToken($this, [
        '/machines',
        '/maintenance/create',
        "/maintenance/{$log->id}/edit",
        '/income/create',
        "/income/{$income->id}/edit",
        '/dashboard',
    ]);

    expect($missing)->toBe([]);
});
