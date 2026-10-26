<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password tidak sesuai.'])->withInput();
        }

        $request->session()->regenerate();

        if (! $request->user()->isAdmin() && $request->user()->role !== 'penyewa') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors(['email' => 'Akun ini tidak memiliki akses admin.'])->withInput();
        }

        if ($request->user()->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }
        $intended = $request->session()->pull('url.intended');
        $path = $intended ? parse_url($intended, PHP_URL_PATH) : null;
        if ($path && preg_match('#^/kamar/\d+/sewa$#', $path)) {
            return redirect($path);
        }

        return redirect()->route('penyewa.index');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }
}
