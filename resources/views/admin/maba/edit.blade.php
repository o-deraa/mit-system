@extends('layouts.app', ['title' => 'Edit Maba'])

@section('content')
<h1 class="h3 mb-3">Edit Maba</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.maba.update', $maba->maba_id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" value="{{ old('nama', $maba->nama) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">NRP</label>
                <input type="text" name="nrp" value="{{ old('nrp', $maba->nrp) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <input type="text" name="password" class="form-control">
                <div class="form-text">Kosongkan jika tidak ingin mengubah password.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <option value="active" @selected(old('status', $maba->status) === 'active')>active</option>
                    <option value="inactive" @selected(old('status', $maba->status) === 'inactive')>inactive</option>
                </select>
            </div>

            <button class="btn btn-dark">Simpan Perubahan</button>
            <a href="{{ route('admin.maba.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
@endsection
