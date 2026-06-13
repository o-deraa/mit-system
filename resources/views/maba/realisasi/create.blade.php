@extends('layouts.app', ['title' => 'Ajukan Realisasi'])

@section('content')
<h1 class="h3 mb-3">Ajukan Realisasi Booking #{{ $booking->booking_id }}</h1>

<div class="card mb-4">
    <div class="card-header">Informasi Booking</div>
    <div class="card-body">
        <p><strong>Kelompok:</strong> Kelompok {{ $booking->group?->kode_kelompok }}</p>
        <p><strong>Minggu:</strong> Minggu {{ $booking->week?->week_number }}</p>
        <p><strong>Jadwal:</strong> {{ $booking->final_schedule ?: '-' }}</p>
        <p><strong>Lokasi:</strong> {{ $booking->final_location ?: '-' }}</p>
    </div>
</div>

<form action="{{ route('maba.realisasi.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="booking_id" value="{{ $booking->booking_id }}">

    <div class="card mb-4">
        <div class="card-header">Kehadiran Warga</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Apakah warga hadir sesuai rencana?</label>
                <select name="is_warga_as_planned" class="form-select" required>
                    <option value="1">Ya</option>
                    <option value="0">Tidak / Ada perubahan</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Catatan warga tidak hadir</label>
                <textarea name="absent_warga_notes" class="form-control" rows="3">{{ old('absent_warga_notes') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Catatan warga tambahan</label>
                <textarea name="additional_warga_notes" class="form-control" rows="3">{{ old('additional_warga_notes') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Catatan umum</label>
                <textarea name="general_notes" class="form-control" rows="3">{{ old('general_notes') }}</textarea>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Peserta Hadir dan Klaim TTD</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Hadir</th>
                    <th>Nama</th>
                    <th>NRP</th>
                    <th>TTD 2022</th>
                    <th>TTD 2023</th>
                    <th>TTD 2024</th>
                </tr>
                </thead>
                <tbody>
                @foreach($booking->participants->where('status', 'joined') as $participant)
                    <tr>
                        <td>
                            <input type="checkbox"
                                   name="present_maba_ids[]"
                                   value="{{ $participant->maba_id }}"
                                   checked>
                        </td>
                        <td>{{ $participant->maba?->nama }}</td>
                        <td>{{ $participant->maba?->nrp }}</td>
                        <td>
                            <input type="number"
                                   name="claims[{{ $participant->maba_id }}][claimed_ttd_2022]"
                                   value="0"
                                   min="0"
                                   class="form-control">
                        </td>
                        <td>
                            <input type="number"
                                   name="claims[{{ $participant->maba_id }}][claimed_ttd_2023]"
                                   value="0"
                                   min="0"
                                   class="form-control">
                        </td>
                        <td>
                            <input type="number"
                                   name="claims[{{ $participant->maba_id }}][claimed_ttd_2024]"
                                   value="0"
                                   min="0"
                                   class="form-control">
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="alert alert-warning mb-0">
                Jika ada maba yang join tetapi tidak hadir, hilangkan centang hadir. Maba tersebut dianggap tidak ikut realisasi.
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Upload Bukti Buku MIT</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Foto Buku MIT</label>
                <input type="file" name="upload_bukti" class="form-control" accept="image/*">
                <div class="form-text">Maksimal 4 MB. Format gambar.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Catatan Upload</label>
                <textarea name="upload_notes" class="form-control" rows="3">{{ old('upload_notes') }}</textarea>
            </div>
        </div>
    </div>

    <button class="btn btn-dark">Ajukan Realisasi</button>
    <a href="{{ route('maba.booking.mine') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection
