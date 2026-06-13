@extends('layouts.app', ['title' => 'Cek Pernah Bertemu'])

@section('content')
<h1 class="h3 mb-3">Cek Pernah Bertemu Kelompok</h1>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('maba.history.check') }}" method="GET">
            <div class="mb-3">
                <label class="form-label">Kode Kelompok</label>
                <input type="number" name="kode_kelompok" value="{{ $kodeKelompok }}" class="form-control" required>
            </div>

            <button class="btn btn-dark">Cek</button>
            <a href="{{ route('maba.history.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>

@if($kodeKelompok)
    @if(!$group)
        <div class="alert alert-danger">Kelompok {{ $kodeKelompok }} tidak ditemukan.</div>
    @elseif($result)
        <div class="alert alert-warning">
            Kamu sudah pernah bertemu Kelompok {{ $group->kode_kelompok }} pada Minggu {{ $result->week?->week_number }}.
        </div>
    @else
        <div class="alert alert-success">Kamu belum pernah bertemu Kelompok {{ $group->kode_kelompok }}.</div>
    @endif
@endif
@endsection
