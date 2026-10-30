<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncomeRecordRequest;
use App\Http\Requests\UpdateIncomeRecordRequest;
use App\Models\IncomeRecord;
use App\Models\Machine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncomeController extends Controller
{
    /**
     * List income records with search (machine name / serial) and
     * pagination. SECURITY: staff only see their own rows — the query is
     * scoped with whereBelongsTo(); admins see everything.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['sometimes', 'string', 'max:255'],
        ]);

        $search = trim((string) ($validated['q'] ?? ''));

        $records = IncomeRecord::query()
            ->with(['machine', 'user'])
            // SECURITY: ownership filter — staff cannot browse other users' records.
            ->when(! $request->user()->isAdmin(), fn ($query) => $query->whereBelongsTo($request->user()))
            ->when($search !== '', function ($query) use ($search) {
                // SECURITY: the LIKE pattern is bound as a query parameter,
                // so inputs like "' OR 1=1 --" are treated as plain text.
                $query->whereHas('machine', function ($machine) use ($search) {
                    $machine->where('name', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('income.index', [
            'records' => $records,
        ]);
    }

    /**
     * Show the form to record income (any authenticated user).
     */
    public function create(): View
    {
        return view('income.create', [
            'machines' => Machine::orderBy('name')->get(),
        ]);
    }

    /**
     * Persist a new income record. SECURITY: user_id is forced to the
     * logged-in user — it is not in the validation rules, so request
     * input can never assign someone else's record to another owner.
     */
    public function store(StoreIncomeRecordRequest $request): RedirectResponse
    {
        // SECURITY: only validated fields are used, never $request->all().
        $record = new IncomeRecord($request->validated());
        $record->user_id = $request->user()->id;
        $record->save();

        return redirect()->route('income.index')->with('success', 'Catatan pendapatan berhasil ditambahkan.');
    }

    /**
     * Show one record (policy: admin or record owner only).
     */
    public function show(IncomeRecord $incomeRecord): View
    {
        $this->authorize('view', $incomeRecord);

        $incomeRecord->load(['machine', 'user']);

        return view('income.show', ['record' => $incomeRecord]);
    }

    /**
     * Show the edit form (policy: admin or record owner only).
     */
    public function edit(IncomeRecord $incomeRecord): View
    {
        $this->authorize('update', $incomeRecord);

        return view('income.edit', [
            'record' => $incomeRecord,
            'machines' => Machine::orderBy('name')->get(),
        ]);
    }

    /**
     * Update a record (policy: admin or record owner only). The owner
     * never changes — user_id is not part of the validation rules.
     */
    public function update(UpdateIncomeRecordRequest $request, IncomeRecord $incomeRecord): RedirectResponse
    {
        $this->authorize('update', $incomeRecord);

        // SECURITY: only validated fields are used, never $request->all().
        $incomeRecord->update($request->validated());

        return redirect()->route('income.index')->with('success', 'Catatan pendapatan berhasil diperbarui.');
    }

    /**
     * Delete a record (policy: admin or record owner only).
     */
    public function destroy(IncomeRecord $incomeRecord): RedirectResponse
    {
        $this->authorize('delete', $incomeRecord);

        $incomeRecord->delete();

        return back()->with('success', 'Catatan pendapatan berhasil dihapus.');
    }
}
