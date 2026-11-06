<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CrearcursoController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\MaestrosController;
use App\Http\Controllers\CursoregController;
use App\Http\Controllers\SuscripcionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/registro', [RegistroController::class, 'index'])->name('registro');
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');

// Rutas protegidas para usuarios autenticados y verificados
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/curso', [CursoController::class, 'indexcurso'])->name('curso');
    Route::get('/curso/traercursos', [CrearcursoController::class, 'traertodoscursos'])->name('curso.todos');
    Route::get('/detallescurso/{id}', [CrearcursoController::class, 'detallescurso'])->name('detallescurso');

    Route::get('/crearcurso', [CrearcursoController::class, 'vista'])->name('crearcurso');
    Route::post('/registrarcurso', [CrearcursoController::class, 'registrar'])->name('registrarcurso');
    Route::get('/crearcurso/traercursos', [CrearcursoController::class, 'traercursos'])->name('crearcurso.traer');
    Route::post('/crearcurso/eliminarcurso/{id}', [CrearcursoController::class, 'eliminar'])->name('eliminarcurso');

    Route::post('/suscribirse', [CursoregController::class, 'registrarsubs'])->name('suscribirse');
    Route::post('/desuscribirse', [CursoregController::class, 'desuscribirse'])->name('desuscribirse');
    Route::get('/suscripcion/estado', [CursoregController::class, 'estado'])->name('suscripcion.estado');
    Route::get('/mis-suscripciones', [SuscripcionController::class, 'index'])->name('mis-suscripciones');

    Route::get('/maestros', [MaestrosController::class, 'indexmaestros'])->name('maestros');
    Route::post('/maestros', [MaestrosController::class, 'guardar'])->name('maestros.guardar');
});

// Rutas de perfil y gestión de tarjetas (autenticados)
Route::middleware('auth')->group(function () {
    Route::get('/vistaperfil', [ProfileController::class, 'vistaperfil'])->name('vistaperfil');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/registrar-tarjeta', [ProfileController::class, 'registrartarjetas'])->name('registrar.tarjeta');
    Route::get('/tarjeta/{tarjeta}', [ProfileController::class, 'editarTarjeta'])->name('tarjeta.editar');
    Route::put('/tarjeta/{tarjeta}', [ProfileController::class, 'actualizarTarjeta'])->name('tarjeta.actualizar');
});

require __DIR__.'/auth.php';
