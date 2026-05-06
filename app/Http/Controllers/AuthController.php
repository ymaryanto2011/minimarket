<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string|max:255',
            'password' => 'nullable|string|max:255',
        ]);

        $login = trim($request->input('login'));

        // Cari berdasarkan email atau nama (case-insensitive)
        $user = User::where('email', $login)
            ->orWhereRaw('LOWER(name) = ?', [strtolower($login)])
            ->first();

        if (!$user) {
            return back()->withErrors(['login' => 'Username/email tidak ditemukan.'])->onlyInput('login');
        }

        if (!$user->is_active) {
            return back()->withErrors(['login' => 'Akun tidak aktif. Hubungi administrator.'])->onlyInput('login');
        }

        if (!Hash::check($request->input('password', ''), $user->password)) {
            return back()->withErrors(['login' => 'Username/email atau password salah.'])->onlyInput('login');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return $this->redirectByRole($user);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function redirectByRole(User $user)
    {
        return match ($user->role) {
            'cashier' => redirect()->route('pos.index'),
            default   => redirect()->route('dashboard'),
        };
    }
}
