<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenMeteoService
{
    /**
     * Get current weather details for a specific latitude and longitude.
     */
    public function getWeatherData(float $lat, float $lng): array
    {
        try {
            $response = Http::timeout(5)->get("https://api.open-meteo.com/v1/forecast", [
                'latitude' => $lat,
                'longitude' => $lng,
                'current_weather' => true,
            ]);

            if ($response->successful()) {
                $current = $response->json()['current_weather'] ?? [];
                
                $temp = $current['temperature'] ?? 25.0;
                $wind = $current['windspeed'] ?? 10.0;
                $weathercode = $current['weathercode'] ?? 0;
                
                // Determine weather description based on WMO codes
                $description = $this->getWmoDescription($weathercode);
                $isStormy = in_api_array($weathercode, [95, 96, 99, 71, 73, 75, 77, 85, 86]); // storms/heavy snow
                $rain = in_api_array($weathercode, [51, 53, 55, 61, 63, 65, 80, 81, 82]) ? 1 : 0; // rain/drizzle
                
                return [
                    'temperature' => $temp,
                    'wind_speed' => $wind,
                    'weather_code' => $weathercode,
                    'description' => $description,
                    'rain' => $rain,
                    'stormy' => $isStormy,
                    'source' => 'Open-Meteo API'
                ];
            }
        } catch (\Exception $e) {
            Log::warning("OpenMeteo API failed: " . $e->getMessage());
        }

        // Fallback to mock weather data
        return $this->getMockData($lat, $lng);
    }

    private function getWmoDescription(int $code): string
    {
        $wmoCodes = [
            0 => 'Cerah (Clear Sky)',
            1 => 'Utamanya Cerah (Mainly Clear)',
            2 => 'Berawan Sebagian (Partly Cloudy)',
            3 => 'Mendung (Overcast)',
            45 => 'Kabut (Fog)',
            48 => 'Kabut Rime (Depositing Rime Fog)',
            51 => 'Gerimis Ringan (Light Drizzle)',
            53 => 'Gerimis Sedang (Moderate Drizzle)',
            55 => 'Gerimis Lebat (Heavy Drizzle)',
            61 => 'Hujan Ringan (Slight Rain)',
            63 => 'Hujan Sedang (Moderate Rain)',
            65 => 'Hujan Lebat (Heavy Rain)',
            71 => 'Salju Tipis (Slight Snow)',
            73 => 'Salju Sedang (Moderate Snow)',
            75 => 'Salju Lebat (Heavy Snow)',
            80 => 'Hujan Rintik-Rintik (Slight Rain Showers)',
            81 => 'Hujan Shower Sedang (Moderate Rain Showers)',
            82 => 'Hujan Badai Lokal (Violent Rain Showers)',
            95 => 'Badai Guntur (Thunderstorm)',
            96 => 'Badai Guntur dengan Hujan Es (Thunderstorm with Slight Hail)',
            99 => 'Badai Guntur Hebat dengan Hujan Es (Thunderstorm with Heavy Hail)',
        ];

        return $wmoCodes[$code] ?? 'Cuaca Tidak Diketahui';
    }

    private function getMockData(float $lat, float $lng): array
    {
        // Simple procedural seed based on lat/lng to keep values stable for a location
        $seed = (int)abs(($lat + $lng) * 100);
        srand($seed);
        
        $temp = rand(15, 33);
        $wind = rand(5, 45);
        $isRainy = rand(0, 100) > 60;
        $isStormy = $wind > 35 && $isRainy;
        
        $description = 'Cerah (Clear Sky)';
        $code = 0;
        
        if ($isStormy) {
            $description = 'Badai Guntur (Thunderstorm)';
            $code = 95;
        } elseif ($isRainy) {
            $description = 'Hujan Sedang (Moderate Rain)';
            $code = 63;
        } elseif ($temp < 20) {
            $description = 'Berawan Sebagian (Partly Cloudy)';
            $code = 2;
        }

        // Reset random seed
        srand();

        return [
            'temperature' => (float)$temp,
            'wind_speed' => (float)$wind,
            'weather_code' => $code,
            'description' => $description,
            'rain' => $isRainy ? 1 : 0,
            'stormy' => $isStormy,
            'source' => 'Mock Weather Data'
        ];
    }
}

// Inline helper helper since in_array is standard but let's make sure it is safe
if (!function_exists('in_api_array')) {
    function in_api_array($val, $arr) {
        return in_array($val, $arr);
    }
}
