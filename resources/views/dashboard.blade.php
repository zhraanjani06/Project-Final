@extends('layouts.app')

@section('title', 'Dashboard Overview')
@section('page_title', 'Dashboard Overview')

@section('content')
    <div class="row g-4">
        <!-- Welcome Card -->
        <div class="col-12">
            <div class="custom-card p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="text-white fw-bold mb-2">Selamat Datang di SC Risk Intel!</h2>
                        <p class="text-muted mb-0">Platform Intelijen Risiko Rantai Pasok Global berbasis analisis data real-time, cuaca, fluktuasi mata uang, berita logistik, dan lokasi pelabuhan global.</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        @if($criticalRisksCount > 0)
                            <span class="badge badge-risk badge-risk-high py-2 px-3">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $criticalRisksCount }} Negara Risiko Tinggi
                            </span>
                        @else
                            <span class="badge badge-risk badge-risk-low py-2 px-3">
                                <i class="fa-solid fa-shield-halved me-1"></i> Semua Negara Stabil
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Cards -->
        <div class="col-md-4">
            <div class="custom-card">
                <div class="card-body-custom d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary me-4">
                        <i class="fa-solid fa-earth-asia fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 uppercase tracking-wider" style="font-size: 0.8rem; font-weight: 600; letter-spacing: 1px;">NEGARA TERPANTAU</h6>
                        <h3 class="text-white fw-bold mb-0">{{ $countriesCount }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="custom-card">
                <div class="card-body-custom d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success me-4">
                        <i class="fa-solid fa-anchor fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 uppercase tracking-wider" style="font-size: 0.8rem; font-weight: 600; letter-spacing: 1px;">PELABUHAN TERDAFTAR</h6>
                        <h3 class="text-white fw-bold mb-0">{{ $portsCount }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="custom-card">
                <div class="card-body-custom d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 p-3 rounded-3 text-danger me-4">
                        <i class="fa-solid fa-triangle-exclamation fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 uppercase tracking-wider" style="font-size: 0.8rem; font-weight: 600; letter-spacing: 1px;">RISIKO TINGGI (ALERTI)</h6>
                        <h3 class="text-white fw-bold mb-0">{{ $criticalRisksCount }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk Levels by Country & Latest Articles -->
        <div class="col-lg-8">
            <div class="custom-card h-100">
                <div class="card-header-accent">
                    <span><i class="fa-solid fa-ranking-star me-2 text-primary"></i>Daftar Risiko Negara Terpantau</span>
                    <span class="text-muted" style="font-size: 0.8rem;">Diurutkan berdasarkan Skor Risiko</span>
                </div>
                <div class="card-body-custom">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0" style="border-collapse: separate; border-spacing: 0 8px;">
                            <thead>
                                <tr class="text-muted text-nowrap" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                                    <th class="ps-3 fw-semibold pb-2 border-bottom border-secondary border-opacity-25">NEGARA</th>
                                    <th class="fw-semibold pb-2 border-bottom border-secondary border-opacity-25">WILAYAH</th>
                                    <th class="fw-semibold pb-2 border-bottom border-secondary border-opacity-25">SKOR RISIKO (W)</th>
                                    <th class="fw-semibold pb-2 border-bottom border-secondary border-opacity-25">TINGKAT RISIKO</th>
                                    <th class="text-end pe-3 fw-semibold pb-2 border-bottom border-secondary border-opacity-25">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($countries as $c)
                                    <tr style="background-color: rgba(255,255,255,0.02); border-radius: 8px;">
                                        <td class="py-3 ps-3 fw-semibold text-white">
                                            <span class="fi fi-{{ strtolower($c['code']) }} me-2 fs-5" style="border-radius: 3px;"></span>{{ $c['name'] }}
                                        </td>
                                        <td>{{ $c['region'] }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <strong class="me-2">{{ $c['score'] }}</strong>
                                                <div class="progress w-50 bg-secondary bg-opacity-25" style="height: 6px;">
                                                    <div class="progress-bar @if($c['score'] >= 70) bg-danger @elseif($c['score'] >= 35) bg-warning @else bg-success @endif" 
                                                         role="progressbar" style="width: {{ $c['score'] }}%" aria-valuenow="{{ $c['score'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($c['score'] >= 70)
                                                <span class="badge badge-risk badge-risk-high">High Risk</span>
                                            @elseif($c['score'] >= 35)
                                                <span class="badge badge-risk badge-risk-medium">Medium Risk</span>
                                            @else
                                                <span class="badge badge-risk badge-risk-low">Low Risk</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('country-assessment', ['code' => $c['code']]) }}" class="btn btn-sm btn-outline-primary rounded-3 px-3">
                                                <i class="fa-solid fa-eye me-1"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="custom-card h-100">
                <div class="card-header-accent">
                    <span><i class="fa-solid fa-feather me-2 text-primary"></i>Analisis Ahli Terbaru</span>
                </div>
                <div class="card-body-custom">
                    @if($latestArticles->isEmpty())
                        <div class="text-center text-muted py-5">
                            <i class="fa-solid fa-newspaper fa-3x mb-3 text-white text-opacity-15"></i>
                            <p class="mb-0">Belum ada artikel analisis terbaru.</p>
                        </div>
                    @else
                        <div class="d-flex flex-column gap-3">
                            @foreach($latestArticles as $art)
                                <div class="p-3 rounded-3" style="background-color: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-color);">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="text-white fw-bold mb-0" style="font-size: 0.95rem;">{{ $art->title }}</h6>
                                    </div>
                                    <div class="mb-2 mt-1">
                                        @if($art->country)
                                            <span class="badge border border-primary text-white rounded-pill px-2 py-1 fw-normal" style="background-color: rgba(13, 110, 253, 0.2); font-size: 0.7rem; letter-spacing: 0.3px;">
                                                <i class="fa-solid fa-earth-americas me-1 text-primary"></i>{{ $art->country->name }}
                                            </span>
                                        @else
                                            <span class="badge border border-secondary text-white rounded-pill px-2 py-1 fw-normal" style="background-color: rgba(108, 117, 125, 0.2); font-size: 0.7rem; letter-spacing: 0.3px;">
                                                <i class="fa-solid fa-globe me-1 text-secondary"></i>Global
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-muted mb-3" style="font-size: 0.82rem; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; line-height: 1.45;">
                                        {{ $art->content }}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
                                        <span class="text-primary"><i class="fa-solid fa-user me-1"></i>{{ $art->author ? $art->author->name : 'Admin' }}</span>
                                        <span class="text-muted"><i class="fa-solid fa-calendar-day me-1"></i>{{ $art->created_at->diffForHumans() }}</span>
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
