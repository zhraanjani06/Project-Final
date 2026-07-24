@extends('layouts.app')

@section('title', 'Country Risk Comparison')
@section('page_title', 'Country Risk Comparison')

@section('content')
    <div class="row g-4">
        <!-- Selector Form -->
        <div class="col-12">
            <div class="custom-card p-4">
                <form action="{{ route('compare') }}" method="GET">
                    <div class="row align-items-center g-3">
                        <div class="col-md-5">
                            <label class="form-label text-muted mb-1" style="font-size: 0.8rem;">Negara Pertama (A)</label>
                            <select name="country_a" class="form-select bg-dark border-secondary text-white rounded-3">
                                @foreach($countries as $c)
                                    <option value="{{ $c->code }}" {{ $c->code == $countryA ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 text-center mt-4">
                            <div class="d-inline-block bg-primary bg-opacity-15 text-primary rounded-circle p-2" style="width: 40px; height: 40px; line-height: 24px; font-weight: 800;">VS</div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label text-muted mb-1" style="font-size: 0.8rem;">Negara Kedua (B)</label>
                            <select name="country_b" class="form-select bg-dark border-secondary text-white rounded-3">
                                @foreach($countries as $c)
                                    <option value="{{ $c->code }}" {{ $c->code == $countryB ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-primary rounded-3 px-5">
                                <i class="fa-solid fa-scale-balanced me-2"></i>Bandingkan Sekarang
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @php
            $flagEmojis = ['ID'=>'🇮🇩','US'=>'🇺🇸','SG'=>'🇸🇬','CN'=>'🇨🇳','DE'=>'🇩🇪','AU'=>'🇦🇺','GB'=>'🇬🇧','JP'=>'🇯🇵','BR'=>'🇧🇷','IN'=>'🇮🇳','NL'=>'🇳🇱','AE'=>'🇦🇪'];
            $emojiA = $flagEmojis[$countryA] ?? '🌐';
            $emojiB = $flagEmojis[$countryB] ?? '🌐';
        @endphp

        <!-- Visual Risk Chart Comparison -->
        <div class="col-lg-6">
            <div class="custom-card h-100">
                <div class="card-header-accent">
                    <span><i class="fa-solid fa-chart-column text-primary me-2"></i>Perbandingan Skor 4 Pilar Risiko</span>
                </div>
                <div class="card-body-custom">
                    <canvas id="comparisonChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Comparative Metrics Table -->
        <div class="col-lg-6">
            <div class="custom-card h-100">
                <div class="card-header-accent">
                    <span><i class="fa-solid fa-table-list text-primary me-2"></i>Tabel Perbandingan Risiko</span>
                </div>
                <div class="card-body-custom">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0" style="font-size: 0.95rem; --bs-table-bg: transparent; --bs-table-color: #f8f9fa;">
                            <thead>
                                <tr style="border-bottom: 2px solid rgba(255,255,255,0.1);">
                                    <th class="text-uppercase text-muted tracking-wider fw-bold py-3">INDIKATOR</th>
                                    <th class="py-3"><span class="fi fi-{{ strtolower($countryA) }} me-2" style="border-radius: 3px;"></span> {{ $dataA['profile']['name'] }}</th>
                                    <th class="py-3"><span class="fi fi-{{ strtolower($countryB) }} me-2" style="border-radius: 3px;"></span> {{ $dataB['profile']['name'] }}</th>
                                </tr>
                            </thead>
                            <tbody style="border-top: none;">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                    <td class="text-muted py-3"><i class="fa-solid fa-money-bill-wave me-2 opacity-50"></i>Mata Uang</td>
                                    <td class="fw-semibold py-3">{{ $dataA['profile']['currency_code'] }}</td>
                                    <td class="fw-semibold py-3">{{ $dataB['profile']['currency_code'] }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                    <td class="text-muted py-3"><i class="fa-solid fa-users me-2 opacity-50"></i>Populasi</td>
                                    <td class="py-3">{{ number_format($dataA['profile']['population']) }}</td>
                                    <td class="py-3">{{ number_format($dataB['profile']['population']) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                    <td class="text-muted py-3"><i class="fa-solid fa-building-columns me-2 opacity-50"></i>GDP (Bank Dunia)</td>
                                    <td class="py-3">{{ \App\Helpers\FormatHelper::gdp($dataA['economy']['gdp']) }}</td>
                                    <td class="py-3">{{ \App\Helpers\FormatHelper::gdp($dataB['economy']['gdp']) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                    <td class="text-muted py-3"><i class="fa-solid fa-arrow-trend-up me-2 opacity-50"></i>Tingkat Inflasi</td>
                                    <td class="py-3">{{ \App\Helpers\FormatHelper::percentage($dataA['economy']['inflation']) }}</td>
                                    <td class="py-3">{{ \App\Helpers\FormatHelper::percentage($dataB['economy']['inflation']) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                    <td class="text-muted py-3"><i class="fa-solid fa-cloud-sun me-2 opacity-50"></i>Suhu & Cuaca</td>
                                    <td class="py-3">{{ $dataA['weather']['temperature'] }}°C <span class="text-muted small">({{ $dataA['weather']['description'] }})</span></td>
                                    <td class="py-3">{{ $dataB['weather']['temperature'] }}°C <span class="text-muted small">({{ $dataB['weather']['description'] }})</span></td>
                                </tr>
                                
                                <!-- Risk Scores Section -->
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); background: rgba(255,255,255,0.02);">
                                    <td class="text-light fw-bold py-3"><i class="fa-solid fa-cloud-sun text-warning me-2"></i>Skor Risiko Cuaca (30%)</td>
                                    <td class="fw-bold text-white py-3">{{ \App\Helpers\FormatHelper::decimal($dataA['risk']['weather_score']) }}</td>
                                    <td class="fw-bold text-white py-3">{{ \App\Helpers\FormatHelper::decimal($dataB['risk']['weather_score']) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); background: rgba(255,255,255,0.02);">
                                    <td class="text-light fw-bold py-3"><i class="fa-solid fa-sack-dollar text-warning me-2"></i>Skor Risiko Ekonomi (20%)</td>
                                    <td class="fw-bold text-white py-3">{{ \App\Helpers\FormatHelper::decimal($dataA['risk']['economic_score']) }}</td>
                                    <td class="fw-bold text-white py-3">{{ \App\Helpers\FormatHelper::decimal($dataB['risk']['economic_score']) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); background: rgba(255,255,255,0.02);">
                                    <td class="text-light fw-bold py-3"><i class="fa-solid fa-newspaper text-warning me-2"></i>Skor Risiko Sentimen (40%)</td>
                                    <td class="fw-bold text-white py-3">{{ \App\Helpers\FormatHelper::decimal($dataA['risk']['news_score']) }}</td>
                                    <td class="fw-bold text-white py-3">{{ \App\Helpers\FormatHelper::decimal($dataB['risk']['news_score']) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); background: rgba(255,255,255,0.02);">
                                    <td class="text-light fw-bold py-3"><i class="fa-solid fa-chart-line text-warning me-2"></i>Skor Risiko Valuta (10%)</td>
                                    <td class="fw-bold text-white py-3">{{ \App\Helpers\FormatHelper::decimal($dataA['risk']['currency_score']) }}</td>
                                    <td class="fw-bold text-white py-3">{{ \App\Helpers\FormatHelper::decimal($dataB['risk']['currency_score']) }}</td>
                                </tr>
                                
                                <!-- Total Score -->
                                <tr style="background: linear-gradient(90deg, rgba(236,72,153,0.15) 0%, rgba(139,92,246,0.15) 100%); border-top: 2px solid var(--primary-accent);">
                                    <td class="fw-bold py-4 text-uppercase tracking-wider" style="color: #fdf4ff;">TOTAL SKOR RISIKO</td>
                                    <td class="fw-extrabold text-white fs-4 py-4">{{ \App\Helpers\FormatHelper::decimal($dataA['risk']['total_score']) }} <span class="fs-6 text-white-50 fw-normal">/ 100</span></td>
                                    <td class="fw-extrabold text-white fs-4 py-4">{{ \App\Helpers\FormatHelper::decimal($dataB['risk']['total_score']) }} <span class="fs-6 text-white-50 fw-normal">/ 100</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Setup Chart.js Bar Comparison Chart
            const labelA = "{{ $dataA['profile']['name'] }}";
            const labelB = "{{ $dataB['profile']['name'] }}";
            
            const scoresA = [
                {{ $dataA['risk']['weather_score'] }},
                {{ $dataA['risk']['economic_score'] }},
                {{ $dataA['risk']['news_score'] }},
                {{ $dataA['risk']['currency_score'] }}
            ];

            const scoresB = [
                {{ $dataB['risk']['weather_score'] }},
                {{ $dataB['risk']['economic_score'] }},
                {{ $dataB['risk']['news_score'] }},
                {{ $dataB['risk']['currency_score'] }}
            ];

            const ctx = document.getElementById('comparisonChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Risiko Cuaca', 'Risiko Ekonomi', 'Risiko Sentimen', 'Risiko Valuta'],
                    datasets: [
                        {
                            label: labelA,
                            data: scoresA,
                            backgroundColor: 'rgba(236, 72, 153, 0.65)',
                            borderColor: '#ec4899',
                            borderWidth: 2,
                            borderRadius: 6
                        },
                        {
                            label: labelB,
                            data: scoresB,
                            backgroundColor: 'rgba(192, 132, 252, 0.65)',
                            borderColor: '#c084fc',
                            borderWidth: 2,
                            borderRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
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
                    },
                    plugins: {
                        legend: {
                            labels: {
                                color: '#f1f5f9'
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
