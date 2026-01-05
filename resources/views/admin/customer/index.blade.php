@extends('layouts.admin')

@section('title', 'Customer')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3 text-secondary">Customer</th>
                        <th class="py-3 text-secondary">Contact</th>
                        <th class="py-3 text-secondary">Registered</th>
                        <th class="py-3 text-center text-secondary">Total Transaction</th>
                        <th class="px-4 py-3 text-end text-secondary">Total Spending</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pelanggan as $p)
                    <tr>
                        <td class="px-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px;">
                                    {{ substr($p->nm_user, 0, 1) }}
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $p->nm_user }}</h6>
                                    <small class="text-muted">ID: {{ $p->id_user }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <small><i class="bi bi-envelope me-2"></i>{{ $p->email }}</small>
                                <small class="text-muted"><i class="bi bi-telephone me-2"></i>{{ $p->no_hp ?? '-' }}</small>
                            </div>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($p->tgl_daftar)->format('d M Y') }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary bg-opacity-10 text-dark rounded-pill px-3">
                                {{ $p->total_booking }} Transaction
                            </span>
                        </td>
                        <td class="px-4 text-end fw-bold text-success">
                            Rp {{ number_format($p->total_spent, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">There is no Customers data yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection