@extends('layouts.app', ['title' => 'Bentuk Kelompok Warga'])

@section('content')
<h1 class="h3 mb-3">Bentuk Kelompok Warga</h1>

<div class="alert alert-info">
    Kode kelompok dibuat otomatis oleh sistem. Pilih 1 perwakilan dan minimal 1 anggota tambahan.
    Total anggota termasuk perwakilan harus 2 sampai 4 warga.
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.kelompok-warga.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Perwakilan Kelompok</label>
                <select name="warga_id" class="form-select" required>
                    <option value="">-- Pilih Perwakilan --</option>
                    @foreach($floatingWarga as $warga)
                        <option value="{{ $warga->warga_id }}" @selected(old('warga_id') == $warga->warga_id)>
                            {{ $warga->nama }} | {{ $warga->nrp }} | {{ $warga->angkatan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Anggota Tambahan</label>
                <select name="member_ids[]" class="form-select" multiple required>
                    @foreach($floatingWarga as $warga)
                        <option value="{{ $warga->warga_id }}" @selected(in_array($warga->warga_id, old('member_ids', [])))>
                            {{ $warga->nama }} | {{ $warga->nrp }} | {{ $warga->angkatan }}
                        </option>
                    @endforeach
                </select>
                <div class="form-text">
                    Tahan Ctrl untuk memilih beberapa anggota. Jangan pilih perwakilan lagi sebagai anggota tambahan.
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Nomor WA Perwakilan</label>
                <input type="text" name="nomor_wa_perwakilan" value="{{ old('nomor_wa_perwakilan') }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Rules / Catatan Kelompok</label>
                <textarea name="rules" class="form-control" rows="4">{{ old('rules') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Status Kelompok</label>
                <select name="status" class="form-select" required>
                    <option value="final" @selected(old('status', 'final') === 'final')>final</option>
                    <option value="draft" @selected(old('status') === 'draft')>draft</option>
                </select>
            </div>

            <button class="btn btn-dark">Bentuk Kelompok</button>
            <a href="{{ route('admin.kelompok-warga.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
@endsection
