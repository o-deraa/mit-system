@extends('layouts.app', ['title' => 'Input Availability'])

@section('content')
<h1 class="h3 mb-3">Input Availability Mingguan</h1>

<div class="card mb-3">
    <div class="card-body">
        <p class="mb-1"><strong>Kelompok:</strong> Kelompok {{ $group->kode_kelompok }}</p>
        <p class="mb-1"><strong>Perwakilan:</strong> {{ $warga->nama }}</p>
        <p class="mb-0"><strong>Minggu MIT:</strong> Minggu {{ $week->week_number }}</p>
    </div>
</div>

<form action="{{ route('warga.availability.update') }}" method="POST">
    @csrf

    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Apakah kelompok tersedia minggu ini?</label>
                <select name="is_available" class="form-select" required>
                    <option value="1" @selected(old('is_available', $availability->is_available) == 1)>Ya, tersedia</option>
                    <option value="0" @selected(old('is_available', $availability->is_available) == 0)>Tidak tersedia</option>
                </select>
            </div>

    <div class="mb-3">
        <label class="form-label">Jumlah maba per sesi?</label>
        <select name="session_mode" id="session_mode" class="form-select" required>
            <option value="4" @selected(old('session_mode', $availability->session_mode) == 4)>
                4 maba per sesi
            </option>
            <option value="6" @selected(old('session_mode', $availability->session_mode) == 6)>
                6 maba per sesi
            </option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Jumlah sesi yang diterima minggu ini?</label>
        <select name="session_count" id="session_count" class="form-select" required>
            {{-- Diisi oleh JavaScript --}}
        </select>
        <div class="form-text" id="session_help"></div>
    </div>

    <script>
        const sessionMode = document.getElementById('session_mode');
        const sessionCount = document.getElementById('session_count');
        const sessionHelp = document.getElementById('session_help');

        const oldSessionCount = @json((int) old('session_count', $availability->session_count ?? 1));

        function renderSessionCountOptions() {
            const mode = parseInt(sessionMode.value);
            const maxSession = mode === 6 ? 2 : 3;

            sessionCount.innerHTML = '';

            for (let i = 1; i <= maxSession; i++) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = i + ' sesi';

                if (i === oldSessionCount) {
                    option.selected = true;
                }

                sessionCount.appendChild(option);
            }

            if (parseInt(sessionCount.value) > maxSession) {
                sessionCount.value = maxSession;
            }

            if (mode === 4) {
                sessionHelp.textContent = '4 maba/sesi berarti maksimal 3 sesi dalam minggu itu.';
            } else {
                sessionHelp.textContent = '6 maba/sesi berarti maksimal 2 sesi dalam minggu itu.';
            }
        }

        sessionMode.addEventListener('change', renderSessionCountOptions);
        renderSessionCountOptions();
    </script>

            <button class="btn btn-dark">Simpan Availability</button>
            <a href="{{ route('warga.dashboard') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</form>
@endsection
