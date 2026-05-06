<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!in_array(auth()->user()->role, $roles, true)) {
            // Cashiers are redirected to POS
            if (auth()->user()->role === 'cashier') {
                return redirect()->route('pos.index')->with('warning', 'Akses terbatas. Anda diarahkan ke halaman kasir.');
            }
            abort(403, 'Akses ditolak untuk level pengguna ini.');
        }

        return $next($request);
    }
}
