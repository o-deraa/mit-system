@extends('layouts.app', ['title' => 'Monitor Realisasi'])

@section('content')
<h1 class="h3 mb-3">Monitor Realisasi</h1>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-4">
        <select name="status" class="form-select">
            <option value="">Semua Status</option>
            <option value="pending" @selected($status === 'pending')>pending</option>
            <option value="verified" @selected($status === 'verified')>verified</option>
            <option value="revision" @selected($status === 'revision')>revision</option>
            <option value="rejected" @selected($status === 'rejected')>rejected</option>
        </select>
    </div>
    <div class="col-md-4">
        <input type="number" name="week_id" value="{{ $weekId }}" class="form-control" placeholder="Week ID">
    </div>
    <div class="col-md-4">
        <button class="btn btn-outline-dark">Filter</button>
        <a href="{{ route('admin.realisasi.index') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Booking</th>
                <th>Minggu</th>
                <th>Kelompok</th>
                <th>Submitted By</th>
                <th>Status</th>
                <th>Submitted At</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->realisasi_id }}</td>
                    <td>{{ $item->booking_id }}</td>
                    <td>Minggu {{ $item->week?->week_number }}</td>
                    <td>Kelompok {{ $item->booking?->group?->kode_kelompok }}</td>
                    <td>{{ $item->submitter?->nama }} | {{ $item->submitter?->nrp }}</td>
                    <td>{{ $item->status }}</td>
                    <td>{{ $item->submitted_at }}</td>
                    <td>
                        <a href="{{ route('admin.realisasi.show', $item->realisasi_id) }}" class="btn btn-sm btn-info">Detail</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">Belum ada realisasi.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $items->links() }}
    </div>
</div>
@endsection
