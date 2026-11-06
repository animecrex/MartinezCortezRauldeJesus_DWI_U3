<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Maestro;
use App\Models\Tarjetas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class CrearcursoController extends Controller
{
    public function vista()
    {
        $maestros = Maestro::orderBy('nombre')->get();
        return view('crearcurso.crearcursos', compact('maestros'));
    }

    public function registrar(Request $request)
    {
        // Validación completa de todos los campos
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:3', 'max:100'],
            'desc' => ['required', 'string', 'min:5', 'max:500'],
            'max_alumnos' => ['required', 'integer', 'min:1', 'max:1000'],
            'maestro' => ['required', 'exists:maestros,id'],
            'horas' => ['required', 'integer', 'min:1', 'max:500'],
            'costo' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'imagen' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'objetivos' => ['nullable', 'string', 'max:1000'],
            'requisitos' => ['nullable', 'string', 'max:1000'],
        ], [
            'nombre.required' => 'El nombre del curso es obligatorio.',
            'desc.required' => 'La descripción es obligatoria.',
            'maestro.exists' => 'El instructor seleccionado no existe.',
            'imagen.image' => 'El archivo debe ser una imagen válida.',
            'imagen.mimes' => 'Formatos de imagen permitidos: jpeg, png, jpg, webp.',
            'imagen.max' => 'La imagen no debe superar los 2MB de tamaño.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la de inicio.',
        ]);

        $maestro = Maestro::find($validated['maestro']);
        $nombreMaestro = $maestro ? ($maestro->nombre . ' ' . $maestro->apellido_paterno . ' ' . $maestro->apellido_materno) : null;

        $rutaDocumento = null;

        // Subida segura de imágenes
        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen');

            // Nombre aleatorio seguro y extensión deducida del contenido MIME
            $extension = $imagen->guessExtension() ?: 'jpg';
            $nombreimagen = Str::random(32) . '.' . $extension;

            $destinoCarpeta = storage_path('app/public/images');
            if (!file_exists($destinoCarpeta)) {
                mkdir($destinoCarpeta, 0755, true);
            }

            $ruta = $destinoCarpeta . '/' . $nombreimagen;

            $img = Image::make($imagen)->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $img->save($ruta);

            $rutaDocumento = 'images/' . $nombreimagen;
        }

        Curso::create([
            'nombre' => strip_tags($validated['nombre']),
            'descripcion' => strip_tags($validated['desc']),
            'cant_alumnos' => (int) $validated['max_alumnos'],
            'maestro' => $nombreMaestro,
            'horas' => (int) $validated['horas'],
            'imagen' => $rutaDocumento,
            'costo' => $validated['costo'],
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_fin' => $validated['fecha_fin'],
            'objetivos' => strip_tags($validated['objetivos'] ?? ''),
            'requisitos' => strip_tags($validated['requisitos'] ?? ''),
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Curso registrado correctamente'
        ]);
    }

    public function eliminar($id)
    {
        // Soporta ID numérico o identificador encriptado
        if (!is_numeric($id)) {
            try {
                $id = Crypt::decryptString($id);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Identificador de curso inválido.'
                ], 400);
            }
        }

        $curso = Curso::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$curso) {
            return response()->json([
                'message' => 'El curso no existe o no tienes autorización para eliminarlo.'
            ], 403);
        }

        $curso->delete();

        return response()->json([
            'message' => 'Curso eliminado correctamente'
        ]);
    }

    public function traercursos()
    {
        $cursos = Curso::where('user_id', Auth::id())->get()->map(function ($curso) {
            $curso->hash = Crypt::encryptString($curso->id);
            return $curso;
        });

        return response()->json($cursos);
    }

    public function traertodoscursos()
    {
        $cursos = Curso::all()->map(function ($curso) {
            $curso->hash = Crypt::encryptString($curso->id);
            return $curso;
        });

        return response()->json($cursos);
    }

    public function detallescurso($id)
    {
        try {
            $id = Crypt::decryptString($id);
        } catch (\Exception $e) {
            abort(404);
        }

        $curso = Curso::findOrFail($id);

        $tarjetas = Tarjetas::where('usuario_id', Auth::id())->get();

        return view('cursos.detalles', compact('curso', 'tarjetas'));
    }
}
