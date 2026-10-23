<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CursoController extends Controller
{
    public function indexcurso()
    {
        return view('cursos.curso');
    }
}
