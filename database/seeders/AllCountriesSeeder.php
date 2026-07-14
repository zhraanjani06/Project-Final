<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AllCountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Fetching countries from REST Countries API...');
        
        try {
            $response = Http::timeout(25)->get('https://raw.githubusercontent.com/mledoze/countries/master/dist/countries.json');
            
            if ($response->failed()) {
                $this->command->error('Failed to fetch countries list.');
                return;
            }

            $countriesData = $response->json();
            $this->command->info('Fetched ' . count($countriesData) . ' countries. Inserting into database...');

            $originalActive = ['ID', 'US', 'SG', 'CN', 'DE', 'AU', 'GB', 'JP', 'BR', 'IN', 'NL', 'AE'];

            foreach ($countriesData as $c) {
                $code = strtoupper($c['cca2'] ?? '');
                if (empty($code) || strlen($code) > 3) {
                    continue;
                }

                $name = $c['name']['common'] ?? '';
                $region = $c['region'] ?? '';
                
                $currencyCode = null;
                if (isset($c['currencies'])) {
                    $currencyCode = array_key_first($c['currencies']);
                }

                $latlng = $c['latlng'] ?? [];
                $latitude = isset($latlng[0]) ? (float)$latlng[0] : null;
                $longitude = isset($latlng[1]) ? (float)$latlng[1] : null;

                // Check if the country already exists in the database
                $exists = Country::where('code', $code)->first();

                Country::updateOrCreate(
                    ['code' => $code],
                    [
                        'name' => $name,
                        'region' => $region,
                        'currency_code' => $currencyCode ?? 'USD',
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        // If it already exists, keep its is_active state, otherwise false except the original 12
                        'is_active' => $exists ? $exists->is_active : in_array($code, $originalActive)
                    ]
                );
            }

            $this->command->info('All countries seeded successfully.');

        } catch (\Exception $e) {
            $this->command->error('Error during country seeding: ' . $e->getMessage());
            Log::error('AllCountriesSeeder failed: ' . $e->getMessage());
        }
    }
}
