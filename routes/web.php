<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard (which will redirect to login if guest)
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/weather-map', [DashboardController::class, 'weatherMap'])->name('weather-map');
    Route::get('/port-map', [DashboardController::class, 'portMap'])->name('port-map');
    Route::get('/country-assessment', [DashboardController::class, 'countryAssessment'])->name('country-assessment');
    Route::get('/compare', [DashboardController::class, 'compare'])->name('compare');
    Route::get('/watchlist', [DashboardController::class, 'watchlist'])->name('watchlist');
    Route::post('/watchlist/toggle', [DashboardController::class, 'toggleWatchlist'])->name('watchlist.toggle');
    
    // Admin routes
    Route::get('/admin', [DashboardController::class, 'adminPanel'])->name('admin.panel');
    Route::post('/admin/lexicon', [DashboardController::class, 'addWord'])->name('admin.lexicon.add');
    Route::delete('/admin/lexicon/{type}/{id}', [DashboardController::class, 'deleteWord'])->name('admin.lexicon.delete');
    Route::post('/admin/ports', [DashboardController::class, 'addPort'])->name('admin.ports.add');
    Route::delete('/admin/ports/{id}', [DashboardController::class, 'deletePort'])->name('admin.ports.delete');
    Route::post('/admin/articles', [DashboardController::class, 'addArticle'])->name('admin.articles.add');
    Route::delete('/admin/articles/{id}', [DashboardController::class, 'deleteArticle'])->name('admin.articles.delete');
    Route::post('/admin/country-settings', [DashboardController::class, 'updateCountrySettings'])->name('admin.countries.update');

    // API endpoints
    Route::prefix('api')->group(function () {
        Route::get('/countries', [ApiController::class, 'getCountries']);
        Route::get('/countries/{code}', [ApiController::class, 'getCountryDetails']);
        Route::get('/risk/{code}', [ApiController::class, 'getRiskBreakdown']);
        Route::get('/ports', [ApiController::class, 'getPorts']);
        Route::get('/news/{code}', [ApiController::class, 'getNews']);
        Route::get('/currency/trends', [ApiController::class, 'getCurrencyTrends']);
    });
});
