<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use DateTime;
use App\Models\Beneficiarioventa;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Hamcrest\Type\IsNumeric;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\Models\Maestro;
use App\Models\Tarjetas;

class CrearcursoController extends Controller
{
    public function vista()
    {
        $maestros = Maestro::all();
        return view('crearcurso.crearcursos', compact('maestros'));
    }



    public function registrar(Request $request)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'desc' => ['required', 'string', 'max:100'],
            'max_alumnos' => ['required', 'integer'],
            'maestro' => ['required', 'string', 'max:50'],
            'horas' => ['required', 'integer', 'max:100'],
            'imagen' => ['nullable', 'image'],
            'costo' => ['required', 'numeric', 'min:0'],
        ]);

        $maestro = Maestro::find($request->maestro);

        $nombreMaestro = null;

        if ($maestro) {
            $nombreMaestro = $maestro->nombre . ' ' . $maestro->apellido_paterno . ' ' . $maestro->apellido_materno;
        }

        $rutaDocumento = null;

        if ($request->hasFile('imagen')) {

            $imagen = $request->file('imagen');

            $nombreimagen = time() . '.' . $imagen->getClientOriginalExtension();
            $ruta = storage_path('app/public/images/' . $nombreimagen);

            $img = Image::make($imagen)->resize(300, 300);

            $img->save($ruta);

            $rutaDocumento = 'images/' . $nombreimagen;
        }

        $crearcurso = Curso::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->desc,
            'cant_alumnos' => $request->max_alumnos,
            'maestro' => $nombreMaestro,
            'horas' => $request->horas,
            'imagen' => $rutaDocumento,
            'costo' => $request->costo,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'objetivos' => $request->objetivos,
            'requisitos' => $request->requisitos,
            'user_id' => Auth::id(),
        ]);


        return response()->json([
            'message' => 'Curso registrado correctamente'
        ]);
    }

    public function eliminar($id)
    {
        $curso = Curso::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

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
