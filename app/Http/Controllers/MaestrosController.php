<?php

namespace App\Http\Controllers;


use App\Models\Maestro;
use Illuminate\Http\Request;

class MaestrosController extends Controller
{
    public function indexmaestros()
    {
        $maestros = Maestro::orderBy('id', 'desc')->get();

        return view('maestros.maestro', compact('maestros'));
    }

    public function guardar(Request $request)
    {
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/'
            ],
            'apellido_paterno' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/'
            ],
            'apellido_materno' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/'
            ],
            'correo' => [
                'required',
                'email',
                'max:150',
                'unique:maestros,correo'
            ],
            'materia' => [
                'required',
                'string',
                'min:3',
                'max:100'
            ],
            'turno' => [
                'required',
                'in:Matutino,Vespertino'
            ],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'apellido_paterno.regex' => 'El apellido paterno solo puede contener letras y espacios.',
            'apellido_materno.regex' => 'El apellido materno solo puede contener letras y espacios.',
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'El correo no tiene un formato válido.',
            'correo.unique' => 'Este correo ya está registrado.',
            'materia.required' => 'La materia es obligatoria.',
            'turno.required' => 'El turno es obligatorio.',
            'turno.in' => 'El turno seleccionado no es válido.',
        ]);

        Maestro::create($datos);

        return redirect()
            ->route('maestros')
            ->with('success', 'Maestro registrado correctamente.');
    }
}