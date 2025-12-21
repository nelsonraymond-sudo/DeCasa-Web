@extends('layouts.admin')

@section('title', 'Property List')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark" style="letter-spacing: -0.5px;">PROPERTY LIST</h4>
    <a href="{{ route('admin.properti.create') }}" class="btn btn-primary shadow-sm rounded-0">
        + Add New Property
    </a>
</div>

<div class="row">
    @forelse($properti as $p)
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm bg-white overflow-hidden">
            <div class="row g-0">
                <div class="col-md-4" style="min-height: 260px;">
                    @if(isset($p->url_foto) && $p->url_foto)
                        <img src="{{ asset('storage/' . $p->url_foto) }}" class="h-100 w-100" style="object-fit: cover;">
                    @else
                        <img src="https://placehold.co/600x400?text=No+Image" class="h-100 w-100" style="object-fit: cover; background: #eee;">
                    @endif
                </div>
                
                <div class="col-md-8">
                    <div class="card-body p-4 h-100 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h4 class="fw-bold mb-1 text-dark">{{ $p->nm_properti }}</h4>
                                <div class="text-muted small">📍 {{ $p->alamat }}</div>
                            </div>
                            <span class="badge bg-success px-3 py-2">AVAILABLE</span>
                        </div>

                        <p class="text-muted mt-3 mb-4 flex-grow-1" style="line-height: 1.6;">
                            {{ Str::limit($p->deskripsi, 180) }}
                        </p>

                        <div class="mt-auto border-top pt-3 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small">Price per month</span>
                                <div class="fw-bold text-success fs-4">Rp {{ number_format($p->harga, 0, ',', '.') }}</div>
                            </div>
                            
                        <a href="{{ route('admin.properti.show', $p->id_properti) }}" class="btn btn-outline-primary px-4" title="View Detail">
                        View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <h5 class="text-muted">Tidak ada properti yang tersedia saat ini.</h5>
    </div>
    @endforelse
</div>
@endsection