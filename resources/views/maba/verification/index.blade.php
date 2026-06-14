@extends('layouts.app', ['title' => 'Status Verifikasi'])

@section('content')
<h1 class="h3 mb-3">Status Verifikasi TTD</h1>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>Verification ID</th>
                <th>Minggu</th>
                <th>Kelompok</th>
                <th>Status</th>
                <th>Claim Total</th>
                <th>Verified Total</th>
                <th>Catatan Admin</th>
                <th>Verified At</th>
            </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->verification_id }}</td>
                    <td>Minggu {{ $item->week?->week_number }}</td>
                    <td>Kelompok {{ $item->realisasi?->booking?->group?->kode_kelompok }}</td>
                    <td><x-verification-status :status="$item->status" /></td>
                    <td>{{ $item->claimed_ttd_2022 + $item->claimed_ttd_2023 + $item->claimed_ttd_2024 }}</td>
                    <td>{{ $item->verified_ttd_2022 + $item->verified_ttd_2023 + $item->verified_ttd_2024 }}</td>
                    <td>{{ $item->admin_comment ?: '-' }}</td>
                    <td>{{ $item->verified_at ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">Belum ada status verifikasi.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $items->links() }}
    </div>
</div>
@endsection
