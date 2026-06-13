@extends('layouts.app', ['title' => 'Monitoring Booking'])

@section('content')
<h1 class="h3 mb-3">Monitoring Booking</h1>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-4">
        <select name="status" class="form-select">
            <option value="">Semua Status</option>
            <option value="pending" @selected($status === 'pending')>pending</option>
            <option value="accepted" @selected($status === 'accepted')>accepted</option>
            <option value="cancelled" @selected($status === 'cancelled')>cancelled</option>
            <option value="completed" @selected($status === 'completed')>completed</option>
        </select>
    </div>
    <div class="col-md-4">
        <input type="number" name="week_id" value="{{ $weekId }}" class="form-control" placeholder="Filter Week ID">
    </div>
    <div class="col-md-4">
        <button class="btn btn-outline-dark">Filter</button>
        <a href="{{ route('admin.booking.index') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Minggu</th>
                <th>Kelompok</th>
                <th>Pembuat</th>
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
                    <td>Minggu {{ $booking->week?->week_number }}</td>
                    <td>Kelompok {{ $booking->group?->kode_kelompok }}</td>
                    <td>{{ $booking->creator?->nama }} | {{ $booking->creator?->nrp }}</td>
                    <td>{{ $booking->status }}</td>
                    <td>{{ $booking->participants->whereIn('status', ['joined', 'present'])->count() }}</td>
                    <td>{{ $booking->final_schedule ?: '-' }}</td>
                    <td>{{ $booking->final_location ?: '-' }}</td>
                    <td>
                        <a href="{{ route('admin.booking.show', $booking->booking_id) }}" class="btn btn-sm btn-info">Detail</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted">Belum ada booking.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $bookings->links() }}
    </div>
</div>
@endsection
