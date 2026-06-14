<?php

namespace App\Services;

use App\Models\MabaKelompokHistory;
use App\Models\MitWeek;
use App\Models\Mongo\UploadBuktiLog;
use App\Models\Realisasi;
use App\Models\VerificationResult;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class VerificationService
{
    public function __construct(private MongoLogService $mongoLogService) {}

    public function pendingRequestsByWeekNumber(int $weekNumber): array
    {
        $week = MitWeek::where('week_number', $weekNumber)->first();

        if (!$week) {
            throw new RuntimeException('Minggu MIT tidak ditemukan.');
        }

        return VerificationResult::with([
                'maba',
                'realisasi.booking.group.representativeMember.warga',
                'realisasi.booking.week',
            ])
            ->where('week_id', $week->week_id)
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get()
            ->map(function (VerificationResult $item) {
                $upload = $this->latestUploadBukti(
                    (int) $item->realisasi_id,
                    (int) $item->maba_id
                );

                $booking = $item->realisasi?->booking;
                $group = $booking?->group;

                return [
                    'verification_id' => $item->verification_id,
                    'realisasi_id' => $item->realisasi_id,
                    'booking_id' => $booking?->booking_id,
                    'nama_maba' => $item->maba?->nama,
                    'nrp_maba' => $item->maba?->nrp,
                    'kode_kelompok' => $group?->kode_kelompok,
                    'claimed_ttd_2022' => (int) $item->claimed_ttd_2022,
                    'claimed_ttd_2023' => (int) $item->claimed_ttd_2023,
                    'claimed_ttd_2024' => (int) $item->claimed_ttd_2024,
                    'claimed_total' =>
                        (int) $item->claimed_ttd_2022 +
                        (int) $item->claimed_ttd_2023 +
                        (int) $item->claimed_ttd_2024,
                    'foto_path' => $upload?->file_path ?? '-',
                    'foto_name' => $upload?->file_name ?? '-',
                    'submitted_at' => optional($item->created_at)->format('Y-m-d H:i:s'),
                ];
            })
            ->toArray();
    }

    public function requestsByWeekNumber(int $weekNumber, ?string $status = 'pending'): array
    {
        $week = MitWeek::where('week_number', $weekNumber)->first();

        if (!$week) {
            throw new RuntimeException('Minggu MIT tidak ditemukan.');
        }

        $query = VerificationResult::with([
                'maba',
                'realisasi.booking.group.representativeMember.warga',
                'realisasi.booking.week',
            ])
            ->where('week_id', $week->week_id);

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        return $query
            ->orderBy('created_at')
            ->get()
            ->map(function (VerificationResult $item) {
                $upload = $this->latestUploadBukti(
                    (int) $item->realisasi_id,
                    (int) $item->maba_id
                );

                $booking = $item->realisasi?->booking;
                $group = $booking?->group;

                return [
                    'verification_id' => $item->verification_id,
                    'realisasi_id' => $item->realisasi_id,
                    'booking_id' => $booking?->booking_id,
                    'nama_maba' => $item->maba?->nama,
                    'nrp_maba' => $item->maba?->nrp,
                    'kode_kelompok' => $group?->kode_kelompok,
                    'status' => $item->status,
                    'claimed_ttd_2022' => (int) $item->claimed_ttd_2022,
                    'claimed_ttd_2023' => (int) $item->claimed_ttd_2023,
                    'claimed_ttd_2024' => (int) $item->claimed_ttd_2024,
                    'claimed_total' =>
                        (int) $item->claimed_ttd_2022 +
                        (int) $item->claimed_ttd_2023 +
                        (int) $item->claimed_ttd_2024,
                    'verified_total' =>
                        (int) $item->verified_ttd_2022 +
                        (int) $item->verified_ttd_2023 +
                        (int) $item->verified_ttd_2024,
                    'foto_path' => $upload?->file_path ?? '-',
                    'foto_name' => $upload?->file_name ?? '-',
                    'submitted_at' => optional($item->created_at)->format('Y-m-d H:i:s'),
                ];
            })
            ->toArray();
    }



    public function detailRequest(int $verificationId): array
    {
        $item = VerificationResult::with([
                'maba',
                'realisasi.booking.group.representativeMember.warga',
                'realisasi.booking.week',
            ])
            ->findOrFail($verificationId);

        $upload = $this->latestUploadBukti(
            (int) $item->realisasi_id,
            (int) $item->maba_id
        );

        $booking = $item->realisasi?->booking;
        $group = $booking?->group;
        $week = $booking?->week;

        return [
            'verification_id' => $item->verification_id,
            'status' => $item->status,
            'maba' => [
                'id' => $item->maba?->maba_id,
                'nama' => $item->maba?->nama,
                'nrp' => $item->maba?->nrp,
            ],
            'week' => [
                'id' => $week?->week_id,
                'week_number' => $week?->week_number,
            ],
            'booking' => [
                'id' => $booking?->booking_id,
                'status' => $booking?->status,
                'final_schedule' => $booking?->final_schedule,
                'final_location' => $booking?->final_location,
            ],
            'kelompok' => [
                'id' => $group?->kelompok_warga_id,
                'kode_kelompok' => $group?->kode_kelompok,
                'perwakilan' => $group?->representativeMember?->warga?->nama,
                'wa' => $group?->representativeMember?->nomor_wa,
            ],
            'claimed' => [
                'ttd_2022' => (int) $item->claimed_ttd_2022,
                'ttd_2023' => (int) $item->claimed_ttd_2023,
                'ttd_2024' => (int) $item->claimed_ttd_2024,
                'total' => (int) $item->claimed_ttd_2022 + (int) $item->claimed_ttd_2023 + (int) $item->claimed_ttd_2024,
            ],
            'upload' => [
                'file_name' => $upload?->file_name ?? '-',
                'file_path' => $upload?->file_path ?? '-',
                'mime_type' => $upload?->mime_type ?? '-',
                'file_size' => $upload?->file_size ?? '-',
                'notes' => $upload?->notes ?? '-',
            ],
        ];
    }

    public function verifyRequestById(
        string $adminIdentifier,
        int $verificationId,
        string $status,
        int $verified2022,
        int $verified2023,
        int $verified2024,
        ?string $comment
    ): void {
        DB::transaction(function () use ($adminIdentifier, $verificationId, $status, $verified2022, $verified2023, $verified2024, $comment) {
            if (!in_array($status, ['verified', 'revision', 'rejected'], true)) {
                throw new RuntimeException('Status verifikasi tidak valid.');
            }

            if (in_array($status, ['revision', 'rejected'], true) && trim((string) $comment) === '') {
                throw new RuntimeException('Komentar admin wajib untuk revision/rejected.');
            }

            $item = VerificationResult::with('realisasi.booking')
                ->lockForUpdate()
                ->findOrFail($verificationId);

            if ($item->status !== 'pending') {
                throw new RuntimeException('Request ini sudah pernah diproses.');
            }

            if ($status === 'verified') {
                if ($verified2022 < 0 || $verified2023 < 0 || $verified2024 < 0) {
                    throw new RuntimeException('Angka verified TTD tidak boleh negatif.');
                }
            } else {
                $verified2022 = 0;
                $verified2023 = 0;
                $verified2024 = 0;
            }

            $oldStatus = $item->status;

            $item->update([
                'verified_ttd_2022' => $verified2022,
                'verified_ttd_2023' => $verified2023,
                'verified_ttd_2024' => $verified2024,
                'status' => $status,
                'admin_comment' => $comment,
                'verified_by_admin_identifier' => $adminIdentifier,
                'verified_at' => now(),
            ]);

            $realisasi = $item->realisasi;
            $booking = $realisasi->booking;

            if ($status === 'verified') {
                MabaKelompokHistory::firstOrCreate([
                    'maba_id' => $item->maba_id,
                    'kelompok_warga_id' => $booking->kelompok_warga_id,
                ], [
                    'week_id' => $item->week_id,
                    'booking_id' => $realisasi->booking_id,
                    'created_at' => now(),
                ]);
            }

            $this->syncRealisasiStatus($realisasi->realisasi_id);

            $this->mongoLogService->revision([
                'realisasi_id' => $realisasi->realisasi_id,
                'admin_identifier' => $adminIdentifier,
                'old_status' => $oldStatus,
                'new_status' => $status,
                'notes' => $comment,
                'changed_fields' => [
                    'verification_id' => $item->verification_id,
                    'maba_id' => $item->maba_id,
                    'verified_ttd_2022' => $verified2022,
                    'verified_ttd_2023' => $verified2023,
                    'verified_ttd_2024' => $verified2024,
                ],
                'created_at' => now(),
            ]);
        });
    }

    private function latestUploadBukti(int $realisasiId, int $mabaId): ?UploadBuktiLog
    {
        return UploadBuktiLog::where('realisasi_id', $realisasiId)
            ->where('maba_id', $mabaId)
            ->get()
            ->sortByDesc(function ($item) {
                return $item->created_at;
            })
            ->first();
    }

    private function syncRealisasiStatus(int $realisasiId): void
    {
        $results = VerificationResult::where('realisasi_id', $realisasiId)->get();

        if ($results->contains('status', 'pending')) {
            Realisasi::where('realisasi_id', $realisasiId)->update(['status' => 'pending']);
            return;
        }

        if ($results->contains('status', 'revision')) {
            Realisasi::where('realisasi_id', $realisasiId)->update(['status' => 'revision']);
            return;
        }

        if ($results->every(fn ($item) => $item->status === 'verified')) {
            Realisasi::where('realisasi_id', $realisasiId)->update([
                'status' => 'verified',
                'verified_at' => now(),
            ]);
            return;
        }

        if ($results->every(fn ($item) => $item->status === 'rejected')) {
            Realisasi::where('realisasi_id', $realisasiId)->update([
                'status' => 'rejected',
                'verified_at' => now(),
            ]);
            return;
        }

        Realisasi::where('realisasi_id', $realisasiId)->update(['status' => 'revision']);
    }
}
