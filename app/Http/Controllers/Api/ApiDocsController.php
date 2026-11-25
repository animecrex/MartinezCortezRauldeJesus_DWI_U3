<?php

namespace App\Http\Controllers\Api;

use App\Services\ExchangeRateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Catálogo, Health-check y documentación interactiva de los Servicios Web.
 */
class ApiDocsController extends ApiController
{
    /**
     * Muestra el catálogo y especificación de endpoints en formato JSON.
     *
     * @return JsonResponse
     */
    public function info(): JsonResponse
    {
        $spec = [
            'nombre' => 'Curso+ Web Services API',
            'version' => '1.0.0',
            'descripcion' => 'API RESTful propia y consumo de Web Services de terceros para el sistema Curso+ (DWI U3).',
            'autor' => 'Raúl de Jesús Martínez Cortez',
            'base_url' => url('/api/v1'),
            'servicios_web_propios' => [
                [
                    'metodo' => 'GET',
                    'endpoint' => '/api/v1/cursos',
                    'descripcion' => 'Obtiene el catálogo de cursos con búsqueda, filtros y precios convertidos.',
                    'parametros_query' => ['buscar', 'precio_max', 'orden_por', 'direccion', 'limite'],
                ],
                [
                    'metodo' => 'GET',
                    'endpoint' => '/api/v1/cursos/{id}',
                    'descripcion' => 'Obtiene la información detallada de un curso y tarifas internacionales.',
                ],
                [
                    'metodo' => 'GET',
                    'endpoint' => '/api/v1/cursos/{id}/disponibilidad',
                    'descripcion' => 'Consulta ligera en tiempo real de cupos y lugares disponibles.',
                ],
                [
                    'metodo' => 'GET',
                    'endpoint' => '/api/v1/maestros',
                    'descripcion' => 'Lista de profesores registrados y cantidad de cursos asignados.',
                ],
                [
                    'metodo' => 'GET',
                    'endpoint' => '/api/v1/maestros/{id}',
                    'descripcion' => 'Detalle del profesor y materias/cursos que imparte.',
                ],
                [
                    'metodo' => 'GET',
                    'endpoint' => '/api/v1/health',
                    'descripcion' => 'Verificación del estado operativo de la API y dependencias.',
                ],
            ],
            'servicio_web_de_terceros' => [
                'proveedor' => 'Open Exchange Rates (ExchangeRate-API)',
                'tipo' => 'REST API externa sobre HTTPS con JSON',
                'endpoints_disponibles' => [
                    [
                        'metodo' => 'GET',
                        'endpoint' => '/api/v1/tipo-cambio',
                        'descripcion' => 'Consulta las tasas de cambio de MXN frente a USD, EUR, GBP, CAD y JPY en tiempo real.',
                    ],
                    [
                        'metodo' => 'GET',
                        'endpoint' => '/api/v1/tipo-cambio/convertir',
                        'descripcion' => 'Realiza conversiones automáticas de Pesos Mexicanos a cualquier divisa soportada.',
                        'parametros_query' => ['monto (requerido)', 'moneda (ej: USD, EUR)'],
                    ],
                ],
            ],
        ];

        return $this->sendResponse($spec, 'Especificación de Servicios Web Curso+');
    }

    /**
     * Endpoint de salud del sistema (Health Check).
     *
     * @param ExchangeRateService $exchangeService
     * @return JsonResponse
     */
    public function health(ExchangeRateService $exchangeService): JsonResponse
    {
        $dbStatus = 'OK';
        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $dbStatus = 'Error de conexión: ' . $e->getMessage();
        }

        $rates = $exchangeService->obtenerTasas();

        $health = [
            'api_status' => 'ONLINE',
            'base_datos' => $dbStatus,
            'web_service_terceros' => [
                'proveedor' => $rates['origen'],
                'en_linea' => $rates['en_linea'],
                'ultima_actualizacion' => $rates['fecha_actualizacion'],
            ],
            'entorno' => app()->environment(),
            'version_laravel' => app()->version(),
            'version_php' => PHP_VERSION,
        ];

        return $this->sendResponse($health, 'Chequeo de salud completado');
    }
}
