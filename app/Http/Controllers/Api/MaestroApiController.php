<?php

namespace App\Http\Controllers\Api;

use App\Models\Curso;
use App\Models\Maestro;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Web Service Propio: Endpoint RESTful para la consulta de Profesores y su carga académica.
 */
class MaestroApiController extends ApiController
{
    /**
     * Lista de profesores registrados y cantidad de cursos asignados.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $maestros = Maestro::orderBy('nombre')->get();

        $datos = $maestros->map(function ($m) {
            $nombreCompleto = trim("{$m->nombre} {$m->apellido_paterno} {$m->apellido_materno}");
            $totalCursos = Curso::where('maestro', 'LIKE', "%{$m->nombre}%")->count();

            return [
                'id' => $m->id,
                'nombre_completo' => $nombreCompleto,
                'nombre' => $m->nombre,
                'apellido_paterno' => $m->apellido_paterno,
                'apellido_materno' => $m->apellido_materno,
                'correo' => $m->correo,
                'materia_principal' => $m->materia,
                'turno' => $m->turno,
                'total_cursos_asignados' => $totalCursos,
            ];
        });

        return $this->sendResponse($datos, 'Lista de profesores obtenida exitosamente', 200, [
            'total' => $datos->count(),
        ]);
    }

    /**
     * Consulta del perfil de un maestro y sus cursos asignados.
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $m = Maestro::find($id);

        if (!$m) {
            return $this->sendError('El profesor solicitado no existe.', [], 404);
        }

        $nombreCompleto = trim("{$m->nombre} {$m->apellido_paterno} {$m->apellido_materno}");
        $cursosAsignados = Curso::where('maestro', 'LIKE', "%{$m->nombre}%")
            ->get(['id', 'nombre', 'costo', 'horas', 'cant_alumnos']);

        return $this->sendResponse([
            'id' => $m->id,
            'nombre_completo' => $nombreCompleto,
            'correo' => $m->correo,
            'materia' => $m->materia,
            'turno' => $m->turno,
            'cursos_impartidos' => $cursosAsignados,
        ], 'Perfil del profesor obtenido con éxito');
    }
}
