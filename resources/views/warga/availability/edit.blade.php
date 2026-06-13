@extends('layouts.app', ['title' => 'Input Ketersediaan Mingguan'])

@section('content')
<h1 class="h3 mb-3">Input Ketersediaan Mingguan</h1>

<div class="card">
    <div class="card-body">
        <p><strong>Kelompok:</strong> Kelompok {{ $group->kode_kelompok }}</p>
        <p><strong>Minggu Aktif:</strong> Minggu {{ $week->week_number }}</p>

        <form action="{{ route('warga.availability.update') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Apakah kelompok tersedia?</label>
                <select name="is_available" class="form-select" required>
                    <option value="1" @selected(old('is_available', $availability->is_available ?? 1) == 1)>Ya, tersedia</option>
                    <option value="0" @selected(old('is_available', $availability->is_available ?? 1) == 0)>Tidak tersedia</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Mode Sesi</label>
                <select name="session_mode" class="form-select" required>
                    <option value="4" @selected(old('session_mode', $availability->session_mode ?? 4) == 4)>4 + 4 + 4</option>
                    <option value="6" @selected(old('session_mode', $availability->session_mode ?? 4) == 6)>6 + 6</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Catatan</label>
                <textarea name="notes" class="form-control" rows="4">{{ old('notes', $availability->notes ?? '') }}</textarea>
            </div>

            <button class="btn btn-dark">Simpan Ketersediaan</button>
            <a href="{{ route('warga.dashboard') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
@endsection
