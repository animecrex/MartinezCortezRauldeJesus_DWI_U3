<?php

namespace App\Http\Controllers;

use App\Services\DomainConfigService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConfiguracionDominioController extends Controller
{
    public function __construct(private DomainConfigService $domainService)
    {
    }

    /**
     * Muestra el panel de Configuración de Dominio, DNS, VirtualHost y SSL.
     */
    public function index(Request $request): View
    {
        $dominio = $this->domainService->obtenerConfiguracion($request);

        return view('servidor.dominio', compact('dominio'));
    }

    /**
     * Actualiza la configuración del dominio y regenera zona DNS y plantillas.
     */
    public function actualizar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dominio_principal' => ['required', 'string', 'max:120', 'regex:/^(?:[a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}$/'],
            'subdominio_api' => ['required', 'string', 'max:40', 'regex:/^[a-zA-Z0-9-]+$/'],
            'ip_destino' => ['required', 'ip'],
            'ttl_dns' => ['required', 'integer', 'min:60', 'max:86400'],
            'forzar_https' => ['nullable', 'boolean'],
        ], [
            'dominio_principal.regex' => 'Ingresa un dominio válido (ej. cursoplus.local o cursoplus.edu.mx).',
            'subdominio_api.regex' => 'El subdominio solo puede contener letras, números y guiones.',
            'ip_destino.ip' => 'Ingresa una dirección IP válida (ej. 127.0.0.1).',
        ]);

        $actualizado = $this->domainService->actualizar($validated, $request);

        return response()->json([
            'exito' => true,
            'mensaje' => 'Configuración de dominio actualizada y plantillas regeneradas.',
            'datos' => $actualizado,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Verifica la resolución DNS de un dominio en tiempo real.
     */
    public function verificarDns(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dominio' => ['required', 'string', 'max:120', 'regex:/^[a-zA-Z0-9.-]+$/'],
        ], [
            'dominio.regex' => 'Formato de host/dominio no válido.',
        ]);

        $resultado = $this->domainService->verificarDns($validated['dominio']);

        return response()->json([
            'exito' => true,
            'mensaje' => $resultado['resuelto']
                ? "Resolución completada: {$resultado['dominio']} → {$resultado['ip_resuelta']}."
                : "El dominio {$resultado['dominio']} no resolvió a una IP en el DNS actual (verifica archivo hosts o zona DNS).",
            'datos' => $resultado,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Restablece la configuración de dominio a valores predeterminados.
     */
    public function restablecer(Request $request): JsonResponse
    {
        $restablecido = $this->domainService->restablecer($request);

        return response()->json([
            'exito' => true,
            'mensaje' => 'Configuración de dominio restablecida a los valores predeterminados.',
            'datos' => $restablecido,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
