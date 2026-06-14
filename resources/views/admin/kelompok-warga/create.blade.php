@extends('layouts.app', ['title' => 'Tambah Kelompok Warga'])

@section('content')
<h1 class="h3 mb-3">Tambah Kelompok Warga</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.kelompok-warga.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Kode Kelompok</label>
                <input type="number" name="kode_kelompok" class="form-control" value="{{ old('kode_kelompok') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Rules / Catatan Kelompok</label>
                <textarea name="rules" class="form-control" rows="4">{{ old('rules') }}</textarea>
            </div>

            <div class="alert alert-info">
                Kelompok baru otomatis berstatus <strong>draft</strong>. Setelah anggota dan perwakilan diatur, lakukan finalisasi dari halaman detail.
            </div>

            <button class="btn btn-dark">Simpan</button>
            <a href="{{ route('admin.kelompok-warga.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
@endsection
