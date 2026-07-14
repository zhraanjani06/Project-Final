<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Port;
use App\Models\Country;

class AllPortsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Populating global ports matching country locations...');
        
        $countries = Country::all();
        $count = 0;

        foreach ($countries as $country) {
            if ($country->latitude && $country->longitude) {
                // Check if country already has a port to avoid duplicates
                $exists = Port::where('country_code', $country->code)->exists();
                
                if (!$exists) {
                    // Create a simulated major port for the country
                    Port::create([
                        'name' => 'Port of ' . $country->name,
                        'country_code' => $country->code,
                        'latitude' => $country->latitude,
                        'longitude' => $country->longitude,
                    ]);
                    $count++;
                }
            }
        }

        $this->command->info("Successfully added {$count} global ports to match weather map coverage.");
    }
}
