<?php

use App\Http\Controllers\Api\ApiDocsController;
use App\Http\Controllers\Api\CursoApiController;
use App\Http\Controllers\Api\MaestroApiController;
use App\Http\Controllers\Api\TipoCambioApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Web Services)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Documentación y catálogo de la API
    Route::get('/', [ApiDocsController::class, 'info'])->name('api.v1.info');
    Route::get('/health', [ApiDocsController::class, 'health'])->name('api.v1.health');

    // Web Service Propio: Cursos
    Route::get('/cursos', [CursoApiController::class, 'index'])->name('api.v1.cursos.index');
    Route::get('/cursos/{id}', [CursoApiController::class, 'show'])->name('api.v1.cursos.show');
    Route::get('/cursos/{id}/disponibilidad', [CursoApiController::class, 'disponibilidad'])->name('api.v1.cursos.disponibilidad');

    // Web Service Propio: Maestros
    Route::get('/maestros', [MaestroApiController::class, 'index'])->name('api.v1.maestros.index');
    Route::get('/maestros/{id}', [MaestroApiController::class, 'show'])->name('api.v1.maestros.show');

    // Web Service de Terceros: Tipos de cambio y conversión (Open Exchange Rates)
    Route::get('/tipo-cambio', [TipoCambioApiController::class, 'tasas'])->name('api.v1.tipocambio.tasas');
    Route::get('/tipo-cambio/convertir', [TipoCambioApiController::class, 'convertir'])->name('api.v1.tipocambio.convertir');
});
