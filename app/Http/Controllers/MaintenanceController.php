<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaintenanceLogRequest;
use App\Http\Requests\UpdateMaintenanceLogRequest;
use App\Models\Machine;
use App\Models\MaintenanceLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    /**
     * List maintenance logs with search (issue / machine name) and status
     * filter. SECURITY: staff only see their own rows — the query is
     * scoped with whereBelongsTo(); admins see everything.
     */
    public function index(Request $request): View
    {
        // Invalid filter values are rejected with a validation error
        // instead of being silently ignored.
        $validated = $request->validate([
            'q' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'in:'.implode(',', StoreMaintenanceLogRequest::statuses())],
        ]);

        $search = trim((string) ($validated['q'] ?? ''));
        $status = $validated['status'] ?? null;

        $logs = MaintenanceLog::query()
            ->with(['machine', 'user'])
            // SECURITY: ownership filter — staff cannot browse other users' logs.
            ->when(! $request->user()->isAdmin(), fn ($query) => $query->whereBelongsTo($request->user()))
            ->when($search !== '', function ($query) use ($search) {
                // SECURITY: the LIKE pattern is bound as a query parameter,
                // so inputs like "' OR 1=1 --" are treated as plain text.
                $query->where(function ($q) use ($search) {
                    $q->where('issue', 'like', "%{$search}%")
                        ->orWhereHas('machine', function ($machine) use ($search) {
                            $machine->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status !== null, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('maintenance.index', [
            'logs' => $logs,
            'statuses' => MaintenanceLog::STATUS_LABELS,
        ]);
    }

    /**
     * Show the form to log a new maintenance issue (any authenticated user).
     */
    public function create(): View
    {
        return view('maintenance.create', [
            'machines' => Machine::orderBy('name')->get(),
            'statuses' => MaintenanceLog::STATUS_LABELS,
        ]);
    }

    /**
     * Persist a new maintenance log. SECURITY: user_id is forced to the
     * logged-in user — it is not in the validation rules, so request
     * input can never assign someone else's record to another owner.
     */
    public function store(StoreMaintenanceLogRequest $request): RedirectResponse
    {
        // SECURITY: only validated fields are used, never $request->all().
        $log = new MaintenanceLog($request->validated());
        $log->user_id = $request->user()->id;
        $log->save();

        return redirect()->route('maintenance.index')->with('success', 'Log perawatan berhasil ditambahkan.');
    }

    /**
     * Show one log (policy: admin or record owner only).
     */
    public function show(MaintenanceLog $maintenanceLog): View
    {
        $this->authorize('view', $maintenanceLog);

        $maintenanceLog->load(['machine', 'user']);

        return view('maintenance.show', ['log' => $maintenanceLog]);
    }

    /**
     * Show the edit form (policy: admin or record owner only).
     */
    public function edit(MaintenanceLog $maintenanceLog): View
    {
        $this->authorize('update', $maintenanceLog);

        return view('maintenance.edit', [
            'log' => $maintenanceLog,
            'machines' => Machine::orderBy('name')->get(),
            'statuses' => MaintenanceLog::STATUS_LABELS,
        ]);
    }

    /**
     * Update a log (policy: admin or record owner only). The owner never
     * changes — user_id is not part of the validation rules.
     */
    public function update(UpdateMaintenanceLogRequest $request, MaintenanceLog $maintenanceLog): RedirectResponse
    {
        $this->authorize('update', $maintenanceLog);

        // SECURITY: only validated fields are used, never $request->all().
        $maintenanceLog->update($request->validated());

        return redirect()->route('maintenance.index')->with('success', 'Log perawatan berhasil diperbarui.');
    }

    /**
     * Delete a log (policy: admin or record owner only).
     */
    public function destroy(MaintenanceLog $maintenanceLog): RedirectResponse
    {
        $this->authorize('delete', $maintenanceLog);

        $maintenanceLog->delete();

        return back()->with('success', 'Log perawatan berhasil dihapus.');
    }
}
