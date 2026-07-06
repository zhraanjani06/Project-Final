<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Country;
use App\Models\Port;
use App\Models\PositiveWord;
use App\Models\NegativeWord;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // 2. Seed Countries
        $countries = [
            ['code' => 'ID', 'name' => 'Indonesia', 'region' => 'Asia', 'currency_code' => 'IDR', 'latitude' => -0.789275, 'longitude' => 113.921327],
            ['code' => 'US', 'name' => 'United States', 'region' => 'Americas', 'currency_code' => 'USD', 'latitude' => 37.090240, 'longitude' => -95.712891],
            ['code' => 'SG', 'name' => 'Singapore', 'region' => 'Asia', 'currency_code' => 'SGD', 'latitude' => 1.352083, 'longitude' => 103.819836],
            ['code' => 'CN', 'name' => 'China', 'region' => 'Asia', 'currency_code' => 'CNY', 'latitude' => 35.861660, 'longitude' => 104.195397],
            ['code' => 'DE', 'name' => 'Germany', 'region' => 'Europe', 'currency_code' => 'EUR', 'latitude' => 51.165691, 'longitude' => 10.451526],
            ['code' => 'AU', 'name' => 'Australia', 'region' => 'Oceania', 'currency_code' => 'AUD', 'latitude' => -25.274398, 'longitude' => 133.775136],
            ['code' => 'GB', 'name' => 'United Kingdom', 'region' => 'Europe', 'currency_code' => 'GBP', 'latitude' => 55.378051, 'longitude' => -3.435973],
            ['code' => 'JP', 'name' => 'Japan', 'region' => 'Asia', 'currency_code' => 'JPY', 'latitude' => 36.204824, 'longitude' => 138.252924],
            ['code' => 'BR', 'name' => 'Brazil', 'region' => 'Americas', 'currency_code' => 'BRL', 'latitude' => -14.235004, 'longitude' => -51.925280],
            ['code' => 'IN', 'name' => 'India', 'region' => 'Asia', 'currency_code' => 'INR', 'latitude' => 20.593684, 'longitude' => 78.962880],
            ['code' => 'NL', 'name' => 'Netherlands', 'region' => 'Europe', 'currency_code' => 'EUR', 'latitude' => 52.132633, 'longitude' => 5.291266],
            ['code' => 'AE', 'name' => 'United Arab Emirates', 'region' => 'Asia', 'currency_code' => 'AED', 'latitude' => 23.424076, 'longitude' => 53.847818],
        ];

        foreach ($countries as $c) {
            Country::create($c);
        }

        // 3. Seed Ports
        $ports = [
            ['name' => 'Tanjung Priok (Jakarta)', 'country_code' => 'ID', 'latitude' => -6.1017, 'longitude' => 106.8831],
            ['name' => 'Tanjung Perak (Surabaya)', 'country_code' => 'ID', 'latitude' => -7.2023, 'longitude' => 112.7303],
            ['name' => 'Port of Singapore', 'country_code' => 'SG', 'latitude' => 1.2740, 'longitude' => 103.8440],
            ['name' => 'Port of Shanghai', 'country_code' => 'CN', 'latitude' => 30.6270, 'longitude' => 122.0670],
            ['name' => 'Port of Rotterdam', 'country_code' => 'NL', 'latitude' => 51.9480, 'longitude' => 4.1430],
            ['name' => 'Port of Los Angeles', 'country_code' => 'US', 'latitude' => 33.7290, 'longitude' => -118.2620],
            ['name' => 'Port of Hamburg', 'country_code' => 'DE', 'latitude' => 53.5380, 'longitude' => 9.9670],
            ['name' => 'Port of Tokyo', 'country_code' => 'JP', 'latitude' => 35.6170, 'longitude' => 139.7910],
            ['name' => 'Port of Sydney', 'country_code' => 'AU', 'latitude' => -33.8610, 'longitude' => 151.2110],
            ['name' => 'Port of London', 'country_code' => 'GB', 'latitude' => 51.5030, 'longitude' => 0.0480],
            ['name' => 'Port of Santos', 'country_code' => 'BR', 'latitude' => -23.9680, 'longitude' => -46.2980],
            ['name' => 'Port of Mumbai', 'country_code' => 'IN', 'latitude' => 18.9480, 'longitude' => 72.8460],
            ['name' => 'Port of Jebel Ali (Dubai)', 'country_code' => 'AE', 'latitude' => 24.9880, 'longitude' => 55.0740],
        ];

        foreach ($ports as $p) {
            Port::create($p);
        }

        // 4. Seed Positive Words
        $positives = [
            'good', 'great', 'growth', 'positive', 'boom', 'recovery', 'strong', 'rise', 'benefit', 
            'gain', 'stable', 'increase', 'improving', 'expand', 'success', 'safe', 'optimistic', 
            'opportunity', 'profit', 'progress', 'boost', 'secure', 'surge', 'develop', 'advance'
        ];

        foreach ($positives as $word) {
            PositiveWord::create(['word' => $word]);
        }

        // 5. Seed Negative Words
        $negatives = [
            'bad', 'worse', 'crisis', 'negative', 'recession', 'risk', 'weak', 'fall', 'loss', 
            'decline', 'unstable', 'decrease', 'drop', 'fail', 'danger', 'pessimistic', 'threat', 
            'inflation', 'disruption', 'strike', 'war', 'conflict', 'shortage', 'bottleneck', 
            'delay', 'collapse', 'damage', 'tensions', 'uncertainty', 'halt', 'plunge'
        ];

        foreach ($negatives as $word) {
            NegativeWord::create(['word' => $word]);
        }
    }
}
