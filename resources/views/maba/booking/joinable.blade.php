@extends('layouts.app', ['title' => 'Gabung Booking Accepted'])

@section('content')
<h1 class="h3 mb-3">Booking Accepted yang Bisa Digabung</h1>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>Booking ID</th>
                <th>Kelompok</th>
                <th>Perwakilan</th>
                <th>Jadwal</th>
                <th>Lokasi</th>
                <th>Peserta</th>
                <th>Sisa Slot</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row['booking_id'] }}</td>
                    <td>Kelompok {{ $row['kode_kelompok'] }}</td>
                    <td>{{ $row['perwakilan'] }}</td>
                    <td>{{ $row['final_schedule'] ?: '-' }}</td>
                    <td>{{ $row['final_location'] ?: '-' }}</td>
                    <td>{{ $row['peserta'] }} / {{ $row['kapasitas'] }}</td>
                    <td>{{ $row['sisa_slot'] }}</td>
                    <td>
                        <form action="{{ route('maba.booking.join') }}" method="POST">
                            @csrf
                            <input type="hidden" name="booking_id" value="{{ $row['booking_id'] }}">
                            <button class="btn btn-sm btn-dark">Gabung</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">Tidak ada booking accepted yang bisa kamu gabung.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
