@extends('layouts.customer')

@section('content')

<div class="container py-5 mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $properti->nm_properti }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            
            <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                @php
                    // Cek apakah array $fotos ada isinya
                    if(count($fotos) > 0) {
                        // Ambil foto pertama dari array
                        $fotoUtama = asset('storage/' . $fotos[0]->url_foto);
                    } else {
                        // Jika tidak ada foto sama sekali di database
                        $fotoUtama = 'https://via.placeholder.com/800x500?text=No+Image+Available';
                    }
                @endphp
                
                <img src="{{ $fotoUtama }}" class="w-100 object-fit-cover" style="height: 400px;" alt="Main Image">
            </div>

            @if(count($fotos) > 1)
            <div class="row mb-4">
                @foreach($fotos as $f)
                <div class="col-3">
                    {{-- Pastikan url_foto sesuai nama kolom di tabel foto_properti --}}
                    <img src="{{ asset('storage/' . $f->url_foto) }}" class="img-thumbnail" style="height: 80px; width:100%; object-fit: cover;">
                </div>
                @endforeach
            </div>
            @endif

            <h2 class="fw-bold">{{ $properti->nm_properti }}</h2>
            <p class="text-muted"><i class="bi bi-geo-alt-fill text-danger"></i> {{ $properti->alamat }}</p>
            
            <div class="mb-3">
                <span class="badge bg-primary">{{ $properti->nm_kategori }}</span>
                <span class="badge {{ $properti->status == 'available' ? 'bg-success' : 'bg-secondary' }}">
                    {{ ucfirst($properti->status) }}
                </span>
            </div>

            <h4 class="mt-4">Description</h4>
            <p class="text-secondary">{{ $properti->deskripsi }}</p>

            <h4 class="mt-4">Fasilities</h4>
            <div class="row">
                @forelse($fasilitas as $fas)
                    <div class="col-md-6 mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i> {{ $fas->nm_fasilitas }}
                    </div>
                @empty
                    <div class="col-12 text-muted">No facility data available.</div>
                @endforelse
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow p-4 border-0 sticky-top" style="top: 100px;">
                <h5 class="text-muted">Rental Price</h5>
                <h3 class="fw-bold text-primary">Rp {{ number_format($properti->harga, 0, ',', '.') }}</h3>
                <small class="text-muted">/ Day</small>
                <hr>

                <div class="mb-3">
                    <label class="fw-bold text-dark">Owner:</label>
                    <div>{{ $properti->pemilik ?? 'Admin Decasa' }}</div>
                </div>

                <a href="https://wa.me/{{ $properti->no_hp ?? '' }}" target="_blank" class="btn btn-success w-100 mb-2">
                    <i class="bi bi-whatsapp"></i> Chat Owner
                </a>

                @if($properti->status == 'available')
                    <div class="d-grid gap-2">
                @auth
                    <a href="{{ route('booking.form', $properti->id_properti) }}" class="btn btn-primary rounded-pill py-3 fw-bold">
                        Book Now
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-secondary rounded-pill py-3 fw-bold">
                        Login for Booking
                    </a>
                @endauth
            </div>
                @else
                    <button class="btn btn-secondary w-100" disabled>Full Booked</button>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection