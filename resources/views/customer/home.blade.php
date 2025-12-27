@extends('layouts.customer')

@section('content')

{{-- === 1. SECTION HOME (HERO) === --}}
<section id="home" class="d-flex align-items-center justify-content-center text-center" 
    style="height: 100vh; background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center;">
    
    <div class="container text-white mt-5">
        <h1 class="display-3 fw-bold mb-3">Find your ideal home</h1>
        <p class="lead mb-5">The best properties are ready to move into with an easy and secure process.</p>
        
        <div class="card p-4 border-0 shadow rounded-4 mx-auto" style="max-width: 800px; background: rgba(255,255,255,0.9);">
            <form action="{{ route('properti.search') }}" method="GET">
                <div class="row g-2">
                    <div class="col-md-5">
                        <input type="text" name="lokasi" class="form-control form-control-lg border-0 bg-light" placeholder="Seacrh by location...">
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
                        <button type="submit" class="btn btn-decasa w-100 h-100 fs-5">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>


{{-- === 2. SECTION PROPERTIES === --}}
<section id="properties" class="py-5 bg-white">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Popular Properties</h2>
        </div>

        <div class="row g-4">
            @forelse($properti as $p)
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm" style="border-radius: var(--radius-md); overflow: hidden;">
                    {{-- Gambar --}}
                    <div style="height: 220px; position: relative;">
                            @php
                                // Cek apakah url_foto kosong?
                                if(empty($p->url_foto)) {
                                    $imgUrl = 'https://via.placeholder.com/400x300?text=No+Image';
                                } 
                                // Cek apakah ini link eksternal (dari Seeder/Unsplash)?
                                elseif(Str::startsWith($p->url_foto, 'http')) {
                                    $imgUrl = $p->url_foto;
                                } 
                                // Jika bukan, berarti file lokal di storage
                                else {
                                    $imgUrl = asset('storage/'.$p->url_foto);
                                }
                            @endphp

                            <img src="{{ $imgUrl }}" 
                                 class="w-100 h-100 object-fit-cover" 
                                 alt="{{ $p->nm_properti }}">
                                 
                            <span class="badge bg-white text-dark position-absolute top-0 start-0 m-3 shadow-sm">
                                {{ $p->nm_kategori }}
                            </span>
                        </div>

                    <div class="card-body p-4">
                        
                        <h5 class="card-title fw-bold text-truncate">{{ $p->nm_properti }}</h5>
                        <p class="text-muted small"><i class="bi bi-geo-alt"></i> {{ Str::limit($p->alamat, 40) }}</p>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="text-primary fw-bold mb-0">Rp {{ number_format($p->harga, 0, ',', '.') }}</h5>
                            
                            @auth
                                <a href="{{ route('properti.detail', $p->id_properti) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">Detail</a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Login For Detail</a>
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
            <a href="{{ route('properti.search') }}" class="btn btn-outline-primary px-5 rounded-pill">See All Properties</a>
        </div>
    </div>
</section>


{{-- === 3. SECTION SERVICES  === --}}
<section id="services" class="py-5" style="background-color: var(--clr-bg-light);">
    <div class="container py-5">

        {{-- Teks Atas --}}
        <div class="text-center mb-5">
            <h6 class="text-primary fw-bold text-uppercase">Our Services</h6>
            <h2 class="fw-bold mb-4">Why Choose Decasa?</h2>
            <p class="text-muted mb-4">
                We provide the most transparent, easy, and secure property rental experience.
            </p>
            <a href="#" class="btn btn-decasa">Learn More</a>
        </div>

        <div class="row g-4">
            @foreach($services as $s)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 p-4 h-100 shadow-sm text-center"
                     style="border-radius: var(--radius-md);">
                    <div class="mb-3 text-primary">
                        <i class="bi {{ $s['icon'] }} fs-1"></i>
                    </div>
                    <h5 class="fw-bold">{{ $s['judul'] }}</h5>
                    <p class="text-muted small mb-0">{{ $s['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>


{{-- === 4. SECTION OUR CUSTOMER (TESTIMONI) === --}}
<section id="customer" class="py-5 bg-white">
    <div class="container py-5 text-center">
        <h6 class="text-primary fw-bold text-uppercase">Testimonial</h6>
        <h2 class="fw-bold mb-5">Customer Review</h2>

        <div class="row g-4 justify-content-center">
            @foreach($reviews as $r)
            <div class="col-md-4">
                <div class="card h-100 border-0 p-4 shadow-sm" style="background-color: #f8f9fa; border-radius: var(--radius-md);">
                    <div class="card-body">
                        <div class="mb-3 text-warning">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <p class="fst-italic text-muted">"{{ $r['isi'] }}"</p>
                        <div class="d-flex align-items-center justify-content-center mt-4 gap-3">
                            <div class="bg-secondary rounded-circle text-white d-flex align-items-center justify-content-center fw-bold" style="width: 50px; height: 50px;">
                                {{ substr($r['nama'], 0, 1) }}
                            </div>
                            <div class="text-start">
                                <h6 class="fw-bold mb-0">{{ $r['nama'] }}</h6>
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