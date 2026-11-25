<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Cursoreg;
use App\Models\Maestro;
use App\Models\Tarjetas;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ServidorBaseDatosController extends Controller
{
    /**
     * Muestra el panel de diagnóstico y monitoreo del Servidor de Base de Datos.
     */
    public function index(): View
    {
        $bd = $this->recopilarMetricas();

        return view('servidor.base_datos', compact('bd'));
    }

    /**
     * Devuelve las métricas actualizadas del servidor de base de datos en JSON.
     */
    public function estadoJson(): JsonResponse
    {
        return response()->json([
            'exito' => true,
            'mensaje' => 'Métricas del servidor de base de datos actualizadas.',
            'datos' => $this->recopilarMetricas(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Ejecuta una prueba de conectividad y latencia PDO en tiempo real.
     */
    public function probarConexion(): JsonResponse
    {
        try {
            $inicio = microtime(true);
            DB::connection()->getPdo();
            DB::selectOne('SELECT 1 AS ok');
            $latenciaMs = round((microtime(true) - $inicio) * 1000, 2);
            $versionRow = DB::selectOne('SELECT VERSION() AS version');

            return response()->json([
                'exito' => true,
                'mensaje' => 'Conexión PDO verificada exitosamente con el servidor de base de datos.',
                'datos' => [
                    'estado' => 'ONLINE',
                    'latencia_ms' => $latenciaMs,
                    'version' => $versionRow->version ?? 'MariaDB / MySQL',
                    'base_datos' => config('database.connections.mysql.database'),
                ],
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'No se pudo establecer conexión con el servidor de base de datos. Verifica que MySQL/MariaDB esté activo en XAMPP.',
                'datos' => [
                    'estado' => 'ERROR',
                    'latencia_ms' => 0,
                ],
                'timestamp' => now()->toIso8601String(),
            ], 503);
        }
    }

    /**
     * Recopila información real del motor MariaDB/MySQL, tablas, migraciones y entidades.
     */
    private function recopilarMetricas(): array
    {
        $conexion = config('database.default', 'mysql');
        $dbNombre = config("database.connections.{$conexion}.database", 'proyectoalan');
        $dbHost = config("database.connections.{$conexion}.host", '127.0.0.1');
        $dbPuerto = config("database.connections.{$conexion}.port", '3306');
        $dbCharset = config("database.connections.{$conexion}.charset", 'utf8mb4');
        $dbCollation = config("database.connections.{$conexion}.collation", 'utf8mb4_unicode_ci');

        $estado = 'ONLINE';
        $latenciaMs = 0;
        $version = 'No disponible';
        $comentarioVersion = 'MariaDB / MySQL';
        $motorDefecto = 'InnoDB';
        $uptimeFormateado = 'N/D';
        $conexionesActivas = 0;
        $maxConexiones = 0;
        $consultasTotales = 0;
        $tablas = [];
        $tamanoTotalKb = 0;
        $totalFilasTablas = 0;
        $migraciones = [];
        $conteos = [
            'usuarios' => 0,
            'cursos' => 0,
            'maestros' => 0,
            'suscripciones' => 0,
            'tarjetas' => 0,
        ];

        try {
            $inicio = microtime(true);
            DB::connection()->getPdo();
            $versionRow = DB::selectOne('SELECT VERSION() AS version');
            $latenciaMs = round((microtime(true) - $inicio) * 1000, 2);
            $version = $versionRow->version ?? 'MariaDB / MySQL';

            // Variables de configuración del servidor
            $vars = collect(DB::select("SHOW VARIABLES WHERE Variable_name IN ('version_comment', 'default_storage_engine', 'max_connections')"))
                ->pluck('Value', 'Variable_name');

            $comentarioVersion = $vars['version_comment'] ?? $comentarioVersion;
            $motorDefecto = $vars['default_storage_engine'] ?? $motorDefecto;
            $maxConexiones = (int) ($vars['max_connections'] ?? 0);

            // Métricas de estado en vivo
            $status = collect(DB::select("SHOW GLOBAL STATUS WHERE Variable_name IN ('Uptime', 'Threads_connected', 'Questions')"))
                ->pluck('Value', 'Variable_name');

            $uptimeSegundos = (int) ($status['Uptime'] ?? 0);
            $uptimeFormateado = $this->formatearUptime($uptimeSegundos);
            $conexionesActivas = (int) ($status['Threads_connected'] ?? 0);
            $consultasTotales = (int) ($status['Questions'] ?? 0);

            // Inventario real de tablas desde information_schema
            $tablasRaw = DB::select(
                'SELECT TABLE_NAME, ENGINE, TABLE_ROWS, ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024, 2) AS SIZE_KB, TABLE_COLLATION, UPDATE_TIME
                 FROM information_schema.TABLES
                 WHERE TABLE_SCHEMA = ?
                 ORDER BY TABLE_NAME ASC',
                [$dbNombre]
            );

            foreach ($tablasRaw as $t) {
                $sizeKb = (float) ($t->SIZE_KB ?? 0);
                $rows = (int) ($t->TABLE_ROWS ?? 0);
                $tamanoTotalKb += $sizeKb;
                $totalFilasTablas += $rows;

                $tablas[] = [
                    'nombre' => $t->TABLE_NAME,
                    'motor' => $t->ENGINE ?? 'InnoDB',
                    'filas' => $rows,
                    'tamano_kb' => $sizeKb,
                    'collation' => $t->TABLE_COLLATION ?? $dbCollation,
                    'actualizacion' => $t->UPDATE_TIME ? date('d/m/Y H:i', strtotime($t->UPDATE_TIME)) : '—',
                ];
            }

            // Conteos exactos de entidades del dominio Curso+
            $conteos = [
                'usuarios' => User::count(),
                'cursos' => Curso::count(),
                'maestros' => Maestro::count(),
                'suscripciones' => Cursoreg::count(),
                'tarjetas' => Tarjetas::count(),
            ];

            // Últimas migraciones ejecutadas
            try {
                $migraciones = DB::table('migrations')
                    ->orderByDesc('id')
                    ->limit(8)
                    ->get(['migration', 'batch'])
                    ->map(fn ($m) => ['nombre' => $m->migration, 'lote' => $m->batch])
                    ->toArray();
            } catch (\Throwable $e) {
                $migraciones = [];
            }
        } catch (\Throwable $e) {
            $estado = 'ERROR';
            $version = 'Sin conexión activa';
        }

        return [
            'conexion' => [
                'estado' => $estado,
                'driver' => strtoupper($conexion),
                'host' => $dbHost,
                'puerto' => $dbPuerto,
                'nombre' => $dbNombre,
                'version' => $version,
                'distribucion' => $comentarioVersion,
                'motor_defecto' => $motorDefecto,
                'charset' => $dbCharset,
                'collation' => $dbCollation,
                'latencia_ms' => $latenciaMs,
            ],
            'rendimiento' => [
                'uptime' => $uptimeFormateado,
                'conexiones_activas' => $conexionesActivas,
                'max_connections' => $maxConexiones,
                'consultas_totales' => $consultasTotales,
                'total_tablas' => count($tablas),
                'tamano_total_kb' => round($tamanoTotalKb, 2),
                'tamano_total_mb' => round($tamanoTotalKb / 1024, 2),
                'total_filas_estimadas' => $totalFilasTablas,
            ],
            'conteos' => $conteos,
            'tablas' => $tablas,
            'migraciones' => $migraciones,
            'seguridad_bd' => [
                'Consultas parametrizadas mediante Eloquent ORM y PDO (prevención SQL Injection).',
                'Credenciales aisladas en variables de entorno (.env) bloqueadas por Apache (403 Forbidden).',
                'Contraseñas almacenadas con hashing unidireccional Bcrypt (Hash::make).',
                'Ocultamiento estricto del atributo CVV ($hidden = [\'cvv\']) y enmascaramiento PCI-DSS.',
                'Validación de integridad referencial y propiedad de registros (usuario_id / user_id).',
            ],
            'hora_consulta' => now()->format('d/m/Y H:i:s'),
        ];
    }

    private function formatearUptime(int $segundos): string
    {
        if ($segundos <= 0) {
            return 'N/D';
        }

        $dias = intdiv($segundos, 86400);
        $horas = intdiv($segundos % 86400, 3600);
        $minutos = intdiv($segundos % 3600, 60);
        $segs = $segundos % 60;

        if ($dias > 0) {
            return "{$dias}d {$horas}h {$minutos}m";
        }
        if ($horas > 0) {
            return "{$horas}h {$minutos}m {$segs}s";
        }

        return "{$minutos}m {$segs}s";
    }
}
