<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Decasa Admin')</title>
    {{-- STYLESHEETS --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/leaflet.css') }}">
    {{-- CUSTOM STYLES --}}
    <style>
        :root {
            --primary: #697565; 
            --primary-dark: #4E634D;
            --secondary: #DAA520;
            --bg-light: #F4F6F4;
            --sidebar-width: 250px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            overflow-x: hidden;
        }

        .btn, .card, .form-control, .nav-link, .badge, .alert, img {
            border-radius: 0 !important;
        }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: white;
            border-right: 1px solid #e0e0e0;
            z-index: 1000;
            padding-top: 1.5rem;
            overflow-y: auto;
        }

        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--secondary);
            text-align: center;
            margin-bottom: 2rem;
            letter-spacing: 2px;
        }

        .nav-link {
            color: #666;
            padding: 12px 25px;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: all 0.2s;
            border-left: 4px solid transparent;
        }

        .nav-link:hover {
            background-color: #f8f9fa;
            color: var(--primary);
        }

        .nav-link.active {
            background-color: var(--primary);
            color: white !important;
            border-left: 4px solid var(--primary-dark);
        }

        .nav-link i { margin-right: 10px; width: 20px; text-align: center; font-size: 1.1rem; }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }

        .top-bar {
            background: white;
            padding: 1rem 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        .text-primary { color: var(--primary) !important; }
    </style>
</head>
<body>
    
    <nav class="sidebar">
        <div class="sidebar-brand">DeCasa</div>
        
        <div class="nav flex-column">
            
            {{-- Dashboard--}}
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
                Dashboard
            </a>

            {{-- Facility --}}
            <a href="{{ route('admin.fasilitas.index') }}" class="nav-link {{ Request::routeIs('admin.fasilitas.index') ? 'active' : '' }}">
                Facilities
            </a>
    
            </div>

            <div class="my-2 border-top"></div> 
            
            {{-- Property --}}
            <a href="{{ route('admin.properti.index') }}" class="nav-link {{ Request::routeIs('admin.properti.index') ? 'active' : '' }}">
                Property List
            </a>

            <a href="{{ route('admin.transaksi.index') }}" class="nav-link {{ Request::routeIs('admin.transaksi.index') ? 'active' : '' }}">
                Transaction
            </a>

            {{-- Manage --}}
            <a href="{{ route('admin.properti.manage') }}" class="nav-link {{ Request::routeIs('admin.properti.manage') ? 'active' : '' }}">
                Manage
            </a>

            {{-- Add --}}
            <a href="{{ route('admin.properti.create') }}" class="nav-link {{ Request::routeIs('admin.properti.create') ? 'active' : '' }}">
                Add New
            </a>

            <div class="my-2 border-top"></div> 
            
            {{-- Reports --}}
            <a href="{{ route('admin.laporan.index') }}" class="nav-link {{ Request::routeIs('admin.laporan.index') ? 'active' : '' }}">
                Reports
            </a>

            {{-- Customer --}}
            <a href="{{ route('admin.customer.index') }}" class="nav-link {{ Request::routeIs('admin.customer.*') ? 'active' : '' }}">
                Customers
            </a>

            {{-- Setting --}}
            <a href="{{ route('admin.setting.index') }}" class="nav-link {{ Request::routeIs('admin.setting.*') ? 'active' : '' }}">
                Settings
            </a>

        </div>
    </nav>

    <div class="main-content">
        
        <div class="top-bar">
            <h5 class="m-0 fw-bold text-dark">@yield('title')</h5>
            
            <div class="d-flex align-items-center">
                <div class="text-end me-3 lh-1">
                    <div class="fw-bold text-dark">{{ Auth::user()->nm_user }}</div> 
                    <small class="text-muted" style="font-size: 0.75rem;">ID: {{ Auth::user()->id_user }}</small>
                </div>

                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nm_user) }}&background=697565&color=fff" width="40" height="40" class="me-3 rounded-circle">

                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Logout">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <div class="container-fluid px-0 mb-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle me-2"></i><strong>Success!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-0 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i><strong>An error has occurred!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if ($errors->any())
                <div class="alert alert-danger rounded-0 shadow-sm">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- CONTENT SECTION --}}
        @yield('content')
        
    </div>
    {{-- SCRIPTS --}}
    <script src="{{ asset('js/leaflet.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>