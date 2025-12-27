@extends('layouts.admin')

@section('title', 'Account Settings')

@section('content')
<div class="row">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-person-gear me-2"></i>Edit My Profile</h6>
            </div>
            <div class="card-body p-4">
    
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.setting.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Name</label>
                            <input type="text" name="nm_user" class="form-control" value="{{ old('nm_user', $user->nm_user) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Phone Number</label>
                            <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $user->no_hp) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small">Email Address</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <hr class="my-4">
                    <p class="text-muted small mb-3"><i class="bi bi-info-circle me-1"></i> Leave blank if you don't want to change the password.</p>

                    <div class="mb-3">
                        <label class="form-label text-muted small">New Password</label>
                        <input type="password" name="password" class="form-control" placeholder="******">
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="******">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-2">
                            <i class="bi bi-save me-2"></i> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection