@extends('layouts.app', ['title' => 'Booking Accepted'])

@section('content')
<h1 class="h3 mb-3">Booking Accepted</h1>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
            <tr>
                <th>ID</th>
                <th>Minggu</th>
                <th>Dibuat Oleh</th>
                <th>Jadwal Final</th>
                <th>Lokasi Final</th>
                <th>Peserta</th>
                <th>Catatan Warga</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>{{ $booking->booking_id }}</td>
                    <td>Minggu {{ $booking->week?->week_number ?? '-' }}</td>
                    <td>
                        {{ $booking->creator?->nama ?? '-' }}<br>
                        <small class="text-muted">{{ $booking->creator?->nrp ?? '-' }}</small>
                    </td>
                    <td>{{ $booking->final_schedule ?: '-' }}</td>
                    <td>{{ $booking->final_location ?: '-' }}</td>
                    <td>
                        {{ $booking->participants->whereIn('status', ['joined', 'present'])->count() }}
                    </td>
                    <td>{{ $booking->warga_notes ?: '-' }}</td>
                    <td>
                        <a href="{{ route('warga.booking.show', $booking->booking_id) }}"
                           class="btn btn-sm btn-info">
                            Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">
                        Belum ada booking accepted.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $bookings->links() }}
    </div>
</div>
@endsection
