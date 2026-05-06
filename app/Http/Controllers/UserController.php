<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        abort_if(auth()->user()->role !== 'admin', 403);
        $users = User::orderBy('name')->get();
        return view('user.index', compact('users'));
    }

    public function store(Request $request)
    {
        abort_if(auth()->user()->role !== 'admin', 403);
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role'  => 'required|in:admin,supervisor,cashier',
        ]);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'role'      => $request->role,
            'password'  => Hash::make(''),   // default blank
            'is_active' => true,
        ]);

        return back()->with('success', "User {$request->name} berhasil ditambahkan. Password default: kosong.");
    }

    public function update(Request $request, User $user)
    {
        abort_if(auth()->user()->role !== 'admin', 403);
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role'      => 'required|in:admin,supervisor,cashier',
            'is_active' => 'boolean',
        ]);

        $user->update([
            'name'      => $request->name,
            'email'     => $request->email,
            'role'      => $request->role,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', "User {$user->name} berhasil diperbarui.");
    }

    public function resetPassword(User $user)
    {
        abort_if(auth()->user()->role !== 'admin', 403);
        $user->update(['password' => Hash::make('')]);
        return back()->with('success', "Password {$user->name} berhasil direset ke kosong.");
    }

    public function setPassword(Request $request, User $user)
    {
        abort_if(auth()->user()->role !== 'admin', 403);
        $request->validate([
            'new_password' => 'nullable|string|max:255|confirmed',
        ]);
        $user->update(['password' => Hash::make($request->input('new_password', ''))]);
        return back()->with('success', "Password {$user->name} berhasil diubah.");
    }

    public function destroy(User $user)
    {
        abort_if(auth()->user()->role !== 'admin', 403);
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }
        $user->delete();
        return back()->with('success', "User {$user->name} berhasil dihapus.");
    }
}
