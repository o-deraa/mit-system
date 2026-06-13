@extends('layouts.app', ['title' => 'Hasil Rekomendasi'])

@section('content')
<h1 class="h3 mb-3">Hasil Rekomendasi Kelompok</h1>

<div class="alert alert-info">
    Input NRP: <strong>{{ implode(', ', $inputNrpList) }}</strong>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
            <tr>
                <th>Ranking</th>
                <th>Kelompok</th>
                <th>Perwakilan</th>
                <th>WA</th>
                <th>Score</th>
                <th>Queue</th>
                <th>Sisa Queue</th>
                <th>Mode</th>
                <th>Alasan</th>
            </tr>
            </thead>
            <tbody>
            @forelse($results as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>Kelompok {{ $row['kode_kelompok'] }}</td>
                    <td>{{ $row['perwakilan'] ?? '-' }}</td>
                    <td>
                        @if(!empty($row['wa']))
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $row['wa']) }}" target="_blank">
                                {{ $row['wa'] }}
                            </a>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $row['score'] }}</td>
                    <td>{{ $row['queue_count'] }} / {{ $row['max_queue'] ?? '-' }}</td>
                    <td>{{ $row['sisa_queue'] }}</td>
                    <td>{{ $row['session_mode'] }} maba/sesi</td>
                    <td>
                        <ul class="mb-0">
                            @foreach($row['reasons'] as $reason)
                                <li>{{ $reason }}</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted">Tidak ada rekomendasi kelompok.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <a href="{{ route('maba.recommendation.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>
@endsection
