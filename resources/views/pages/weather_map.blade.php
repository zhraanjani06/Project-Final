@extends('layouts.app')

@section('title', 'Weather Risk Map')
@section('page_title', 'Weather Risk Map')

@section('styles')
    <style>
        #weather-map {
            height: 600px;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .weather-popup {
            background-color: var(--sidebar-bg);
            color: var(--text-main);
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
        }
        .leaflet-popup-content-wrapper, .leaflet-popup-tip {
            background: #0f172a !important;
            color: #f1f5f9 !important;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
        }
    </style>
@endsection

@section('content')
    <div class="row g-4">
        <!-- Map view -->
        <div class="col-lg-8">
            <div class="custom-card">
                <div class="card-header-accent">
                    <span><i class="fa-solid fa-map-location-dot text-primary me-2"></i>Peta Risiko Cuaca Global</span>
                    <span class="badge bg-primary bg-opacity-20 text-primary border border-primary border-opacity-30">Live Open-Meteo</span>
                </div>
                <div class="card-body-custom p-0">
                    <div id="weather-map"></div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="custom-card h-100">
                <div class="card-header-accent">
                    <span><i class="fa-solid fa-cloud-showers-heavy text-primary me-2"></i>Detail Informasi Cuaca</span>
                </div>
                <div class="card-body-custom" id="weather-info-box">
                    <div class="text-center py-5 text-muted">
                        <i class="fa-solid fa-location-crosshairs fa-3x mb-3 text-secondary opacity-50"></i>
                        <p class="mb-0">Pilih penanda negara di peta untuk melihat laporan cuaca real-time dan analisis risikonya.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Leaflet map
            // Center map roughly on the equator
            const map = L.map('weather-map').setView([10, 20], 2);

            // Add dark mode map tiles from CartoDB (perfect fit for our dark premium theme!)
            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(map);

            // Load countries from REST API
            fetch('/api/countries')
                .then(response => response.json())
                .then(countries => {
                    countries.forEach(country => {
                        // Determine marker color based on risk score
                        let markerColor = '#10b981'; // Green (Low Risk)
                        if (country.risk_score >= 70) {
                            markerColor = '#ef4444'; // Red (High Risk)
                        } else if (country.risk_score >= 35) {
                            markerColor = '#f59e0b'; // Orange (Medium Risk)
                        }

                        // Add circle marker
                        const marker = L.circleMarker([country.latitude, country.longitude], {
                            radius: 12,
                            fillColor: markerColor,
                            color: '#ffffff',
                            weight: 1.5,
                            opacity: 1,
                            fillOpacity: 0.8
                        }).addTo(map);

                        marker.bindTooltip(`<strong>${country.name}</strong><br>Skor Risiko: ${country.risk_score || 'N/A'}`);

                        // Add click listener
                        marker.on('click', function () {
                            loadWeatherDetails(country.code);
                        });
                    });
                });

            function loadWeatherDetails(code) {
                const infoBox = document.getElementById('weather-info-box');
                infoBox.innerHTML = `
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Mengambil data cuaca...</p>
                    </div>
                `;

                fetch(`/api/countries/${code}`)
                    .then(response => response.json())
                    .then(data => {
                        const weather = data.weather;
                        const risk = data.risk;
                        const country = data.country;
                        
                        let weatherIcon = 'fa-sun text-warning';
                        if (weather.stormy) {
                            weatherIcon = 'fa-cloud-bolt text-danger';
                        } else if (weather.rain) {
                            weatherIcon = 'fa-cloud-showers-heavy text-info';
                        } else if (weather.temperature < 15) {
                            weatherIcon = 'fa-snowflake text-primary';
                        } else if (weather.weather_code >= 1 && weather.weather_code <= 3) {
                            weatherIcon = 'fa-cloud text-secondary';
                        }

                        let badgeClass = 'badge-risk-low';
                        if (risk.weather_score >= 70) {
                            badgeClass = 'badge-risk-high';
                        } else if (risk.weather_score >= 35) {
                            badgeClass = 'badge-risk-medium';
                        }

                        infoBox.innerHTML = `
                            <div class="text-center mb-4">
                                <h4 class="text-white fw-bold mb-1">${country.name}</h4>
                                <span class="text-muted text-uppercase tracking-wider fs-7" style="font-size: 0.75rem;">Kode ISO: ${country.code}</span>
                            </div>

                            <div class="d-flex align-items-center justify-content-around bg-black bg-opacity-25 rounded-4 p-3 mb-4 border border-white border-opacity-5">
                                <div class="text-center">
                                    <i class="fa-solid ${weatherIcon} fa-3x mb-2"></i>
                                    <div class="small text-muted">${weather.description}</div>
                                </div>
                                <div class="text-center">
                                    <div class="fs-1 fw-bold text-white">${weather.temperature}°C</div>
                                    <div class="small text-muted">Suhu Saat Ini</div>
                                </div>
                            </div>

                            <h5 class="text-white fw-semibold mb-3 border-bottom pb-2" style="border-color: rgba(255,255,255,0.05) !important;">Parameter Cuaca</h5>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <div class="p-3 bg-white bg-opacity-5 rounded-3 border border-white border-opacity-5">
                                        <div class="small text-muted mb-1"><i class="fa-solid fa-wind me-1 text-primary"></i> Kecepatan Angin</div>
                                        <strong class="text-white">${weather.wind_speed} km/h</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-white bg-opacity-5 rounded-3 border border-white border-opacity-5">
                                        <div class="small text-muted mb-1"><i class="fa-solid fa-droplet me-1 text-primary"></i> Curah Hujan</div>
                                        <strong class="text-white">${weather.rain ? 'Ya' : 'Tidak'}</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="p-3 rounded-4 border" style="background-color: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.05);">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted font-weight-500">Skor Risiko Cuaca:</span>
                                    <span class="badge badge-risk ${badgeClass}">${risk.weather_score} / 100</span>
                                </div>
                                <p class="small text-muted mb-0">
                                    Skor risiko cuaca didasarkan pada tingkat badai guntur (stormy) atau intensitas curah hujan tinggi yang berpotensi menghambat logistik pengiriman.
                                </p>
                            </div>

                            <div class="mt-4 text-center">
                                <a href="/country-assessment?code=${country.code}" class="btn btn-primary rounded-3 w-100">
                                    <i class="fa-solid fa-circle-info me-2"></i>Lihat Asesmen Lengkap
                                </a>
                            </div>
                        `;
                    });
            }
        });
    </script>
@endsection
