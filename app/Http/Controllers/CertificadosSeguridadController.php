<?php

namespace App\Http\Controllers;

use App\Services\SslCertificateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CertificadosSeguridadController extends Controller
{
    public function __construct(private SslCertificateService $sslService)
    {
    }

    /**
     * Muestra el panel de gestión e inspección de Certificados de Seguridad SSL/TLS.
     */
    public function index(Request $request): View
    {
        $ssl = $this->sslService->obtenerEstado($request);

        return view('servidor.certificados', compact('ssl'));
    }

    /**
     * Genera un certificado X.509 autofirmado real (RSA 2048 + SHA-256).
     */
    public function generar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cn' => ['required', 'string', 'max:120', 'regex:/^[a-zA-Z0-9.-]+$/'],
            'organizacion' => ['required', 'string', 'max:100'],
            'unidad' => ['nullable', 'string', 'max:80'],
            'pais' => ['required', 'string', 'size:2', 'alpha'],
            'dias' => ['required', 'integer', 'min:30', 'max:1095'],
        ], [
            'cn.regex' => 'El Common Name (CN) solo debe incluir letras, números, puntos y guiones.',
            'pais.size' => 'El código de país debe tener 2 letras ISO (ej. MX).',
        ]);

        $validated['organizacion'] = strip_tags($validated['organizacion']);
        $validated['unidad'] = strip_tags($validated['unidad'] ?? 'Ingenieria DWI');

        $resultado = $this->sslService->generarCertificadoAutofirmado($validated);

        if (!$resultado['exito']) {
            return response()->json([
                'exito' => false,
                'mensaje' => $resultado['mensaje'],
            ], 500);
        }

        return response()->json([
            'exito' => true,
            'mensaje' => $resultado['mensaje'],
            'datos' => $this->sslService->obtenerEstado($request),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Inspecciona el certificado TLS/SSL de un servidor remoto o local en tiempo real.
     */
    public function inspeccionar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'host' => ['required', 'string', 'max:120', 'regex:/^[a-zA-Z0-9.-]+$/'],
            'puerto' => ['nullable', 'integer', 'min:1', 'max:65535'],
        ], [
            'host.regex' => 'Ingresa un nombre de host o dominio válido.',
        ]);

        $resultado = $this->sslService->inspeccionarHostTls(
            $validated['host'],
            (int) ($validated['puerto'] ?? 443)
        );

        return response()->json([
            'exito' => $resultado['exito'],
            'mensaje' => $resultado['mensaje'],
            'datos' => $resultado,
            'timestamp' => now()->toIso8601String(),
        ], $resultado['exito'] ? 200 : 502);
    }

    /**
     * Descarga únicamente el certificado público (.crt PEM), nunca la llave privada.
     */
    public function descargarPem(): BinaryFileResponse
    {
        $ruta = $this->sslService->rutaCertificadoActivo();
        abort_if(!$ruta || !is_readable($ruta), 404, 'No existe un certificado público disponible.');

        return response()->download($ruta, 'cursoplus-public.crt', [
            'Content-Type' => 'application/x-x509-ca-cert',
        ]);
    }
}
