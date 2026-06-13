@extends('layouts.app', ['title' => 'Verifikasi TTD'])

@section('content')
<h1 class="h3 mb-3">Verifikasi TTD Berbasis Request</h1>

<div class="alert alert-info">
    Pilih minggu MIT. Sistem hanya akan menampilkan request verifikasi TTD dengan status <strong>pending</strong>.
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.verification.requests') }}" method="GET">
            <div class="mb-3">
                <label class="form-label">Pilih Minggu MIT</label>
                <select name="week_number" class="form-select" required>
                    <option value="">-- Pilih Minggu --</option>
                    @foreach($weeks as $week)
                        <option value="{{ $week->week_number }}">
                            Minggu {{ $week->week_number }} | {{ $week->status }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button class="btn btn-dark">Lihat Request Pending</button>
        </form>
    </div>
</div>
@endsection
