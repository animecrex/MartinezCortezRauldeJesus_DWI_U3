<?php

namespace App\Http\Controllers\Api;

use App\Models\Curso;
use App\Models\Cursoreg;
use App\Services\ExchangeRateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Web Service Propio: Endpoint RESTful para la gestión y consulta de Cursos.
 */
class CursoApiController extends ApiController
{
    protected ExchangeRateService $exchangeService;

    public function __construct(ExchangeRateService $exchangeService)
    {
        $this->exchangeService = $exchangeService;
    }

    /**
     * Catálogo público de cursos disponibles con filtros opcionales.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Curso::query();

        // Filtro por término de búsqueda (nombre, descripción, maestro)
        if ($request->filled('buscar')) {
            $buscar = trim($request->input('buscar'));
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'LIKE', "%{$buscar}%")
                  ->orWhere('descripcion', 'LIKE', "%{$buscar}%")
                  ->orWhere('maestro', 'LIKE', "%{$buscar}%");
            });
        }

        // Filtro por costo máximo
        if ($request->filled('precio_max') && is_numeric($request->input('precio_max'))) {
            $query->where('costo', '<=', (float) $request->input('precio_max'));
        }

        // Ordenamiento seguro
        $ordenColumna = in_array($request->input('orden_por'), ['id', 'nombre', 'costo', 'horas']) 
            ? $request->input('orden_por') 
            : 'id';
        $ordenDireccion = strtolower($request->input('direccion', 'desc')) === 'asc' ? 'asc' : 'desc';
        $query->orderBy($ordenColumna, $ordenDireccion);

        $limite = min(max((int) $request->input('limite', 15), 1), 100);
        $cursos = $query->take($limite)->get();

        // Transformación y enriquecimiento de datos con Web Service de divisas
        $resultado = $cursos->map(function ($curso) {
            $inscritosCount = Cursoreg::where('id_curso', $curso->id)->count();
            $cantMax = max((int) $curso->cant_alumnos, 1);
            $cuposDisponibles = max($cantMax - $inscritosCount, 0);

            // Conversión internacional calculada a través del Web Service de Terceros
            $conversionUsd = $this->exchangeService->convertir((float) $curso->costo, 'USD');

            return [
                'id' => $curso->id,
                'nombre' => $curso->nombre,
                'descripcion' => $curso->descripcion,
                'maestro' => $curso->maestro ?: 'Sin instructor asignado',
                'horas' => (int) $curso->horas,
                'precios' => [
                    'mxn' => (float) $curso->costo,
                    'usd' => $conversionUsd['monto_convertido'] ?? null,
                    'tipo_cambio_aplicado' => $conversionUsd['tasa_aplicada'] ?? null,
                ],
                'cupo' => [
                    'capacidad_maxima' => $cantMax,
                    'total_inscritos' => $inscritosCount,
                    'cupos_disponibles' => $cuposDisponibles,
                    'porcentaje_ocupacion' => round(($inscritosCount / $cantMax) * 100, 1),
                    'estado' => $cuposDisponibles === 0 ? 'Agotado' : ($cuposDisponibles <= 3 ? 'Últimos lugares' : 'Disponible'),
                ],
                'fechas' => [
                    'inicio' => $curso->fecha_inicio,
                    'fin' => $curso->fecha_fin,
                ],
                'imagen_url' => $curso->imagen ? asset('storage/' . $curso->imagen) : asset('assets/media/misc/curso.jpg'),
            ];
        });

        return $this->sendResponse($resultado, 'Catálogo de cursos obtenido exitosamente', 200, [
            'total_registros' => $resultado->count(),
            'proveedor_divisas' => 'Open Exchange Rates (Terceros)',
        ]);
    }

    /**
     * Consulta detallada de un curso específico por ID.
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $curso = Curso::find($id);

        if (!$curso) {
            return $this->sendError('El curso solicitado no fue encontrado en la plataforma.', [], 404);
        }

        $inscritosCount = Cursoreg::where('id_curso', $curso->id)->count();
        $cantMax = max((int) $curso->cant_alumnos, 1);
        $cuposDisponibles = max($cantMax - $inscritosCount, 0);

        // Conversiones múltiples usando el servicio externo
        $convUsd = $this->exchangeService->convertir((float) $curso->costo, 'USD');
        $convEur = $this->exchangeService->convertir((float) $curso->costo, 'EUR');

        $detalle = [
            'id' => $curso->id,
            'nombre' => $curso->nombre,
            'descripcion' => $curso->descripcion,
            'objetivos' => $curso->objetivos ?: 'Desarrollo de competencias y habilidades prácticas.',
            'requisitos' => $curso->requisitos ?: 'Ganas de aprender y conocimientos básicos.',
            'instructor' => $curso->maestro ?: 'Profesor Asignado',
            'horas_totales' => (int) $curso->horas,
            'tarifas' => [
                'mxn' => (float) $curso->costo,
                'usd' => $convUsd['monto_convertido'] ?? null,
                'eur' => $convEur['monto_convertido'] ?? null,
                'fuente_tasa' => $convUsd['proveedor'] ?? 'Open Exchange API',
            ],
            'disponibilidad' => [
                'cupo_maximo' => $cantMax,
                'alumnos_inscritos' => $inscritosCount,
                'lugares_restantes' => $cuposDisponibles,
                'esta_lleno' => $cuposDisponibles === 0,
            ],
            'periodo' => [
                'fecha_inicio' => $curso->fecha_inicio,
                'fecha_fin' => $curso->fecha_fin,
            ],
            'imagen_url' => $curso->imagen ? asset('storage/' . $curso->imagen) : asset('assets/media/misc/curso.jpg'),
        ];

        return $this->sendResponse($detalle, 'Detalle del curso obtenido correctamente');
    }

    /**
     * Consulta ligera y rápida de cupos y disponibilidad para un curso.
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function disponibilidad($id): JsonResponse
    {
        $curso = Curso::find($id);

        if (!$curso) {
            return $this->sendError('Curso no encontrado', [], 404);
        }

        $inscritos = Cursoreg::where('id_curso', $curso->id)->count();
        $maximo = max((int) $curso->cant_alumnos, 1);
        $disponibles = max($maximo - $inscritos, 0);

        return $this->sendResponse([
            'id_curso' => $curso->id,
            'nombre' => $curso->nombre,
            'cupo_maximo' => $maximo,
            'inscritos_actuales' => $inscritos,
            'cupos_disponibles' => $disponibles,
            'porcentaje_ocupacion' => round(($inscritos / $maximo) * 100, 2),
            'admite_inscripciones' => $disponibles > 0,
        ], 'Estado de disponibilidad verificado');
    }
}
