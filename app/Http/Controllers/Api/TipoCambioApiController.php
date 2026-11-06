<?php

namespace App\Http\Controllers\Api;

use App\Services\ExchangeRateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Web Service Propio que encapsula y expone la integración con el Web Service de Terceros.
 */
class TipoCambioApiController extends ApiController
{
    protected ExchangeRateService $exchangeService;

    public function __construct(ExchangeRateService $exchangeService)
    {
        $this->exchangeService = $exchangeService;
    }

    /**
     * Consulta las tasas de cambio actuales desde el Web Service de Terceros.
     *
     * @return JsonResponse
     */
    public function tasas(): JsonResponse
    {
        $datos = $this->exchangeService->obtenerTasas();
        return $this->sendResponse($datos, 'Tipos de cambio obtenidos desde el servicio externo');
    }

    /**
     * Realiza la conversión de un monto en MXN a otra divisa utilizando las tasas en tiempo real.
     *
     * Parámetros: ?monto=1500&moneda=USD
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function convertir(Request $request): JsonResponse
    {
        $request->validate([
            'monto' => ['required', 'numeric', 'min:0.01'],
            'moneda' => ['nullable', 'string', 'size:3'],
        ], [
            'monto.required' => 'Debes proporcionar el monto a convertir.',
            'monto.numeric' => 'El monto debe ser un valor numérico válido.',
            'moneda.size' => 'El código de moneda debe tener 3 letras (ej. USD, EUR, CAD).',
        ]);

        $monto = (float) $request->input('monto');
        $moneda = strtoupper($request->input('moneda', 'USD'));

        $resultado = $this->exchangeService->convertir($monto, $moneda);

        if (!$resultado['exito']) {
            return $this->sendError($resultado['mensaje'], [
                'divisas_soportadas' => $resultado['monedas_disponibles'] ?? [],
            ], 422);
        }

        return $this->sendResponse($resultado, "Conversión de {$monto} MXN a {$moneda} calculada con éxito");
    }
}
