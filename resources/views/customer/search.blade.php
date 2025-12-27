@extends('layouts.customer')

@section('title', 'Hasil Pencarian - Decasa')

@section('content')

{{-- HEADER PENCARIAN --}}
<section class="bg-light py-5 mt-5">
    <div class="container">
        <h2 class="fw-bold mb-4">Property Search Results</h2>
        
        {{-- Form Pencarian Ulang --}}
        <div class="card border-0 shadow-sm p-3 rounded-4">
            <form action="{{ route('properti.search') }}" method="GET">
                <div class="row g-2">
                    <div class="col-md-5">
                        <input type="text" name="alamat" value="{{ $keyword ?? '' }}" class="form-control border-0 bg-light py-2" placeholder="Type in the location or property name...">
                    </div>
                    <div class="col-md-4">
                        <select name="kategori" class="form-select border-0 bg-light py-2">
                            <option value="">All Category</option>
                            @foreach($kategori as $k)
                                <option value="{{ $k->id_kategori }}" {{ ($id_kategori ?? '') == $k->id_kategori ? 'selected' : '' }}>
                                    {{ $k->nm_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill">Search Again</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- LISTING HASIL --}}
<section class="py-5">
    <div class="container">
        @if(isset($properti) && $properti->count() > 0)
            <div class="row g-4">
                @foreach($properti as $p)
                <div class="col-md-4 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                        
                        {{-- LOGIKA GAMBAR (Thumbnail) --}}
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
                            <p class="text-muted small mb-3">
                                <i class="bi bi-geo-alt-fill me-1"></i> {{ Str::limit($p->alamat, 35) }}
                            </p>
                            
                            <h5 class="fw-bold text-primary">
                                Rp {{ number_format($p->harga, 0, ',', '.') }}
                            </h5>
                            <hr class="text-muted opacity-25">
                            
                            <div class="d-grid">
                                {{-- Link ke Detail --}}
                                <a href="{{ route('properti.detail', $p->id_properti) }}" class="btn btn-outline-primary rounded-pill">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination Links --}}
            <div class="d-flex justify-content-center mt-5">
                {{ $properti->withQueryString()->links('pagination::bootstrap-5') }}
            </div>

        @else
            {{-- TAMPILAN JIKA TIDAK ADA HASIL --}}
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="bi bi-search display-1 text-muted opacity-25"></i>
                </div>
                <h3 class="fw-bold">Property Not Found</h3>
                <p class="text-muted">Sorry, we couldn't find any properties matching your search criteria. "{{ $keyword ?? '' }}".</p>
                <a href="{{ url('/') }}" class="btn btn-primary rounded-pill px-4">Back to Home</a>
            </div>
        @endif
    </div>
</section>

@endsection