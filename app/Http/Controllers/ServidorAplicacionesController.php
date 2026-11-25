<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Cursoreg;
use App\Models\Maestro;
use App\Models\User;
use App\Services\ExchangeRateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ServidorAplicacionesController extends Controller
{
    public function __construct(private ExchangeRateService $exchangeRateService)
    {
    }

    /**
     * Muestra el panel de monitoreo y diagnóstico del Servidor de Aplicaciones.
     */
    public function index(Request $request): View
    {
        $diagnostico = $this->recopilarDiagnostico($request);

        return view('servidor.index', compact('diagnostico'));
    }

    /**
     * Devuelve el diagnóstico actualizado del servidor en formato JSON.
     */
    public function estadoJson(Request $request): JsonResponse
    {
        return response()->json([
            'exito' => true,
            'mensaje' => 'Diagnóstico del servidor de aplicaciones actualizado.',
            'datos' => $this->recopilarDiagnostico($request),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Limpia la caché de aplicación y de tasas de cambio del servidor.
     */
    public function limpiarCache(): JsonResponse
    {
        Cache::flush();

        return response()->json([
            'exito' => true,
            'mensaje' => 'Caché del servidor de aplicaciones y del servicio de divisas limpiada correctamente.',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Recopila métricas reales del servidor HTTP, PHP, Laravel, BD, seguridad y logs.
     */
    private function recopilarDiagnostico(Request $request): array
    {
        // 1. Prueba de base de datos y latencia
        $dbEstado = 'ONLINE';
        $dbVersion = 'No disponible';
        $dbLatenciaMs = 0;
        $conteos = [
            'usuarios' => 0,
            'cursos' => 0,
            'maestros' => 0,
            'suscripciones' => 0,
        ];

        try {
            $inicio = microtime(true);
            DB::connection()->getPdo();
            $versionRow = DB::selectOne('SELECT VERSION() as version');
            $dbLatenciaMs = round((microtime(true) - $inicio) * 1000, 2);
            $dbVersion = $versionRow->version ?? 'MySQL / MariaDB';

            $conteos = [
                'usuarios' => User::count(),
                'cursos' => Curso::count(),
                'maestros' => Maestro::count(),
                'suscripciones' => Cursoreg::count(),
            ];
        } catch (\Throwable $e) {
            $dbEstado = 'ERROR';
            $dbVersion = 'Sin conexión activa';
        }

        // 2. Estado del Web Service externo de tasas de cambio
        $tasas = $this->exchangeRateService->obtenerTasas();

        // 3. Extensiones PHP críticas para el servidor de aplicaciones
        $extensionesRequeridas = ['pdo_mysql', 'openssl', 'mbstring', 'curl', 'gd', 'fileinfo', 'json', 'tokenizer'];
        $extensiones = [];
        foreach ($extensionesRequeridas as $ext) {
            $extensiones[$ext] = extension_loaded($ext);
        }

        // 4. Estado de archivos de bitácora y almacenamiento
        $logs = [];
        $archivosLog = [
            'laravel' => storage_path('logs/laravel.log'),
            'auth' => storage_path('logs/auth.log'),
            'security' => storage_path('logs/security.log'),
        ];

        foreach ($archivosLog as $canal => $ruta) {
            // También soporta archivos rotativos diarios (ej. auth-YYYY-MM-DD.log)
            $rutaActiva = $ruta;
            if (!file_exists($rutaActiva)) {
                $patron = glob(storage_path("logs/{$canal}-*.log"));
                if (!empty($patron)) {
                    rsort($patron);
                    $rutaActiva = $patron[0];
                }
            }

            $existe = file_exists($rutaActiva);
            $logs[$canal] = [
                'archivo' => basename($rutaActiva),
                'activo' => $existe,
                'tamano_kb' => $existe ? round(filesize($rutaActiva) / 1024, 2) : 0,
                'ultima_modificacion' => $existe ? date('d/m/Y H:i:s', filemtime($rutaActiva)) : 'Sin registros aún',
            ];
        }

        $publicStoragePath = public_path('storage');
        $storageEnlazado = is_link($publicStoragePath) || is_dir($publicStoragePath);

        $htaccessRaiz = file_exists(base_path('.htaccess'));
        $htaccessPublic = file_exists(public_path('.htaccess'));

        return [
            'servidor' => [
                'software' => $request->server('SERVER_SOFTWARE', 'Apache / PHP Built-in Server'),
                'protocolo' => $request->server('SERVER_PROTOCOL', 'HTTP/1.1'),
                'host' => $request->getHost(),
                'puerto' => $request->getPort(),
                'esquema' => strtoupper($request->getScheme()),
                'sistema_operativo' => PHP_OS_FAMILY . ' (' . php_uname('s') . ' ' . php_uname('r') . ')',
                'hora_servidor' => now()->format('d/m/Y H:i:s'),
                'zona_horaria' => config('app.timezone', 'UTC'),
            ],
            'runtime' => [
                'php_version' => PHP_VERSION,
                'php_sapi' => PHP_SAPI,
                'laravel_version' => app()->version(),
                'entorno' => app()->environment(),
                'memoria_usada_mb' => round(memory_get_usage(true) / 1048576, 2),
                'memoria_limite' => ini_get('memory_limit'),
                'upload_max' => ini_get('upload_max_filesize'),
                'post_max' => ini_get('post_max_size'),
                'extensiones' => $extensiones,
            ],
            'base_datos' => [
                'estado' => $dbEstado,
                'driver' => strtoupper(config('database.default', 'mysql')),
                'host' => config('database.connections.mysql.host', '127.0.0.1'),
                'puerto' => config('database.connections.mysql.port', '3306'),
                'nombre' => config('database.connections.mysql.database', 'proyectoalan'),
                'version' => $dbVersion,
                'latencia_ms' => $dbLatenciaMs,
                'conteos' => $conteos,
            ],
            'seguridad_y_almacenamiento' => [
                'session_driver' => config('session.driver', 'file'),
                'session_lifetime' => config('session.lifetime', 120),
                'session_http_only' => (bool) config('session.http_only', true),
                'session_same_site' => config('session.same_site', 'lax'),
                'cache_driver' => config('cache.default', 'file'),
                'queue_driver' => config('queue.default', 'sync'),
                'storage_enlazado' => $storageEnlazado,
                'htaccess_raiz' => $htaccessRaiz,
                'htaccess_public' => $htaccessPublic,
                'middlewares_activos' => [
                    'SecurityHeaders (CSP / X-Frame / Nosniff)',
                    'RequestSecurityLogger (Detección SQLi/XSS)',
                    'VerifyCsrfToken (Protección CSRF)',
                    'ThrottleRequests (throttle:api)',
                    'Authenticate & EnsureEmailIsVerified',
                ],
                'logs' => $logs,
            ],
            'web_services' => [
                'externo' => [
                    'proveedor' => $tasas['origen'] ?? 'Open Exchange Rates API',
                    'en_linea' => (bool) ($tasas['en_linea'] ?? false),
                    'ultima_actualizacion' => $tasas['fecha_actualizacion'] ?? 'N/A',
                    'tasa_usd' => $tasas['tasas']['USD'] ?? null,
                    'tasa_eur' => $tasas['tasas']['EUR'] ?? null,
                ],
                'endpoints_propios' => [
                    ['metodo' => 'GET', 'ruta' => '/api/v1', 'descripcion' => 'Catálogo general de la API'],
                    ['metodo' => 'GET', 'ruta' => '/api/v1/health', 'descripcion' => 'Health-Check del servidor y dependencias'],
                    ['metodo' => 'GET', 'ruta' => '/api/v1/cursos', 'descripcion' => 'Listado y filtrado de cursos'],
                    ['metodo' => 'GET', 'ruta' => '/api/v1/maestros', 'descripcion' => 'Directorio de maestros'],
                    ['metodo' => 'GET', 'ruta' => '/api/v1/tipo-cambio', 'descripcion' => 'Tasas de cambio en tiempo real (MXN)'],
                ],
            ],
        ];
    }
}
