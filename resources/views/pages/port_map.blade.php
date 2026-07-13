@extends('layouts.app')

@section('title', 'Port Map')
@section('page_title', 'Port Map')

@section('styles')
    <style>
        #port-map {
            height: 600px;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            z-index: 1;
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
        <!-- Filter Card -->
        <div class="col-12">
            <div class="custom-card p-4">
                <div class="row align-items-center g-3">
                    <div class="col-md-6">
                        <h4 class="text-white fw-bold mb-1"><i class="fa-solid fa-anchor text-primary me-2"></i>Peta Pelabuhan Logistik Global</h4>
                        <p class="text-muted mb-0">Temukan lokasi pelabuhan dan pantau kondisi cuaca lokal secara langsung.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="d-inline-block text-start" style="width: 280px;">
                            <label class="form-label text-muted mb-1" style="font-size: 0.8rem;">Filter Berdasarkan Negara</label>
                            <select id="country-filter" class="form-select bg-dark border-secondary text-white rounded-3">
                                <option value="all">Tampilkan Semua Negara</option>
                                @foreach($countries as $c)
                                    <option value="{{ $c->code }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map and Detail Column -->
        <div class="col-lg-8">
            <div class="custom-card">
                <div class="card-body-custom p-0">
                    <div id="port-map"></div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="custom-card h-100">
                <div class="card-header-accent">
                    <span><i class="fa-solid fa-circle-info text-primary me-2"></i>Informasi Pelabuhan</span>
                </div>
                <div class="card-body-custom" id="port-info-box">
                    <div class="text-center py-5 text-muted">
                        <i class="fa-solid fa-anchor fa-3x mb-3 text-secondary opacity-50"></i>
                        <p class="mb-0">Pilih penanda pelabuhan di peta untuk melihat detail koordinat dan data cuaca logistik saat ini.</p>
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
            const map = L.map('port-map').setView([10, 20], 2);
            let portMarkers = [];

            // Add dark mode map tiles
            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(map);

            // Fetch ports and plot them
            loadPorts('all');

            // Handle country filter change
            document.getElementById('country-filter').addEventListener('change', function (e) {
                loadPorts(e.target.value);
            });

            function loadPorts(countryCode) {
                // Clear existing markers
                portMarkers.forEach(marker => map.removeLayer(marker));
                portMarkers = [];

                let url = '/api/ports';
                if (countryCode !== 'all') {
                    url += `?country_code=${countryCode}`;
                }

                fetch(url)
                    .then(response => response.json())
                    .then(ports => {
                        if (ports.length === 0) {
                            return;
                        }

                        // Bounds array to fit map
                        const bounds = [];

                        ports.forEach(port => {
                            // Define coordinates
                            const pos = [port.latitude, port.longitude];
                            bounds.push(pos);

                            // Create blue anchor icon marker using FontAwesome markup
                            const anchorIcon = L.divIcon({
                                html: '<div class="text-primary fs-4"><i class="fa-solid fa-anchor"></i></div>',
                                iconSize: [24, 24],
                                iconAnchor: [12, 12],
                                className: 'custom-div-icon'
                            });

                            const marker = L.marker(pos, { icon: anchorIcon }).addTo(map);
                            marker.bindTooltip(`<strong>${port.name}</strong>`);
                            
                            marker.on('click', function () {
                                loadPortDetails(port);
                            });

                            portMarkers.push(marker);
                        });

                        // Fit map bounds if filter is set to single country
                        if (countryCode !== 'all' && bounds.length > 0) {
                            map.fitBounds(bounds, { maxZoom: 6, padding: [50, 50] });
                        }
                    });
            }

            function loadPortDetails(port) {
                const infoBox = document.getElementById('port-info-box');
                infoBox.innerHTML = `
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat data cuaca pelabuhan...</p>
                    </div>
                `;

                // Fetch current weather directly from Open-Meteo for these coordinates
                fetch(`https://api.open-meteo.com/v1/forecast?latitude=${port.latitude}&longitude=${port.longitude}&current_weather=true`)
                    .then(res => res.json())
                    .then(weatherData => {
                        const current = weatherData.current_weather;
                        const temp = current.temperature;
                        const wind = current.windspeed;
                        const code = current.weathercode;
                        const desc = getWmoDescription(code);

                        let weatherIcon = 'fa-sun text-warning';
                        if ([95, 96, 99].includes(code)) {
                            weatherIcon = 'fa-cloud-bolt text-danger';
                        } else if ([51, 53, 55, 61, 63, 65, 80, 81, 82].includes(code)) {
                            weatherIcon = 'fa-cloud-showers-heavy text-info';
                        } else if ([1, 2, 3].includes(code)) {
                            weatherIcon = 'fa-cloud text-secondary';
                        }

                        infoBox.innerHTML = `
                            <div class="mb-4">
                                <span class="badge mb-2" style="background-color: rgba(59, 130, 246, 0.15) !important; color: #f1f5f9 !important; border: 1px solid rgba(59, 130, 246, 0.3) !important; font-size: 0.75rem;"><i class="fa-solid fa-anchor me-1"></i> Pelabuhan</span>
                                <h4 class="text-white fw-bold mb-1">${port.name}</h4>
                                <span class="text-muted text-uppercase tracking-wider fs-7">Kordinat: ${port.latitude.toFixed(4)}, ${port.longitude.toFixed(4)}</span>
                            </div>

                            <h5 class="text-white fw-semibold mb-3 border-bottom pb-2" style="border-color: rgba(255,255,255,0.05) !important;">Cuaca Pelabuhan Saat Ini</h5>
                            
                            <div class="d-flex align-items-center justify-content-around bg-black bg-opacity-25 rounded-4 p-3 mb-4 border border-white border-opacity-5">
                                <div class="text-center">
                                    <i class="fa-solid ${weatherIcon} fa-3x mb-2"></i>
                                    <div class="small text-muted">${desc}</div>
                                </div>
                                <div class="text-center">
                                    <div class="fs-1 fw-bold text-white">${temp}°C</div>
                                    <div class="small text-muted">Suhu Pelabuhan</div>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <div class="p-3 bg-white bg-opacity-5 rounded-3 border border-white border-opacity-5 d-flex justify-content-between align-items-center">
                                        <span class="text-muted"><i class="fa-solid fa-wind me-1 text-primary"></i> Kecepatan Angin</span>
                                        <strong class="text-white">${wind} km/h</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info border-0 bg-info bg-opacity-10 text-info" style="font-size: 0.85rem;">
                                <i class="fa-solid fa-circle-info me-2"></i>
                                Kecepatan angin di atas 40 km/h atau cuaca badai petir dapat menunda aktivitas bongkar muat kapal kontainer di pelabuhan.
                            </div>
                        `;
                    })
                    .catch(err => {
                        infoBox.innerHTML = `
                            <div class="text-center py-5 text-danger">
                                <i class="fa-solid fa-triangle-exclamation fa-3x mb-3"></i>
                                <p class="mb-0">Gagal mengambil data cuaca pelabuhan.</p>
                            </div>
                        `;
                    });
            }

            function getWmoDescription(code) {
                const wmoCodes = {
                    0: 'Cerah (Clear Sky)',
                    1: 'Utamanya Cerah',
                    2: 'Berawan Sebagian',
                    3: 'Mendung (Overcast)',
                    45: 'Kabut',
                    48: 'Kabut Rime',
                    51: 'Gerimis Ringan',
                    53: 'Gerimis Sedang',
                    55: 'Gerimis Lebat',
                    61: 'Hujan Ringan',
                    63: 'Hujan Sedang',
                    65: 'Hujan Lebat',
                    80: 'Hujan Rintik-Rintik',
                    81: 'Hujan Sedang',
                    82: 'Hujan Badai Lokal',
                    95: 'Badai Guntur',
                    96: 'Badai Guntur Hujan Es',
                    99: 'Badai Guntur Hebat'
                };
                return wmoCodes[code] || 'Cuaca Normal';
            }
        });
    </script>
@endsection
