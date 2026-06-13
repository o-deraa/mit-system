@extends('layouts.app', ['title' => 'Booking Saya'])

@section('content')
<h1 class="h3 mb-3">Booking Saya</h1>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Kelompok</th>
                <th>Status</th>
                <th>Jadwal</th>
                <th>Lokasi</th>
                <th>Peserta</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>{{ $booking->booking_id }}</td>
                    <td>Kelompok {{ $booking->group?->kode_kelompok }}</td>
                    <td>{{ $booking->status }}</td>
                    <td>{{ $booking->final_schedule ?: '-' }}</td>
                    <td>{{ $booking->final_location ?: '-' }}</td>
                    <td>{{ $booking->participants->whereIn('status', ['joined', 'present'])->count() }}</td>
                    <td>
                        <a href="{{ route('maba.booking.show', $booking->booking_id) }}" class="btn btn-sm btn-info">Detail</a>

                        @if($booking->status === 'accepted')
                            <a href="{{ route('maba.booking.final-schedule.edit', $booking->booking_id) }}" class="btn btn-sm btn-warning">
                                Isi Jadwal
                            </a>

                            <a href="{{ route('maba.realisasi.create', ['booking_id' => $booking->booking_id]) }}" class="btn btn-sm btn-success">
                                Ajukan Realisasi
                            </a>
                        @endif

                        @if(in_array($booking->status, ['pending', 'accepted'], true))
                            <form action="{{ route('maba.booking.leave', $booking->booking_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Keluar dari booking ini?')">
                                @csrf
                                <button class="btn btn-sm btn-danger">Keluar</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Belum ada booking.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $bookings->links() }}
    </div>
</div>
@endsection
