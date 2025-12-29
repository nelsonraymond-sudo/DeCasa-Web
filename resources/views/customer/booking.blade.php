@extends('layouts.customer') {{-- Sesuaikan dengan layout utama Anda --}}

@section('content')
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h2 class="fw-bold mb-4">Order Confirmation</h2>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm p-4">
                       <form action="{{ route('booking.process') }}" method="POST">
    @csrf
    
    <input type="hidden" name="id_properti" value="{{ $properti->id_properti }}">

    <div class="mb-3">
        <label>Check-in Date</label>
        <input type="date" name="checkin" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Check-out Date</label>
        <input type="date" name="checkout" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Payment Method</label>
        <select name="metode_bayar" class="form-select" required>
            <option value="">-- Choose Payment --</option>
            @foreach($payment as $p)
        <option value="{{ $p->id_metode }}">{{ $p->nama_bank }}</option>
    @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-success w-100">Confirm & Pay Now</button>
</form>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card border-0 shadow-sm bg-light">
                        <img src="{{ $properti->url_foto ?? asset('img/no-image.jpg') }}" class="card-img-top" alt="Properti" style="height: 200px; object-fit: cover;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold">{{ $properti->nm_properti }}</h5>
                            <p class="text-muted small"><i class="bi bi-geo-alt"></i> {{ $properti->alamat }}</p>
                            
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span>Price per Day</span>
                                <span class="fw-bold text-primary">Rp {{ number_format($properti->harga, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <span>Owner</span>
                                <span>{{ $properti->nama_pemilik }}</span>
                            </div>
                            
                            <div class="alert alert-info mt-3 small">
                                <i class="bi bi-info-circle"></i> The total price will be calculated automatically by the system after you click Book.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection