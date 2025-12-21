@extends('layouts.admin')

@section('title', 'Detail Property')
@section('content')
<div class="card border-0 shadow-sm rounded-0 bg-white p-4">
    <div class="row">
        <div class="col-md-5">
         
            <div class="mb-2">
                <img id="mainImage" 
                     src="{{ Str::startsWith($properti->url_foto, 'http') ? $properti->url_foto : asset('storage/' . $properti->url_foto) }}" 
                     class="w-100 border" 
                     style="height: 350px; object-fit: cover;"
                     alt="{{ $properti->nm_properti }}">
            </div>

            <div class="d-flex gap-2 overflow-auto pb-2" style="white-space: nowrap;">
                
                <img src="{{ Str::startsWith($properti->url_foto, 'http') ? $properti->url_foto : asset('storage/' . $properti->url_foto) }}" 
                     class="thumb-img border border-primary p-1" 
                     style="width: 80px; height: 60px; object-fit: cover; cursor: pointer;"
                     onclick="changeImage(this)">

                @if(isset($fotos) && count($fotos) > 0)
                    @foreach($fotos as $foto)
                        <img src="{{ Str::startsWith($foto->url_foto, 'http') ? $foto->url_foto : asset('storage/' . $foto->url_foto) }}" 
                             class="thumb-img border p-1" 
                             style="width: 80px; height: 60px; object-fit: cover; cursor: pointer;"
                             onclick="changeImage(this)">
                    @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-7">
            <h2 class="fw-bold">{{ $properti->nm_properti }}</h2>
            <h4 class="text-success fw-bold">Rp {{ number_format($properti->harga, 0, ',', '.') }}</h4>
            <hr>
            
            <h6 class="fw-bold">Fasilitas:</h6>
            <div class="d-flex flex-wrap gap-2 mb-3">
                @foreach($fasilitas as $f)
                    <span class="badge bg-light text-dark border rounded-0 p-2">
                        <i class="fas fa-check text-success"></i> {{ $f->nm_fasilitas }}
                    </span>
                @endforeach
            </div>

            <h6 class="fw-bold mt-4">Deskripsi:</h6>
            <p class="text-muted" style="text-align: justify;">{{ $properti->deskripsi }}</p>

            <div class="alert alert-light border mt-4">
                <strong><i class="fas fa-map-marker-alt text-danger"></i> Lokasi:</strong> <br>
                {{ $properti->alamat }}
            </div>

            {{-- Tombol Kembali --}}
            <div class="mt-4">
                <a href="{{ route('admin.properti.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function changeImage(element) {
        // 1. Ganti source gambar utama dengan source thumbnail yang diklik
        document.getElementById('mainImage').src = element.src;

        // 2. Hapus border biru (active) dari semua thumbnail
        document.querySelectorAll('.thumb-img').forEach(img => {
            img.classList.remove('border-primary');
            img.classList.add('border'); // Balikin border biasa
        });

        // 3. Tambah border biru ke thumbnail yang sedang diklik
        element.classList.remove('border');
        element.classList.add('border-primary');
    }
</script>
@endsection