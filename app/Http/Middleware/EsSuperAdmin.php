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

        return $next($request);
    }
}
