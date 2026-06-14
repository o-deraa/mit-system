@extends('layouts.app', ['title' => 'Detail Booking'])

@section('content')
<h1 class="h3 mb-3">Detail Booking #{{ $booking->booking_id }}</h1>

<div class="card mb-4">
    <div class="card-header">Informasi Booking</div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th style="width: 220px;">ID Booking</th>
                <td>{{ $booking->booking_id }}</td>
            </tr>
            <tr>
                <th>Minggu MIT</th>
                <td>Minggu {{ $booking->week?->week_number ?? '-' }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span class="badge bg-secondary">{{ $booking->status }}</span>
                </td>
            </tr>
            <tr>
                <th>Dibuat Oleh</th>
                <td>
                    {{ $booking->creator?->nama ?? '-' }}
                    @if($booking->creator)
                        | {{ $booking->creator?->nrp }}
                    @endif
                </td>
            </tr>
            <tr>
                <th>Jadwal Final</th>
                <td>{{ $booking->final_schedule ?: '-' }}</td>
            </tr>
            <tr>
                <th>Lokasi Final</th>
                <td>{{ $booking->final_location ?: '-' }}</td>
            </tr>
            <tr>
                <th>Catatan Warga</th>
                <td>{{ $booking->warga_notes ?: '-' }}</td>
            </tr>
            <tr>
                <th>Alasan Cancel</th>
                <td>{{ $booking->cancelled_reason ?: '-' }}</td>
            </tr>
            <tr>
                <th>Realisasi</th>
                <td>
                    @if($booking->realisasi)
                        Sudah diajukan, status: {{ $booking->realisasi->status }}
                    @else
                        Belum ada realisasi
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Daftar Peserta</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>Nama</th>
                <th>NRP</th>
                <th>Status Peserta</th>
                <th>Joined At</th>
            </tr>
            </thead>
            <tbody>
            @forelse($booking->participants as $participant)
                <tr>
                    <td>{{ $participant->maba?->nama ?? '-' }}</td>
                    <td>{{ $participant->maba?->nrp ?? '-' }}</td>
                    <td>{{ $participant->status }}</td>
                    <td>{{ $participant->joined_at ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">
                        Belum ada peserta.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($booking->status === 'pending')
    <div class="card mb-4">
        <div class="card-header">Terima Booking</div>
        <div class="card-body">
            <form action="{{ route('warga.booking.accept', $booking->booking_id) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Catatan Warga</label>
                    <textarea name="warga_notes" class="form-control" rows="3">{{ old('warga_notes') }}</textarea>
                    <div class="form-text">
                        Jadwal dan lokasi final nanti diisi oleh maba setelah booking diterima.
                    </div>
                </div>

                <button class="btn btn-success">Terima Booking</button>
            </form>
        </div>
    </div>
@endif

@if(in_array($booking->status, ['pending', 'accepted'], true))
    <div class="card mb-4">
        <div class="card-header">Batalkan Booking</div>
        <div class="card-body">
            <form action="{{ route('warga.booking.cancel', $booking->booking_id) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Alasan Pembatalan</label>
                    <textarea name="cancelled_reason" class="form-control" rows="3" required>{{ old('cancelled_reason') }}</textarea>
                </div>

                <button class="btn btn-danger"
                        onclick="return confirm('Yakin ingin membatalkan booking ini?')">
                    Batalkan Booking
                </button>
            </form>
        </div>
    </div>
@endif

<div class="d-flex gap-2">
    <a href="{{ route('warga.booking.incoming') }}" class="btn btn-secondary">Booking Masuk</a>
    <a href="{{ route('warga.booking.accepted') }}" class="btn btn-secondary">Booking Accepted</a>
    <a href="{{ route('warga.booking.history') }}" class="btn btn-secondary">Riwayat Booking</a>
</div>
@endsection
