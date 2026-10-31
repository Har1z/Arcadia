<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * List users with search (name / email) and role filter.
     * Admin-only (route middleware 'role:admin').
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['sometimes', 'string', 'max:255'],
            'role' => ['sometimes', 'in:'.implode(',', StoreUserRequest::roles())],
        ]);

        $search = trim((string) ($validated['q'] ?? ''));
        $role = $validated['role'] ?? null;

        $users = User::query()
            // SECURITY: bound LIKE parameter; "' OR 1=1 --" is plain text.
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role !== null, fn ($query) => $query->where('role', $role))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('users.index', [
            'users' => $users,
            'roles' => User::ROLE_LABELS,
        ]);
    }

    /**
     * Show the form to create a user (admin only).
     */
    public function create(): View
    {
        return view('users.create', [
            'roles' => User::ROLE_LABELS,
        ]);
    }

    /**
     * Persist a new user (admin only).
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        // SECURITY: 'role' is not fillable, so it can never sneak in through
        // mass assignment. It is assigned explicitly from validated input.
        $user = User::create($request->safe()->only(['name', 'email', 'password']));
        $user->forceFill(['role' => $request->validated('role')])->save();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Show the edit form (admin only).
     */
    public function edit(User $user): View
    {
        return view('users.edit', [
            'user' => $user,
            'roles' => User::ROLE_LABELS,
        ]);
    }

    /**
     * Update a user (admin only). A blank password keeps the current one.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        // SECURITY: only validated fields are used, never $request->all().
        $data = $request->safe()->only(['name', 'email']);
        if ($request->validated('password') !== null) {
            $data['password'] = $request->validated('password');
        }

        $user->fill($data);
        $user->forceFill(['role' => $request->validated('role')]);
        $user->save();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Delete a user (admin only). Users who still have maintenance logs or
     * income records cannot be removed — those foreign keys are
     * restrictOnDelete, and the database error becomes a friendly message.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        // SECURITY: an admin must not be able to delete their own account
        // (locking themselves out of the admin panel).
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        try {
            $user->delete();

            return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
        } catch (QueryException $exception) {
            // SECURITY/UX: the foreign key (restrict) blocked the delete
            // because this user still has maintenance logs or income
            // records. Show a message instead of a 500 error.
            return back()->with('error', 'Pengguna masih memiliki catatan perawatan atau pendapatan dan tidak bisa dihapus.');
        }
    }
}
