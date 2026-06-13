@extends('layouts.app', ['title' => 'Tambah Warga'])

@section('content')
<h1 class="h3 mb-3">Tambah Warga</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.warga.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" value="{{ old('nama') }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">NRP</label>
                <input type="text" name="nrp" value="{{ old('nrp') }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Angkatan</label>
                <select name="angkatan" class="form-select" required>
                    <option value="2022" @selected(old('angkatan') == '2022')>2022</option>
                    <option value="2023" @selected(old('angkatan') == '2023')>2023</option>
                    <option value="2024" @selected(old('angkatan', '2024') == '2024')>2024</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="text" name="password" value="{{ old('password') }}" class="form-control">
                <div class="form-text">Kosongkan jika ingin password default mengikuti NRP.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <option value="active" @selected(old('status', 'active') === 'active')>active</option>
                    <option value="inactive" @selected(old('status') === 'inactive')>inactive</option>
                </select>
            </div>

            <button class="btn btn-dark">Simpan</button>
            <a href="{{ route('admin.warga.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
@endsection
