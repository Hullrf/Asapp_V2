<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Evita que la app se cargue en un iframe (clickjacking)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Evita que el navegador adivine el tipo de contenido (MIME sniffing)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Fuerza HTTPS por 1 año en producción
        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Controla qué información se envía en el header Referer
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Desactiva funcionalidades del navegador que la app no usa
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Oculta que es una app PHP/Laravel
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}
