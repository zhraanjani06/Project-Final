<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Port;
use App\Models\RiskScore;
use App\Models\NewsCache;
use App\Models\PositiveWord;
use App\Models\NegativeWord;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RiskIntelligenceService
{
    protected RestCountriesService $restCountries;
    protected OpenMeteoService $openMeteo;
    protected WorldBankService $worldBank;
    protected ExchangeRateService $exchangeRate;
    protected GNewsService $gnews;

    public function __construct(
        RestCountriesService $restCountries,
        OpenMeteoService $openMeteo,
        WorldBankService $worldBank,
        ExchangeRateService $exchangeRate,
        GNewsService $gnews
    ) {
        $this->restCountries = $restCountries;
        $this->openMeteo = $openMeteo;
        $this->worldBank = $worldBank;
        $this->exchangeRate = $exchangeRate;
        $this->gnews = $gnews;
    }

    /**
     * Get all data for a country (Profile, Weather, Economy, Currency, News, Risk Score).
     * Automatically handles caching.
     */
    public function getCountryAssessment(string $code): array
    {
        $code = strtoupper($code);
        $country = Country::where('code', $code)->first();
        if (!$country) {
            return ['error' => 'Negara tidak ditemukan dalam database.'];
        }

        // 1. Get Country Profile (Cache: 30 days)
        $profile = Cache::remember("country_profile_{$code}", now()->addDays(30), function () use ($code) {
            return $this->restCountries->getCountryDetails($code);
        });

        // 2. Get Weather Data (Cache: 1 hour)
        $weather = Cache::remember("country_weather_{$code}", now()->addHours(1), function () use ($country) {
            return $this->openMeteo->getWeatherData($country->latitude, $country->longitude);
        });

        // 3. Get Economic Data (Cache: 7 days)
        $economy = Cache::remember("country_economy_{$code}", now()->addDays(7), function () use ($code) {
            return $this->worldBank->getEconomicData($code);
        });

        // 4. Get Currency Exchange Rate & Trend (Cache: 12 hours)
        $currencyCode = $country->currency_code ?? 'USD';
        $currency = Cache::remember("country_currency_{$code}", now()->addHours(12), function () use ($currencyCode) {
            $rate = $this->exchangeRate->getRate('USD', $currencyCode);
            $trend = $this->exchangeRate->getHistoricalTrend('USD', $currencyCode);
            return [
                'rate_vs_usd' => $rate,
                'trend' => $trend,
                'source' => 'ExchangeRate API'
            ];
        });

        // 5. Get News & Analyze Sentiment (Cache Flag: 3 hours)
        $newsCacheKey = "country_news_updated_{$code}";
        $needsUpdate = !Cache::has($newsCacheKey);

        if ($needsUpdate) {
            try {
                $articles = $this->gnews->getNews($country->name, $code);
                $this->analyzeAndCacheNews($articles, $code);
                Cache::put($newsCacheKey, true, now()->addHours(3));
            } catch (\Exception $e) {
                Log::error("Failed to fetch/analyze news for {$code}: " . $e->getMessage());
            }
        }

        // Get news directly from the database table (news_caches)
        $dbArticles = NewsCache::where('country_code', $code)
            ->orderBy('published_at', 'desc')
            ->get();

        $newsData = [
            'articles' => $dbArticles,
            'positive_count' => $dbArticles->where('sentiment', 'Positive')->count(),
            'negative_count' => $dbArticles->where('sentiment', 'Negative')->count(),
            'neutral_count' => $dbArticles->where('sentiment', 'Neutral')->count(),
            'total_count' => $dbArticles->count(),
            'news_risk_score' => $dbArticles->count() > 0 
                ? ($dbArticles->where('sentiment', 'Negative')->count() / $dbArticles->count()) * 100 
                : 0.0
        ];

        // 6. Calculate Risk Scores
        $riskData = $this->calculateWeightedRisk($code, $weather, $economy, $currency, $newsData);

        return [
            'country' => $country,
            'profile' => $profile,
            'weather' => $weather,
            'economy' => $economy,
            'currency' => $currency,
            'news' => $newsData,
            'risk' => $riskData
        ];
    }

    /**
     * Run Lexicon-Based Sentiment Analysis on fetched articles, cache them in DB, and return.
     */
    protected function analyzeAndCacheNews(array $articles, string $code): array
    {
        // Fetch lexicon words from database
        $positiveWords = PositiveWord::pluck('word')->toArray();
        $negativeWords = NegativeWord::pluck('word')->toArray();

        $analyzedArticles = [];
        $negativeCount = 0;
        $positiveCount = 0;
        $totalArticlesCount = count($articles);

        // Delete old cached articles for this country in DB
        NewsCache::where('country_code', $code)->delete();

        foreach ($articles as $art) {
            $textToAnalyze = strtolower(($art['title'] ?? '') . ' ' . ($art['description'] ?? ''));
            
            // Clean text (remove punctuation)
            $words = str_word_count($textToAnalyze, 1);
            
            $posMatches = 0;
            $negMatches = 0;

            foreach ($words as $word) {
                if (in_array($word, $positiveWords)) {
                    $posMatches++;
                }
                if (in_array($word, $negativeWords)) {
                    $negMatches++;
                }
            }

            $sentiment = 'Neutral';
            $score = $posMatches - $negMatches;

            if ($posMatches > $negMatches) {
                $sentiment = 'Positive';
                $positiveCount++;
            } elseif ($posMatches < $negMatches) {
                $sentiment = 'Negative';
                $negativeCount++;
            }

            // Save to DB
            $dbArticle = NewsCache::create([
                'country_code' => $code,
                'title' => substr($art['title'] ?? '', 0, 490),
                'description' => $art['description'] ?? '',
                'url' => $art['url'] ?? '#',
                'source_name' => $art['source_name'] ?? 'GNews',
                'published_at' => $art['published_at'] ?? now()->toDateTimeString(),
                'sentiment' => $sentiment,
                'sentiment_score' => $score
            ]);

            $analyzedArticles[] = $dbArticle;
        }

        // Determine overall news sentiment score (0 - 100)
        // If 0 articles, score is 0. If some, negative percentage determines risk.
        $newsRiskScore = 0.0;
        if ($totalArticlesCount > 0) {
            $newsRiskScore = ($negativeCount / $totalArticlesCount) * 100;
        }

        return [
            'articles' => $analyzedArticles,
            'positive_count' => $positiveCount,
            'negative_count' => $negativeCount,
            'neutral_count' => $totalArticlesCount - ($positiveCount + $negativeCount),
            'total_count' => $totalArticlesCount,
            'news_risk_score' => $newsRiskScore
        ];
    }

    /**
     * Compute Weighted Risk Score based on Weather, Economy, Currency, and News.
     */
    protected function calculateWeightedRisk(string $code, array $weather, array $economy, array $currency, array $news): array
    {
        // 1. Weather Risk Score (0-100) - Weight 30%
        $weatherRisk = 0;
        if ($weather['stormy'] ?? false) {
            $weatherRisk = 100;
        } elseif (($weather['rain'] ?? 0) === 1) {
            $weatherRisk = 50;
        } elseif (($weather['temperature'] ?? 25) > 38 || ($weather['temperature'] ?? 25) < 3) {
            $weatherRisk = 30;
        }

        // 2. Economic Risk Score (0-100) - Weight 20%
        // Inflation: stable under 3% = 0, under 5% = 30, under 10% = 60, high inflation = 100
        $inflation = $economy['inflation'] ?? 2.5;
        $economicRisk = 0;
        if ($inflation > 10) {
            $economicRisk = 100;
        } elseif ($inflation > 5) {
            $economicRisk = 60;
        } elseif ($inflation > 3) {
            $economicRisk = 30;
        }

        // 3. News Risk Score (0-100) - Weight 40%
        $newsRisk = $news['news_risk_score'] ?? 0.0;

        // 4. Currency Fluctuation Risk Score (0-100) - Weight 10%
        // Variance in historical trend
        $rates = $currency['trend']['rates'] ?? [];
        $currencyRisk = 0;
        if (count($rates) > 1) {
            $min = min($rates);
            $max = max($rates);
            if ($min > 0) {
                $fluctuation = (($max - $min) / $min) * 100; // % fluctuation
                // Scale it: 1% fluctuation = 20 points, 5% fluctuation = 100 points
                $currencyRisk = min(100, $fluctuation * 20);
            }
        }

        // 5. Total Weighted Risk Score
        $totalRisk = ($weatherRisk * 0.3) + ($economicRisk * 0.2) + ($newsRisk * 0.4) + ($currencyRisk * 0.1);
        $totalRisk = round($totalRisk, 2);

        // Save history score to DB
        $dbScore = RiskScore::create([
            'country_code' => $code,
            'weather_score' => $weatherRisk,
            'economic_score' => $economicRisk,
            'news_score' => $newsRisk,
            'currency_score' => $currencyRisk,
            'total_score' => $totalRisk,
            'calculated_at' => now()
        ]);

        return [
            'weather_score' => $weatherRisk,
            'economic_score' => $economicRisk,
            'news_score' => $newsRisk,
            'currency_score' => $currencyRisk,
            'total_score' => $totalRisk,
            'db_record' => $dbScore
        ];
    }
}
