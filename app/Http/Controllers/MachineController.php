<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMachineRequest;
use App\Http\Requests\UpdateMachineRequest;
use App\Models\Category;
use App\Models\Machine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MachineController extends Controller
{
    /**
     * List machines with search (name / serial / location) and status
     * filter. Filters persist across pages via withQueryString().
     */
    public function index(Request $request): View
    {
        // Invalid filter values are rejected with a validation error
        // instead of being silently ignored.
        $validated = $request->validate([
            'q' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'in:'.implode(',', StoreMachineRequest::statuses())],
        ]);

        $search = trim((string) ($validated['q'] ?? ''));
        $status = $validated['status'] ?? null;

        $machines = Machine::query()
            ->with('category')
            ->when($search !== '', function ($query) use ($search) {
                // SECURITY: the LIKE pattern is bound as a query parameter,
                // so inputs like "' OR 1=1 --" are treated as plain text.
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->when($status !== null, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('machines.index', [
            'machines' => $machines,
            'statuses' => Machine::STATUS_LABELS,
        ]);
    }

    /**
     * Show the form to create a new machine (admin only).
     */
    public function create(): View
    {
        return view('machines.create', [
            'categories' => Category::orderBy('name')->get(),
            'statuses' => Machine::STATUS_LABELS,
        ]);
    }

    /**
     * Persist a new machine (admin only, enforced by route middleware
     * and StoreMachineRequest::authorize).
     */
    public function store(StoreMachineRequest $request): RedirectResponse
    {
        // SECURITY: only validated fields are used, never $request->all().
        Machine::create($request->validated());

        return redirect()->route('machines.index')->with('success', 'Mesin berhasil ditambahkan.');
    }

    /**
     * Show one machine with its details.
     */
    public function show(Machine $machine): View
    {
        $machine->load('category');

        return view('machines.show', ['machine' => $machine]);
    }

    /**
     * Show the edit form. Staff only receive the status field.
     */
    public function edit(Machine $machine): View
    {
        return view('machines.edit', [
            'machine' => $machine,
            'categories' => Category::orderBy('name')->get(),
            'statuses' => Machine::STATUS_LABELS,
        ]);
    }

    /**
     * Update a machine. Admins may edit everything; staff only the status
     * (UpdateMachineRequest rejects any other field from staff).
     */
    public function update(UpdateMachineRequest $request, Machine $machine): RedirectResponse
    {
        // SECURITY: staff are limited to the status field twice over —
        // the request 'prohibits' other fields, and here only 'status'
        // is read for non-admins.
        $data = $request->user()->isAdmin()
            ? $request->validated()
            : $request->safe()->only(['status']);

        $machine->update($data);

        return redirect()->route('machines.index')->with('success', 'Mesin berhasil diperbarui.');
    }

    /**
     * Delete a machine (admin only). Maintenance logs and income records
     * are removed with it by the cascadeOnDelete foreign keys.
     */
    public function destroy(Machine $machine): RedirectResponse
    {
        $machine->delete();

        return back()->with('success', 'Mesin berhasil dihapus.');
    }
}
