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

        // Protección contra Clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Protección contra MIME Sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Política de Referrer segura
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Restricción de permisos y APIs del navegador
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(self)');

        // Protección contra XSS legacy en navegadores antiguos
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Restringir políticas entre dominios Flash/PDF
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        // Ocultar cabecera de tecnología del servidor
        $response->headers->remove('X-Powered-By');

        // Content Security Policy endurecido
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' https://code.jquery.com https://cdn.jsdelivr.net; " .
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; " .
            "font-src 'self' https://fonts.gstatic.com data:; " .
            "img-src 'self' data: blob:; " .
            "connect-src 'self' https://cdn.jsdelivr.net; " .
            "frame-ancestors 'self'; " .
            "form-action 'self'; " .
            "base-uri 'self'; " .
            "object-src 'none';"
        );

        return $response;
    }
}
