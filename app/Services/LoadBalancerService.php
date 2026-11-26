<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LoadBalancerService
{
    private const CACHE_KEY = 'cursoplus_load_balancer_state_v1';

    public const ALGORITMOS = [
        'round_robin' => [
            'nombre' => 'Round Robin',
            'descripcion' => 'Distribuye las peticiones de manera cíclica y equitativa entre todos los nodos saludables.',
        ],
        'weighted_round_robin' => [
            'nombre' => 'Weighted Round Robin (Ponderado)',
            'descripcion' => 'Asigna tráfico proporcional al peso/capacidad de cada nodo del clúster.',
        ],
        'least_connections' => [
            'nombre' => 'Least Connections (Menor Carga)',
            'descripcion' => 'Dirige cada nueva solicitud al nodo saludable con menos conexiones activas simultáneas.',
        ],
    ];

    /**
     * Obtiene el estado actual del clúster desde caché (o inicializa el estado base).
     */
    public function obtenerEstado(?Request $request = null): array
    {
        $estado = Cache::get(self::CACHE_KEY);

        if (!is_array($estado) || empty($estado['nodos'])) {
            $estado = $this->estadoInicial();
            Cache::put(self::CACHE_KEY, $estado, now()->addHours(12));
        }

        return $this->enriquecerEstado($estado, $request);
    }

    /**
     * Cambia el algoritmo activo del balanceador de cargas.
     */
    public function cambiarAlgoritmo(string $algoritmo, ?Request $request = null): array
    {
        if (!array_key_exists($algoritmo, self::ALGORITMOS)) {
            $algoritmo = 'round_robin';
        }

        $estado = Cache::get(self::CACHE_KEY) ?: $this->estadoInicial();
        $estado['algoritmo'] = $algoritmo;
        $estado['rr_index'] = 0;
        $estado['wrr_index'] = 0;

        Cache::put(self::CACHE_KEY, $estado, now()->addHours(12));

        return $this->enriquecerEstado($estado, $request);
    }

    /**
     * Activa o desactiva un nodo específico para demostrar failover automático.
     */
    public function alternarNodo(string $nodoId, ?Request $request = null): ?array
    {
        $estado = Cache::get(self::CACHE_KEY) ?: $this->estadoInicial();
        $encontrado = false;

        foreach ($estado['nodos'] as &$nodo) {
            if ($nodo['id'] === $nodoId) {
                $encontrado = true;
                if ($nodo['estado'] === 'ONLINE') {
                    $nodo['estado'] = 'OFFLINE';
                    $nodo['conexiones_activas'] = 0;
                    $nodo['ultimo_evento'] = 'Fuera de servicio (Failover activado)';
                } else {
                    $nodo['estado'] = 'ONLINE';
                    $nodo['ultimo_evento'] = 'Reincorporado tras Health-Check OK';
                }
                $nodo['ultima_verificacion'] = now()->format('H:i:s');
                break;
            }
        }
        unset($nodo);

        if (!$encontrado) {
            return null;
        }

        Cache::put(self::CACHE_KEY, $estado, now()->addHours(12));

        return $this->enriquecerEstado($estado, $request);
    }

    /**
     * Despacha N peticiones a través del algoritmo de balanceo seleccionado.
     */
    public function despacharPeticiones(int $cantidad = 1, string $ruta = '/api/v1/cursos', ?Request $request = null): array
    {
        $cantidad = max(1, min(20, $cantidad));
        $estado = Cache::get(self::CACHE_KEY) ?: $this->estadoInicial();
        $despachadas = [];

        for ($i = 0; $i < $cantidad; $i++) {
            $nodosActivos = array_values(array_filter(
                $estado['nodos'],
                fn (array $n) => $n['estado'] === 'ONLINE'
            ));

            if (empty($nodosActivos)) {
                return [
                    'exito' => false,
                    'codigo_http' => 503,
                    'mensaje' => '503 Service Unavailable: No hay nodos saludables disponibles en el clúster.',
                    'despachadas' => $despachadas,
                    'estado' => $this->enriquecerEstado($estado, $request),
                ];
            }

            $seleccionadoId = $this->seleccionarNodo($estado, $nodosActivos);

            // Simular variación realista de latencia y actualización de conexiones activas
            foreach ($estado['nodos'] as &$nodo) {
                if ($nodo['estado'] !== 'ONLINE') {
                    $nodo['conexiones_activas'] = 0;
                    continue;
                }

                if ($nodo['id'] === $seleccionadoId) {
                    $latencia = max(6, $nodo['latencia_base_ms'] + random_int(-4, 9));
                    $nodo['peticiones']++;
                    $nodo['conexiones_activas'] = min(50, $nodo['conexiones_activas'] + 1);
                    $nodo['latencia_ms'] = $latencia;
                    $nodo['ultima_verificacion'] = now()->format('H:i:s');
                    $nodo['ultimo_evento'] = "Atendió {$ruta} ({$latencia} ms)";

                    $estado['total_peticiones']++;
                    $registro = [
                        'id' => 'REQ-' . str_pad((string) $estado['total_peticiones'], 4, '0', STR_PAD_LEFT),
                        'hora' => now()->format('H:i:s'),
                        'ruta' => $ruta,
                        'algoritmo' => self::ALGORITMOS[$estado['algoritmo']]['nombre'] ?? $estado['algoritmo'],
                        'nodo_id' => $nodo['id'],
                        'nodo_host' => $nodo['host'] . ':' . $nodo['puerto'],
                        'latencia_ms' => $latencia,
                        'http_status' => 200,
                    ];

                    array_unshift($estado['historial'], $registro);
                    $estado['historial'] = array_slice($estado['historial'], 0, 12);
                    $despachadas[] = $registro;
                } else {
                    // Liberación gradual de conexiones en otros nodos activos
                    if ($nodo['conexiones_activas'] > 0 && random_int(0, 1) === 1) {
                        $nodo['conexiones_activas']--;
                    }
                }
            }
            unset($nodo);
        }

        Cache::put(self::CACHE_KEY, $estado, now()->addHours(12));

        return [
            'exito' => true,
            'codigo_http' => 200,
            'mensaje' => $cantidad === 1
                ? "Petición enrutada correctamente hacia {$despachadas[0]['nodo_id']}."
                : "Ráfaga de {$cantidad} peticiones balanceadas correctamente en el clúster.",
            'despachadas' => $despachadas,
            'estado' => $this->enriquecerEstado($estado, $request),
        ];
    }

    /**
     * Reinicia métricas y restaura todos los nodos del clúster a estado ONLINE.
     */
    public function reiniciar(?Request $request = null): array
    {
        $estado = $this->estadoInicial();
        Cache::put(self::CACHE_KEY, $estado, now()->addHours(12));

        return $this->enriquecerEstado($estado, $request);
    }

    /**
     * Selecciona el siguiente nodo según el algoritmo configurado.
     */
    private function seleccionarNodo(array &$estado, array $nodosActivos): string
    {
        $algoritmo = $estado['algoritmo'] ?? 'round_robin';

        if ($algoritmo === 'weighted_round_robin') {
            $secuencia = [];
            foreach ($nodosActivos as $nodo) {
                $peso = max(1, (int) ($nodo['peso'] ?? 1));
                for ($w = 0; $w < $peso; $w++) {
                    $secuencia[] = $nodo['id'];
                }
            }
            $indice = $estado['wrr_index'] % count($secuencia);
            $estado['wrr_index'] = $indice + 1;

            return $secuencia[$indice];
        }

        if ($algoritmo === 'least_connections') {
            usort($nodosActivos, function (array $a, array $b) {
                if ($a['conexiones_activas'] !== $b['conexiones_activas']) {
                    return $a['conexiones_activas'] <=> $b['conexiones_activas'];
                }
                if ($a['peso'] !== $b['peso']) {
                    return $b['peso'] <=> $a['peso'];
                }

                return $a['latencia_ms'] <=> $b['latencia_ms'];
            });

            return $nodosActivos[0]['id'];
        }

        // Default: Round Robin
        $indice = $estado['rr_index'] % count($nodosActivos);
        $estado['rr_index'] = $indice + 1;

        return $nodosActivos[$indice]['id'];
    }

    /**
     * Estado inicial del clúster de 3 nodos backend para Curso+.
     */
    private function estadoInicial(): array
    {
        $hora = now()->format('H:i:s');

        return [
            'algoritmo' => 'round_robin',
            'rr_index' => 0,
            'wrr_index' => 0,
            'total_peticiones' => 0,
            'nodos' => [
                [
                    'id' => 'node-app-1',
                    'nombre' => 'Nodo Primario (Apache/PHP)',
                    'host' => '127.0.0.1',
                    'puerto' => 8000,
                    'rol' => 'Primario',
                    'peso' => 3,
                    'estado' => 'ONLINE',
                    'peticiones' => 0,
                    'conexiones_activas' => 2,
                    'latencia_base_ms' => 14,
                    'latencia_ms' => 14,
                    'ultima_verificacion' => $hora,
                    'ultimo_evento' => 'Health-Check /api/v1/health OK',
                ],
                [
                    'id' => 'node-app-2',
                    'nombre' => 'Nodo Secundario (Worker HTTP)',
                    'host' => '127.0.0.1',
                    'puerto' => 8001,
                    'rol' => 'Secundario',
                    'peso' => 2,
                    'estado' => 'ONLINE',
                    'peticiones' => 0,
                    'conexiones_activas' => 1,
                    'latencia_base_ms' => 19,
                    'latencia_ms' => 19,
                    'ultima_verificacion' => $hora,
                    'ultimo_evento' => 'Health-Check /api/v1/health OK',
                ],
                [
                    'id' => 'node-app-3',
                    'nombre' => 'Nodo Respaldo (Réplica API)',
                    'host' => '127.0.0.1',
                    'puerto' => 8002,
                    'rol' => 'Respaldo',
                    'peso' => 1,
                    'estado' => 'ONLINE',
                    'peticiones' => 0,
                    'conexiones_activas' => 0,
                    'latencia_base_ms' => 24,
                    'latencia_ms' => 24,
                    'ultima_verificacion' => $hora,
                    'ultimo_evento' => 'Health-Check /api/v1/health OK',
                ],
            ],
            'historial' => [],
        ];
    }

    /**
     * Agrega métricas derivadas, porcentajes y configuración real de Laravel proxy/sesiones.
     */
    private function enriquecerEstado(array $estado, ?Request $request = null): array
    {
        $totalPeticiones = (int) ($estado['total_peticiones'] ?? 0);
        $activos = 0;
        $sumaLatencia = 0;

        foreach ($estado['nodos'] as &$nodo) {
            $nodo['porcentaje'] = $totalPeticiones > 0
                ? round(($nodo['peticiones'] / $totalPeticiones) * 100, 1)
                : 0;

            if ($nodo['estado'] === 'ONLINE') {
                $activos++;
                $sumaLatencia += $nodo['latencia_ms'];
            }
        }
        unset($nodo);

        $latenciaPromedio = $activos > 0 ? round($sumaLatencia / $activos, 1) : 0;
        $totalNodos = count($estado['nodos']);

        $estado['resumen'] = [
            'nodos_online' => $activos,
            'nodos_totales' => $totalNodos,
            'estado_cluster' => $activos === $totalNodos
                ? 'ÓPTIMO'
                : ($activos > 0 ? 'DEGRADADO (FAILOVER ACTIVO)' : 'CRÍTICO (SIN NODOS)'),
            'latencia_promedio_ms' => $latenciaPromedio,
            'algoritmo_nombre' => self::ALGORITMOS[$estado['algoritmo']]['nombre'] ?? $estado['algoritmo'],
            'algoritmo_descripcion' => self::ALGORITMOS[$estado['algoritmo']]['descripcion'] ?? '',
            'hora_servidor' => now()->format('d/m/Y H:i:s'),
        ];

        $estado['algoritmos_disponibles'] = self::ALGORITMOS;

        $estado['configuracion_laravel'] = [
            'trust_proxies' => 'App\Http\Middleware\TrustProxies (X-Forwarded-For, Host, Port, Proto)',
            'client_ip' => $request ? $request->ip() : '127.0.0.1',
            'forwarded_for' => $request ? ($request->header('X-Forwarded-For') ?: 'Conexión directa (sin proxy intermedio)') : 'N/D',
            'session_driver' => config('session.driver', 'file'),
            'cache_driver' => config('cache.default', 'file'),
            'health_endpoint' => '/api/v1/health',
            'rate_limit' => 'throttle:api (60 req/min por IP/usuario)',
            'recomendacion_produccion' => 'En clúster multi-servidor se recomienda cambiar SESSION_DRIVER y CACHE_DRIVER a redis o database para sesiones compartidas (stateless).',
        ];

        return $estado;
    }
}
