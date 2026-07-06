<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Port;
use App\Models\PositiveWord;
use App\Models\NegativeWord;
use App\Models\User;
use App\Models\Watchlist;
use App\Models\RiskScore;
use App\Services\RiskIntelligenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected RiskIntelligenceService $riskService;

    public function __construct(RiskIntelligenceService $riskService)
    {
        $this->riskService = $riskService;
    }

    /**
     * Dashboard index overview.
     */
    public function index()
    {
        $countriesCount = Country::count();
        $portsCount = Port::count();
        
        // Get all countries with latest risk
        $countries = Country::all();
        $criticalRisksCount = 0;
        
        $countriesData = [];
        foreach ($countries as $c) {
            $latestRisk = RiskScore::where('country_code', $c->code)
                ->orderBy('calculated_at', 'desc')
                ->first();
                
            $score = $latestRisk ? $latestRisk->total_score : 0;
            if ($score >= 70) {
                $criticalRisksCount++;
            }
            
            $countriesData[] = [
                'code' => $c->code,
                'name' => $c->name,
                'region' => $c->region,
                'score' => $score,
            ];
        }

        // Sort by risk score desc
        usort($countriesData, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return view('dashboard', [
            'countriesCount' => $countriesCount,
            'portsCount' => $portsCount,
            'criticalRisksCount' => $criticalRisksCount,
            'countries' => $countriesData,
        ]);
    }

    /**
     * Weather Map page.
     */
    public function weatherMap()
    {
        $countries = Country::all();
        return view('pages.weather_map', compact('countries'));
    }

    /**
     * Port Map page.
     */
    public function portMap()
    {
        $countries = Country::all();
        return view('pages.port_map', compact('countries'));
    }

    /**
     * Detailed Country Assessment.
     */
    public function countryAssessment(Request $request)
    {
        $countries = Country::all();
        $selectedCode = $request->input('code', 'ID');
        
        $assessment = $this->riskService->getCountryAssessment($selectedCode);
        
        if (isset($assessment['error'])) {
            return redirect()->route('dashboard')->with('error', $assessment['error']);
        }
        
        // Check if country is in current user's watchlist
        $inWatchlist = Watchlist::where('user_id', Auth::id())
            ->where('country_code', $selectedCode)
            ->exists();

        return view('pages.country_assessment', [
            'countries' => $countries,
            'selectedCode' => $selectedCode,
            'assessment' => $assessment,
            'inWatchlist' => $inWatchlist,
        ]);
    }

    /**
     * Compare page.
     */
    public function compare(Request $request)
    {
        $countries = Country::all();
        
        $countryA = $request->input('country_a', 'ID');
        $countryB = $request->input('country_b', 'US');
        
        $dataA = $this->riskService->getCountryAssessment($countryA);
        $dataB = $this->riskService->getCountryAssessment($countryB);
        
        return view('pages.compare', [
            'countries' => $countries,
            'countryA' => $countryA,
            'countryB' => $countryB,
            'dataA' => $dataA,
            'dataB' => $dataB,
        ]);
    }

    /**
     * User Watchlist.
     */
    public function watchlist()
    {
        $watchlistItems = Watchlist::where('user_id', Auth::id())->get();
        
        $watchlistData = [];
        foreach ($watchlistItems as $item) {
            $country = Country::where('code', $item->country_code)->first();
            if ($country) {
                $assessment = $this->riskService->getCountryAssessment($item->country_code);
                $watchlistData[] = [
                    'code' => $item->country_code,
                    'name' => $country->name,
                    'region' => $country->region,
                    'score' => $assessment['risk']['total_score'] ?? 0,
                    'weather' => $assessment['weather'] ?? [],
                    'currency_code' => $country->currency_code,
                ];
            }
        }
        
        return view('pages.watchlist', [
            'watchlist' => $watchlistData
        ]);
    }

    /**
     * Toggle Watchlist via AJAX.
     */
    public function toggleWatchlist(Request $request)
    {
        $code = strtoupper($request->input('country_code'));
        $exists = Watchlist::where('user_id', Auth::id())
            ->where('country_code', $code)
            ->first();
            
        if ($exists) {
            $exists->delete();
            return response()->json(['status' => 'removed']);
        } else {
            Watchlist::create([
                'user_id' => Auth::id(),
                'country_code' => $code
            ]);
            return response()->json(['status' => 'added']);
        }
    }

    /**
     * Admin Panel (Manage lexicon, users, and logged articles).
     */
    public function adminPanel(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        $users = User::all();
        $positives = PositiveWord::orderBy('word', 'asc')->paginate(10, ['*'], 'pos_page');
        $negatives = NegativeWord::orderBy('word', 'asc')->paginate(10, ['*'], 'neg_page');

        return view('pages.admin_panel', [
            'users' => $users,
            'positives' => $positives,
            'negatives' => $negatives
        ]);
    }

    /**
     * Admin: Add Lexicon Word.
     */
    public function addWord(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'word' => 'required|string|max:255',
            'type' => 'required|in:positive,negative'
        ]);

        $word = strtolower($request->word);

        if ($request->type === 'positive') {
            PositiveWord::firstOrCreate(['word' => $word]);
        } else {
            NegativeWord::firstOrCreate(['word' => $word]);
        }

        // Clear news caches to trigger recalculation of sentiments on next load
        Cache::flush();

        return back()->with('success', "Kata '{$word}' berhasil ditambahkan ke lexicon.");
    }

    /**
     * Admin: Delete Lexicon Word.
     */
    public function deleteWord(string $type, int $id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        if ($type === 'positive') {
            $word = PositiveWord::find($id);
        } else {
            $word = NegativeWord::find($id);
        }

        if ($word) {
            $wordName = $word->word;
            $word->delete();
            Cache::flush();
            return back()->with('success', "Kata '{$wordName}' berhasil dihapus dari lexicon.");
        }

        return back()->with('error', "Kata tidak ditemukan.");
    }
}
