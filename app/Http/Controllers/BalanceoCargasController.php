<?php

namespace App\Http\Controllers;

use App\Services\LoadBalancerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BalanceoCargasController extends Controller
{
    public function __construct(private LoadBalancerService $loadBalancer)
    {
    }

    /**
     * Muestra el panel interactivo de Balanceo de Cargas de Curso+.
     */
    public function index(Request $request): View
    {
        $estado = $this->loadBalancer->obtenerEstado($request);

        return view('servidor.balanceo', compact('estado'));
    }

    /**
     * Devuelve el estado actual del clúster y métricas en JSON.
     */
    public function estadoJson(Request $request): JsonResponse
    {
        return response()->json([
            'exito' => true,
            'mensaje' => 'Estado del balanceador de cargas obtenido correctamente.',
            'datos' => $this->loadBalancer->obtenerEstado($request),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Despacha una o varias peticiones a través del algoritmo de balanceo activo.
     */
    public function despachar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cantidad' => 'nullable|integer|min:1|max:20',
            'ruta' => 'nullable|string|max:80',
        ]);

        $cantidad = (int) ($validated['cantidad'] ?? 1);
        $ruta = strip_tags($validated['ruta'] ?? '/api/v1/cursos');

        $resultado = $this->loadBalancer->despacharPeticiones($cantidad, $ruta, $request);

        return response()->json([
            'exito' => $resultado['exito'],
            'mensaje' => $resultado['mensaje'],
            'despachadas' => $resultado['despachadas'],
            'datos' => $resultado['estado'],
            'timestamp' => now()->toIso8601String(),
        ], $resultado['codigo_http']);
    }

    /**
     * Cambia el algoritmo de distribución de tráfico en caliente.
     */
    public function cambiarAlgoritmo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'algoritmo' => 'required|string|in:round_robin,weighted_round_robin,least_connections',
        ]);

        $estado = $this->loadBalancer->cambiarAlgoritmo($validated['algoritmo'], $request);

        return response()->json([
            'exito' => true,
            'mensaje' => 'Algoritmo actualizado a ' . $estado['resumen']['algoritmo_nombre'] . '.',
            'datos' => $estado,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Alterna el estado ONLINE/OFFLINE de un nodo para demostrar failover automático.
     */
    public function alternarNodo(Request $request, string $id): JsonResponse
    {
        $estado = $this->loadBalancer->alternarNodo($id, $request);

        if ($estado === null) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Nodo no encontrado en el clúster.',
            ], 404);
        }

        return response()->json([
            'exito' => true,
            'mensaje' => "Estado del nodo {$id} actualizado correctamente.",
            'datos' => $estado,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Reinicia contadores y restaura todos los nodos del clúster.
     */
    public function reiniciar(Request $request): JsonResponse
    {
        $estado = $this->loadBalancer->reiniciar($request);

        return response()->json([
            'exito' => true,
            'mensaje' => 'Clúster reiniciado y todos los nodos restaurados a ONLINE.',
            'datos' => $estado,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
