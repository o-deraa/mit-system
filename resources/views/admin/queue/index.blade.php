@extends('layouts.app', ['title' => 'Monitoring Queue Aktif'])

@section('content')
<h1 class="h3 mb-3">Monitoring Queue Aktif</h1>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-6">
        <select name="week_id" class="form-select">
            <option value="">Minggu Aktif</option>
            @foreach($weeks as $week)
                <option value="{{ $week->week_id }}" @selected($selectedWeek?->week_id == $week->week_id)>
                    Minggu {{ $week->week_number }} | {{ $week->status }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <button class="btn btn-outline-dark">Filter</button>
        <a href="{{ route('admin.queue.index') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

@if(!$selectedWeek)
    <div class="alert alert-warning">Tidak ada minggu aktif atau minggu yang dipilih tidak ditemukan.</div>
@else
    <div class="alert alert-info">Menampilkan queue untuk <strong>Minggu {{ $selectedWeek->week_number }}</strong>.</div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                <tr>
                    <th>Kelompok</th>
                    <th>Perwakilan</th>
                    <th>Mode</th>
                    <th>Status Availability</th>
                    <th>Pending</th>
                    <th>Accepted</th>
                    <th>Queue Aktif</th>
                    <th>Sisa Queue</th>
                    <th>Catatan</th>
                </tr>
                </thead>
                <tbody>
                @forelse($rows as $row)
                    <tr>
                        <td>Kelompok {{ $row['group']?->kode_kelompok }}</td>
                        <td>
                            {{ $row['group']?->representative?->nama }}<br>
                            <small class="text-muted">{{ $row['group']?->representative?->nrp }}</small>
                        </td>
                        <td>{{ $row['availability']->session_mode }} maba/sesi</td>
                        <td>{{ $row['availability']->is_available ? 'available' : 'not available' }}</td>
                        <td>{{ $row['pending_count'] }}</td>
                        <td>{{ $row['accepted_count'] }}</td>
                        <td>{{ $row['active_queue'] }} / {{ $row['max_queue'] }}</td>
                        <td>{{ $row['sisa_queue'] }}</td>
                        <td>{{ $row['availability']->notes ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">Belum ada availability pada minggu ini.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
