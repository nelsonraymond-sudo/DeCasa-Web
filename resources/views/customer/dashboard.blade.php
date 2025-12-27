@extends('layouts.customer')

@section('content')
<div class="container mt-5 pt-5">
    <h2 class="mb-4 fw-bold">My History</h2>

    {{-- Alert Pesan Sukses/Gagal --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary">Booking History</h5>
        </div>
        <div class="card-body">
            @if($transactions->isEmpty())
                <div class="text-center py-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" width="100" class="mb-3 opacity-50">
                    <p class="text-muted">No booking history yet.</p>
                    <a href="{{ url('/') }}" class="btn btn-primary">Search Property</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID Transaction</th>
                                <th>Properties</th>
                                <th>Schedule</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $t)
                            @php $idTrans = $t->id_transaksi ?? $t->id_trans; @endphp
                            
                            <tr>
                                <td class="fw-bold">#{{ $idTrans }}</td>
                                <td>
                                    {{ $t->properti->nm_properti ?? 'Properti Tidak Ditemukan' }}
                                </td>
                                <td>
                                    <small class="d-block text-muted">In: {{ \Carbon\Carbon::parse($t->checkin)->format('d M Y') }}</small>
                                    <small class="d-block text-muted">Out: {{ \Carbon\Carbon::parse($t->checkout)->format('d M Y') }}</small>
                                </td>
                                <td class="fw-bold text-success">
                                    Rp {{ number_format($t->total_harga, 0, ',', '.') }}
                                </td>
                                <td>
                                    @if($t->status == 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($t->status == 'lunas')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif($t->status == 'batal')
                                        <span class="badge bg-danger">Cancelled</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($t->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($t->status != 'batal' && $t->status != 'selesai')
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#cancelModal-{{ $idTrans }}">
                                            Cancel
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-light" disabled>Not available</button>
                                    @endif
                                </td>
                            </tr>

                            <div class="modal fade" id="cancelModal-{{ $idTrans }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('customer.transaksi.cancel', $idTrans) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            
                                            <div class="modal-header">
                                                <h5 class="modal-title text-danger">Cancel Booking #{{ $idTrans }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            
                                            <div class="modal-body">
                                                <div class="alert alert-warning">
                                                    <small><i class="bi bi-info-circle"></i> Cancellations cannot be made if the status is Paid in Full and less than 2 days before check-in.</small>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Reasons for Cancellation <span class="text-danger">*</span></label>
                                                    <textarea name="alasan" class="form-control" rows="3" required placeholder="Ex: There is a sudden event / Wrong date selection"></textarea>
                                                </div>
                                            </div>
                                            
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection