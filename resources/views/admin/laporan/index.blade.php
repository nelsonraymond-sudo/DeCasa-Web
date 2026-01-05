@extends('layouts.admin')

@section('title', 'Transaction Report')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <div>
            <h6 class="m-0 fw-bold text-muted">Transaction Data</h6>
        </div>
        <button class="btn btn-sm btn-outline-primary" onclick="window.print()">Print PDF</button>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="bg-light text-secondary text-uppercase small fw-bold">
                    <tr>
                        <th class="py-3 ps-4">ID Transaction</th>
                        <th class="py-3">Date</th>
                        <th class="py-3">Customer</th>
                        <th class="py-3">Property</th>
                        <th class="py-3">Status</th> 
                        <th class="py-3 text-end pe-4">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan as $l)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">#{{ $l->id_transaksi }}</td>
                        
                        <td>{{ date('d/m/Y', strtotime($l->tanggal_book)) }}</td>
                        
                        <td>
                            <div class="fw-bold text-dark">{{ $l->nama_customer }}</div>
                        </td>
                        
                        <td>{{ $l->nama_properti }}</td>

                        <td>
                            @if($l->status == 'lunas')
                                <span class="badge bg-success">Paid</span>
                            @elseif($l->status == 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($l->status == 'batal')
                                <span class="badge bg-danger">Cancelled</span>
                            @else
                                <span class="badge bg-secondary">{{ $l->status }}</span>
                            @endif
                        </td>
                        
                        <td class="text-end pe-4 fw-bold text-success">
                            Rp {{ number_format($l->total_harga, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-file-earmark-x fs-1 mb-2"></i>
                                <span>There is no transaction data yet.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection