<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class SuscripcionController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();

        $cursos = $usuario->cursosSuscritos()
            ->orderBy('cursos.id', 'desc')
            ->get();

        return view('suscripciones.index', compact('cursos'));
    }
}