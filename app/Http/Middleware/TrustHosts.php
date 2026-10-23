<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request)
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('X-Frame-Options', 'SAMEORIGIN')
            ->header('X-XSS-Protection', '1; mode=block')
            ->header('Referrer-Policy', 'no-referrer-when-downgrade')
            ->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains')
            ->header('Content-Security-Policy', "default-src 'self'; img-src 'self' data: blob:; script-src 'self' 'unsafe-inline' https://code.jquery.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; font-src 'self' https://fonts.gstatic.com data:; connect-src 'self'; frame-ancestors 'self'; form-action 'self';");
    }
}