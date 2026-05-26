<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ExchangeRateService
{
    public function getCurrentRate()
    {
        // Cache the exchange rate for 1 hour to avoid too many API calls
        return Cache::remember('current_exchange_rate', 3600, function () {
            try {
                $response = Http::get('https://ve.dolarapi.com/v1/dolares');
                $data = $response->json();
                // Find the 'oficial' rate from the array
                $oficialRate = collect($data)->firstWhere('fuente', 'oficial');
                return $oficialRate['promedio'] ?? null;
            } catch (\Exception $e) {
                \Log::error('Error fetching exchange rate: ' . $e->getMessage());
                return null;
            }
        });
    }
}
