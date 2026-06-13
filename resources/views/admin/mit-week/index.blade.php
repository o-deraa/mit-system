@extends('layouts.app', ['title' => 'Manajemen Minggu MIT'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Manajemen Minggu MIT</h1>
    <a href="{{ route('admin.mit-week.create') }}" class="btn btn-dark">Buat Minggu MIT</a>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>Week ID</th>
                <th>Minggu</th>
                <th>Tanggal Awal</th>
                <th>Tanggal Akhir</th>
                <th>Status</th>
                <th>Input Ketersediaan</th>
                <th style="width: 330px">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($weeks as $week)
                <tr>
                    <td>{{ $week->week_id }}</td>
                    <td>Minggu {{ $week->week_number }}</td>
                    <td>{{ $week->start_date }}</td>
                    <td>{{ $week->end_date }}</td>
                    <td>{{ $week->status }}</td>
                    <td>{{ $week->availability_input_status }}</td>
                    <td>
                        @if($week->status === 'upcoming')
                            <form action="{{ route('admin.mit-week.activate', $week->week_id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-success">Aktifkan</button>
                            </form>
                        @endif

                        @if($week->status === 'active')
                            <form action="{{ route('admin.mit-week.close', $week->week_id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Tutup minggu aktif ini?')">
                                @csrf
                                <button class="btn btn-sm btn-danger">Tutup</button>
                            </form>

                            <form action="{{ route('admin.mit-week.toggle-availability', $week->week_id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-warning">
                                    Toggle Ketersediaan
                                </button>
                            </form>
                        @endif

                        @if($week->status === 'completed')
                            <span class="text-muted">Selesai</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Belum ada minggu MIT.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $weeks->links() }}
    </div>
</div>
@endsection
