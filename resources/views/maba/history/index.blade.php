@extends('layouts.app', ['title' => 'Riwayat Kelompok'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Riwayat Kelompok Warga</h1>
    <a href="{{ route('maba.history.check') }}" class="btn btn-dark">Cek Pernah Bertemu</a>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>History ID</th>
                <th>Kelompok</th>
                <th>Perwakilan</th>
                <th>Minggu</th>
                <th>Booking ID</th>
                <th>Tanggal Tercatat</th>
            </tr>
            </thead>
            <tbody>
            @forelse($histories as $history)
                <tr>
                    <td>{{ $history->history_id }}</td>
                    <td>Kelompok {{ $history->group?->kode_kelompok }}</td>
                    <td>{{ $history->group?->representativeMember?->warga?->nama }}</td>
                    <td>Minggu {{ $history->week?->week_number }}</td>
                    <td>{{ $history->booking_id }}</td>
                    <td>{{ $history->created_at }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada riwayat kelompok warga.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $histories->links() }}
    </div>
</div>
@endsection
