@extends('layouts.app', ['title' => 'Buat Minggu MIT'])

@section('content')
<h1 class="h3 mb-3">Buat Minggu MIT</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.mit-week.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Minggu Ke</label>
                <input type="number" name="week_number" value="{{ old('week_number') }}" class="form-control" min="1" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal Awal</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}" class="form-control" required>
            </div>

            <button class="btn btn-dark">Simpan</button>
            <a href="{{ route('admin.mit-week.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
@endsection
