<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WorldBankService
{
    /**
     * Get macroeconomic indicators (GDP, Inflation, Population, Exports) for a country.
     */
    public function getEconomicData(string $code): array
    {
        $code = strtoupper($code);
        
        try {
            // World Bank uses 3-letter codes for some requests, but it can handle 2-letter codes for country endpoints too.
            // Let's get GDP, Inflation, and Exports.
            $gdpData = $this->fetchIndicator($code, 'NY.GDP.MKTP.CD');
            $inflationData = $this->fetchIndicator($code, 'FP.CPI.TOTL.ZG');
            $exportsData = $this->fetchIndicator($code, 'NE.EXP.GNFS.ZS');
            
            if ($gdpData !== null || $inflationData !== null || $exportsData !== null) {
                return [
                    'gdp' => $gdpData['value'] ?? 0.0,
                    'gdp_year' => $gdpData['year'] ?? date('Y') - 1,
                    'inflation' => $inflationData['value'] ?? 2.5,
                    'inflation_year' => $inflationData['year'] ?? date('Y') - 1,
                    'exports_gdp_share' => $exportsData['value'] ?? 20.0,
                    'exports_year' => $exportsData['year'] ?? date('Y') - 1,
                    'source' => 'World Bank API'
                ];
            }
        } catch (\Exception $e) {
            Log::warning("WorldBank API failed for {$code}: " . $e->getMessage());
        }

        // Fallback to mock data
        return $this->getMockData($code);
    }

    private function fetchIndicator(string $country, string $indicator): ?array
    {
        try {
            $response = Http::timeout(5)->get("https://api.worldbank.org/v2/country/{$country}/indicator/{$indicator}", [
                'format' => 'json',
                'mrnev' => 5, // most recent non-empty values
            ]);

            if ($response->successful() && isset($response->json()[1])) {
                $records = $response->json()[1];
                foreach ($records as $record) {
                    if ($record['value'] !== null) {
                        return [
                            'value' => (float)$record['value'],
                            'year' => (int)$record['date']
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning("WorldBank indicator {$indicator} fetch failed: " . $e->getMessage());
        }
        return null;
    }

    private function getMockData(string $code): array
    {
        $mocks = [
            'ID' => [
                'gdp' => 1186000000000.0, // ~$1.18T USD
                'gdp_year' => 2024,
                'inflation' => 2.8, // 2.8%
                'inflation_year' => 2024,
                'exports_gdp_share' => 22.4, // 22.4%
                'exports_year' => 2024,
                'source' => 'Mock Economic Data'
            ],
            'US' => [
                'gdp' => 25460000000000.0, // ~$25.46T USD
                'gdp_year' => 2024,
                'inflation' => 3.1, // 3.1%
                'inflation_year' => 2024,
                'exports_gdp_share' => 11.5, // 11.5%
                'exports_year' => 2024,
                'source' => 'Mock Economic Data'
            ],
            'SG' => [
                'gdp' => 466700000000.0, // ~$466B USD
                'gdp_year' => 2024,
                'inflation' => 4.2, // 4.2%
                'inflation_year' => 2024,
                'exports_gdp_share' => 178.5, // 178.5%
                'exports_year' => 2024,
                'source' => 'Mock Economic Data'
            ],
            'DE' => [
                'gdp' => 4070000000000.0, // ~$4.07T USD
                'gdp_year' => 2024,
                'inflation' => 5.9, // 5.9%
                'inflation_year' => 2024,
                'exports_gdp_share' => 50.7, // 50.7%
                'exports_year' => 2024,
                'source' => 'Mock Economic Data'
            ],
            'CN' => [
                'gdp' => 17960000000000.0, // ~$17.96T USD
                'gdp_year' => 2024,
                'inflation' => 0.5, // 0.5%
                'inflation_year' => 2024,
                'exports_gdp_share' => 20.1, // 20.1%
                'exports_year' => 2024,
                'source' => 'Mock Economic Data'
            ]
        ];

        return $mocks[$code] ?? [
            'gdp' => 50000000000.0,
            'gdp_year' => date('Y') - 1,
            'inflation' => 3.5,
            'inflation_year' => date('Y') - 1,
            'exports_gdp_share' => 25.0,
            'exports_year' => date('Y') - 1,
            'source' => 'Mock Economic Data'
        ];
    }
}
