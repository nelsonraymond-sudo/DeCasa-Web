@extends('layouts.admin')

@section('title', 'Transaction Report')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 fw-bold text-muted">DATA TRANSAKSI</h6>
        <button class="btn btn-sm btn-outline-dark" onclick="window.print()">🖨 Print PDF</button>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="bg-light text-secondary text-uppercase small fw-bold">
                    <tr>
                        <th class="py-3 ps-4">ID Transaksi</th>
                        <th class="py-3">Tanggal</th>
                        <th class="py-3">Customer</th>
                        <th class="py-3">Properti</th>
                        <th class="py-3 text-end pe-4">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan as $l)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">#{{ $l->id_trans }}</td>
                        <td>{{ date('d/m/Y', strtotime($l->tgl_trans)) }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $l->nm_user }}</div>
                        </td>
                        <td>{{ $l->nm_properti }}</td>
                        <td class="text-end pe-4 fw-bold text-success">
                            Rp {{ number_format($l->total_harga, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            Belum ada data transaksi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection