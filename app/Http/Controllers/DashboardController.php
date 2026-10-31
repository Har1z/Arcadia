<?php

namespace App\Http\Controllers;

use App\Models\IncomeRecord;
use App\Models\Machine;
use App\Models\MaintenanceLog;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Summary dashboard. Totals come from aggregate queries on indexed
     * columns, and both tables eagerly load their machine so rendering
     * does not trigger N+1 queries.
     */
    public function index(): View
    {
        $statusCounts = Machine::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('dashboard', [
            'totalMachines' => Machine::count(),
            'activeMachines' => Machine::where('status', Machine::STATUS_ACTIVE)->count(),
            // Revenue of the current calendar month (date column, no time).
            'monthlyIncome' => IncomeRecord::query()
                ->whereYear('date', now()->year)
                ->whereMonth('date', now()->month)
                ->sum('total_income'),
            'openMaintenanceCount' => MaintenanceLog::where('status', MaintenanceLog::STATUS_OPEN)->count(),
            'openMaintenanceLogs' => MaintenanceLog::query()
                ->with('machine')
                ->where('status', MaintenanceLog::STATUS_OPEN)
                ->orderByDesc('date')
                ->take(5)
                ->get(),
            'latestIncomeRecords' => IncomeRecord::query()
                ->with('machine')
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->take(5)
                ->get(),
            'statusCounts' => $statusCounts,
        ]);
    }
}
