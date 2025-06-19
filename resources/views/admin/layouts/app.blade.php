<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - {{ config('app.name', 'Kedai Coffee Kiosk') }}</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <style>
        :root {
            --primary-color: #6a3412;
            --secondary-color: #a87d56;
            --dark-color: #212529;
            --light-color: #f8f9fa;
            --text-color: #333333;
            --light-text-color: #ffffff;
            --accent-color: #ffb74d;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            color: var(--text-color);
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(to bottom, var(--dark-color), #3a3a3a);
            color: white;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 250px;
            z-index: 100;
            transition: all 0.3s;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-item {
            margin-bottom: 5px;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            margin: 0 10px;
            padding: 0.8rem 1rem;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background-color: var(--primary-color);
            box-shadow: 0 4px 10px rgba(106, 52, 18, 0.3);
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
        }

        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color);
        }

        .page-header {
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            border-bottom: 1px solid #eee;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 25px;
            transition: all 0.3s;
        }

        .card:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }

        .card-header {
            font-weight: 600;
            border-bottom: none;
            padding: 1.25rem 1.5rem;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(106, 52, 18, 0.05);
        }

        .btn {
            border-radius: 8px;
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(106, 52, 18, 0.2);
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .badge {
            padding: 0.5em 0.8em;
            font-weight: 500;
        }

        .alert {
            border-radius: 10px;
            padding: 1rem;
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            .sidebar.active {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0;
            }
            .main-content.active {
                margin-left: 250px;
            }
            .toggle-sidebar {
                display: block;
            }
        }

        /* Toggle sidebar button */
        .toggle-sidebar {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 101;
            display: none;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            text-align: center;
            line-height: 40px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        /* Dashboard widgets */
        .dashboard-widget {
            padding: 1.5rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }

        .dashboard-widget:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .widget-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            display: inline-block;
            width: 70px;
            height: 70px;
            line-height: 70px;
            border-radius: 50%;
            background-color: var(--light-color);
            color: var(--primary-color);
        }

        .widget-primary {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .widget-primary .widget-icon {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .widget-info {
            background: linear-gradient(to right, #2193b0, #6dd5ed);
            color: white;
        }

        .widget-info .widget-icon {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .widget-success {
            background: linear-gradient(to right, #11998e, #38ef7d);
            color: white;
        }

        .widget-success .widget-icon {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .widget-warning {
            background: linear-gradient(to right, #f2994a, #f2c94c);
            color: white;
        }

        .widget-warning .widget-icon {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }
    </style>

    @yield('styles')
</head>
<body>
    <button class="toggle-sidebar" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            @php
                $adminSidebarTitle = DB::table('site_settings')->where('key', 'admin_sidebar_title')->value('value') ?? 'Kedai Coffee Kiosk';
                $adminSidebarSubtitle = DB::table('site_settings')->where('key', 'admin_sidebar_subtitle')->value('value') ?? 'Admin Panel';
            @endphp
            <h5 class="mb-0">{{ $adminSidebarTitle }}</h5>
            <div class="text-muted">{{ $adminSidebarSubtitle }}</div>
        </div>

        <div class="p-3">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.products.*') && !request()->routeIs('admin.products.images.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                        <i class="fas fa-mug-hot me-2"></i> Produk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.products.images.*') ? 'active' : '' }}" href="{{ route('admin.products.images.index') }}">
                        <i class="fas fa-images me-2"></i> Gambar Produk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                        <i class="fas fa-th-large me-2"></i> Kategori
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                        <i class="fas fa-shopping-cart me-2"></i>
                        <span>Pesanan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.changelog.*') ? 'active' : '' }}" href="{{ route('admin.changelog.index') }}">
                        <i class="fas fa-history me-2"></i>
                        <span>Changelog</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                        <i class="fas fa-cogs me-2"></i>
                        <span>Pengaturan</span>
                    </a>
                </li>
                <hr class="sidebar-divider d-none d-md-block">
                <li class="nav-item mt-4">
                    <a class="nav-link" href="{{ route('kiosk.index') }}" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i> Lihat Website
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main content -->
    <main class="main-content" id="mainContent">
        <div class="page-header d-flex justify-content-between align-items-center">
            <h2 class="h3 mb-0">@yield('title', 'Dashboard')</h2>
            <div>
                <span class="text-muted me-2">Halo, Admin</span>
                <a href="#" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('mainContent').classList.toggle('active');
        });

        // Auto-dismiss alerts after 5 seconds
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove();
            });
        }, 5000);
    </script>

    @yield('scripts')
</body>
</html>
