@extends('layouts.app', ['title' => 'Rekomendasi Kelompok'])

@section('content')
<h1 class="h3 mb-3">Rekomendasi Kelompok</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('maba.recommendation.generate') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">NRP Maba</label>
                <input type="text"
                       name="nrp_list"
                       value="{{ old('nrp_list', $maba->nrp) }}"
                       class="form-control"
                       placeholder="Contoh: 5027251001, 5027251002"
                       required>
                <div class="form-text">Masukkan 1 sampai 4 NRP maba, pisahkan dengan koma.</div>
            </div>

            <button class="btn btn-dark">Cari Rekomendasi</button>
            <a href="{{ route('maba.dashboard') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
@endsection
