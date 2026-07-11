<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - SC Risk Intel</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Leaflet.js CSS for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    
    <style>
        :root {
            --bg-dark: #0f0717;
            --card-bg: rgba(30, 16, 45, 0.7);
            --sidebar-bg: #150a21;
            --border-color: rgba(236, 72, 153, 0.15);
            --primary-accent: #ec4899;
            --accent-glow: rgba(236, 72, 153, 0.25);
            --text-main: #fdf4ff;
            --text-muted: #d8b4fe;
            --success-color: #10b981;
            --danger-color: #f43f5e;
            --warning-color: #f59e0b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
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
            background: linear-gradient(135deg, #f472b6, #ec4899, #c084fc);
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
            background-color: rgba(236, 72, 153, 0.12);
        }

        .sidebar-item.active .sidebar-link {
            color: #fff;
            background-color: var(--primary-accent);
            box-shadow: 0 4px 15px rgba(236, 72, 153, 0.4);
        }

        .sidebar-link-logout {
            color: #f43f5e;
        }

        .sidebar-link-logout:hover {
            background-color: rgba(244, 63, 94, 0.1) !important;
            color: #f43f5e;
        }

        /* Form & Input controls */
        .form-control, .form-select, textarea {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(236, 72, 153, 0.3) !important;
            color: #fdf4ff !important;
        }
        .form-control::placeholder {
            color: rgba(216, 180, 254, 0.5) !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: #ec4899 !important;
            box-shadow: 0 0 0 0.25rem rgba(236, 72, 153, 0.25) !important;
            background-color: rgba(255, 255, 255, 0.08) !important;
            color: #fdf4ff !important;
        }
        .form-text {
            color: #d8b4fe !important;
            opacity: 0.8;
        }
        .form-label {
            color: #fdf4ff !important;
            font-weight: 500;
        }
        select option {
            background-color: #150a21 !important;
            color: #fdf4ff !important;
        }

        .table {
            color: #fdf4ff !important;
        }
        .table-striped>tbody>tr:nth-of-type(odd)>* {
            color: #fdf4ff !important;
        }
        .table-hover>tbody>tr:hover>* {
            color: #fff !important;
            background-color: rgba(236, 72, 153, 0.08) !important;
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        /* Top Navbar Styling */
        .top-navbar {
            height: 70px;
            background-color: rgba(15, 7, 23, 0.85);
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
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }

        .custom-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #f472b6, #ec4899, #c084fc);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .custom-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 22px 45px rgba(0, 0, 0, 0.35), 0 0 25px var(--accent-glow);
            border-color: rgba(236, 72, 153, 0.4);
        }

        .custom-card:hover::before {
            opacity: 1;
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

        /* Cute Gradient Buttons */
        .btn-primary, .btn-accent {
            background: linear-gradient(135deg, var(--primary-accent), #db2777) !important;
            border: none !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(236, 72, 153, 0.3) !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
            border-radius: 10px !important;
            padding: 8px 16px !important;
            font-weight: 600 !important;
        }
        .btn-primary:hover, .btn-accent:hover {
            background: linear-gradient(135deg, #f472b6, var(--primary-accent)) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 18px rgba(236, 72, 153, 0.5) !important;
        }
        .btn-primary:active, .btn-accent:active {
            transform: translateY(0px) !important;
        }
        .btn-outline-primary {
            color: var(--primary-accent) !important;
            border-color: rgba(236, 72, 153, 0.4) !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
            border-radius: 10px !important;
            padding: 8px 16px !important;
            font-weight: 600 !important;
            background-color: transparent !important;
        }
        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #f472b6, var(--primary-accent)) !important;
            color: #fff !important;
            border-color: transparent !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(236, 72, 153, 0.3) !important;
        }
        .btn-outline-primary:active {
            transform: translateY(0px) !important;
        }

        /* Clickable News Card Hover Effect */
        .news-card-hover {
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
            cursor: pointer;
        }
        .news-card-hover:hover {
            transform: translateY(-5px);
            background-color: rgba(255, 255, 255, 0.05) !important;
            border-color: rgba(236, 72, 153, 0.4) !important;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4), 0 0 15px var(--accent-glow) !important;
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
