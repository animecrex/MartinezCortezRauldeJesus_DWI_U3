<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Controlador base para la API REST propia.
 * Proporciona un formato estandarizado y uniforme para todas las respuestas JSON.
 */
class ApiController extends Controller
{
    /**
     * Construye una respuesta exitosa estandarizada.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @param array $meta
     * @return JsonResponse
     */
    protected function sendResponse($data, string $message = 'Operación realizada con éxito', int $code = 200, array $meta = []): JsonResponse
    {
        $response = [
            'status' => 'success',
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
        ];

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    /**
     * Construye una respuesta de error estandarizada.
     *
     * @param string $error
     * @param array $errorMessages
     * @param int $code
     * @return JsonResponse
     */
    protected function sendError(string $error, array $errorMessages = [], int $code = 404): JsonResponse
    {
        $response = [
            'status' => 'error',
            'code' => $code,
            'message' => $error,
            'timestamp' => now()->toIso8601String(),
        ];

        if (!empty($errorMessages)) {
            $response['errors'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
