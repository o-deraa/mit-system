@extends('layouts.app', ['title' => 'Proses Verifikasi'])

@section('content')
<h1 class="h3 mb-3">Proses Verifikasi #{{ $detail['verification_id'] }}</h1>

<div class="card mb-4">
    <div class="card-header">Detail Request</div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th>Maba</th><td>{{ $detail['maba']['nama'] }} | {{ $detail['maba']['nrp'] }}</td></tr>
            <tr><th>Minggu</th><td>Minggu {{ $detail['week']['week_number'] }}</td></tr>
            <tr><th>Booking</th><td>{{ $detail['booking']['id'] }} | {{ $detail['booking']['status'] }}</td></tr>
            <tr><th>Kelompok</th><td>Kelompok {{ $detail['kelompok']['kode_kelompok'] }}</td></tr>
            <tr><th>Perwakilan</th><td>{{ $detail['kelompok']['perwakilan'] }}</td></tr>
            <tr><th>Jadwal</th><td>{{ $detail['booking']['final_schedule'] ?: '-' }}</td></tr>
            <tr><th>Lokasi</th><td>{{ $detail['booking']['final_location'] ?: '-' }}</td></tr>
        </table>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Bukti Foto Buku MIT</div>
    <div class="card-body">
        <p><strong>File:</strong> {{ $detail['upload']['file_name'] }}</p>
        <p><strong>Path:</strong> {{ $detail['upload']['file_path'] }}</p>
        <p><strong>Mime:</strong> {{ $detail['upload']['mime_type'] }}</p>
        <p><strong>Notes:</strong> {{ $detail['upload']['notes'] }}</p>

        @if($detail['upload']['file_path'] && $detail['upload']['file_path'] !== '-')
            <img src="{{ asset('storage/' . $detail['upload']['file_path']) }}"
                 class="img-fluid border rounded"
                 style="max-height: 500px"
                 alt="Bukti Buku MIT">
        @else
            <div class="alert alert-warning mb-0">Tidak ada file bukti yang bisa ditampilkan.</div>
        @endif
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Klaim Maba</div>
    <div class="card-body">
        <table class="table table-bordered mb-0">
            <tr><th>Claim TTD 2022</th><td>{{ $detail['claimed']['ttd_2022'] }}</td></tr>
            <tr><th>Claim TTD 2023</th><td>{{ $detail['claimed']['ttd_2023'] }}</td></tr>
            <tr><th>Claim TTD 2024</th><td>{{ $detail['claimed']['ttd_2024'] }}</td></tr>
            <tr><th>Total Claim</th><td>{{ $detail['claimed']['total'] }}</td></tr>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">Keputusan Admin</div>
    <div class="card-body">
        <form action="{{ route('admin.verification.process', $detail['verification_id']) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Status Verifikasi</label>
                <select name="status" class="form-select" required>
                    <option value="verified">verified</option>
                    <option value="revision">revision</option>
                    <option value="rejected">rejected</option>
                </select>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Verified TTD 2022</label>
                    <input type="number" name="verified_ttd_2022" class="form-control" min="0"
                           value="{{ $detail['claimed']['ttd_2022'] }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Verified TTD 2023</label>
                    <input type="number" name="verified_ttd_2023" class="form-control" min="0"
                           value="{{ $detail['claimed']['ttd_2023'] }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Verified TTD 2024</label>
                    <input type="number" name="verified_ttd_2024" class="form-control" min="0"
                           value="{{ $detail['claimed']['ttd_2024'] }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Catatan Admin</label>
                <textarea name="admin_comment" class="form-control" rows="4"></textarea>
                <div class="form-text">Wajib diisi jika status revision atau rejected.</div>
            </div>

            <button class="btn btn-dark">Proses Verifikasi</button>
            <a href="{{ route('admin.verification.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
@endsection
