<?php

namespace App\Http\Controllers;

use App\Models\Cursoreg;
use App\Models\Tarjetas;
use Illuminate\Http\Request;

class CursoregController extends Controller
{
    public function registrarsubs(Request $request)
    {
        try {
            $request->validate([
                'id_curso' => 'required|exists:cursos,id',
            ]);

            $cursoId = $request->input('id_curso');
            $userId = auth()->id();
            $tarjetaId = $request->input('id_tarjeta');

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debes iniciar sesión para inscribirte.'
                ], 401);
            }

            if ($tarjetaId) {
                $tarjeta = Tarjetas::where('id', $tarjetaId)
                    ->where('usuario_id', $userId)
                    ->first();

                if (!$tarjeta) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Debes seleccionar una tarjeta registrada para suscribirte.'
                    ], 422);
                }
            }

            $exists = Cursoreg::where('id_curso', $cursoId)
                ->where('id_user', $userId)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya estás inscrito en este curso.'
                ], 409);
            }

            Cursoreg::create([
                'id_curso' => $cursoId,
                'id_user' => $userId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registro exitoso en el curso'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo completar la inscripción. Detalle: ' . $e->getMessage()
            ], 500);
        }
    }

    public function desuscribirse(Request $request)
    {
        try {
            $request->validate([
                'id_curso' => 'required|exists:cursos,id',
            ]);

            $cursoId = $request->input('id_curso');
            $userId = auth()->id();

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debes iniciar sesión para desinscribirte.'
                ], 401);
            }

            $deleted = Cursoreg::where('id_curso', $cursoId)
                ->where('id_user', $userId)
                ->delete();

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'No encontré una inscripción activa para este curso.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Te has desinscrito del curso. No se devolverá el dinero.'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo completar la desinscripción. Detalle: ' . $e->getMessage()
            ], 500);
        }
    }

    public function estado(Request $request)
    {
        $cursoId = $request->query('id_curso');
        $userId = auth()->id();

        $suscrito = Cursoreg::where('id_curso', $cursoId)
            ->where('id_user', $userId)
            ->exists();

        return response()->json([
            'suscrito' => $suscrito,
        ]);
    }
}
