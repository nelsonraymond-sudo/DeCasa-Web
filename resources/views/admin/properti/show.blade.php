@extends('layouts.admin')
@section('content')
<div class="card border-0 shadow-sm rounded-0 bg-white p-4">
    <div class="row">
        <div class="col-md-5">
           <img src="{{ Str::startsWith($properti->url_foto, 'http') ? $properti->url_foto : asset('storage/' . $properti->url_foto) }}" 
     class="w-100 rounded-0" 
     style="height: 350px; object-fit: cover;"
     alt="{{ $properti->nm_properti }}">
        </div>
        <div class="col-md-7">
            <h2 class="fw-bold">{{ $properti->nm_properti }}</h2>
            <h4 class="text-success fw-bold">Rp {{ number_format($properti->harga, 0, ',', '.') }}</h4>
            <hr>
            <h6 class="fw-bold">Fasilitas:</h6>
            <div class="d-flex flex-wrap gap-2 mb-3">
                @foreach($fasilitas as $f)
                    <span class="badge bg-light text-dark border rounded-0 p-2">✔ {{ $f->nm_fasilitas }}</span>
                @endforeach
            </div>
            <p class="text-muted">{{ $properti->deskripsi }}</p>
            <p><strong>Lokasi:</strong> {{ $properti->alamat }}</p>
        </div>
    </div>
</div>
@endsection