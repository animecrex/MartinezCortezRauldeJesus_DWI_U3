<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestSecurityLogger
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // NUNCA registrar contraseñas ni datos bancarios (PCI-DSS & Privacidad)
        $payload = json_encode($request->except([
            'password',
            'password_confirmation',
            'current_password',
            'new_password',
            '_token',
            'numero_tarjeta',
            'cvv',
            'tarjeta',
            'card_number',
        ]));

        $sospechoso = $this->detectarPayloadSospechoso($payload);

        $datosLog = [
            'ip' => $request->ip(),
            'metodo' => $request->method(),
            'ruta' => $request->path(),
            'usuario_id' => auth()->check() ? auth()->id() : null,
            'estado' => $response->getStatusCode(),
            'user_agent' => $request->userAgent(),
        ];

        if ($sospechoso) {
            Log::channel('security')->warning('INTENTO_SOSPECHOSO_DETECTADO', $datosLog);
        } else {
            Log::info('PETICION_REGISTRADA', $datosLog);
        }

        return $response;
    }

    private function detectarPayloadSospechoso(?string $payload): bool
    {
        if (!$payload) {
            return false;
        }

        // Patrones específicos de inyección (SQLi, XSS, Path Traversal)
        $patrones = [
            '<script',
            'javascript:',
            'onerror=',
            'onload=',
            'eval(',
            'union select',
            'drop table',
            '../',
            '..\\',
            '--',
            '1=1',
            "' or '1'='1",
            '" or "1"="1',
            '<svg/onload',
            '<iframe',
        ];

        $payload = strtolower($payload);

        foreach ($patrones as $patron) {
            if (str_contains($payload, $patron)) {
                return true;
            }
        }

        return false;
    }
}
