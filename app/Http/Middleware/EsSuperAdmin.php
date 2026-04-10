<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EsSuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('superadmin_auth')) {
            return redirect()->route('superadmin.login');
        }

        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        return $response;
    }
}
