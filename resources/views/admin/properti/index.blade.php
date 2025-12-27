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
                <div class="col-md-4 position-relative" style="min-height: 260px;">
                    @if(isset($p->url_foto) && $p->url_foto)
                        <img src="{{ Str::startsWith($p->url_foto, 'http') ? $p->url_foto : asset('storage/' . $p->url_foto) }}" 
                             class="h-100 w-100 position-absolute" 
                             style="object-fit: cover; top: 0; left: 0;" 
                             alt="{{ $p->nm_properti }}">
                             
                    @else
                        <div class="h-100 w-100 d-flex align-items-center justify-content-center bg-light text-muted position-absolute">
                            <div class="text-center">
                                <i class="bi bi-image fs-1"></i>
                                <p class="small m-0">No Image</p>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="col-md-8">
                    <div class="card-body p-4 h-100 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h4 class="fw-bold mb-1 text-dark">{{ $p->nm_properti }}</h4>
                                <div class="text-muted small">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $p->alamat }}
                                </div>
                            </div>
                            
                            @if($p->status == 'available')
                                <span class="badge bg-success px-3 py-2 text-uppercase">AVAILABLE</span>
                            @else
                                <span class="badge bg-secondary px-3 py-2 text-uppercase">{{ $p->status }}</span>
                            @endif
                        </div>

                        <p class="text-muted mt-3 mb-4 flex-grow-1" style="line-height: 1.6;">
                            {{ Str::limit($p->deskripsi, 180) }}
                        </p>

                        <div class="mt-auto border-top pt-3 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small">Price per month</span>
                                <div class="fw-bold text-success fs-4">Rp {{ number_format($p->harga, 0, ',', '.') }}</div>
                            </div>
                            
                            <a href="{{ route('admin.properti.show', $p->id_properti) }}" class="btn btn-outline-primary px-4 rounded-pill">
                                View Details <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <div class="mb-3">
            <i class="bi bi-house-x text-muted" style="font-size: 3rem;"></i>
        </div>
        <h5 class="text-muted">Tidak ada properti yang tersedia saat ini.</h5>
        <p class="text-muted small">Silakan tambahkan properti baru.</p>
    </div>
    @endforelse
</div>
@endsection