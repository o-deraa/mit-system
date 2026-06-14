@extends('layouts.app', ['title' => 'Login MIT'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <strong>Login MIT Management System</strong>
            </div>

            <div class="card-body">
                <form action="{{ route('mit.login.post') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Login Sebagai</label>
                        <select name="role" id="role" class="form-select" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                            <option value="warga" @selected(old('role') === 'warga')>Warga</option>
                            <option value="maba" @selected(old('role') === 'maba')>Maba</option>
                        </select>
                    </div>

                    <div class="mb-3" id="username-wrapper">
                        <label class="form-label">Username Admin</label>
                        <input type="text" name="username" class="form-control" value="{{ old('username') }}">
                    </div>

                    <div class="mb-3" id="nrp-wrapper">
                        <label class="form-label">NRP</label>
                        <input type="text" name="nrp" class="form-control" value="{{ old('nrp') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-dark w-100">
                        Login
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function syncLoginFields() {
        const role = document.getElementById('role').value;
        const usernameWrapper = document.getElementById('username-wrapper');
        const nrpWrapper = document.getElementById('nrp-wrapper');

        if (role === 'admin') {
            usernameWrapper.style.display = 'block';
            nrpWrapper.style.display = 'none';
        } else {
            usernameWrapper.style.display = 'none';
            nrpWrapper.style.display = 'block';
        }
    }

    document.getElementById('role').addEventListener('change', syncLoginFields);
    syncLoginFields();
</script>
@endpush
