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
            
            <h6 class="fw-bold">Fasilities:</h6>
            <div class="d-flex flex-wrap gap-2 mb-3">
                @foreach($fasilitas as $f)
                    <span class="badge bg-light text-dark border rounded-0 p-2">
                        <i class="bi bi-check-circle text-success"></i> {{ $f->nm_fasilitas }}
                    </span>
                @endforeach
            </div>

            <h6 class="fw-bold mt-4">Description:</h6>
            <p class="text-muted" style="text-align: justify;">{{ $properti->deskripsi }}</p>

            <h6 class="fw-bold mt-4">Location:</h6>
            <div id="map-detail" style="height: 350px; border-radius: 12px; z-index: 0; border: 1px solid #ddd;"></div>

            <div class="mt-4">
                <a href="{{ route('admin.properti.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/leaflet.js') }}"></script>
<script>
    function changeImage(element) {
        document.getElementById('mainImage').src = element.src;
        document.querySelectorAll('.thumb-img').forEach(img => {
            img.classList.remove('border-primary');
            img.classList.add('border');
        });
        element.classList.remove('border');
        element.classList.add('border-primary');
    };

    var lat = {{ $properti->latitude ?? -7.8011945 }};
    var lng = {{ $properti->longitude ?? 110.364917 }};

    if (!lat) lat = -7.8011945;
    if (!lng) lng = 110.364917;

    var mapDetail = L.map('map-detail').setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Decasa Property'
    }).addTo(mapDetail);

    L.marker([lat, lng]).addTo(mapDetail)
        .bindPopup("<b>{{ $properti->nm_properti }}</b><br>Location here.")
        .openPopup();
</script>


@endsection