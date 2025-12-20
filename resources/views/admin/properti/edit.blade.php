@extends('layouts.admin')

@section('title', 'Edit Property')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm p-4 bg-white rounded-0">
            
            <h5 class="fw-bold mb-4">Edit Property: {{ $properti->id_properti }}</h5>

            <form action="{{ route('admin.properti.update', $properti->id_properti) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') 
                
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">PROPERTY NAME</label>
                    <input type="text" name="nm_properti" class="form-control" value="{{ old('nm_properti', $properti->nm_properti) }}" required>
                </div>

                <div class="mb-3">
                <label class="form-label text-uppercase small fw-bold">Category</label>
                <select name="id_kategori" class="form-select" required>
                    <option value="">-- Select Category --</option>
                    @foreach($kategori as $item)
                        <option value="{{ $item->id_kategori }}" 
                            {{ $properti->id_kategori == $item->id_kategori ? 'selected' : '' }}>
                            {{ $item->nm_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small fw-bold">PRICE</label>
                        <input type="number" name="harga" class="form-control" value="{{ old('harga', $properti->harga) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small fw-bold">STATUS</label>
                        <select name="status" class="form-select">
                            <option value="tersedia" {{ $properti->status == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="terisi" {{ $properti->status != 'tersedia' ? 'selected' : '' }}>Terisi</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">ADDRESS</label>
                    <input type="text" name="alamat" class="form-control" value="{{ old('alamat', $properti->alamat) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">DESCRIPTION</label>
                    <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi', $properti->deskripsi) }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold d-block">FACILITIES</label>
                    <div class="card p-3 border bg-light">
                        <div class="row">
                            @foreach($fasilitas as $f)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input rounded-0" type="checkbox" name="fasilitas[]" 
                                           value="{{ $f->id_fasilitas }}" id="fas-{{ $f->id_fasilitas }}"
                                           {{ in_array($f->id_fasilitas, $selectedFasilitas) ? 'checked' : '' }}>
                                    
                                    <label class="form-check-label small" for="fas-{{ $f->id_fasilitas }}">
                                        {{ $f->nm_fasilitas }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold">UPDATE PHOTO (Optional)</label>
                    <input type="file" name="foto" class="form-control mb-2">
                    @if($properti->url_foto)
                        <small class="text-muted">Current Photo:</small><br>
                        <img src="{{ asset('storage/' . $properti->url_foto) }}" height="80" class="border p-1">
                    @endif
                </div>

                <hr>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.properti.manage') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary px-5">Update Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection