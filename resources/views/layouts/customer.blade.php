<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Decasa Property')</title>

    {{-- FONT & ICONS --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

   
    

   <style>
    :root {
        --clr-primary: #4A5D23;       
        --clr-primary-hover: #364419; 
        --clr-secondary: #8B5A2B;    
        --clr-secondary-hover: #6F451E;
        --clr-bg-body: #F9F7F0;       
        --clr-bg-card: #EFEBD6;      
        --clr-text-heading: #2C241B;  
        --clr-text-body: #5A524A;     
        --clr-footer: #1A230F;        
        --radius-md: 12px;            
    }

    body {
        font-family: 'Poppins', sans-serif;
        color: var(--clr-text-body);
        background-color: var(--clr-bg-body);
    }

    h1, h2, h3, h4, h5, h6 {
        color: var(--clr-text-heading);
        font-weight: 700;
    }

    .text-primary {
        color: var(--clr-primary) !important; 
    }
    .bg-primary {
        background-color: var(--clr-primary) !important;
    }

    a { text-decoration: none; color: var(--clr-primary); transition: 0.3s; }
    a:hover { color: var(--clr-secondary); }

    .btn-primary {
        background-color: var(--clr-primary) !important;
        border-color: var(--clr-primary) !important;
        color: #fff;
        padding: 10px 24px;
        border-radius: 8px;
    }
    .btn-primary:hover {
        background-color: var(--clr-primary-hover) !important;
        transform: translateY(-2px);
    }

    .btn-decasa-gold {
        background-color: var(--clr-secondary);
        color: #fff;
        border-radius: 8px;
        padding: 8px 25px;
        font-weight: 600;
        border: none;
        transition: all 0.3s ease;
    }
    .btn-decasa-gold:hover {
        background-color: var(--clr-secondary-hover);
        color: #fff;
        box-shadow: 0 4px 10px rgba(139, 90, 43, 0.3);
    }

    .navbar {
        background-color: rgba(249, 247, 240, 0.95);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(139, 90, 43, 0.1);
        padding: 15px 0;
    }
    .nav-link { color: var(--clr-text-heading) !important; font-weight: 500; }
    .nav-link:hover { color: var(--clr-secondary) !important; }

    footer {
        background-color: var(--clr-footer) !important; 
        color: #d3d3d3 !important; 
        margin-top: 0 !important;
    }
    footer h6 { color: #fff !important; }
    footer a { color: #aaaaaa !important; }
    footer a:hover { color: var(--clr-secondary) !important; }
    
    .card { border: none; }
    .form-control:focus, .form-select:focus {
        border-color: var(--clr-secondary) !important;
        box-shadow: 0 0 0 0.25rem rgba(139, 90, 43, 0.2) !important;
    }
</style>
</head>
<body>

    {{-- NAVBAR --}}
    <nav class="navbar navbar-expand-lg fixed-top " style="background-color: var(--clr-bg-card);">
        <div class="container">

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="bi bi-list fs-1"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto align-items-center">
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

                    @auth
                        <li class="nav-item">
                            <a class="nav-link  text-secondary rounded-pill px-3 ms-2" 
                               href="{{ route('customer.dashboard') }}">
                                History
                            </a>
                        </li>
                    @endauth

                </ul>
                
                <div class="d-flex gap-2 mt-3 mt-lg-0 align-items-center justify-content-center">
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
    <footer class="text-center text-lg-start mt-5">
      <section class="border-top-accent py-5" style="background-color: var(--clr-footer);">
          <div class="container text-center text-md-start">
            <div class="row mt-3">
              <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
                <h6 class="text-uppercase fw-bold mb-4">DeCasa Property</h6>
                <p>
                  Finding a place to rent in Yogyakarta shouldn’t be complicated.
                  We have you covered.
                </p>
              </div>

              <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
                <h6 class="text-uppercase fw-bold mb-4">Properties</h6>
                <p><a href="#!" class="text-reset">Home</a></p>
                <p><a href="#!" class="text-reset">Villa</a></p>
                <p><a href="#!" class="text-reset">Appartment</a></p>
                <p><a href="#!" class="text-reset">Costs</a></p>
              </div>

              <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
                <h6 class="text-uppercase fw-bold mb-4">Menu</h6>
                <p><a href="/#home" class="text-reset">Home</a></p>
                <p><a href="/#properties" class="text-reset">Properties</a></p>
                <p><a href="/#services" class="text-reset">Service</a></p>
                <p><a href="/#customer" class="text-reset">Review</a></p>
                 <p><a href="{{ route('customer.dashboard') }}" class="text-reset">History</a></p>
              </div>

              <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
                <h6 class="text-uppercase fw-bold mb-4">Contact</h6>
                <p><i class="fas fa-home me-3"></i> Yogyakarta, YK 56125, DIY</p>
                <p><i class="fas fa-envelope me-3"></i> decasa@company.com</p>
                <p><i class="fas fa-phone me-3"></i> +62 812 3353 4426</p>
                  <div>
                      <h5 class="mt-4 mb-3 text-white">Connect with Us</h5>
                      <a href="https://facebook.com" class="me-4 text-reset"><i class="fab fa-facebook-f"></i></a>
                      <a href="https://wa.me/+6281233534426" class="me-4 text-reset"><i class="fab fa-whatsapp"></i></a>
                      <a href="https://tiktok.com" class="me-4 text-reset"><i class="fab fa-tiktok"></i></a>
                      <a href="https://www.instagram.com/ydfallen_" class="me-4 text-reset"><i class="fab fa-instagram"></i></a>
                      <a href="https://linkedin.com" class="me-4 text-reset"><i class="fab fa-linkedin"></i></a>
                  </div>
              </div>
            </div>
          </div>
      </section>
    </footer>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>