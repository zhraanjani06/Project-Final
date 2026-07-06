@extends('layouts.app')

@section('title', 'My Watchlist')
@section('page_title', 'My Watchlist')

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="custom-card p-4">
                <h4 class="text-white fw-bold mb-1"><i class="fa-regular fa-star text-warning me-2"></i>Daftar Pantauan Anda (Watchlist)</h4>
                <p class="text-muted mb-0">Pantau secara cepat negara-negara prioritas dalam rantai pasok Anda.</p>
            </div>
        </div>

        <div class="col-12">
            <div class="custom-card">
                <div class="card-body-custom">
                    @if(count($watchlist) === 0)
                        <div class="text-center py-5 text-muted">
                            <i class="fa-regular fa-star fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-0">Watchlist Anda masih kosong. Tambahkan negara melalui menu Asesmen Negara.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-dark table-hover align-middle mb-0">
                                <thead>
                                    <tr class="text-muted" style="font-size: 0.85rem; border-bottom: 1px solid var(--border-color);">
                                        <th class="ps-3 pb-3">NEGARA</th>
                                        <th class="pb-3">WILAYAH</th>
                                        <th class="pb-3">TOTAL RISIKO</th>
                                        <th class="pb-3">CUACA SAAT INI</th>
                                        <th class="pb-3">MATA UANG</th>
                                        <th class="text-end pe-3 pb-3">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($watchlist as $item)
                                        @php
                                            $flagEmojis = ['ID'=>'🇮🇩','US'=>'🇺🇸','SG'=>'🇸🇬','CN'=>'🇨🇳','DE'=>'🇩🇪','AU'=>'🇦🇺','GB'=>'🇬🇧','JP'=>'🇯🇵','BR'=>'🇧🇷','IN'=>'🇮🇳','NL'=>'🇳🇱','AE'=>'🇦🇪'];
                                            $emoji = $flagEmojis[$item['code']] ?? '🌐';
                                        @endphp
                                        <tr id="row-{{ $item['code'] }}" style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                                            <td class="py-3 ps-3 fw-semibold text-white">
                                                <span class="me-2 fs-5">{{ $emoji }}</span>{{ $item['name'] }}
                                            </td>
                                            <td>{{ $item['region'] }}</td>
                                            <td>
                                                @php
                                                    $badgeClass = 'badge-risk-low';
                                                    if ($item['score'] >= 70) {
                                                        $badgeClass = 'badge-risk-high';
                                                    } elseif ($item['score'] >= 35) {
                                                        $badgeClass = 'badge-risk-medium';
                                                    }
                                                @endphp
                                                <span class="badge badge-risk {{ $badgeClass }}">{{ $item['score'] }} / 100</span>
                                            </td>
                                            <td>
                                                @if(isset($item['weather']['temperature']))
                                                    <i class="fa-solid fa-cloud-sun text-warning me-1"></i> {{ $item['weather']['temperature'] }}°C, {{ $item['weather']['description'] }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                1 USD = {{ number_format($item['currency']['rate_vs_usd'] ?? 1.0, 2) }} {{ $item['currency_code'] }}
                                            </td>
                                            <td class="text-end pe-3">
                                                <a href="{{ route('country-assessment', ['code' => $item['code']]) }}" class="btn btn-sm btn-outline-primary rounded-3 px-3 me-2">
                                                    <i class="fa-solid fa-eye me-1"></i> Detail
                                                </a>
                                                <button class="btn btn-sm btn-outline-danger rounded-3 px-3 btn-remove-watchlist" data-code="{{ $item['code'] }}">
                                                    <i class="fa-solid fa-trash-can me-1"></i> Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Handle Watchlist Removal
            const removeButtons = document.querySelectorAll('.btn-remove-watchlist');
            removeButtons.forEach(button => {
                button.addEventListener('click', function () {
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
                        if (data.status === 'removed') {
                            // Fade out row smoothly
                            const row = document.getElementById(`row-${countryCode}`);
                            row.style.transition = 'all 0.4s ease';
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(-20px)';
                            setTimeout(() => {
                                row.remove();
                                // If no rows left, refresh page to show empty state
                                if (document.querySelectorAll('tbody tr').length === 0) {
                                    window.location.reload();
                                }
                            }, 400);
                        }
                    });
                });
            });
        });
    </script>
@endsection
