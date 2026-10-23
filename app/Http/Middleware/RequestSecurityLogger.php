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

        $payload = json_encode($request->except(['password', 'password_confirmation', '_token']));

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
            Log::warning('INTENTO_SOSPECHOSO_DETECTADO', $datosLog);
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

        $patrones = [
            '<script',
            'javascript:',
            'onerror=',
            'onload=',
            'union select',
            'drop table',
            '../',
            '--',
            ' or ',
            ' and ',
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