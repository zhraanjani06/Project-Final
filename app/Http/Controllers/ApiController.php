<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Port;
use App\Models\RiskScore;
use App\Services\RiskIntelligenceService;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    protected RiskIntelligenceService $riskService;

    public function __construct(RiskIntelligenceService $riskService)
    {
        $this->riskService = $riskService;
    }

    /**
     * GET /api/countries
     * Get list of all countries with their latest total risk scores.
     */
    public function getCountries()
    {
        $countries = Country::all();
        
        $result = [];
        foreach ($countries as $c) {
            // Get the latest risk score from DB
            $latestRisk = RiskScore::where('country_code', $c->code)
                ->orderBy('calculated_at', 'desc')
                ->first();
                
            $result[] = [
                'id' => $c->id,
                'code' => $c->code,
                'name' => $c->name,
                'region' => $c->region,
                'currency_code' => $c->currency_code,
                'latitude' => (float)$c->latitude,
                'longitude' => (float)$c->longitude,
                'risk_score' => $latestRisk ? (float)$latestRisk->total_score : null,
                'risk_level' => $latestRisk ? $this->getRiskLevel($latestRisk->total_score) : 'Unknown'
            ];
        }

        return response()->json($result);
    }

    /**
     * GET /api/countries/{code}
     * Get complete data and risk assessment for a single country.
     */
    public function getCountryDetails(string $code)
    {
        $assessment = $this->riskService->getCountryAssessment($code);
        
        if (isset($assessment['error'])) {
            return response()->json(['message' => $assessment['error']], 404);
        }

        return response()->json($assessment);
    }

    /**
     * GET /api/risk/{code}
     * Get just the risk scoring breakdown for a country.
     */
    public function getRiskBreakdown(string $code)
    {
        $assessment = $this->riskService->getCountryAssessment($code);
        
        if (isset($assessment['error'])) {
            return response()->json(['message' => $assessment['error']], 404);
        }

        return response()->json($assessment['risk']);
    }

    /**
     * GET /api/ports
     * Get all port coordinates and names.
     */
    public function getPorts(Request $request)
    {
        $query = Port::query();
        
        if ($request->has('country_code')) {
            $query->where('country_code', strtoupper($request->country_code));
        }

        $ports = $query->get()->map(function ($port) {
            return [
                'id' => $port->id,
                'name' => $port->name,
                'country_code' => $port->country_code,
                'latitude' => (float)$port->latitude,
                'longitude' => (float)$port->longitude,
            ];
        });

        return response()->json($ports);
    }

    /**
     * GET /api/news/{code}
     * Get news articles and sentiment stats for a country.
     */
    public function getNews(string $code)
    {
        $assessment = $this->riskService->getCountryAssessment($code);
        
        if (isset($assessment['error'])) {
            return response()->json(['message' => $assessment['error']], 404);
        }

        return response()->json($assessment['news']);
    }

    /**
     * GET /api/currency/trends
     * Get currency trend points for a specific base/target currency.
     */
    public function getCurrencyTrends(Request $request)
    {
        $base = $request->input('base', 'USD');
        $target = $request->input('target', 'IDR');
        
        // We use the exchange rate service directly to avoid triggering a full country assessment if not needed
        $exchangeService = app(\App\Services\ExchangeRateService::class);
        $trends = $exchangeService->getHistoricalTrend($base, $target);
        
        return response()->json($trends);
    }

    private function getRiskLevel(float $score): string
    {
        if ($score >= 70) {
            return 'High';
        } elseif ($score >= 35) {
            return 'Medium';
        }
        return 'Low';
    }
}
