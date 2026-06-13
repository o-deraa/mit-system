@extends('layouts.app', ['title' => 'Edit Kelompok Warga'])

@section('content')
<h1 class="h3 mb-3">Edit Kelompok {{ $group->kode_kelompok }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.kelompok-warga.update', $group->kelompok_warga_id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Perwakilan</label>
                <input type="text" class="form-control"
                       value="{{ $group->representative?->nama }} | {{ $group->representative?->nrp }} | {{ $group->representative?->angkatan }}"
                       disabled>
                <div class="form-text">Perubahan perwakilan tidak dibuat di tahap ini agar relasi anggota tetap aman.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Nomor WA Perwakilan</label>
                <input type="text" name="nomor_wa_perwakilan" value="{{ old('nomor_wa_perwakilan', $group->nomor_wa_perwakilan) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Rules / Catatan Kelompok</label>
                <textarea name="rules" class="form-control" rows="4">{{ old('rules', $group->rules) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Status Kelompok</label>
                <select name="status" class="form-select" required>
                    <option value="final" @selected(old('status', $group->status) === 'final')>final</option>
                    <option value="draft" @selected(old('status', $group->status) === 'draft')>draft</option>
                </select>
            </div>

            <button class="btn btn-dark">Simpan Perubahan</button>
            <a href="{{ route('admin.kelompok-warga.show', $group->kelompok_warga_id) }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
@endsection
