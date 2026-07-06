<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    /**
     * Obtiene los tipos de cambio actuales respecto al Dólar (USD).
     * Retorna un arreglo con las tasas de BOB y EUR.
     * Si la API falla, usa valores por defecto.
     */
    public function getExchangeRates(): array
    {
        // Caché por 1 hora (3600 segundos) para no saturar la API
        return Cache::remember('exchange_rates_usd', 3600, function () {
            try {
                // Consumo de API REST Externa
                $response = Http::timeout(5)->get('https://open.er-api.com/v6/latest/USD');

                if ($response->successful()) {
                    $data = $response->json();
                    
                    return [
                        'BOB' => $data['rates']['BOB'] ?? 6.96,
                        'EUR' => $data['rates']['EUR'] ?? 0.92,
                        'source' => 'API Externa (En Vivo)'
                    ];
                }
            } catch (\Exception $e) {
                // Log del error para auditoría
                Log::error('Error al consumir API de Divisas: ' . $e->getMessage());
            }

            // Fallback (Valores por defecto de rescate si no hay internet)
            return [
                'BOB' => 6.96,
                'EUR' => 0.92,
                'source' => 'Respaldo Local (Sin conexión)'
            ];
        });
    }

    /**
     * Convierte un monto en Bolivianos (BOB) a Dólares (USD) y Euros (EUR).
     */
    public function convertFromBob(float $amountInBob): array
    {
        $rates = $this->getExchangeRates();
        
        // 1 USD = X BOB -> USD = BOB / X
        $usdAmount = $amountInBob / $rates['BOB'];
        
        // USD a EUR -> EUR = USD * Tasa_EUR
        $eurAmount = $usdAmount * $rates['EUR'];

        return [
            'BOB' => round($amountInBob, 2),
            'USD' => round($usdAmount, 2),
            'EUR' => round($eurAmount, 2),
            'rate_usd_bob' => round($rates['BOB'], 2),
            'source' => $rates['source']
        ];
    }
}
