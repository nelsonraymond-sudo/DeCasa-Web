@extends('layouts.admin') 
@section('title', 'Transaction List')
@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Transaction List </h1>
    <p class="text-muted">Manage customer payments and booking status.</p>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white fw-bold">
            <i class="fas fa-table me-1"></i> Transaction Data
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID Transaction</th>
                            <th>Customer</th>
                            <th>Properties</th>
                            <th>Check-in Date</th>
                            <th>Total Price</th>
                            <th>Current Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $trx)
                        <tr>
                            <td class="fw-bold">{{ $trx->id_trans }}</td>
                            <td>
                                <div class="fw-bold">{{ $trx->user->nm_user ?? 'Deleted User' }}</div>
                                <small class="text-muted">{{ $trx->user->email ?? '-' }}</small>
                            </td>
                            <td>{{ $trx->properti->nm_properti ?? 'Deleted Properties' }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($trx->checkin)->format('d M Y') }}
                                <br>
                                <small class="text-muted">Duration: {{ $trx->durasi }} Day</small>
                            </td>
                            <td class="fw-bold text-success">
                                Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                            </td>
                            <td>
                                @if($trx->status == 'lunas')
                                    <span class="badge bg-success rounded-pill px-3">Paid Off</span>
                                @elseif($trx->status == 'pending')
                                    <span class="badge bg-warning text-dark rounded-pill px-3">Pending</span>
                                @else
                                    <span class="badge bg-danger rounded-pill px-3">{{ ucfirst($trx->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($trx->status == 'pending')
                                    <form action="{{ route('admin.transaksi.confirm', $trx->id_trans) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to mark this transaction as Paid?');">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="bi bi-check-circle"></i> Received / Paid Off
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.transaksi.destroy', $trx->id_trans) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this transaction?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-x-circle"></i>Cancel
                                        </button>
                                    </form>
                                @elseif($trx->status == 'lunas')
                                    <button class="btn btn-sm btn-secondary" disabled>
                                        <i class="bi bi-check-all"></i> Verified
                                    </button>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                No transaction data has been entered yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection