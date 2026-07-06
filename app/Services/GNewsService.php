<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GNewsService
{
    protected ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.gnews.key') ?? env('GNEWS_API_KEY');
    }

    /**
     * Get news articles related to supply chain, logistics, or economy for a specific country.
     */
    public function getNews(string $countryName, string $countryCode): array
    {
        if ($this->apiKey && !str_starts_with($this->apiKey, 'YOUR_')) {
            try {
                // Search query: country name + (supply chain OR logistics OR port OR trade)
                $query = urlencode("\"{$countryName}\" AND (supply chain OR logistics OR port OR trade OR tariff OR economy)");
                $response = Http::timeout(5)->get("https://gnews.io/api/v4/search", [
                    'q' => $query,
                    'lang' => 'en',
                    'apikey' => $this->apiKey,
                    'max' => 5
                ]);

                if ($response->successful()) {
                    $articles = $response->json()['articles'] ?? [];
                    $result = [];
                    foreach ($articles as $article) {
                        $result[] = [
                            'title' => $article['title'] ?? '',
                            'description' => $article['description'] ?? '',
                            'url' => $article['url'] ?? '#',
                            'source_name' => $article['source']['name'] ?? 'GNews',
                            'published_at' => isset($article['publishedAt']) ? date('Y-m-d H:i:s', strtotime($article['publishedAt'])) : now()->toDateTimeString(),
                            'country_code' => strtoupper($countryCode),
                        ];
                    }
                    return $result;
                }
            } catch (\Exception $e) {
                Log::warning("GNews API failed for {$countryName}: " . $e->getMessage());
            }
        }

        // Fallback to mock data
        return $this->getMockNews($countryName, $countryCode);
    }

    private function getMockNews(string $countryName, string $countryCode): array
    {
        $countryCode = strtoupper($countryCode);
        $now = now();
        
        $mocks = [
            'ID' => [
                [
                    'title' => 'Tanjung Priok Port Implements Digital Log Book to Combat Vessel Congestion',
                    'description' => 'The Indonesian Ministry of Transportation has launched a new system at Jakarta’s main port to reduce queue times and streamline container clearance amid rising trade volumes.',
                    'url' => 'https://example.com/news/id-port-digitalization',
                    'source_name' => 'Jakarta Logistics Review',
                    'published_at' => $now->subHours(2)->toDateTimeString(),
                    'country_code' => 'ID'
                ],
                [
                    'title' => 'Severe Weather warning in Java Sea Disrupts Inter-Island Shipping Routes',
                    'description' => 'High waves and heavy rain have prompted maritime authorities to issue a warning for cargo vessels, delaying coal and agricultural shipments across the Indonesian archipelago.',
                    'url' => 'https://example.com/news/id-weather-disruption',
                    'source_name' => 'Maritime Asia',
                    'published_at' => $now->subHours(10)->toDateTimeString(),
                    'country_code' => 'ID'
                ],
                [
                    'title' => 'Indonesia Economic growth remains stable at 5% supported by Nickel Export Boost',
                    'description' => 'Despite global head winds, resource processing and regional trade agreements continue to bolster Indonesian supply chains and manufacturing sectors.',
                    'url' => 'https://example.com/news/id-gdp-boost',
                    'source_name' => 'Globe Finance',
                    'published_at' => $now->subDays(1)->toDateTimeString(),
                    'country_code' => 'ID'
                ]
            ],
            'US' => [
                [
                    'title' => 'Port of Los Angeles reports 15% increase in cargo volume amidst manufacturing recovery',
                    'description' => 'The busiest port complex in North America experienced a significant surge in imports, raising concerns over potential rail network bottlenecks in the upcoming peak season.',
                    'url' => 'https://example.com/news/us-port-congestion',
                    'source_name' => 'US Logistics Daily',
                    'published_at' => $now->subHours(4)->toDateTimeString(),
                    'country_code' => 'US'
                ],
                [
                    'title' => 'Tariff expansion proposals inject uncertainty into US-China trade routes',
                    'description' => 'Supply chain managers are actively diversifying fulfillment paths as new import restrictions on battery components and solar panels loom.',
                    'url' => 'https://example.com/news/us-china-tariff',
                    'source_name' => 'Trade Policy Monitor',
                    'published_at' => $now->subHours(12)->toDateTimeString(),
                    'country_code' => 'US'
                ],
                [
                    'title' => 'Federal Reserve maintains high interest rates to curb inflation, hitting warehouse expansions',
                    'description' => 'Rising borrowing costs are slowing down capital investments for logistics properties, threatening to restrict future supply chain capacity.',
                    'url' => 'https://example.com/news/us-inflation-rates',
                    'source_name' => 'Wall Street Economic Digest',
                    'published_at' => $now->subDays(2)->toDateTimeString(),
                    'country_code' => 'US'
                ]
            ],
            'SG' => [
                [
                    'title' => 'Singapore Tuas Port expansion uses AI to optimize automated guided vehicles',
                    'description' => 'The Maritime and Port Authority of Singapore announced a new fleet of autonomous vehicles to speed up container transit times at the mega-port.',
                    'url' => 'https://example.com/news/sg-tuas-ai',
                    'source_name' => 'Singapore Technology Focus',
                    'published_at' => $now->subHours(6)->toDateTimeString(),
                    'country_code' => 'SG'
                ],
                [
                    'title' => 'Maritime hubs collaborate to establish green shipping corridor via Singapore',
                    'description' => 'A new coalition aims to introduce low-emission fuels for cargo vessels, altering refueling logistics along primary East-West shipping lanes.',
                    'url' => 'https://example.com/news/sg-green-shipping',
                    'source_name' => 'Green Logistics Int',
                    'published_at' => $now->subDays(1)->toDateTimeString(),
                    'country_code' => 'SG'
                ]
            ],
            'CN' => [
                [
                    'title' => 'Shanghai Port Sets Record for Monthly Container Throughput',
                    'description' => 'Operations at the world\'s largest port reached record efficiency due to automated deep-water berths, boosting export logistics throughout China\'s industrial belts.',
                    'url' => 'https://example.com/news/cn-shanghai-throughput',
                    'source_name' => 'China Shipping News',
                    'published_at' => $now->subHours(8)->toDateTimeString(),
                    'country_code' => 'CN'
                ],
                [
                    'title' => 'Factory Output and Exports Dip Slightly Amid Slowing Global Demand',
                    'description' => 'Chinese manufacturers are experiencing a reduction in new export orders, reflecting inflationary pressures and inventory adjustments in Western economies.',
                    'url' => 'https://example.com/news/cn-export-slowdown',
                    'source_name' => 'East Asia Financial',
                    'published_at' => $now->subDays(1)->toDateTimeString(),
                    'country_code' => 'CN'
                ]
            ],
            'DE' => [
                [
                    'title' => 'Rhine River Water Levels Decline, Threatening Inland Barging Logistics',
                    'description' => 'A lack of rainfall in central Europe has lowered depth indicators, forcing cargo barges to load at half capacity, increasing transport costs for chemicals and coal.',
                    'url' => 'https://example.com/news/de-rhine-river',
                    'source_name' => 'European Waterways Journal',
                    'published_at' => $now->subHours(5)->toDateTimeString(),
                    'country_code' => 'DE'
                ],
                [
                    'title' => 'German Manufacturers warn of supply chain bottlenecks due to Suez canal rerouting',
                    'description' => 'Extended voyages around Africa have delayed critical electronics components, impacting production schedules at automotive and machinery plants.',
                    'url' => 'https://example.com/news/de-suez-reroute',
                    'source_name' => 'German Industrial Report',
                    'published_at' => $now->subDays(1)->toDateTimeString(),
                    'country_code' => 'DE'
                ]
            ]
        ];

        return $mocks[$countryCode] ?? [
            [
                'title' => "Supply Chain operations adapt to local economic changes in {$countryName}",
                'description' => "Local logistics networks in {$countryName} are adjusting to shifting market trends, ensuring regional distribution channels remain resilient.",
                'url' => 'https://example.com/news/general-logistics',
                'source_name' => 'Global Logistics Wire',
                'published_at' => $now->subHours(12)->toDateTimeString(),
                'country_code' => $countryCode
            ],
            [
                'title' => "{$countryName} Trade Policy adjustments trigger logistics network restructuring",
                'description' => "New import-export guidelines in {$countryName} have led business leaders to optimize warehouse capacity and supply routes.",
                'url' => 'https://example.com/news/general-trade',
                'source_name' => 'World Trade Insights',
                'published_at' => $now->subDays(1)->toDateTimeString(),
                'country_code' => $countryCode
            ]
        ];
    }
}
