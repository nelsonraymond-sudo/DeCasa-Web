@extends('layouts.admin')

@section('title', 'Manage Property')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark" style="letter-spacing: -0.5px;">MANAGE PROPERTY</h4>
    <a href="{{ route('admin.properti.create') }}" class="btn btn-primary shadow-sm rounded-0">
        + Add New Property
    </a>
</div>

<div class="row">
    @foreach($properti as $p)
    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm h-100 bg-white rounded-0">
            <div style="height: 220px; overflow: hidden; background: #eee;">
                @php
                    $thumbnail = DB::table('foto')->where('id_properti', $p->id_properti)->first();
                @endphp

                @if($thumbnail)
                    @if(Str::startsWith($thumbnail->url_foto, 'http'))
                        <img src="{{ $thumbnail->url_foto }}" class="w-100 h-100" style="object-fit: cover;">
                    @else
                        <img src="{{ asset('storage/' . $thumbnail->url_foto) }}" class="w-100 h-100" style="object-fit: cover;">
                    @endif
                @else
                    <img src="https://placehold.co/600x400?text=No+Image" class="w-100 h-100" style="object-fit: cover;">
                @endif
            </div>
            
            <div class="card-body p-4">
                <div class="d-flex justify-content-between mb-2">
                    <h5 class="fw-bold mb-0 text-dark">{{ $p->nm_properti }}</h5>
                    <span class="badge rounded-0 {{ $p->status == 'available' ? 'bg-success' : 'bg-secondary' }}">
                        {{ strtoupper($p->status) }}
                    </span>
                </div>

                <div class="text-muted small mb-3">
                    @php
                        $fasilitas = DB::table('detailfasilitas')
                            ->join('fasilitas', 'detailfasilitas.id_fasilitas', '=', 'fasilitas.id_fasilitas')
                            ->where('detailfasilitas.id_properti', $p->id_properti)
                            ->limit(3) // Tampilkan 3 saja di kartu depan
                            ->get();
                    @endphp

                    @forelse($fasilitas as $f)
                        <span class="me-2">✔ {{ $f->nm_fasilitas }}</span>
                    @empty
                        <span class="text-warning">No facilities listed</span>
                    @endforelse
                </div>

                <p class="text-muted small" style="line-height: 1.6;">
                    {{ Str::limit($p->deskripsi, 90) }}
                </p>
                
                <hr style="border-top: 1px dashed #ddd;">

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <span class="fw-bold fs-5 text-dark">Rp {{ number_format($p->harga, 0, ',', '.') }}</span>
                    
                    <div class="d-flex gap-1">
                        
                        <a href="{{ route('admin.properti.edit', $p->id_properti) }}" class="btn btn-sm btn-outline-warning border-0" title="Edit">
                            Edit
                        </a>

                        <form action="{{ route('admin.properti.destroy', $p->id_properti) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Delete item?')" title="Delete">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection