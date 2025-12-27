@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-4 h-100 bg-white">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted text-uppercase small fw-bold mb-1">Total Unit</p>
                    <h2 class="fw-bold text-dark m-0">{{ $totalUnit }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-4 h-100 bg-white">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted text-uppercase small fw-bold mb-1">Available</p>
                    <h2 class="fw-bold text-success m-0">{{ $available }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-4 h-100 bg-white">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted text-uppercase small fw-bold mb-1">Occupied</p>
                    <h2 class="fw-bold text-secondary m-0">{{ $occupied }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-4 h-100 bg-white">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted text-uppercase small fw-bold mb-1">Total Revenue</p>
                    <h4 class="fw-bold text-dark m-0">Rp {{ number_format($revenue, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold mb-3">Quick Actions</h5>
            <div class="d-flex gap-3">
                <a href="{{ route('admin.properti.create') }}" class="btn btn-outline-primary px-4 py-2">
                    + Add New Property
                </a>
                <a href="{{ route('admin.laporan.index') }}" class="btn btn-primary px-4 py-2">
                    View Reports
                </a>
            </div>
        </div>
    </div>
</div>
@endsection