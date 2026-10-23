<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PerfilController extends Controller
{
    public function vistaperfil(){
        return view('tarjetas.perfil');
    }
}
