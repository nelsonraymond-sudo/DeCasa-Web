<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Decasa Property')</title>

    {{-- FONT & ICONS --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        /* === 1. DEFINISI WARNA (ROOT) === */
        :root {
            --clr-primary: #697565;       /* Sage Green (Utama) */
            --clr-secondary: #DAA520;     /* Gold (Aksen) */
            --clr-bg-light: #f4f7f6;      
            --clr-text-dark: #B6B09F;     
            --clr-text-body: #5f6c7b;    
            --radius-md: 12px;            
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--clr-text-body);
            background-color: #ffffff;
            scroll-behavior: smooth; 
        }

        /* === 2. HAPUS SEMUA WARNA BIRU BOOTSTRAP === */
        
        /* Ubah warna teks link default */
        a { 
            color: var(--clr-primary); 
            text-decoration: none; 
            transition: 0.3s;
        }
        a:hover { color: var(--clr-secondary); }

        /* Timpa class .text-primary (agar judul tidak biru) */
        .text-primary { color: var(--clr-primary) !important; }
        
        /* Timpa class .bg-primary */
        .bg-primary { background-color: var(--clr-primary) !important; }

        /* Timpa tombol .btn-primary */
        .btn-primary {
            background-color: var(--clr-primary) !important;
            border-color: var(--clr-primary) !important;
            color: #fff;
        }
        .btn-primary:hover, .btn-primary:active, .btn-primary:focus {
            background-color: #556052 !important; /* Versi lebih gelap */
            border-color: #556052 !important;
        }

        /* Timpa tombol .btn-outline-primary */
        .btn-outline-primary {
            color: var(--clr-primary) !important;
            border-color: var(--clr-primary) !important;
        }
        .btn-outline-primary:hover {
            background-color: var(--clr-primary) !important;
            color: #fff !important;
        }

        /* Hapus efek GLOW BIRU saat klik Input / Tombol */
        .form-control:focus, .form-select:focus, .btn:focus {
            border-color: var(--clr-secondary) !important;
            box-shadow: 0 0 0 0.25rem rgba(218, 165, 32, 0.25) !important; /* Glow jadi Gold pudar */
        }

        /* Warna saat teks diblok/highlight mouse */
        ::selection {
            background-color: var(--clr-primary);
            color: #fff;
        }

        /* === 3. CUSTOM NAVBAR === */
        .navbar {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            padding: 15px 0;
        }
        
        .nav-link {
            color: #333 !important;
            font-weight: 500;
            margin: 0 5px;
            transition: all 0.3s ease;
            padding: 8px 16px !important;
        }

        /* Hover Menu: Background Gold */
        .nav-link:hover, .nav-link.active {
            color: #fff !important; 
            background-color: var(--clr-secondary);
            border-radius: 50px;
        }

        /* Logo Decasa */
        .navbar-brand {
            color: var(--clr-primary) !important;
        }

        /* Hamburger Menu (Mobile) */
        .navbar-toggler {
            border: none; 
            color: var(--clr-primary);
        }
        .navbar-toggler:focus {
            box-shadow: none; /* Hapus kotak biru di mobile menu */
        }
        
        h1, h2, h3, h4, h5, h6 {
            color: var(--clr-primary);
            font-weight: 700;
        }

        /* === 4. CUSTOM BUTTON CLASS (OPSIONAL JIKA BUTUH EXTRA) === */
        .btn-decasa-gold {
            background-color: var(--clr-secondary);
            color: #fff;
            border-radius: 50px;
            padding: 8px 25px;
            font-weight: 600;
            border: none;
        }
        .btn-decasa-gold:hover {
            background-color: #b5891b;
            color: #fff;
        }
    </style>
</head>
<body>

    {{-- NAVBAR --}}
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="bi bi-list fs-1"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                @php
                    $isHome = Route::currentRouteName() == 'home';
                @endphp
                <li class="nav-item">
                    <a class="nav-link" href="{{ $isHome ? '#home' : url('/#home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ $isHome ? '#properties' : url('/#properties') }}">Properties</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ $isHome ? '#services' : url('/#services') }}">Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ $isHome ? '#customer' : url('/#customer') }}">Our Customer</a>
                </li>
            </ul>
                
               <div class="d-flex gap-2 mt-3 mt-lg-0 align-items-center">
    @guest
        <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
            Login / Register
        </a>
    @else
        <div class="dropdown">
            <button class="btn btn-outline-primary rounded-pill px-4 fw-bold dropdown-toggle" 
                    type="button" 
                    id="userMenuButton" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false">
                <i class="bi bi-person-circle me-1"></i> 
                {{ Auth::user()->nm_user ?? Auth::user()->name ?? 'Akun Saya' }}
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" 
                aria-labelledby="userMenuButton" 
                style="border-radius: 12px; overflow: hidden;">
                
                <li>
                    <h6 class="dropdown-header text-uppercase small text-muted">Account</h6>
                </li>

                <li>
                    <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                        <i class="bi bi-gear me-2 text-secondary"></i> Profile Settings
                    </a>
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <a class="dropdown-item py-2 text-danger fw-bold" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form-nav').submit();">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>

        <form id="logout-form-nav" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    @endguest
</div>
            </div>
        </div>
    </nav>

    {{-- CONTENT --}}
    <main>
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="py-4 text-center mt-5" style="background-color: var(--clr-bg-light); color: var(--clr-primary);">
        <small class="fw-bold">&copy; 2024 Decasa Property. All rights reserved.</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>