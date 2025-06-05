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
                $response = Http::get('https://pydolarve.org/api/v1/dollar?page=bcv');
                $data = $response->json();
                return $data['monitors']['usd']['price'] ?? null;
            } catch (\Exception $e) {
                \Log::error('Error fetching exchange rate: ' . $e->getMessage());
                return null;
            }
        });
    }
}
