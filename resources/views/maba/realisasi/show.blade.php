@extends('layouts.app', ['title' => 'Detail Realisasi'])

@section('content')
<h1 class="h3 mb-3">Detail Realisasi #{{ $realisasi->realisasi_id }}</h1>

<div class="card mb-4">
    <div class="card-header">Informasi Realisasi</div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th>Status</th><td><x-realisasi-status :status="$realisasi->status" /></td></tr>
            <tr><th>Booking ID</th><td>{{ $realisasi->booking_id }}</td></tr>
            <tr><th>Kelompok</th><td>Kelompok {{ $realisasi->booking?->group?->kode_kelompok }}</td></tr>
            <tr><th>Submitted At</th><td>{{ $realisasi->submitted_at }}</td></tr>
            <tr><th>Catatan Umum</th><td>{{ $realisasi->general_notes ?: '-' }}</td></tr>
            <tr><th>Warga Tidak Hadir</th><td>{{ $realisasi->absent_warga_notes ?: '-' }}</td></tr>
            <tr><th>Warga Tambahan</th><td>{{ $realisasi->additional_warga_notes ?: '-' }}</td></tr>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">Verification Result</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Maba</th>
                <th>Status</th>
                <th>Claim 2022</th>
                <th>Claim 2023</th>
                <th>Claim 2024</th>
                <th>Verified 2022</th>
                <th>Verified 2023</th>
                <th>Verified 2024</th>
                <th>Catatan Admin</th>
            </tr>
            </thead>
            <tbody>
            @foreach($realisasi->verificationResults as $item)
                <tr>
                    <td>{{ $item->maba?->nama }} | {{ $item->maba?->nrp }}</td>
                    <td>{{ $item->status }}</td>
                    <td>{{ $item->claimed_ttd_2022 }}</td>
                    <td>{{ $item->claimed_ttd_2023 }}</td>
                    <td>{{ $item->claimed_ttd_2024 }}</td>
                    <td>{{ $item->verified_ttd_2022 }}</td>
                    <td>{{ $item->verified_ttd_2023 }}</td>
                    <td>{{ $item->verified_ttd_2024 }}</td>
                    <td>{{ $item->admin_comment ?: '-' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
