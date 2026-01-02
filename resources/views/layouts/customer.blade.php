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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --clr-primary: #697565;       
            --clr-secondary: #DAA520;     
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

        a { 
            color: var(--clr-primary); 
            text-decoration: none; 
            transition: 0.3s;
        }
        a:hover { color: var(--clr-secondary); }

        .text-primary { color: var(--clr-primary) !important; }
        
        .bg-primary { background-color: var(--clr-primary) !important; }

        .btn-primary {
            background-color: var(--clr-primary) !important;
            border-color: var(--clr-primary) !important;
            color: #fff;
        }
        .btn-primary:hover, .btn-primary:active, .btn-primary:focus {
            background-color: #556052 !important;
            border-color: #556052 !important;
        }

        .btn-outline-primary {
            color: var(--clr-primary) !important;
            border-color: var(--clr-primary) !important;
        }
        .btn-outline-primary:hover {
            background-color: var(--clr-primary) !important;
            color: #fff !important;
        }

        .form-control:focus, .form-select:focus, .btn:focus {
            border-color: var(--clr-secondary) !important;
            box-shadow: 0 0 0 0.25rem rgba(218, 165, 32, 0.25) !important;
        }

        ::selection {
            background-color: var(--clr-primary);
            color: #fff;
        }

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

        .nav-link:hover, .nav-link.active {
            color: #fff !important; 
            background-color: var(--clr-secondary);
            border-radius: 50px;
        }

        .navbar-brand {
            color: var(--clr-primary) !important;
        }

        .navbar-toggler {
            border: none; 
            color: var(--clr-primary);
        }
        .navbar-toggler:focus {
            box-shadow: none; 
        }
        
        h1, h2, h3, h4, h5, h6 {
            color: var(--clr-primary);
            font-weight: 700;
        }

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
<footer class="text-center text-lg-start bg-body-tertiary text-muted">
  <section class="d-flex justify-content-center justify-content-lg-between p-4 border-bottom">
    <div class="me-5 d-none d-lg-block">
      <span>Connect with Us on Social Media</span>
    </div>
    <div>
      <a href="https://facebook.com" class="me-4 text-reset">
        <i class="fab fa-facebook-f"></i>
      </a>
      <a href="https://wa.me/+6281233534426" class="me-4 text-reset">
        <i class="fab fa-whatsapp"></i>
      </a>
      <a href="https://tiktok.com" class="me-4 text-reset">
        <i class="fab fa-tiktok"></i>
      </a>
      <a href="https://www.instagram.com/ydfallen_" class="me-4 text-reset">
        <i class="fab fa-instagram"></i>
      </a>
      <a href="https://linkedin.com" class="me-4 text-reset">
        <i class="fab fa-linkedin"></i>
      </a>
    </div>
  </section>
  <section class="">

    <div class="container text-center text-md-start mt-5">
      <div class="row mt-3">
        <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
    
          <h6 class="text-uppercase fw-bold mb-4">
            DeCasa Property
          </h6>
          <p>
            Finding a place to rent in Yogyakarta shouldn’t be complicated.
            Whether you are looking for a modern apartment in the city center,
            a spacious family home in the suburbs, or a quiet villa near the rice fields, 
            we have you covered.
          </p>
        </div>

        <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
          <h6 class="text-uppercase fw-bold mb-4">
            Properties
          </h6>
          <p>
            <a href="#!" class="text-reset">Home</a>
          </p>
          <p>
            <a href="#!" class="text-reset">Villa</a>
          </p>
          <p>
            <a href="#!" class="text-reset">Appartment</a>
          </p>
          <p>
            <a href="#!" class="text-reset">Costs</a>
          </p>
        </div>

        <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
          <h6 class="text-uppercase fw-bold mb-4">
            Menu
          </h6>
          <p>
            <a href="/#home" class="text-reset">Home</a>
          </p>
          <p>
            <a href="/#properties" class="text-reset">Properties</a>
          </p>
          <p>
            <a href="/#services" class="text-reset">Service</a>
          </p>
          <p>
            <a href="/#customer" class="text-reset">Review</a>
          </p>
        </div>

        <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
          <h6 class="text-uppercase fw-bold mb-4">Contact</h6>
          <p><i class="fas fa-home me-3"></i> Yogyakarta, YK 56125, DIY</p>
          <p>
            <i class="fas fa-envelope me-3"></i>
            decasa@company.com
          </p>
          <p><i class="fas fa-phone me-3"></i> +62 812 3353 4426</p>
        </div>
        <!-- Grid column -->
      </div>
      <!-- Grid row -->
    </div>
  </section>
  <!-- Section: Links  -->

  <!-- Copyright -->
  <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">
    © 2025 Copyright:
    <a class="text-reset fw-bold" href="https://mdbootstrap.com/">DeCasa.com</a>
  </div>
  <!-- Copyright -->
</footer>
<!-- Footer -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>