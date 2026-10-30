<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Root: authenticated users go to the dashboard, guests to the login page.
Route::get('/', fn () => auth()->check()
    ? redirect()->route('dashboard')
    : redirect()->route('login')
)->name('home');

// Guest-only authentication pages. The 'guest' middleware bounces
// already-authenticated users away from these routes.
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'storeRegister'])->name('register.store');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    // SECURITY: throttle:login limits attempts to 5 per minute per email+IP
    // (limiter defined in AppServiceProvider).
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('login.attempt');
});

// Authenticated area. The 'auth' middleware redirects guests to the login page.
Route::middleware('auth')->group(function () {
    // Logout is POST-only so it cannot be triggered by a crafted link.
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Component/theme verification page. Not linked from the sidebar;
// remove before production deployment.
Route::get('/samples/ui', fn () => view('samples.ui'))->name('samples.ui');
