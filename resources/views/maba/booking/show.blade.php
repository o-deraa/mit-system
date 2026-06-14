@extends('layouts.app', ['title' => 'Detail Booking'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Detail Booking #{{ $booking->booking_id }}</h1>
    <a href="{{ route('maba.booking.mine') }}" class="btn btn-secondary">
        Kembali
    </a>
</div>

<div class="card mb-4">
    <div class="card-header">
        Informasi Booking
    </div>

    <div class="card-body">
        <table class="table table-bordered mb-0">
            <tr>
                <th style="width: 220px;">ID Booking</th>
                <td>{{ $booking->booking_id }}</td>
            </tr>

            <tr>
                <th>Status</th>
                <td>
                    @if($booking->status === 'pending')
                        <span class="badge bg-warning text-dark">Pending</span>
                    @elseif($booking->status === 'accepted')
                        <span class="badge bg-success">Accepted</span>
                    @elseif($booking->status === 'cancelled')
                        <span class="badge bg-danger">Cancelled</span>
                    @elseif($booking->status === 'completed')
                        <span class="badge bg-primary">Completed</span>
                    @else
                        <span class="badge bg-secondary"><x-booking-status :status="$booking->status" /></span>
                    @endif
                </td>
            </tr>

            <tr>
                <th>Minggu MIT</th>
                <td>
                    Minggu {{ $booking->week?->week_number ?? '-' }}
                </td>
            </tr>

            <tr>
                <th>Kelompok Warga</th>
                <td>
                    Kelompok {{ $booking->group?->kode_kelompok ?? '-' }}
                </td>
            </tr>

            <tr>
                <th>Perwakilan</th>
                <td>
                    {{ $booking->group?->representativeMember?->warga?->nama ?? '-' }}
                </td>
            </tr>

            <tr>
                <th>WA Perwakilan</th>
                <td>
                    @if($booking->group?->nomor_wa_perwakilan)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $booking->group->representativeMember?->nomor_wa) }}" target="_blank">
                            {{ $booking->representativeMember?->nomor_wa }}
                        </a>
                    @else
                        -
                    @endif
                </td>
            </tr>

            <tr>
                <th>Dibuat Oleh</th>
                <td>
                    {{ $booking->creator?->nama ?? '-' }}
                    @if($booking->creator?->nrp)
                        | {{ $booking->creator->nrp }}
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
                <th>Dibuat Pada</th>
                <td>{{ $booking->created_at }}</td>
            </tr>
        </table>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        Peserta Booking
    </div>

    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped mb-0">
            <thead>
            <tr>
                <th>Nama</th>
                <th>NRP</th>
                <th>Status Peserta</th>
                <th>Join At</th>
            </tr>
            </thead>

            <tbody>
            @forelse($booking->participants as $participant)
                <tr>
                    <td>{{ $participant->maba?->nama ?? '-' }}</td>
                    <td>{{ $participant->maba?->nrp ?? '-' }}</td>
                    <td>
                        @if($participant->status === 'joined')
                            <span class="badge bg-success">Joined</span>
                        @elseif($participant->status === 'left')
                            <span class="badge bg-secondary">Left</span>
                        @elseif($participant->status === 'present')
                            <span class="badge bg-primary">Present</span>
                        @elseif($participant->status === 'absent')
                            <span class="badge bg-danger">Absent</span>
                        @elseif($participant->status === 'replaced')
                            <span class="badge bg-warning text-dark">Replaced</span>
                        @else
                            <span class="badge bg-secondary">{{ $participant->status }}</span>
                        @endif
                    </td>
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

@if($booking->group?->rules)
    <div class="card mb-4">
        <div class="card-header">
            Rules Kelompok
        </div>

        <div class="card-body">
            {{ $booking->group->rules }}
        </div>
    </div>
@endif

<div class="card">
    <div class="card-header">
        Aksi
    </div>

    <div class="card-body">
        @if($booking->status === 'accepted')
            <a href="{{ route('maba.booking.final-schedule.edit', $booking->booking_id) }}" class="btn btn-warning">
                Isi / Ubah Jadwal Final
            </a>

            @if(!$booking->realisasi)
                <a href="{{ route('maba.realisasi.create', ['booking_id' => $booking->booking_id]) }}" class="btn btn-success">
                    Ajukan Realisasi
                </a>
            @else
                <a href="{{ route('maba.realisasi.show', $booking->realisasi->realisasi_id) }}" class="btn btn-info">
                    Lihat Realisasi
                </a>
            @endif
        @endif

        @if(in_array($booking->status, ['pending', 'accepted']))
            <form action="{{ route('maba.booking.leave', $booking->booking_id) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Yakin ingin keluar dari booking ini?')">
                @csrf
                <button class="btn btn-danger">
                    Keluar dari Booking
                </button>
            </form>
        @endif

        <a href="{{ route('maba.booking.mine') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>
</div>
@endsection
