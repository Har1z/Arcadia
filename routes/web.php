<?php

use Illuminate\Support\Facades\Route;

// Temporary routes for the UI foundation work. They are replaced by
// controllers starting with the manual authentication step.
Route::get('/', fn () => redirect('/dashboard'));

Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

// Component/theme verification page. Not linked from the sidebar;
// remove before production deployment.
Route::get('/samples/ui', fn () => view('samples.ui'));

// SECURITY: stub only — logout becomes a real POST action (with session
// invalidation) once manual authentication is implemented.
Route::post('/logout', fn () => back());
