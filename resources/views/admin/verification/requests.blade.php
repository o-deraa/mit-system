@extends('layouts.app', ['title' => 'Request Verifikasi Pending'])

@section('content')
<h1 class="h3 mb-3">Request Verifikasi Pending — Minggu {{ $weekNumber }}</h1>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>Verification ID</th>
                <th>Realisasi ID</th>
                <th>Booking ID</th>
                <th>Maba</th>
                <th>NRP</th>
                <th>Kelompok</th>
                <th>TTD 2022</th>
                <th>TTD 2023</th>
                <th>TTD 2024</th>
                <th>Total Klaim</th>
                <th>Foto</th>
                <th>Submitted At</th>
                <th>Aksi</th>
            </tr>
            </thead>

            <tbody>
            @forelse($requests as $request)
                <tr>
                    <td>{{ $request['verification_id'] }}</td>
                    <td>{{ $request['realisasi_id'] }}</td>
                    <td>{{ $request['booking_id'] }}</td>
                    <td>{{ $request['nama_maba'] }}</td>
                    <td>{{ $request['nrp_maba'] }}</td>
                    <td>Kelompok {{ $request['kode_kelompok'] }}</td>
                    <td>{{ $request['claimed_ttd_2022'] }}</td>
                    <td>{{ $request['claimed_ttd_2023'] }}</td>
                    <td>{{ $request['claimed_ttd_2024'] }}</td>
                    <td>{{ $request['claimed_total'] }}</td>
                    <td>{{ $request['foto_name'] }}</td>
                    <td>{{ $request['submitted_at'] }}</td>
                    <td>
                        <a href="{{ route('admin.verification.show', $request['verification_id']) }}"
                           class="btn btn-sm btn-info">
                            Verifikasi
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center text-muted">
                        Tidak ada request verifikasi pending pada minggu ini.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <a href="{{ route('admin.verification.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>
@endsection
