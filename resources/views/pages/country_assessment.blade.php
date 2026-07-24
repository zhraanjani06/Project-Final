@extends('layouts.app')

@section('title', 'Country Risk Assessment')
@section('page_title', 'Country Risk Assessment')

@section('content')
    <div class="row g-4">
        @if(session('success'))
            <div class="col-12">
                <div class="alert border-0 rounded-3 text-white shadow-sm d-flex align-items-center mb-0" style="background-color: rgba(25, 135, 84, 0.15); border-left: 4px solid #198754 !important;">
                    <i class="fa-solid fa-circle-check fs-5 me-3 text-success"></i>
                    <div>
                        <strong>Berhasil!</strong> {{ session('success') }}
                    </div>
                </div>
            </div>
        @endif
        <!-- Selector & Watchlist Header -->
        <div class="col-12">
            <div class="custom-card p-4">
                <div class="row align-items-center g-3">
                    <div class="col-md-6 d-flex align-items-center">
                        <label class="form-label text-muted mb-0 me-3" style="white-space: nowrap;">Pilih Negara:</label>
                        <select id="country-selector" class="form-select bg-dark border-secondary text-white rounded-3 w-50" onchange="window.location.href='/country-assessment?code=' + this.value">
                            @foreach($countries as $c)
                                <option value="{{ $c->code }}" {{ $c->code == $selectedCode ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <button id="btn-watchlist" class="btn {{ $inWatchlist ? 'btn-warning' : 'btn-outline-warning' }} rounded-3 px-4" data-code="{{ $selectedCode }}">
                            <i class="fa-{{ $inWatchlist ? 'solid' : 'regular' }} fa-star me-2"></i>
                            <span id="watchlist-text">{{ $inWatchlist ? 'Hapus dari Watchlist' : 'Tambah ke Watchlist' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @php
            $profile = $assessment['profile'];
            $weather = $assessment['weather'];
            $economy = $assessment['economy'];
            $currency = $assessment['currency'];
            $news = $assessment['news'];
            $risk = $assessment['risk'];
            $country = $assessment['country'];
        @endphp

        <!-- Overall Risk Summary Card -->
        <div class="col-md-4">
            <div class="custom-card h-100 text-center p-4 d-flex flex-column justify-content-center align-items-center">
                <span class="fs-4 text-muted mb-2">Total Skor Risiko (Weighted)</span>
                <div class="position-relative d-flex align-items-center justify-content-center mb-3" style="width: 160px; height: 160px;">
                    <!-- Circular Gauge -->
                    <svg width="160" height="160" viewBox="0 0 160 160">
                        <circle cx="80" cy="80" r="70" stroke="rgba(255,255,255,0.05)" stroke-width="12" fill="transparent"/>
                        @php
                            $strokeDash = 2 * pi() * 70;
                            $offset = $strokeDash - ($risk['total_score'] / 100) * $strokeDash;
                            $color = '#10b981';
                            if ($risk['total_score'] >= 70) {
                                $color = '#ef4444';
                            } elseif ($risk['total_score'] >= 35) {
                                $color = '#f59e0b';
                            }
                        @endphp
                        <circle cx="80" cy="80" r="70" stroke="{{ $color }}" stroke-width="12" fill="transparent"
                                stroke-dasharray="{{ $strokeDash }}" stroke-dashoffset="{{ $offset }}"
                                stroke-linecap="round" transform="rotate(-90 80 80)"/>
                    </svg>
                    <div class="position-absolute text-center">
                        <div class="fs-1 fw-extrabold text-white">{{ \App\Helpers\FormatHelper::decimal($risk['total_score']) }}</div>
                        <div class="small text-muted">dari 100</div>
                    </div>
                    <!-- Status Badge -->
                    <div class="position-absolute" style="bottom: -15px;">
                        @if($risk['total_score'] >= 70)
                            <span class="badge bg-danger text-white px-3 py-2 rounded-pill shadow-sm">RISIKO TINGGI (HIGH)</span>
                        @elseif($risk['total_score'] >= 35)
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill shadow-sm">RISIKO SEDANG (MEDIUM)</span>
                        @else
                            <span class="badge bg-success text-white px-3 py-2 rounded-pill shadow-sm">RISIKO RENDAH (LOW)</span>
                        @endif
                    </div>
                </div>
                
                @php $prediction = $assessment['prediction']; @endphp
                <!-- Prediction Box -->
                <div class="mt-4 w-100 p-2 rounded bg-dark border border-secondary text-center" style="max-width: 250px;">
                    <small class="text-muted d-block mb-1">Prediksi Risiko Mendatang</small>
                    <div class="fs-5 fw-bold {{ $prediction['color'] }}">
                        <i class="fa-solid {{ $prediction['icon'] }} me-1"></i>
                        {{ $prediction['predicted_score'] }} ({{ $prediction['trend_label'] }})
                    </div>
                </div>
            </div>
        </div>

        <!-- Country Profile Details Card -->
        <div class="col-md-8">
            <div class="custom-card h-100">
                <div class="card-header-accent">
                    <span><i class="fa-solid fa-earth-asia text-primary me-2"></i>Profil Negara</span>
                    <span class="fi fi-{{ strtolower($selectedCode) }} fs-4" style="border-radius: 3px;"></span>
                </div>
                <div class="card-body-custom">
                    <div class="row g-4 mt-0">
                        <div class="col-md-6">
                            <div class="d-flex flex-column gap-2">
                                <div>
                                    <span class="text-muted small d-block mb-1">Nama Umum</span>
                                    <h6 class="text-white fw-bold mb-0">{{ $profile['name'] }}</h6>
                                </div>
                                <div class="mt-2">
                                    <span class="text-muted small d-block mb-1">Nama Resmi</span>
                                    <h6 class="text-white fw-bold mb-0">{{ $profile['official_name'] }}</h6>
                                </div>
                                <div class="mt-2">
                                    <span class="text-muted small d-block mb-1">Ibu Kota</span>
                                    <h6 class="text-white fw-bold mb-0">{{ $profile['capital'] }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column gap-2">
                                <div>
                                    <span class="text-muted small d-block mb-1">Wilayah / Subwilayah</span>
                                    <h6 class="text-white fw-bold mb-0">{{ $profile['region'] }} / {{ $profile['subregion'] }}</h6>
                                </div>
                                <div class="mt-2">
                                    <span class="text-muted small d-block mb-1">Mata Uang</span>
                                    <h6 class="text-white fw-bold mb-0">{{ $profile['currency_name'] }} ({{ $profile['currency_code'] }} - {{ $profile['currency_symbol'] }})</h6>
                                </div>
                                <div class="mt-2">
                                    <span class="text-muted small d-block mb-1">Populasi</span>
                                    <h6 class="text-white fw-bold mb-0">{{ number_format($profile['population']) }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4 Weighted Pillars breakdown -->
        <div class="col-12">
            <div class="row g-4">
                <!-- weather pillar -->
                <div class="col-md-3">
                    <div class="custom-card p-3 h-100 position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small"><i class="fa-solid fa-cloud-sun me-1"></i> Cuaca (30%)</span>
                            <span class="badge @if($risk['weather_score'] >= 70) bg-danger @elseif($risk['weather_score'] >= 35) bg-warning @else bg-success @endif bg-opacity-20 text-light border border-white border-opacity-10">{{ \App\Helpers\FormatHelper::decimal($risk['weather_score']) }}</span>
                        </div>
                        <h4 class="text-white fw-bold mb-1">{{ $weather['temperature'] }}°C</h4>
                        <p class="text-muted small mb-0">{{ $weather['description'] }}</p>
                        <div class="position-absolute" style="bottom: 8px; right: 12px;">
                            <small class="text-muted" style="font-size: 0.65rem;"><i class="fa-regular fa-clock me-1"></i>{{ $weather['last_update'] ?? 'Baru saja' }}</small>
                        </div>
                    </div>
                </div>
                <!-- economics pillar -->
                <div class="col-md-3">
                    <div class="custom-card p-3 h-100 position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small"><i class="fa-solid fa-sack-dollar me-1"></i> Ekonomi (20%)</span>
                            <span class="badge @if($risk['economic_score'] >= 70) bg-danger @elseif($risk['economic_score'] >= 35) bg-warning @else bg-success @endif bg-opacity-20 text-light border border-white border-opacity-10">{{ \App\Helpers\FormatHelper::decimal($risk['economic_score']) }}</span>
                        </div>
                        <h4 class="text-white fw-bold mb-1">{{ \App\Helpers\FormatHelper::percentage($economy['inflation']) }}</h4>
                        <p class="text-muted small mb-0">Inflasi Tahun {{ $economy['inflation_year'] }}</p>
                        <div class="position-absolute" style="bottom: 8px; right: 12px;">
                            <small class="text-muted" style="font-size: 0.65rem;"><i class="fa-regular fa-clock me-1"></i>{{ $economy['last_update'] ?? 'Baru saja' }}</small>
                        </div>
                    </div>
                </div>
                <!-- news pillar -->
                <div class="col-md-3">
                    <div class="custom-card p-3 h-100 position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small"><i class="fa-solid fa-newspaper me-1"></i> Sentimen (40%)</span>
                            <span class="badge @if($risk['news_score'] >= 70) bg-danger @elseif($risk['news_score'] >= 35) bg-warning @else bg-success @endif bg-opacity-20 text-light border border-white border-opacity-10">{{ \App\Helpers\FormatHelper::decimal($risk['news_score']) }}</span>
                        </div>
                        <h4 class="text-white fw-bold mb-1">{{ $news['negative_count'] }} Negatif</h4>
                        <p class="text-muted small mb-0">dari total {{ $news['total_count'] }} berita</p>
                        <div class="position-absolute" style="bottom: 8px; right: 12px;">
                            <small class="text-muted" style="font-size: 0.65rem;"><i class="fa-regular fa-clock me-1"></i>{{ $news['last_update'] ?? 'Baru saja' }}</small>
                        </div>
                    </div>
                </div>
                <!-- currency pillar -->
                <div class="col-md-3">
                    <div class="custom-card p-3 h-100 position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small"><i class="fa-solid fa-chart-line me-1"></i> Valuta (10%)</span>
                            <span class="badge @if($risk['currency_score'] >= 70) bg-danger @elseif($risk['currency_score'] >= 35) bg-warning @else bg-success @endif bg-opacity-20 text-light border border-white border-opacity-10">{{ \App\Helpers\FormatHelper::decimal($risk['currency_score']) }}</span>
                        </div>
                        <h5 class="text-white fw-bold mb-1 mt-3">{{ \App\Helpers\FormatHelper::currency($currency['rate_vs_usd'], $profile['currency_code']) }}</h5>
                        <div class="position-absolute" style="bottom: 8px; right: 12px;">
                            <small class="text-muted" style="font-size: 0.65rem;"><i class="fa-regular fa-clock me-1"></i>{{ $currency['last_update'] ?? 'Baru saja' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Currency Trend Chart -->
        <div class="col-lg-6">
            <div class="custom-card h-100">
                <div class="card-header-accent">
                    <span><i class="fa-solid fa-money-bill-trend-up text-primary me-2"></i>Tren Fluktuasi Nilai Tukar (7 Hari Terakhir)</span>
                    <span class="badge bg-secondary bg-opacity-15 text-white border-0">USD ke {{ $profile['currency_code'] }}</span>
                </div>
                <div class="card-body-custom">
                    <canvas id="currencyChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Economic statistics details -->
        <div class="col-lg-6">
            <div class="custom-card h-100">
                <div class="card-header-accent">
                    <span><i class="fa-solid fa-chart-simple text-primary me-2"></i>Rincian Ekonomi (Bank Dunia)</span>
                </div>
                <div class="card-body-custom">
                    <div class="d-flex flex-column gap-4 mt-2">
                        <div>
                            <span class="text-muted small d-block mb-1">Gross Domestic Product (GDP Nominal)</span>
                            <h5 class="text-white fw-bold mb-0">{{ \App\Helpers\FormatHelper::gdp($economy['gdp']) }} <span class="text-muted fs-6 fw-normal ms-1">(Tahun {{ $economy['gdp_year'] }})</span></h5>
                        </div>
                        <div>
                            <span class="text-muted small d-block mb-1">Tingkat Inflasi Tahunan</span>
                            <h5 class="text-white fw-bold mb-0">{{ \App\Helpers\FormatHelper::percentage($economy['inflation']) }} <span class="text-muted fs-6 fw-normal ms-1">(Tahun {{ $economy['inflation_year'] }})</span></h5>
                        </div>
                        <div>
                            <span class="text-muted small d-block mb-1">Kontribusi Ekspor Terhadap GDP</span>
                            <h5 class="text-white fw-bold mb-0">{{ \App\Helpers\FormatHelper::percentage($economy['exports_gdp_share']) }} <span class="text-muted fs-6 fw-normal ms-1">(Tahun {{ $economy['exports_year'] }})</span></h5>
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-white bg-opacity-5 rounded-3 border border-white border-opacity-5 text-muted" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-circle-info me-2 text-info"></i>
                        Data ekonomi di atas ditarik langsung secara periodik dari API Bank Dunia (World Bank API) dengan menggunakan cache 7 hari untuk efisiensi server.
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk Trend Chart -->
        <div class="col-12">
            <div class="custom-card h-100">
                <div class="card-header-accent">
                    <span><i class="fa-solid fa-chart-area text-warning me-2"></i>Histori Pergerakan Risiko (Risk Trend)</span>
                    <span class="badge bg-warning text-dark border-0">Total Skor Risiko</span>
                </div>
                <div class="card-body-custom">
                    <canvas id="riskChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- GDP Trend Chart -->
        <div class="col-lg-6">
            <div class="custom-card h-100">
                <div class="card-header-accent">
                    <span><i class="fa-solid fa-money-check-dollar text-success me-2"></i>Tren GDP (5 Tahun Terakhir)</span>
                    <span class="badge bg-success text-white border-0">Bank Dunia</span>
                </div>
                <div class="card-body-custom">
                    <canvas id="gdpChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Inflation Trend Chart -->
        <div class="col-lg-6">
            <div class="custom-card h-100">
                <div class="card-header-accent">
                    <span><i class="fa-solid fa-arrow-trend-up text-danger me-2"></i>Tren Inflasi (5 Tahun Terakhir)</span>
                    <span class="badge bg-danger text-white border-0">Bank Dunia</span>
                </div>
                <div class="card-body-custom">
                    <canvas id="inflationChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Expert Analysis Section -->
        <div class="col-12">
            <div class="custom-card">
                <div class="card-header-accent">
                    <span><i class="fa-solid fa-feather text-primary me-2"></i>Artikel Analisis Pakar (Admin)</span>
                    <span class="text-muted" style="font-size: 0.8rem;">Analisis mendalam mengenai risiko logistik & supply chain di {{ $profile['name'] }}</span>
                </div>
                <div class="card-body-custom">
                    @if($analysisArticles->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="fa-solid fa-feather fa-2x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-0">Belum ada artikel analisis pakar yang ditulis untuk {{ $profile['name'] }} saat ini.</p>
                        </div>
                    @else
                        <div class="row g-4">
                            @foreach($analysisArticles as $art)
                                <div class="col-12">
                                    <div class="p-4 rounded-3 text-white" style="background-color: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-color);">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="fw-bold mb-0 text-white">{{ $art->title }}</h5>
                                            <span class="text-muted small"><i class="fa-solid fa-calendar-day me-1"></i>{{ $art->created_at->format('d M Y H:i') }}</span>
                                        </div>
                                        <div class="mb-3 text-primary small">
                                            <i class="fa-solid fa-user-tie me-1"></i> Ditulis oleh: <strong>{{ $art->author ? $art->author->name : 'Administrator' }}</strong>
                                        </div>
                                        <p class="mb-0 text-muted" style="white-space: pre-line; line-height: 1.6; font-size: 0.92rem;">{{ $art->content }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- News Feed & Sentiment Analysis -->
        <div class="col-12">
            <div class="custom-card">
                <div class="card-header-accent d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fa-solid fa-newspaper text-primary me-2"></i>Umpan Berita Logistik & Sentimen Lexicon
                        <span class="badge ms-2" style="background-color: rgba(244, 63, 94, 0.15) !important; color: #fdf4ff !important; border: 1px solid rgba(244, 63, 94, 0.3) !important; font-size: 0.75rem;">Skor Risiko Sentimen: {{ $risk['news_score'] }}</span>
                    </div>
                    <a href="{{ route('country-assessment', ['code' => $selectedCode, 'sync' => 1]) }}" class="btn btn-sm btn-outline-primary py-1 px-3 rounded-3" style="font-size: 0.8rem;">
                        <i class="fa-solid fa-rotate me-1"></i> Sync Berita
                    </a>
                </div>
                <div class="card-body-custom">
                    @if(count($news['articles']) === 0)
                        <div class="text-center py-5 text-muted">
                            <i class="fa-regular fa-newspaper fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-3">Tidak ada berita yang relevan untuk negara ini saat ini.</p>
                            <a href="{{ route('country-assessment', ['code' => $selectedCode, 'sync' => 1]) }}" class="btn btn-sm btn-primary rounded-3">
                                <i class="fa-solid fa-rotate me-1"></i> Tarik Berita Baru
                            </a>
                        </div>
                    @else
                        <div class="row g-4">
                            @foreach($news['articles'] as $art)
                                <div class="col-md-6 col-lg-4">
                                    @php
                                        $cardBorder = 'rgba(255,255,255,0.05)';
                                        $badgeColor = 'bg-secondary';
                                        if ($art->sentiment === 'Positive') {
                                            $cardBorder = 'rgba(16, 185, 129, 0.3)';
                                            $badgeColor = 'bg-success';
                                        } elseif ($art->sentiment === 'Negative') {
                                            $cardBorder = 'rgba(239, 68, 68, 0.3)';
                                            $badgeColor = 'bg-danger';
                                        }
                                    @endphp
                                    <div class="card bg-black bg-opacity-35 text-white h-100 rounded-3 position-relative news-card-hover" style="border: 1px solid {{ $cardBorder }}; cursor: pointer;" onclick="window.open('{{ $art->url }}', '_blank')">
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                                <span class="badge {{ $badgeColor }} text-uppercase" style="font-size: 0.7rem; white-space: nowrap;">{{ $art->sentiment }} (Score: {{ $art->sentiment_score }})</span>
                                                <span class="small text-muted text-end" style="font-size: 0.75rem; line-height: 1.2; word-break: break-word; max-width: 60%;">
                                                    <i class="fa-solid fa-square-rss me-1 text-primary"></i>{{ $art->source_name }}
                                                </span>
                                            </div>
                                            <h6 class="card-title fw-bold text-white mb-2" style="font-size: 0.95rem; line-height: 1.4;">{{ $art->title }}</h6>
                                            <p class="card-text text-muted small flex-grow-1" style="font-size: 0.82rem; line-height: 1.5;">{{ Str::limit($art->description, 130) }}</p>
                                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top border-secondary border-opacity-25">
                                                <span class="small text-muted" style="font-size: 0.75rem;">{{ date('d M Y H:i', strtotime($art->published_at)) }}</span>
                                                <span class="btn btn-xs btn-outline-light py-1 px-2 rounded-3" style="font-size: 0.75rem;">
                                                    Baca <i class="fa-solid fa-arrow-up-right-from-square ms-1" style="font-size: 0.65rem;"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Setup CSRF header for AJAX requests
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Handle Watchlist Toggle
            const btnWatchlist = document.getElementById('btn-watchlist');
            if (btnWatchlist) {
                btnWatchlist.addEventListener('click', function () {
                    const countryCode = this.getAttribute('data-code');
                    
                    fetch('{{ route("watchlist.toggle") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ country_code: countryCode })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'added') {
                            btnWatchlist.classList.remove('btn-outline-warning');
                            btnWatchlist.classList.add('btn-warning');
                            document.getElementById('watchlist-text').innerText = 'Hapus dari Watchlist';
                            btnWatchlist.querySelector('i').classList.remove('fa-regular');
                            btnWatchlist.querySelector('i').classList.add('fa-solid');
                        } else {
                            btnWatchlist.classList.remove('btn-warning');
                            btnWatchlist.classList.add('btn-outline-warning');
                            document.getElementById('watchlist-text').innerText = 'Tambah ke Watchlist';
                            btnWatchlist.querySelector('i').classList.remove('fa-solid');
                            btnWatchlist.querySelector('i').classList.add('fa-regular');
                        }
                    });
                });
            }

            // Setup Chart.js currency trend
            const chartLabels = {!! json_encode($currency['trend']['labels']) !!};
            const chartRates = {!! json_encode($currency['trend']['rates']) !!};

            const ctx = document.getElementById('currencyChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Nilai Tukar vs USD',
                        data: chartRates,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#60a5fa',
                        pointBorderColor: '#ffffff',
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.05)'
                            },
                            ticks: {
                                color: '#94a3b8'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#94a3b8'
                            }
                        }
                    }
                }
            });

            // Setup Chart.js Risk Trend
            const riskLabels = {!! json_encode($assessment['risk_trend']['labels'] ?? []) !!};
            const riskData = {!! json_encode($assessment['risk_trend']['data'] ?? []) !!};
            
            // Append Prediction to chart
            riskLabels.push('Prediksi Besok');
            riskData.push({{ $assessment['prediction']['predicted_score'] ?? 0 }});
            
            new Chart(document.getElementById('riskChart').getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: riskLabels,
                        datasets: [{
                            label: 'Total Risk Score',
                            data: riskData,
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.3,
                            pointBackgroundColor: '#fbbf24',
                            pointBorderColor: '#ffffff',
                            pointRadius: 4,
                            segment: {
                                borderDash: ctx => ctx.p0DataIndex >= (riskData.length - 2) ? [6, 6] : undefined,
                                borderColor: ctx => ctx.p0DataIndex >= (riskData.length - 2) ? '#fbbf24' : undefined,
                            }
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { min: 0, max: 100, grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94a3b8' } },
                            x: { grid: { display: false }, ticks: { color: '#94a3b8', maxRotation: 45, minRotation: 45 } }
                        }
                    }
                });

            // Setup Chart.js GDP Trend
            const gdpLabels = {!! json_encode($economy['gdp_trend']['labels'] ?? []) !!};
            const gdpData = {!! json_encode($economy['gdp_trend']['data'] ?? []) !!};
            
            new Chart(document.getElementById('gdpChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: gdpLabels,
                        datasets: [{
                            label: 'GDP (USD)',
                            data: gdpData,
                            backgroundColor: 'rgba(16, 185, 129, 0.8)',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94a3b8' } },
                            x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                        }
                    }
                });

            // Setup Chart.js Inflation Trend
            const inflationLabels = {!! json_encode($economy['inflation_trend']['labels'] ?? []) !!};
            const inflationData = {!! json_encode($economy['inflation_trend']['data'] ?? []) !!};
            
            new Chart(document.getElementById('inflationChart').getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: inflationLabels,
                        datasets: [{
                            label: 'Inflation (%)',
                            data: inflationData,
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.3,
                            pointBackgroundColor: '#f87171',
                            pointBorderColor: '#ffffff',
                            pointRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94a3b8' } },
                            x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                        }
                    }
                });
        });
    </script>
@endsection
