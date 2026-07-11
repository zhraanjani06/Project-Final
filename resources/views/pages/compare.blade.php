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
                        <table class="table table-dark table-striped align-middle mb-0" style="font-size: 0.9rem;">
                            <thead>
                                <tr class="text-muted" style="border-bottom: 1px solid var(--border-color);">
                                    <th>INDIKATOR</th>
                                    <th>{{ $emojiA }} {{ $dataA['profile']['name'] }}</th>
                                    <th>{{ $emojiB }} {{ $dataB['profile']['name'] }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-muted">Mata Uang</td>
                                    <td class="fw-semibold">{{ $dataA['profile']['currency_code'] }}</td>
                                    <td class="fw-semibold">{{ $dataB['profile']['currency_code'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Populasi</td>
                                    <td>{{ number_format($dataA['profile']['population']) }}</td>
                                    <td>{{ number_format($dataB['profile']['population']) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">GDP (Bank Dunia)</td>
                                    <td>${{ number_format($dataA['economy']['gdp'] / 1e9, 2) }} Milyar</td>
                                    <td>${{ number_format($dataB['economy']['gdp'] / 1e9, 2) }} Milyar</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tingkat Inflasi</td>
                                    <td>{{ $dataA['economy']['inflation'] }}%</td>
                                    <td>{{ $dataB['economy']['inflation'] }}%</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Suhu & Cuaca</td>
                                    <td>{{ $dataA['weather']['temperature'] }}°C ({{ $dataA['weather']['description'] }})</td>
                                    <td>{{ $dataB['weather']['temperature'] }}°C ({{ $dataB['weather']['description'] }})</td>
                                </tr>
                                <tr class="border-top" style="border-color: rgba(255,255,255,0.08) !important;">
                                    <td class="text-muted fw-bold">Skor Risiko Cuaca (30%)</td>
                                    <td class="fw-bold text-light">{{ $dataA['risk']['weather_score'] }}</td>
                                    <td class="fw-bold text-light">{{ $dataB['risk']['weather_score'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-bold">Skor Risiko Ekonomi (20%)</td>
                                    <td class="fw-bold text-light">{{ $dataA['risk']['economic_score'] }}</td>
                                    <td class="fw-bold text-light">{{ $dataB['risk']['economic_score'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-bold">Skor Risiko Sentimen (40%)</td>
                                    <td class="fw-bold text-light">{{ $dataA['risk']['news_score'] }}</td>
                                    <td class="fw-bold text-light">{{ $dataB['risk']['news_score'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-bold">Skor Risiko Valuta (10%)</td>
                                    <td class="fw-bold text-light">{{ $dataA['risk']['currency_score'] }}</td>
                                    <td class="fw-bold text-light">{{ $dataB['risk']['currency_score'] }}</td>
                                </tr>
                                <tr style="background: rgba(236, 72, 153, 0.15) !important; border-top: 2px solid var(--primary-accent) !important;">
                                    <td class="fw-bold" style="color: #fdf4ff !important;">TOTAL SKOR RISIKO</td>
                                    <td class="fw-bold text-white fs-5">{{ $dataA['risk']['total_score'] }} / 100</td>
                                    <td class="fw-bold text-white fs-5">{{ $dataB['risk']['total_score'] }} / 100</td>
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
