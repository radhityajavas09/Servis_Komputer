<?php

namespace App\Http\Middleware;

use App\Enums\RolePengguna;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $userRole = $user->role instanceof RolePengguna ? $user->role->value : (string) $user->role;

        // Flatten roles if passed as comma-separated string e.g. "admin,petugas"
        $allowedRoles = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $subRole) {
                $trimmed = trim($subRole);
                if ($trimmed !== '') {
                    $allowedRoles[] = $trimmed;
                }
            }
        }

        if (! in_array($userRole, $allowedRoles, true)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
