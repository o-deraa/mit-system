<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MitRoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $currentRole = session('mit_role');

        if (!$currentRole) {
            return redirect()
                ->route('mit.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        if (!in_array($currentRole, $roles, true)) {
            return redirect()
                ->route('mit.login')
                ->with('error', 'Kamu tidak memiliki akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
