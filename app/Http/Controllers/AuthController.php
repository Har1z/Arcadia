<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Create a new account. The role attribute is not fillable, so every
     * self-registered user is a staff member by construction.
     */
    public function storeRegister(RegisterRequest $request): RedirectResponse
    {
        // SECURITY: only validated fields are used, never $request->all().
        $user = User::create($request->validated());

        // SECURITY: log the user in immediately and rotate the session id so
        // the pre-login session cannot be reused (session fixation).
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Pendaftaran berhasil, selamat datang!');
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        // SECURITY: only validated credentials are checked; the remember box
        // is intentionally omitted to keep sessions simple.
        $credentials = $request->safe()->only(['email', 'password']);

        // One generic message for wrong email or wrong password so the form
        // never reveals whether an account exists.
        if (! Auth::attempt($credentials)) {
            return back()
                ->withErrors(['email' => 'Email atau password salah.'])
                ->onlyInput('email');
        }

        // SECURITY: rotate the session id after login (session fixation).
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    /**
     * End the session. POST-only, so a crafted link cannot log users out.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        // SECURITY: invalidate() deletes the session data server-side and
        // regenerateToken() rotates the CSRF token for the next request.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil keluar.');
    }
}
