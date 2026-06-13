@extends('layouts.app', ['title' => 'Kelompok Warga Tersedia'])

@section('content')
<h1 class="h3 mb-3">Kelompok Warga Tersedia</h1>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>Group ID</th>
                <th>Kode</th>
                <th>Perwakilan</th>
                <th>WA</th>
                <th>Mode</th>
                <th>Sesi Aktif</th>
                <th>Sisa Sesi</th>
                <th>Sisa Slot Accepted</th>
                <th>Status Untuk Kamu</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row['kelompok_warga_id'] }}</td>
                    <td>Kelompok {{ $row['kode_kelompok'] }}</td>
                    <td>{{ $row['perwakilan'] }}</td>
                    <td>
                        @if($row['wa'])
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $row['wa']) }}" target="_blank">
                                {{ $row['wa'] }}
                            </a>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $row['session_mode'] }} maba/sesi</td>
                    <td>{{ $row['queue_aktif'] }} / {{ $row['session_count'] ?? 3 }}</td>
                    <td>{{ $row['sisa_queue'] }}</td>
                    <td>{{ $row['sisa_slot_booking_accepted'] }}</td>
                    <td>{{ $row['catatan_validasi'] }}</td>
                    <td>
                        @if($row['boleh_booking_baru'])
                            <form action="{{ route('maba.booking.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="kelompok_warga_id" value="{{ $row['kelompok_warga_id'] }}">
                                <button class="btn btn-sm btn-dark">Buat Booking</button>
                            </form>
                        @else
                            <span class="text-muted">Tidak tersedia</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center text-muted">Tidak ada kelompok tersedia pada minggu aktif.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
