@extends('layouts.app', ['title' => 'Booking Masuk'])

@section('content')
<h1 class="h3 mb-3">Booking Masuk Kelompok {{ $group->kode_kelompok }}</h1>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Maba Pembuat</th>
                <th>Status</th>
                <th>Peserta</th>
                <th>Jadwal</th>
                <th>Lokasi</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>{{ $booking->booking_id }}</td>
                    <td>{{ $booking->creator?->nama }} | {{ $booking->creator?->nrp }}</td>
                    <td><x-booking-status :status="$booking->status" /></td>
                    <td>{{ $booking->participants->whereIn('status', ['joined', 'present'])->count() }}</td>
                    <td>{{ $booking->final_schedule ?: '-' }}</td>
                    <td>{{ $booking->final_location ?: '-' }}</td>
                    <td>
                        <a href="{{ route('warga.booking.show', $booking->booking_id) }}" class="btn btn-sm btn-info">Detail</a>

                        @if($booking->status === 'pending')
                            <form action="{{ route('warga.booking.accept', $booking->booking_id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-success">Accept</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Tidak ada booking masuk.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $bookings->links() }}
    </div>
</div>
@endsection
