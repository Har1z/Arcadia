<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Placeholder dashboard. Real numbers are aggregated from the
     * database in the dashboard/user-management step.
     */
    public function index(): View
    {
        return view('dashboard');
    }
}
