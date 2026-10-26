<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class RegistrasiController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        $user = User::create($data + ['role' => 'penyewa']);
        Auth::login($user);
        $request->session()->regenerate();

        $intended = $request->session()->pull('url.intended');
        $path = $intended ? parse_url($intended, PHP_URL_PATH) : null;

        return $path && preg_match('#^/kamar/\d+/sewa$#', $path)
            ? redirect($path) : redirect()->route('penyewa.index');
    }
}
