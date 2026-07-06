<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    /**
     * Get exchange rate from base currency to target currency.
     */
    public function getRate(string $base = 'USD', string $target = 'IDR'): float
    {
        $base = strtoupper($base);
        $target = strtoupper($target);
        
        if ($base === $target) {
            return 1.0;
        }

        try {
            // Using er-api.com public free rates (no API key required)
            $response = Http::timeout(5)->get("https://open.er-api.com/v6/latest/{$base}");
            if ($response->successful()) {
                $rates = $response->json()['rates'] ?? [];
                if (isset($rates[$target])) {
                    return (float)$rates[$target];
                }
            }
        } catch (\Exception $e) {
            Log::warning("ExchangeRate API failed for {$base} to {$target}: " . $e->getMessage());
        }

        // Fallback to mock rate
        return $this->getMockRate($base, $target);
    }

    /**
     * Get historical trend points for the last 7 days (mocked for visualization).
     */
    public function getHistoricalTrend(string $base = 'USD', string $target = 'IDR'): array
    {
        $currentRate = $this->getRate($base, $target);
        $dates = [];
        $rates = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $dates[] = date('d M', strtotime("-{$i} days"));
            // Generate minor fluctuations
            $variance = (rand(-15, 15) / 1000) * $currentRate;
            $rates[] = round($currentRate + $variance, 4);
        }
        
        // Ensure the last rate matches the exact current rate
        $rates[6] = $currentRate;

        return [
            'labels' => $dates,
            'rates' => $rates
        ];
    }

    private function getMockRate(string $base, string $target): float
    {
        $ratesFromUsd = [
            'USD' => 1.0,
            'IDR' => 16350.0,
            'SGD' => 1.35,
            'CNY' => 7.25,
            'EUR' => 0.92,
            'AUD' => 1.50,
            'GBP' => 0.79,
            'JPY' => 158.0,
            'BRL' => 5.40,
            'INR' => 83.50,
            'AED' => 3.67
        ];

        // If base is USD, direct return
        if ($base === 'USD' && isset($ratesFromUsd[$target])) {
            return $ratesFromUsd[$target];
        }

        // Cross-currency conversion using USD as pivot
        $rateBaseToUsd = isset($ratesFromUsd[$base]) ? (1.0 / $ratesFromUsd[$base]) : 1.0;
        $rateUsdToTarget = $ratesFromUsd[$target] ?? 1.0;

        return round($rateBaseToUsd * $rateUsdToTarget, 6);
    }
}
