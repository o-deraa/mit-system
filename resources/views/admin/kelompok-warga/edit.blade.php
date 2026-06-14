@extends('layouts.app', ['title' => 'Edit Kelompok Warga'])

@section('content')
<h1 class="h3 mb-3">Edit Kelompok Warga</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.kelompok-warga.update', $group->kelompok_warga_id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Kode Kelompok</label>
                <input type="number" name="kode_kelompok" class="form-control"
                       value="{{ old('kode_kelompok', $group->kode_kelompok) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Rules / Catatan Kelompok</label>
                <textarea name="rules" class="form-control" rows="4">{{ old('rules', $group->rules) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <option value="draft" @selected(old('status', $group->status) === 'draft')>draft</option>
                    <option value="final" @selected(old('status', $group->status) === 'final')>final</option>
                </select>
            </div>

            <button class="btn btn-dark">Update</button>
            <a href="{{ route('admin.kelompok-warga.show', $group->kelompok_warga_id) }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
@endsection
