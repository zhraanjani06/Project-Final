@extends('layouts.app')

@section('title', 'Admin Panel')
@section('page_title', 'Admin Panel')

@section('content')
    <style>
        .admin-tabs {
            border-bottom: 1px solid var(--border-color);
        }
        .admin-tabs .nav-link {
            color: var(--text-muted);
            border: none;
            border-bottom: 2px solid transparent;
            background: transparent;
            padding: 12px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            border-radius: 0;
        }
        .admin-tabs .nav-link:hover {
            color: #fff;
            border-bottom: 2px solid rgba(59, 130, 246, 0.4);
        }
        .admin-tabs .nav-link.active {
            color: #fff;
            background: transparent !important;
            border-bottom: 2px solid var(--primary-accent);
            font-weight: 600;
        }
    </style>

    <div class="row g-4">
        <!-- Main Admin Stats -->
        <div class="col-12">
            <div class="custom-card p-4">
                <h4 class="text-white fw-bold mb-1"><i class="fa-solid fa-user-shield text-primary me-2"></i>Panel Administrator</h4>
                <p class="text-muted mb-0">Kelola pengguna sistem, kamus kata sentimen (lexicon), lokasi pelabuhan, dan publikasikan artikel analisis risiko.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="col-12">
                <div class="alert alert-success border-0 bg-success bg-opacity-15 text-success rounded-3">
                    <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="col-12">
                <div class="alert alert-danger border-0 bg-danger bg-opacity-15 text-danger rounded-3">
                    <i class="fa-solid fa-circle-xmark me-2"></i>{{ session('error') }}
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="col-12">
                <div class="alert alert-danger border-0 bg-danger bg-opacity-15 text-danger rounded-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Tab Navigation -->
        <div class="col-12">
            <ul class="nav admin-tabs mb-4" id="adminTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if(!request()->has('ports_page') && !request()->has('articles_page')) active @endif" id="lexicon-tab" data-bs-toggle="tab" data-bs-target="#lexicon-content" type="button" role="tab" aria-controls="lexicon-content" aria-selected="@if(!request()->has('ports_page') && !request()->has('articles_page')) true @else false @endif">
                        <i class="fa-solid fa-book-open me-2"></i>Kamus & Pengguna
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if(request()->has('ports_page')) active @endif" id="ports-tab" data-bs-toggle="tab" data-bs-target="#ports-content" type="button" role="tab" aria-controls="ports-content" aria-selected="@if(request()->has('ports_page')) true @else false @endif">
                        <i class="fa-solid fa-anchor me-2"></i>Manajemen Pelabuhan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if(request()->has('articles_page')) active @endif" id="articles-tab" data-bs-toggle="tab" data-bs-target="#articles-content" type="button" role="tab" aria-controls="articles-content" aria-selected="@if(request()->has('articles_page')) true @else false @endif">
                        <i class="fa-solid fa-file-pen me-2"></i>Artikel Analisis
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="adminTabsContent">
                <!-- Tab: Lexicon & Users -->
                <div class="tab-pane fade @if(!request()->has('ports_page') && !request()->has('articles_page')) show active @endif" id="lexicon-content" role="tabpanel" aria-labelledby="lexicon-tab">
                    <div class="row g-4">
                        <!-- Users Management Table -->
                        <div class="col-lg-6">
                            <div class="custom-card h-100">
                                <div class="card-header-accent">
                                    <span><i class="fa-solid fa-users text-primary me-2"></i>Manajemen Pengguna</span>
                                </div>
                                <div class="card-body-custom">
                                    <div class="table-responsive">
                                        <table class="table table-dark table-striped align-middle mb-0" style="font-size: 0.9rem;">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>NAMA</th>
                                                    <th>EMAIL</th>
                                                    <th>HAK AKSES</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($users as $user)
                                                    <tr>
                                                        <td>{{ $user->id }}</td>
                                                        <td class="fw-semibold text-white">{{ $user->name }}</td>
                                                        <td>{{ $user->email }}</td>
                                                        <td>
                                                            <span class="badge @if($user->role === 'admin') bg-primary @else bg-secondary @endif bg-opacity-25 text-light border border-white border-opacity-10">
                                                                {{ strtoupper($user->role) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Add Lexicon Form -->
                        <div class="col-lg-6">
                            <div class="custom-card h-100">
                                <div class="card-header-accent">
                                    <span><i class="fa-solid fa-plus text-primary me-2"></i>Tambah Kata Lexicon Baru</span>
                                </div>
                                <div class="card-body-custom">
                                    <form action="{{ route('admin.lexicon.add') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="word" class="form-label">Kata Sentimen</label>
                                            <input type="text" name="word" id="word" class="form-control" placeholder="Contoh: shutdown, supply, success" required>
                                            <div class="form-text text-muted">Kata harus berupa huruf kecil tanpa spasi. Contoh kata logistik, cuaca, ekonomi.</div>
                                        </div>
                                        <div class="mb-4">
                                            <label for="type" class="form-label">Jenis Sentimen</label>
                                            <select name="type" id="type" class="form-select bg-dark border-secondary text-white rounded-3">
                                                <option value="positive">Positif (Meningkatkan skor stabil/mengurangi risiko)</option>
                                                <option value="negative">Negatif (Meningkatkan skor risiko)</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary rounded-3 w-100">
                                            <i class="fa-solid fa-floppy-disk me-2"></i>Simpan Kata
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Lexicon Positive List -->
                        <div class="col-lg-6">
                            <div class="custom-card">
                                <div class="card-header-accent">
                                    <span><i class="fa-solid fa-thumbs-up text-success me-2"></i>Kata Sentimen Positif</span>
                                </div>
                                <div class="card-body-custom">
                                    <div class="table-responsive mb-3">
                                        <table class="table table-dark table-striped align-middle mb-0" style="font-size: 0.9rem;">
                                            <thead>
                                                <tr>
                                                    <th>KATA</th>
                                                    <th class="text-end">AKSI</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($positives as $pos)
                                                    <tr>
                                                        <td class="fw-semibold text-success">{{ $pos->word }}</td>
                                                        <td class="text-end">
                                                            <form action="{{ route('admin.lexicon.delete', ['type' => 'positive', 'id' => $pos->id]) }}" method="POST" onsubmit="return confirm('Hapus kata ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-xs btn-outline-danger py-1 px-2 rounded-3">
                                                                    <i class="fa-solid fa-trash-can"></i>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div>
                                        {{ $positives->appends(['neg_page' => $negatives->currentPage(), 'ports_page' => $ports->currentPage(), 'articles_page' => $articles->currentPage()])->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lexicon Negative List -->
                        <div class="col-lg-6">
                            <div class="custom-card">
                                <div class="card-header-accent">
                                    <span><i class="fa-solid fa-thumbs-down text-danger me-2"></i>Kata Sentimen Negatif</span>
                                </div>
                                <div class="card-body-custom">
                                    <div class="table-responsive mb-3">
                                        <table class="table table-dark table-striped align-middle mb-0" style="font-size: 0.9rem;">
                                            <thead>
                                                <tr>
                                                    <th>KATA</th>
                                                    <th class="text-end">AKSI</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($negatives as $neg)
                                                    <tr>
                                                        <td class="fw-semibold text-danger">{{ $neg->word }}</td>
                                                        <td class="text-end">
                                                            <form action="{{ route('admin.lexicon.delete', ['type' => 'negative', 'id' => $neg->id]) }}" method="POST" onsubmit="return confirm('Hapus kata ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-xs btn-outline-danger py-1 px-2 rounded-3">
                                                                    <i class="fa-solid fa-trash-can"></i>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div>
                                        {{ $negatives->appends(['pos_page' => $positives->currentPage(), 'ports_page' => $ports->currentPage(), 'articles_page' => $articles->currentPage()])->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Port Management -->
                <div class="tab-pane fade @if(request()->has('ports_page')) show active @endif" id="ports-content" role="tabpanel" aria-labelledby="ports-tab">
                    <div class="row g-4">
                        <!-- Add Port Form -->
                        <div class="col-lg-4">
                            <div class="custom-card">
                                <div class="card-header-accent">
                                    <span><i class="fa-solid fa-anchor text-primary me-2"></i>Tambah Pelabuhan Baru</span>
                                </div>
                                <div class="card-body-custom">
                                    <form action="{{ route('admin.ports.add') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="port_name" class="form-label">Nama Pelabuhan</label>
                                            <input type="text" name="name" id="port_name" class="form-control" placeholder="Contoh: Port of Rotterdam" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="port_country" class="form-label">Negara</label>
                                            <select name="country_code" id="port_country" class="form-select bg-dark border-secondary text-white rounded-3" required>
                                                <option value="" disabled selected>Pilih Negara...</option>
                                                @foreach($countriesList as $c)
                                                    <option value="{{ $c->code }}">{{ $c->name }} ({{ $c->code }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="port_lat" class="form-label">Latitude</label>
                                            <input type="number" step="any" name="latitude" id="port_lat" class="form-control" placeholder="Contoh: 51.924" required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="port_lng" class="form-label">Longitude</label>
                                            <input type="number" step="any" name="longitude" id="port_lng" class="form-control" placeholder="Contoh: 4.477" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary rounded-3 w-100">
                                            <i class="fa-solid fa-floppy-disk me-2"></i>Simpan Pelabuhan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Ports List Table -->
                        <div class="col-lg-8">
                            <div class="custom-card">
                                <div class="card-header-accent">
                                    <span><i class="fa-solid fa-list text-primary me-2"></i>Daftar Pelabuhan Terdaftar</span>
                                </div>
                                <div class="card-body-custom">
                                    <div class="table-responsive mb-3">
                                        <table class="table table-dark table-striped align-middle mb-0" style="font-size: 0.9rem;">
                                            <thead>
                                                <tr>
                                                    <th>NAMA</th>
                                                    <th>NEGARA</th>
                                                    <th>LATITUDE</th>
                                                    <th>LONGITUDE</th>
                                                    <th class="text-end">AKSI</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($ports as $port)
                                                    <tr>
                                                        <td class="fw-semibold text-white">{{ $port->name }}</td>
                                                        <td>{{ $port->country_code }}</td>
                                                        <td>{{ number_format($port->latitude, 4) }}</td>
                                                        <td>{{ number_format($port->longitude, 4) }}</td>
                                                        <td class="text-end">
                                                            <form action="{{ route('admin.ports.delete', $port->id) }}" method="POST" onsubmit="return confirm('Hapus pelabuhan ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-xs btn-outline-danger py-1 px-2 rounded-3">
                                                                    <i class="fa-solid fa-trash-can"></i>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div>
                                        {{ $ports->appends(['pos_page' => $positives->currentPage(), 'neg_page' => $negatives->currentPage(), 'articles_page' => $articles->currentPage()])->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Artikel Analisis -->
                <div class="tab-pane fade @if(request()->has('articles_page')) show active @endif" id="articles-content" role="tabpanel" aria-labelledby="articles-tab">
                    <div class="row g-4">
                        <!-- Add Article Form -->
                        <div class="col-lg-4">
                            <div class="custom-card">
                                <div class="card-header-accent">
                                    <span><i class="fa-solid fa-feather text-primary me-2"></i>Tulis Artikel Analisis</span>
                                </div>
                                <div class="card-body-custom">
                                    <form action="{{ route('admin.articles.add') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="art_title" class="form-label">Judul Artikel</label>
                                            <input type="text" name="title" id="art_title" class="form-control" placeholder="Contoh: Analisis Kestabilan Selat Malaka" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="art_country" class="form-label">Negara Terkait (Opsional)</label>
                                            <select name="country_code" id="art_country" class="form-select bg-dark border-secondary text-white rounded-3">
                                                <option value="">Umum / Global (Tidak terikat negara tertentu)</option>
                                                @foreach($countriesList as $c)
                                                    <option value="{{ $c->code }}">{{ $c->name }} ({{ $c->code }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-4">
                                            <label for="art_content" class="form-label">Konten Artikel</label>
                                            <textarea name="content" id="art_content" rows="6" class="form-control" placeholder="Tulis konten analisis logistik dan risiko di sini..." required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary rounded-3 w-100">
                                            <i class="fa-solid fa-paper-plane me-2"></i>Terbitkan Artikel
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Articles List Table -->
                        <div class="col-lg-8">
                            <div class="custom-card">
                                <div class="card-header-accent">
                                    <span><i class="fa-solid fa-file-invoice text-primary me-2"></i>Daftar Artikel Diterbitkan</span>
                                </div>
                                <div class="card-body-custom">
                                    <div class="table-responsive mb-3">
                                        <table class="table table-dark table-striped align-middle mb-0" style="font-size: 0.9rem;">
                                            <thead>
                                                <tr>
                                                    <th>JUDUL</th>
                                                    <th>NEGARA</th>
                                                    <th>PENULIS</th>
                                                    <th>TANGGAL</th>
                                                    <th class="text-end">AKSI</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($articles->count() == 0)
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted py-4">Belum ada artikel analisis yang diterbitkan.</td>
                                                    </tr>
                                                @else
                                                    @foreach($articles as $art)
                                                        <tr>
                                                            <td class="fw-semibold text-white">{{ $art->title }}</td>
                                                            <td>
                                                                @if($art->country)
                                                                    <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-10">{{ $art->country->name }}</span>
                                                                @else
                                                                    <span class="badge bg-secondary bg-opacity-25 text-muted border border-secondary border-opacity-10">Global</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $art->author ? $art->author->name : 'N/A' }}</td>
                                                            <td>{{ $art->created_at->format('d M Y H:i') }}</td>
                                                            <td class="text-end">
                                                                <form action="{{ route('admin.articles.delete', $art->id) }}" method="POST" onsubmit="return confirm('Hapus artikel ini?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-xs btn-outline-danger py-1 px-2 rounded-3">
                                                                        <i class="fa-solid fa-trash-can"></i>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    <div>
                                        {{ $articles->appends(['pos_page' => $positives->currentPage(), 'neg_page' => $negatives->currentPage(), 'ports_page' => $ports->currentPage()])->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
