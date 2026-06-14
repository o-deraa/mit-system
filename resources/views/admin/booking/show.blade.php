@extends('layouts.app', ['title' => 'Detail Booking'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Detail Booking #{{ $booking->booking_id }}</h1>

    <a href="{{ route('admin.booking.index') }}" class="btn btn-secondary">
        Kembali
    </a>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Informasi Booking
            </div>
            <div class="card-body">
                <table class="table table-bordered mb-0">
                    <tr>
                        <th style="width: 180px;">Booking ID</th>
                        <td>{{ $booking->booking_id }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge bg-dark">
                                <x-booking-status :status="$booking->status" />
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Minggu MIT</th>
                        <td>
                            @if($booking->week)
                                Minggu {{ $booking->week->week_number }}
                                <br>
                                <small class="text-muted">
                                    {{ $booking->week->start_date }} - {{ $booking->week->end_date }}
                                </small>
                            @else
                                -
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
                    <tr>
                        <th>Diupdate Pada</th>
                        <td>{{ $booking->updated_at }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Kelompok Warga
            </div>
            <div class="card-body">
                <table class="table table-bordered mb-0">
                    <tr>
                        <th style="width: 180px;">Kelompok</th>
                        <td>
                            @if($booking->group)
                                Kelompok {{ $booking->group->kode_kelompok }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Perwakilan</th>
                        <td>{{ $booking->group?->representativeMember?->warga?->nama ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>NRP Perwakilan</th>
                        <td>{{ $booking->group?->representativeMember?->warga?->nrp ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>WA Perwakilan</th>
                        <td>
                            @if($booking->group?->nomor_wa_perwakilan)
                                <a href="https://wa.me/{{ preg_replace('/\D/', '', $booking->group->nomor_wa_perwakilan) }}" target="_blank">
                                    {{ $booking->group->representativeMember?->nomor_wa }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Rules / Catatan</th>
                        <td>{{ $booking->group?->rules ?: '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        Pembuat Booking
    </div>
    <div class="card-body">
        <table class="table table-bordered mb-0">
            <tr>
                <th style="width: 180px;">Nama</th>
                <td>{{ $booking->creator?->nama ?: '-' }}</td>
            </tr>
            <tr>
                <th>NRP</th>
                <td>{{ $booking->creator?->nrp ?: '-' }}</td>
            </tr>
            <tr>
                <th>Status Maba</th>
                <td>{{ $booking->creator?->status ?: '-' }}</td>
            </tr>
        </table>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        Peserta Booking
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped align-middle mb-0">
            <thead>
            <tr>
                <th>No</th>
                <th>Nama Maba</th>
                <th>NRP</th>
                <th>Status Peserta</th>
                <th>Joined At</th>
            </tr>
            </thead>
            <tbody>
            @forelse($booking->participants as $participant)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $participant->maba?->nama ?: '-' }}</td>
                    <td>{{ $participant->maba?->nrp ?: '-' }}</td>
                    <td>
                        <span class="badge bg-secondary">
                            {{ $participant->status }}
                        </span>
                    </td>
                    <td>{{ $participant->joined_at ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">
                        Belum ada peserta booking.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        Realisasi
    </div>
    <div class="card-body">
        @if($booking->realisasi)
            <table class="table table-bordered mb-3">
                <tr>
                    <th style="width: 180px;">Realisasi ID</th>
                    <td>{{ $booking->realisasi->realisasi_id }}</td>
                </tr>
                <tr>
                    <th>Status Realisasi</th>
                    <td>
                        <span class="badge bg-dark">
                            {{ $booking->realisasi->status }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Submitted At</th>
                    <td>{{ $booking->realisasi->submitted_at ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Catatan Umum</th>
                    <td>{{ $booking->realisasi->general_notes ?: '-' }}</td>
                </tr>
            </table>

            @if(\Illuminate\Support\Facades\Route::has('admin.realisasi.show'))
                <a href="{{ route('admin.realisasi.show', $booking->realisasi->realisasi_id) }}" class="btn btn-info">
                    Lihat Detail Realisasi
                </a>
            @endif
        @else
            <div class="alert alert-warning mb-0">
                Booking ini belum memiliki realisasi.
            </div>
        @endif
    </div>
</div>
@endsection
