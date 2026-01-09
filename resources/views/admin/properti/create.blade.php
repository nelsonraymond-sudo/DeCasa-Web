@extends('layouts.admin')

@section('title', 'Add New Property')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm p-4 bg-white">
            
            <form action="{{ route('admin.properti.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <h5 class="fw-bold mb-4">Property Details</h5>

                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">PROPERTY NAME</label>
                    <input type="text" name="nm_properti" class="form-control py-2" placeholder="Ex: Rumah Kentang" value="{{ old('nm_properti') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">CATEGORY</label>
                    <select name="id_kategori" class="form-select py-2" required>
                        <option value="">-- Select Category --</option>
                        @foreach($kategori as $k)
                            <option value="{{ $k->id_kategori }}" {{ old('id_kategori') == $k->id_kategori ? 'selected' : '' }}>
                                {{ $k->nm_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small fw-bold">PRICE (PER DAY)</label>
                        <input type="number" name="harga" class="form-control py-2" placeholder="Ex: 2500000" value="{{ old('harga') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small fw-bold">STATUS</label>
                        <select name="status" class="form-select py-2">
                            <option value="available">Available</option>
                            <option value="full">Full</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">PROPERTY LOCATION</label>
                    <div id="map" style="height: 300px; border-radius: 8px; z-index: 0; border: 1px solid #ddd;"></div>
                    
                    <div class="row mt-2">
                        <div class="col">
                            <input type="text" name="latitude" id="latitude" class="form-control" placeholder="Latitude" readonly>
                        </div>
                        <div class="col">
                            <input type="text" name="longitude" id="longitude" class="form-control" placeholder="Longitude" readonly>
                        </div>
                    </div>
                    <div class="form-text">Drag the marker to the property location.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">DESCRIPTION</label>
                    <textarea name="deskripsi" class="form-control" rows="4" placeholder="Describe facilities, environment, etc.">{{ old('deskripsi') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">ADDRESS</label>
                    <textarea name="alamat" class="form-control" rows="2" placeholder="Complete address">{{ old('alamat') }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold d-block">FACILITIES</label>
                    <div class="card p-3 border bg-light">
                        <div class="row">
                            @foreach($fasilitas as $f)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input rounded-0" type="checkbox" name="fasilitas[]" value="{{ $f->id_fasilitas }}" id="fas-{{ $f->id_fasilitas }}">
                                    <label class="form-check-label small" for="fas-{{ $f->id_fasilitas }}">
                                        {{ $f->nm_fasilitas }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-text">Check all facilities available in this unit.</div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold">PROPERTY IMAGES</label>
                    <input type="file" name="fotos[]" id="inputFotos" class="form-control" multiple accept="image/*" required>
                    <div class="form-text">Select multiple photos at once. The first photo will be the main thumbnail.</div>
                    <div class="d-flex flex-wrap gap-2 mt-3" id="preview-container"></div>
                </div>

                <hr>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.properti.manage') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary px-5">Save Property</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPT AREA --}}
<script src="{{ asset('js/leaflet.js') }}"></script>
<script>
    document.getElementById('inputFotos').addEventListener('change', function(event) {
        const container = document.getElementById('preview-container');
        container.innerHTML = ''; 
        
        Array.from(event.target.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100px';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                img.className = 'border rounded shadow-sm';
                container.appendChild(img);
            }
            reader.readAsDataURL(file);
        });
    });

    var defaultLat = -7.8011945; 
    var defaultLng = 110.364917;
    
    var curLat = document.getElementById('latitude').value || defaultLat;
    var curLng = document.getElementById('longitude').value || defaultLng;

    var map = L.map('map').setView([curLat, curLng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    var marker = L.marker([curLat, curLng], {draggable: true}).addTo(map);

    marker.on('dragend', function(event) {
        var position = marker.getLatLng();
        document.getElementById('latitude').value = position.lat;
        document.getElementById('longitude').value = position.lng;
    });

    document.getElementById('latitude').value = curLat;
    document.getElementById('longitude').value = curLng;
</script>
@endsection