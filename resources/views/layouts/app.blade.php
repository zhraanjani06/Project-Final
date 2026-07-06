<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - SC Risk Intel</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Leaflet.js CSS for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    
    <style>
        :root {
            --bg-dark: #090d16;
            --card-bg: rgba(17, 24, 39, 0.7);
            --sidebar-bg: #0b111e;
            --border-color: rgba(255, 255, 255, 0.08);
            --primary-accent: #3b82f6;
            --accent-glow: rgba(59, 130, 246, 0.15);
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            padding: 24px;
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #60a5fa, #3b82f6, #9333ea);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
        }

        .sidebar-menu {
            padding: 20px 14px;
            list-style: none;
            margin: 0;
        }

        .sidebar-item {
            margin-bottom: 5px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .sidebar-link i {
            width: 24px;
            font-size: 1.1rem;
            margin-right: 12px;
        }

        .sidebar-link:hover, .sidebar-item.active .sidebar-link {
            color: #fff;
            background-color: rgba(59, 130, 246, 0.1);
        }

        .sidebar-item.active .sidebar-link {
            color: #fff;
            background-color: var(--primary-accent);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .sidebar-link-logout {
            color: #f87171;
        }

        .sidebar-link-logout:hover {
            background-color: rgba(239, 68, 68, 0.1) !important;
            color: #ef4444;
        }

        /* Top Navbar Styling */
        .top-navbar {
            height: 70px;
            background-color: rgba(9, 13, 22, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            right: 0;
            left: 260px;
            z-index: 999;
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: justify;
        }

        /* Main Content Container */
        .main-content {
            margin-left: 260px;
            padding: 100px 30px 30px 30px; /* offset top-navbar height */
            min-height: 100vh;
        }

        /* Card Custom Styling */
        .custom-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .custom-card:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25), 0 0 20px var(--accent-glow);
            border-color: rgba(59, 130, 246, 0.2);
        }

        .card-header-accent {
            border-bottom: 1px solid var(--border-color);
            padding: 20px 24px;
            font-weight: 700;
            font-size: 1.1rem;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-body-custom {
            padding: 24px;
        }

        /* Custom badge */
        .badge-risk {
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 8px;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .badge-risk-low {
            background-color: rgba(16, 185, 129, 0.15);
            color: var(--success-color);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-risk-medium {
            background-color: rgba(245, 158, 11, 0.15);
            color: var(--warning-color);
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .badge-risk-high {
            background-color: rgba(239, 68, 68, 0.15);
            color: var(--danger-color);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-dark);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-earth-americas me-2"></i>SC Risk Intel
        </div>
        <ul class="sidebar-menu">
            <li class="sidebar-item {{ Request::is('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" class="sidebar-link">
                    <i class="fa-solid fa-chart-line"></i> Dashboard
                </a>
            </li>
            <li class="sidebar-item {{ Request::is('weather-map') ? 'active' : '' }}">
                <a href="{{ route('weather-map') }}" class="sidebar-link">
                    <i class="fa-solid fa-cloud-sun"></i> Weather Map
                </a>
            </li>
            <li class="sidebar-item {{ Request::is('port-map') ? 'active' : '' }}">
                <a href="{{ route('port-map') }}" class="sidebar-link">
                    <i class="fa-solid fa-anchor"></i> Port Map
                </a>
            </li>
            <li class="sidebar-item {{ Request::is('country-assessment') ? 'active' : '' }}">
                <a href="{{ route('country-assessment') }}" class="sidebar-link">
                    <i class="fa-solid fa-newspaper"></i> Country Assessment
                </a>
            </li>
            <li class="sidebar-item {{ Request::is('compare') ? 'active' : '' }}">
                <a href="{{ route('compare') }}" class="sidebar-link">
                    <i class="fa-solid fa-scale-balanced"></i> Compare Countries
                </a>
            </li>
            <li class="sidebar-item {{ Request::is('watchlist') ? 'active' : '' }}">
                <a href="{{ route('watchlist') }}" class="sidebar-link">
                    <i class="fa-regular fa-star"></i> Watchlist
                </a>
            </li>
            @if(Auth::user()->role === 'admin')
                <li class="sidebar-item {{ Request::is('admin') ? 'active' : '' }}">
                    <a href="{{ route('admin.panel') }}" class="sidebar-link">
                        <i class="fa-solid fa-user-shield"></i> Admin Panel
                    </a>
                </li>
            @endif
            
            <li class="sidebar-item mt-4 pt-4 border-top" style="border-color: var(--border-color) !important;">
                <form action="{{ route('logout') }}" method="POST" id="logout-form" class="d-none">
                    @csrf
                </form>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="sidebar-link sidebar-link-logout">
                    <i class="fa-solid fa-right-from-bracket"></i> Keluar
                </a>
            </li>
        </ul>
    </div>

    <!-- Top Navbar -->
    <div class="top-navbar d-flex align-items-center justify-content-between">
        <div>
            <h5 class="mb-0 text-white font-weight-700">@yield('page_title', 'Dashboard Overview')</h5>
        </div>
        <div class="d-flex align-items-center">
            <span class="me-3 text-muted">Halo, <strong class="text-white">{{ Auth::user()->name }}</strong> ({{ ucfirst(Auth::user()->role) }})</span>
            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px; font-weight: 700;">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <!-- Bootstrap 5 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js for Data Visualizations -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Leaflet.js for Geospatial Maps -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    @yield('scripts')
</body>
</html>
