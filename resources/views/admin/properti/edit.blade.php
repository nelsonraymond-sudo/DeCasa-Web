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
                        <label for="status">Status Property</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="available" {{ $properti->status == 'available' ? 'selected' : '' }}>
                                Available
                            </option>
                            <option value="full" {{ $properti->status == 'full' ? 'selected' : '' }}>
                                Full
                            </option>
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
                    <label class="form-label text-muted small fw-bold d-block">MANAGE PHOTOS</label>
                    
                    <div class="mb-3">
                        <label class="small text-muted">Add New Photos:</label>
                        <input type="file" name="new_fotos[]" class="form-control" multiple accept="image/*">
                        <div class="form-text">You can select multiple files to add to the gallery.</div>
                    </div>

                    @if(isset($fotos) && count($fotos) > 0)
                        <label class="small text-muted mb-2">Existing Gallery (Check to DELETE):</label>
                        <div class="row g-2">
                            @foreach($fotos as $foto)
                                <div class="col-md-3 col-4 text-center">
                                    <div class="border p-1 position-relative bg-light">
                                        <img src="{{ Str::startsWith($foto->url_foto, 'http') ? $foto->url_foto : asset('storage/' . $foto->url_foto) }}" 
                                             class="img-fluid" 
                                             style="height: 80px; width: 100%; object-fit: cover;">
                                        
                                        <div class="mt-1">
                                            <input type="checkbox" name="delete_fotos[]" 
                                                   value="{{ $foto->url_foto }}" 
                                                   id="del-{{ $loop->index }}" 
                                                   class="form-check-input border-danger">
                                            <label for="del-{{ $loop->index }}" class="small text-danger fw-bold" style="cursor: pointer;">
                                                Delete
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning small py-2">No photos available for this property.</div>
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