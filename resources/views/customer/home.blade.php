@extends('layouts.customer') {{-- Pastikan extends ke layout yang benar --}}

@section('content')

{{-- === 1. SECTION HERO === --}}
<section id="home" class="d-flex align-items-center justify-content-center text-center" 
    style="height: 100vh; background: linear-gradient(rgba(20, 30, 10, 0.5), rgba(20, 30, 10, 0.5)), url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center;">
    
    <div class="container text-white mt-5">
        {{-- Widget Cuaca --}}
        @if(isset($cuaca) && $cuaca)
        <div class="d-inline-flex align-items-center bg-white text-dark rounded-pill px-4 py-2 mb-4 shadow animate__animated animate__fadeInDown" style="opacity: 0.95;">
            <img src="http://openweathermap.org/img/wn/{{ $cuaca['weather'][0]['icon'] }}.png" alt="Cuaca" width="40">
            <div class="text-start ms-2 lh-1">
                <span class="d-block fw-bold" style="font-size: 0.9rem; color: var(--clr-text-heading);">Yogyakarta, {{ round($cuaca['main']['temp']) }}°C</span>
                <small class="text-muted" style="font-size: 0.75rem;">{{ ucfirst($cuaca['weather'][0]['description']) }}</small>
            </div>
        </div>
        @endif

        <h1 class="display-3 fw-bold mb-3 text-white">Find your ideal home</h1>
        <p class="lead mb-5">The best properties are ready to move into with an easy and secure process.</p>
        
        {{-- Search Card --}}
        <div class="card p-4 border-0 shadow rounded-4 mx-auto" style="max-width: 800px; background: rgba(255, 255, 255, 0.95);">
            <form action="{{ route('properti.search') }}" method="GET">
                <div class="row g-2">
                    <div class="col-md-5">
                        <input type="text" name="lokasi" class="form-control form-control-lg border-0 bg-light" placeholder="Search by location...">
                    </div>
                    <div class="col-md-4">
                        <select name="kategori" class="form-select form-select-lg border-0 bg-light">
                            <option value="">All Type</option>
                            @foreach($kategori as $k)
                                <option value="{{ $k->id_kategori }}">{{ $k->nm_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-decasa-gold w-100 h-100 fs-5">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>


{{-- === 2. SECTION PROPERTIES === --}}
<section id="properties" class="py-5">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Popular Properties</h2>
            <p style="color: var(--clr-text-body);">Curated list of our best investments</p>
        </div>

        <div class="row g-4">
            @forelse($properti as $p)
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm" style="border-radius: var(--radius-md); overflow: hidden; background-color: #fff;">
                    <div style="height: 220px; position: relative;">
                            @php
                                $imgUrl = !empty($p->url_foto) ? (Str::startsWith($p->url_foto, 'http') ? $p->url_foto : asset('storage/'.$p->url_foto)) : 'https://via.placeholder.com/400x300?text=No+Image';
                            @endphp

                            <img src="{{ $imgUrl }}" 
                                 class="w-100 h-100 object-fit-cover" 
                                 alt="{{ $p->nm_properti }}">
                             
                            <span class="badge bg-white text-dark position-absolute top-0 start-0 m-3 shadow-sm" 
                                  style="color: var(--clr-primary) !important; font-weight: 600;">
                                {{ $p->nm_kategori }}
                            </span>
                        </div>

                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold text-truncate" style="color: var(--clr-text-heading);">{{ $p->nm_properti }}</h5>
                        <p class="small" style="color: var(--clr-text-body);"><i class="bi bi-geo-alt me-1" style="color: var(--clr-secondary);"></i> {{ Str::limit($p->alamat, 40) }}</p>
                        <hr style="opacity: 0.1;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="text-primary fw-bold mb-0">Rp {{ number_format($p->harga, 0, ',', '.') }}</h5>
                            
                            @auth
                                <a href="{{ route('properti.detail', $p->id_properti) }}" class="btn btn-outline-primary btn-sm px-3">Detail</a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm px-3">Login</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <p class="text-muted">No properties yet.</p>
            </div>
            @endforelse
        </div>
        
        <div class="text-center mt-5">
            <a href="{{ route('properti.search') }}" class="btn btn-outline-primary px-5 py-2">See All Properties</a>
        </div>
    </div>
</section>


{{-- === 3. SECTION SERVICES  === --}}
<section id="services" class="py-5" style="background-color: var(--clr-bg-card);">
    <div class="container py-5">

        <div class="text-center mb-5">
            <h6 class="text-primary fw-bold text-uppercase" style="letter-spacing: 2px;">Our Services</h6>
            <h2 class="fw-bold mb-4">Why Choose Decasa?</h2>
            <p class="mb-4 mx-auto" style="max-width: 600px; color: var(--clr-text-body);">
                We provide the most transparent, easy, and secure property rental experience tailored for your comfort.
            </p>
            <a href="#" class="btn btn-decasa-gold shadow-sm">Learn More</a>
        </div>

        <div class="row g-4">
            @foreach($services as $s)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 p-4 h-100 shadow-sm text-center"
                     style="border-radius: var(--radius-md); background-color: #fff;">
                    <div class="mb-3 text-primary">
                        <i class="bi {{ $s['icon'] }} fs-1"></i>
                    </div>
                    <h5 class="fw-bold" style="color: var(--clr-text-heading);">{{ $s['judul'] }}</h5>
                    {{-- Deskripsi service menggunakan warna text body --}}
                    <p class="small mb-0" style="color: var(--clr-text-body);">{{ $s['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>


{{-- === 4. SECTION TESTIMONIAL === --}}
<section id="customer" class="py-5">
    <div class="container py-5 text-center">
        <h6 class="text-primary fw-bold text-uppercase" style="letter-spacing: 2px;">Testimonial</h6>
        <h2 class="fw-bold mb-5">Customer Review</h2>

        <div class="row g-4 justify-content-center">
            @foreach($reviews as $r)
            <div class="col-md-4">
                <div class="card h-100 border-0 p-4 shadow-sm" style="background-color: #fff; border-radius: var(--radius-md);">
                    <div class="card-body">
                        <div class="mb-3" style="color: var(--clr-secondary);">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <p class="fst-italic" style="color: var(--clr-text-body);">"{{ $r['isi'] }}"</p>
                        <div class="d-flex align-items-center justify-content-center mt-4 gap-3">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" 
                                 style="width: 50px; height: 50px; background-color: var(--clr-secondary);">
                                {{ substr($r['nama'], 0, 1) }}
                            </div>
                            <div class="text-start">
                                <h6 class="fw-bold mb-0" style="color: var(--clr-text-heading);">{{ $r['nama'] }}</h6>
                                <small class="text-muted">{{ $r['role'] }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection