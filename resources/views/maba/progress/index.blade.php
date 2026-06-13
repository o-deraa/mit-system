@extends('layouts.app', ['title' => 'Progress TTD'])

@section('content')
<h1 class="h3 mb-3">Progress TTD</h1>

<div class="card mb-4">
    <div class="card-header">Total Progress</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Kategori</th>
                <th>Verified</th>
                <th>Target</th>
                <th>Kekurangan</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>2022</td>
                <td>{{ $progress['total_2022'] }}</td>
                <td>{{ $progress['target_2022'] }}</td>
                <td>{{ $progress['kurang_2022'] }}</td>
            </tr>
            <tr>
                <td>2023</td>
                <td>{{ $progress['total_2023'] }}</td>
                <td>{{ $progress['target_2023'] }}</td>
                <td>{{ $progress['kurang_2023'] }}</td>
            </tr>
            <tr>
                <td>2024</td>
                <td>{{ $progress['total_2024'] }}</td>
                <td>{{ $progress['target_2024'] }}</td>
                <td>{{ $progress['kurang_2024'] }}</td>
            </tr>
            <tr>
                <th>Total</th>
                <th>{{ $progress['total'] }}</th>
                <th>{{ $progress['target_total'] }}</th>
                <th>{{ $progress['kurang_total'] }}</th>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">Rekap Mingguan</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>Minggu</th>
                <th>Kelompok</th>
                <th>Status</th>
                <th>Claim 2022</th>
                <th>Claim 2023</th>
                <th>Claim 2024</th>
                <th>Verified 2022</th>
                <th>Verified 2023</th>
                <th>Verified 2024</th>
                <th>Catatan</th>
            </tr>
            </thead>
            <tbody>
            @forelse($weeklyRecap as $row)
                <tr>
                    <td>{{ $row['week_number'] }}</td>
                    <td>{{ $row['kelompok'] ? 'Kelompok ' . $row['kelompok'] : '-' }}</td>
                    <td>{{ $row['status'] }}</td>
                    <td>{{ $row['claimed_2022'] }}</td>
                    <td>{{ $row['claimed_2023'] }}</td>
                    <td>{{ $row['claimed_2024'] }}</td>
                    <td>{{ $row['verified_2022'] }}</td>
                    <td>{{ $row['verified_2023'] }}</td>
                    <td>{{ $row['verified_2024'] }}</td>
                    <td>{{ $row['admin_comment'] ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center text-muted">Belum ada rekap TTD.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
