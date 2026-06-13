<?php

namespace App\Services\Web;

use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Models\Maba;
use App\Models\Mongo\UploadBuktiLog;
use App\Models\Realisasi;
use App\Models\VerificationResult;
use App\Services\MongoLogService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MabaRealisasiWebService
{
    public function __construct(private MongoLogService $mongoLogService) {}

    public function submit(Maba $maba, int $bookingId, array $data, ?UploadedFile $file): Realisasi
    {
        return DB::transaction(function () use ($maba, $bookingId, $data, $file) {
            $booking = Booking::with(['participants.maba', 'group.members.warga'])
                ->lockForUpdate()
                ->findOrFail($bookingId);

            if ($booking->status !== 'accepted') {
                throw new RuntimeException('Realisasi hanya bisa diajukan untuk booking accepted.');
            }

            $isParticipant = BookingParticipant::where('booking_id', $booking->booking_id)
                ->where('maba_id', $maba->maba_id)
                ->where('status', 'joined')
                ->exists();

            if (!$isParticipant) {
                throw new RuntimeException('Hanya peserta aktif booking yang bisa mengajukan realisasi.');
            }

            if ($booking->realisasi()->exists()) {
                throw new RuntimeException('Booking ini sudah memiliki realisasi.');
            }

            $presentMabaIds = array_values(array_unique(array_map('intval', $data['present_maba_ids'] ?? [])));

            if (!in_array($maba->maba_id, $presentMabaIds, true)) {
                $presentMabaIds[] = $maba->maba_id;
            }

            if (count($presentMabaIds) === 0) {
                throw new RuntimeException('Minimal ada satu maba hadir.');
            }

            $validParticipantIds = BookingParticipant::where('booking_id', $booking->booking_id)
                ->whereIn('status', ['joined', 'left'])
                ->pluck('maba_id')
                ->map(fn ($id) => (int) $id)
                ->toArray();

            foreach ($presentMabaIds as $presentMabaId) {
                if (!in_array($presentMabaId, $validParticipantIds, true)) {
                    throw new RuntimeException('Ada maba hadir yang bukan peserta booking.');
                }
            }

            $claimRows = $data['claims'] ?? [];

            foreach ($presentMabaIds as $presentMabaId) {
                if (!isset($claimRows[$presentMabaId])) {
                    throw new RuntimeException("Klaim TTD untuk maba ID {$presentMabaId} belum diisi.");
                }
            }

            $realisasi = Realisasi::create([
                'booking_id' => $booking->booking_id,
                'week_id' => $booking->week_id,
                'submitted_by_maba_id' => $maba->maba_id,
                'realisasi_is_meeting_held' => true,
                'is_warga_as_planned' => (bool) ($data['is_warga_as_planned'] ?? true),
                'absent_warga_notes' => $data['absent_warga_notes'] ?? null,
                'additional_warga_notes' => $data['additional_warga_notes'] ?? null,
                'general_notes' => $data['general_notes'] ?? null,
                'status' => 'pending',
                'submitted_at' => now(),
            ]);

            foreach ($booking->participants as $participant) {
                if (in_array((int) $participant->maba_id, $presentMabaIds, true)) {
                    $participant->update(['status' => 'present']);
                } else {
                    $participant->update(['status' => 'absent']);
                }
            }

            foreach ($presentMabaIds as $presentMabaId) {
                $claim = $claimRows[$presentMabaId];

                VerificationResult::create([
                    'realisasi_id' => $realisasi->realisasi_id,
                    'maba_id' => $presentMabaId,
                    'week_id' => $booking->week_id,
                    'claimed_ttd_2022' => (int) ($claim['claimed_ttd_2022'] ?? 0),
                    'claimed_ttd_2023' => (int) ($claim['claimed_ttd_2023'] ?? 0),
                    'claimed_ttd_2024' => (int) ($claim['claimed_ttd_2024'] ?? 0),
                    'verified_ttd_2022' => 0,
                    'verified_ttd_2023' => 0,
                    'verified_ttd_2024' => 0,
                    'status' => 'pending',
                ]);
            }

            $uploadData = [
                'realisasi_id' => $realisasi->realisasi_id,
                'booking_id' => $booking->booking_id,
                'maba_id' => $maba->maba_id,
                'notes' => $data['upload_notes'] ?? null,
            ];

            if ($file) {
                $path = $file->store('mit-bukti', 'public');

                $uploadData += [
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_url' => asset('storage/' . $path),
                    'mime_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ];
            } else {
                $uploadData += [
                    'file_name' => null,
                    'file_path' => null,
                    'file_url' => null,
                    'mime_type' => null,
                    'file_size' => null,
                ];
            }

            UploadBuktiLog::create($uploadData + ['created_at' => now()]);

            $booking->update([
                'status' => 'completed',
            ]);

            $this->mongoLogService->activity($maba->maba_id, 'maba', 'submit_realisasi', 'Maba mengajukan realisasi pertemuan.', [
                'booking_id' => $booking->booking_id,
                'realisasi_id' => $realisasi->realisasi_id,
            ]);

            return $realisasi;
        });
    }
}
