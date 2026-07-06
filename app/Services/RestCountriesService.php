<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RestCountriesService
{
    /**
     * Get details for a country by its ISO 2-letter code.
     */
    public function getCountryDetails(string $code): array
    {
        $code = strtoupper($code);
        
        try {
            $response = Http::timeout(5)->get("https://restcountries.com/v3.1/alpha/{$code}");
            if ($response->successful() && !empty($response->json())) {
                $data = $response->json()[0];
                
                $currencyKey = array_key_first($data['currencies'] ?? []);
                $currencyName = $currencyKey ? ($data['currencies'][$currencyKey]['name'] ?? '') : '';
                $currencySymbol = $currencyKey ? ($data['currencies'][$currencyKey]['symbol'] ?? '') : '';
                
                $languages = array_values($data['languages'] ?? []);
                
                return [
                    'name' => $data['name']['common'] ?? '',
                    'official_name' => $data['name']['official'] ?? '',
                    'capital' => $data['capital'][0] ?? 'N/A',
                    'region' => $data['region'] ?? '',
                    'subregion' => $data['subregion'] ?? '',
                    'population' => $data['population'] ?? 0,
                    'currency_code' => $currencyKey ?? '',
                    'currency_name' => $currencyName,
                    'currency_symbol' => $currencySymbol,
                    'language' => $languages[0] ?? 'N/A',
                    'flag_emoji' => $data['flag'] ?? '',
                    'flag_url' => $data['flags']['svg'] ?? '',
                    'source' => 'RestCountries API'
                ];
            }
        } catch (\Exception $e) {
            Log::warning("RestCountries API failed for {$code}: " . $e->getMessage());
        }

        // Fallback to mock data
        return $this->getMockData($code);
    }

    private function getMockData(string $code): array
    {
        $mocks = [
            'ID' => [
                'name' => 'Indonesia',
                'official_name' => 'Republic of Indonesia',
                'capital' => 'Jakarta',
                'region' => 'Asia',
                'subregion' => 'South-Eastern Asia',
                'population' => 273523615,
                'currency_code' => 'IDR',
                'currency_name' => 'Indonesian rupiah',
                'currency_symbol' => 'Rp',
                'language' => 'Indonesian',
                'flag_emoji' => '🇮🇩',
                'flag_url' => 'https://flagcdn.com/id.svg',
                'source' => 'Mock Data'
            ],
            'US' => [
                'name' => 'United States',
                'official_name' => 'United States of America',
                'capital' => 'Washington D.C.',
                'region' => 'Americas',
                'subregion' => 'North America',
                'population' => 331002651,
                'currency_code' => 'USD',
                'currency_name' => 'United States dollar',
                'currency_symbol' => '$',
                'language' => 'English',
                'flag_emoji' => '🇺🇸',
                'flag_url' => 'https://flagcdn.com/us.svg',
                'source' => 'Mock Data'
            ],
            'SG' => [
                'name' => 'Singapore',
                'official_name' => 'Republic of Singapore',
                'capital' => 'Singapore',
                'region' => 'Asia',
                'subregion' => 'South-Eastern Asia',
                'population' => 5850342,
                'currency_code' => 'SGD',
                'currency_name' => 'Singapore dollar',
                'currency_symbol' => '$',
                'language' => 'English, Malay, Mandarin, Tamil',
                'flag_emoji' => '🇸🇬',
                'flag_url' => 'https://flagcdn.com/sg.svg',
                'source' => 'Mock Data'
            ],
            'DE' => [
                'name' => 'Germany',
                'official_name' => 'Federal Republic of Germany',
                'capital' => 'Berlin',
                'region' => 'Europe',
                'subregion' => 'Western Europe',
                'population' => 83240525,
                'currency_code' => 'EUR',
                'currency_name' => 'Euro',
                'currency_symbol' => '€',
                'language' => 'German',
                'flag_emoji' => '🇩🇪',
                'flag_url' => 'https://flagcdn.com/de.svg',
                'source' => 'Mock Data'
            ],
            'CN' => [
                'name' => 'China',
                'official_name' => 'People\'s Republic of China',
                'capital' => 'Beijing',
                'region' => 'Asia',
                'subregion' => 'Eastern Asia',
                'population' => 1411778724,
                'currency_code' => 'CNY',
                'currency_name' => 'Chinese Yuan',
                'currency_symbol' => '¥',
                'language' => 'Mandarin',
                'flag_emoji' => '🇨🇳',
                'flag_url' => 'https://flagcdn.com/cn.svg',
                'source' => 'Mock Data'
            ]
        ];

        return $mocks[$code] ?? [
            'name' => 'Unknown Country',
            'official_name' => 'Unknown Country',
            'capital' => 'N/A',
            'region' => 'N/A',
            'subregion' => 'N/A',
            'population' => 0,
            'currency_code' => 'USD',
            'currency_name' => 'United States dollar',
            'currency_symbol' => '$',
            'language' => 'N/A',
            'flag_emoji' => '🌐',
            'flag_url' => '',
            'source' => 'Mock Data'
        ];
    }
}
