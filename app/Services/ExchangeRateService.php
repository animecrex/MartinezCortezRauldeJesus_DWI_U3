<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Servicio Web de Terceros: Integración con Open Exchange Rates API.
 * Proporciona tipos de cambio en tiempo real y conversión monetaria para los cursos.
 */
class ExchangeRateService
{
    /**
     * URL del endpoint del proveedor externo de tipos de cambio (Open Exchange Rate API).
     */
    protected string $apiUrl = 'https://open.er-api.com/v6/latest/MXN';

    /**
     * Monedas de reserva en caso de falla de red o entorno sin conexión a internet.
     */
    protected array $fallbackRates = [
        'USD' => 0.0512,  // ~19.50 MXN por USD
        'EUR' => 0.0475,  // ~21.05 MXN por EUR
        'GBP' => 0.0402,  // ~24.87 MXN por GBP
        'CAD' => 0.0715,  // ~13.98 MXN por CAD
        'JPY' => 7.8200,  // ~0.128 MXN por JPY
    ];

    /**
     * Obtiene las tasas de cambio con caché automática de 1 hora y tolerancia a fallas.
     *
     * @return array
     */
    public function obtenerTasas(): array
    {
        return Cache::remember('open_exchange_rates_mxN', 3600, function () {
            try {
                // Consumo del Servicio Web externo vía cliente HTTP
                $response = Http::timeout(4)
                    ->acceptJson()
                    ->get($this->apiUrl);

                if ($response->successful()) {
                    $json = $response->json();

                    if (($json['result'] ?? '') === 'success' && isset($json['rates'])) {
                        return [
                            'exito' => true,
                            'origen' => 'Open Exchange Rates API (Web Service de Terceros)',
                            'moneda_base' => 'MXN',
                            'tasas' => $json['rates'],
                            'fecha_actualizacion' => $json['time_last_update_utc'] ?? now()->toIso8601String(),
                            'en_linea' => true,
                        ];
                    }
                }

                Log::warning('Respuesta inesperada del Web Service de tipo de cambio, usando tasas de respaldo.');
            } catch (\Throwable $e) {
                Log::notice('Web Service de tipo de cambio no disponible (' . $e->getMessage() . '), usando respaldo fuera de línea.');
            }

            // Respaldo resiliente
            return [
                'exito' => true,
                'origen' => 'Tasas de respaldo locales (Modo sin conexión / Fallback)',
                'moneda_base' => 'MXN',
                'tasas' => $this->fallbackRates,
                'fecha_actualizacion' => now()->toIso8601String(),
                'en_linea' => false,
            ];
        });
    }

    /**
     * Convierte un monto en Pesos Mexicanos (MXN) a otra divisa extranjera.
     *
     * @param float $montoMxn
     * @param string $monedaDestino
     * @return array
     */
    public function convertir(float $montoMxn, string $monedaDestino = 'USD'): array
    {
        $monedaDestino = strtoupper(trim($monedaDestino));
        $datosTasas = $this->obtenerTasas();
        $tasas = $datosTasas['tasas'] ?? $this->fallbackRates;

        $tasa = $tasas[$monedaDestino] ?? ($this->fallbackRates[$monedaDestino] ?? null);

        if (!$tasa) {
            return [
                'exito' => false,
                'mensaje' => "La divisa [{$monedaDestino}] no está soportada.",
                'monedas_disponibles' => array_keys($this->fallbackRates),
            ];
        }

        $montoConvertido = round($montoMxn * $tasa, 2);

        return [
            'exito' => true,
            'monto_original' => round($montoMxn, 2),
            'moneda_origen' => 'MXN',
            'monto_convertido' => $montoConvertido,
            'moneda_destino' => $monedaDestino,
            'tasa_aplicada' => $tasa,
            'proveedor' => $datosTasas['origen'],
            'en_linea' => $datosTasas['en_linea'],
            'fecha_tasa' => $datosTasas['fecha_actualizacion'],
        ];
    }

    /**
     * Devuelve las principales divisas disponibles para conversión.
     *
     * @return array
     */
    public function obtenerDivisasPrincipales(): array
    {
        return [
            'USD' => 'Dólar Estadounidense',
            'EUR' => 'Euro',
            'CAD' => 'Dólar Canadiense',
            'GBP' => 'Libra Esterlina',
            'JPY' => 'Yen Japonés',
        ];
    }
}
