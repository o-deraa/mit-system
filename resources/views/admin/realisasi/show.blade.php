@extends('layouts.app', ['title' => 'Detail Realisasi Admin'])

@section('content')
<h1 class="h3 mb-3">Detail Realisasi #{{ $realisasi->realisasi_id }}</h1>

<div class="card mb-4">
    <div class="card-header">Informasi Realisasi</div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th>Status</th><td>{{ $realisasi->status }}</td></tr>
            <tr><th>Booking ID</th><td>{{ $realisasi->booking_id }}</td></tr>
            <tr><th>Minggu</th><td>Minggu {{ $realisasi->week?->week_number }}</td></tr>
            <tr><th>Kelompok</th><td>Kelompok {{ $realisasi->booking?->group?->kode_kelompok }}</td></tr>
            <tr><th>Submitted By</th><td>{{ $realisasi->submitter?->nama }} | {{ $realisasi->submitter?->nrp }}</td></tr>
            <tr><th>Submitted At</th><td>{{ $realisasi->submitted_at }}</td></tr>
            <tr><th>Catatan Umum</th><td>{{ $realisasi->general_notes ?: '-' }}</td></tr>
            <tr><th>Warga Tidak Hadir</th><td>{{ $realisasi->absent_warga_notes ?: '-' }}</td></tr>
            <tr><th>Warga Tambahan</th><td>{{ $realisasi->additional_warga_notes ?: '-' }}</td></tr>
        </table>
    </div>
</div>

@php
    $totalClaimed2022 = $realisasi->verificationResults->sum('claimed_ttd_2022');
    $totalClaimed2023 = $realisasi->verificationResults->sum('claimed_ttd_2023');
    $totalClaimed2024 = $realisasi->verificationResults->sum('claimed_ttd_2024');
    $totalClaimed = $totalClaimed2022 + $totalClaimed2023 + $totalClaimed2024;

    $totalVerified2022 = $realisasi->verificationResults->sum('verified_ttd_2022');
    $totalVerified2023 = $realisasi->verificationResults->sum('verified_ttd_2023');
    $totalVerified2024 = $realisasi->verificationResults->sum('verified_ttd_2024');
    $totalVerified = $totalVerified2022 + $totalVerified2023 + $totalVerified2024;
@endphp

<div class="card mb-4">
    <div class="card-header">Ringkasan TTD Realisasi</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered mb-0">
            <thead>
            <tr>
                <th>Kategori</th>
                <th>Klaim Maba</th>
                <th>Aktual / Verified Admin</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>TTD 2022</td>
                <td>{{ $totalClaimed2022 }}</td>
                <td>{{ $totalVerified2022 }}</td>
            </tr>
            <tr>
                <td>TTD 2023</td>
                <td>{{ $totalClaimed2023 }}</td>
                <td>{{ $totalVerified2023 }}</td>
            </tr>
            <tr>
                <td>TTD 2024</td>
                <td>{{ $totalClaimed2024 }}</td>
                <td>{{ $totalVerified2024 }}</td>
            </tr>
            <tr>
                <th>Total</th>
                <th>{{ $totalClaimed }}</th>
                <th>{{ $totalVerified }}</th>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">Verification Result</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Verification ID</th>
                <th>Maba</th>
                <th>Status</th>
                <th>Claim 2022</th>
                <th>Claim 2023</th>
                <th>Claim 2024</th>
                <th>Total Claim</th>
                <th>Actual 2022</th>
                <th>Actual 2023</th>
                <th>Actual 2024</th>
                <th>Total Actual</th>
                <th>Catatan Admin</th>
                <th>Aksi</th>
            </tr>
            </thead>

            <tbody>
            @foreach($realisasi->verificationResults as $item)
                @php
                    $claimTotal =
                        $item->claimed_ttd_2022 +
                        $item->claimed_ttd_2023 +
                        $item->claimed_ttd_2024;

                    $verifiedTotal =
                        $item->verified_ttd_2022 +
                        $item->verified_ttd_2023 +
                        $item->verified_ttd_2024;
                @endphp

                <tr>
                    <td>{{ $item->verification_id }}</td>
                    <td>{{ $item->maba?->nama }} | {{ $item->maba?->nrp }}</td>
                    <td>{{ $item->status }}</td>
                    <td>{{ $item->claimed_ttd_2022 }}</td>
                    <td>{{ $item->claimed_ttd_2023 }}</td>
                    <td>{{ $item->claimed_ttd_2024 }}</td>
                    <td>{{ $claimTotal }}</td>
                    <td>{{ $item->verified_ttd_2022 }}</td>
                    <td>{{ $item->verified_ttd_2023 }}</td>
                    <td>{{ $item->verified_ttd_2024 }}</td>
                    <td>{{ $verifiedTotal }}</td>
                    <td>{{ $item->admin_comment ?: '-' }}</td>
                    <td>
                        @if($item->status === 'pending')
                            <a href="{{ route('admin.verification.show', $item->verification_id) }}"
                               class="btn btn-sm btn-info">
                                Verifikasi
                            </a>
                        @else
                            <a href="{{ route('admin.verification.show', $item->verification_id) }}"
                               class="btn btn-sm btn-secondary">
                                Detail
                            </a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
