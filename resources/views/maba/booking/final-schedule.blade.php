@extends('layouts.app', ['title' => 'Isi Jadwal Final'])

@section('content')
<h1 class="h3 mb-3">Isi Jadwal Final Booking #{{ $booking->booking_id }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('maba.booking.final-schedule.update', $booking->booking_id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Jadwal Final</label>
                <input type="datetime-local" name="final_schedule" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Lokasi Final</label>
                <input type="text" name="final_location" value="{{ old('final_location', $booking->final_location) }}" class="form-control" required>
            </div>

            <button class="btn btn-dark">Simpan</button>
            <a href="{{ route('maba.booking.mine') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
@endsection
