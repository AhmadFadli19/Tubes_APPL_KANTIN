<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu.');
        }

        $user = Auth::user();

        if ($user->status === 'nonaktif') {
            Auth::logout();

            return redirect()->route('login')->with('error', 'Akun Anda dinonaktifkan oleh administrator. Silakan hubungi pihak kampus.');
        }

        if (! in_array($user->role, $roles)) {
            // Req. SMPEK-130: Sistem menolak akses dan menampilkan pesan "Akses Ditolak: Anda tidak memiliki wewenang untuk mengakses halaman ini."
            return response()->view('errors.403', [
                'message' => 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengakses halaman ini.',
                'userRole' => $user->role,
            ], 403);
        }

        return $next($request);
    }
}
