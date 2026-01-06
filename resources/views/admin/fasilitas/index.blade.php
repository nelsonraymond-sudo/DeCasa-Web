@extends('layouts.admin')

@section('title', 'Manage Facilities')

@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary">Add New Facility</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.fasilitas.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted small">Facility Name</label>
                        <input type="text" name="nm_fasilitas" class="form-control" placeholder="Ex: Kolam Renang" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-dark">List Of Facility</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4" width="15%">ID</th>
                                <th>Facility Name</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fasilitas as $item)
                            <tr>
                                <td class="ps-4 text-muted small">{{ $item->id_fasilitas }}</td>
                                <td class="fw-bold text-dark">{{ $item->nm_fasilitas }}</td>
                                <td class="text-end pe-4">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-secondary me-1"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editModal{{ $item->id_fasilitas }}">
                                        Edit
                                    </button>

                                    <form action="{{ route('admin.fasilitas.destroy', $item->id_fasilitas) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus fasilitas ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>

                            <div class="modal fade" id="editModal{{ $item->id_fasilitas }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header border-0">
                                            <h5 class="modal-title">Edit Facility</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('admin.fasilitas.update', $item->id_fasilitas) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">ID Fasility</label>
                                                    <input type="text" class="form-control bg-light" value="{{ $item->id_fasilitas }}" readonly>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Facility Name</label>
                                                    <input type="text" name="nm_fasilitas" class="form-control" value="{{ $item->nm_fasilitas }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">There is no Facility data yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
@endsection