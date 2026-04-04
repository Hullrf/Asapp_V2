<?php

namespace App\Http\Middleware;

use App\Enums\RolUsuario;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EsPersonal
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!auth()->check() || (!$user->esAdmin() && $user->rol !== RolUsuario::Mesero)) {
            return redirect()->route('login');
        }

        $negocio = $user->esAdmin() ? $user->negocioActivo() : $user->negocio;
        if ($negocio?->suspendido) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Tu cuenta ha sido suspendida. Contacta al administrador de ASAPP.']);
        }

        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        return $response;
    }
}
