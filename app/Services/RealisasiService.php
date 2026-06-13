<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Models\Maba;
use App\Models\Realisasi;
use App\Models\VerificationResult;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RealisasiService
{
    public function __construct(private MongoLogService $mongoLogService) {}

    public function submit(Maba $submitter, int $bookingId, array $data): Realisasi
    {
        return DB::transaction(function () use ($submitter, $bookingId, $data) {
            $booking = Booking::lockForUpdate()->findOrFail($bookingId);

            if ($booking->status !== 'accepted') {
                throw new RuntimeException('Realisasi hanya bisa diajukan untuk booking accepted.');
            }

            if ($booking->realisasi()->exists()) {
                throw new RuntimeException('Booking ini sudah memiliki realisasi.');
            }

            $isParticipant = BookingParticipant::where('booking_id', $bookingId)
                ->where('maba_id', $submitter->maba_id)
                ->where('status', 'joined')
                ->exists();
            if (!$isParticipant) {
                throw new RuntimeException('Pengaju realisasi harus peserta booking.');
            }

            $presentIds = array_map('intval', $data['present_maba_ids'] ?? []);
            $absentIds = array_map('intval', $data['absent_maba_ids'] ?? []);
            $replacements = $data['replacements'] ?? [];

            foreach ($presentIds as $mabaId) {
                BookingParticipant::where('booking_id', $bookingId)
                    ->where('maba_id', $mabaId)
                    ->update(['status' => 'present']);
            }

            foreach ($absentIds as $mabaId) {
                BookingParticipant::where('booking_id', $bookingId)
                    ->where('maba_id', $mabaId)
                    ->update(['status' => 'absent']);
            }

            foreach ($replacements as $oldMabaId => $newMabaId) {
                BookingParticipant::where('booking_id', $bookingId)
                    ->where('maba_id', (int) $oldMabaId)
                    ->update(['status' => 'replaced', 'replaced_by_maba_id' => (int) $newMabaId]);

                BookingParticipant::updateOrCreate(
                    ['booking_id' => $bookingId, 'maba_id' => (int) $newMabaId],
                    ['status' => 'present', 'joined_at' => now()]
                );

                $presentIds[] = (int) $newMabaId;
            }

            $presentIds = array_values(array_unique($presentIds));
            if (count($presentIds) === 0) {
                throw new RuntimeException('Minimal ada satu peserta present.');
            }

            $realisasi = Realisasi::create([
                'booking_id' => $booking->booking_id,
                'week_id' => $booking->week_id,
                'submitted_by_maba_id' => $submitter->maba_id,
                'realisasi_is_meeting_held' => (bool) ($data['meeting_held'] ?? true),
                'is_warga_as_planned' => (bool) ($data['is_warga_as_planned'] ?? true),
                'absent_warga_notes' => $data['absent_warga_notes'] ?? null,
                'additional_warga_notes' => $data['additional_warga_notes'] ?? null,
                'general_notes' => $data['general_notes'] ?? null,
                'status' => 'pending',
                'submitted_at' => now(),
            ]);

            foreach ($presentIds as $mabaId) {
                VerificationResult::create([
                    'realisasi_id' => $realisasi->realisasi_id,
                    'maba_id' => $mabaId,
                    'week_id' => $booking->week_id,
                    'claimed_ttd_2022' => (int) ($data['claimed_ttd_2022'] ?? 0),
                    'claimed_ttd_2023' => (int) ($data['claimed_ttd_2023'] ?? 0),
                    'claimed_ttd_2024' => (int) ($data['claimed_ttd_2024'] ?? 0),
                    'status' => 'pending',
                ]);
            }

            $booking->update(['status' => 'completed']);

            if (!empty($data['upload_bukti'])) {
                $this->mongoLogService->uploadBukti($data['upload_bukti'] + [
                    'realisasi_id' => $realisasi->realisasi_id,
                    'maba_id' => $submitter->maba_id,
                ]);
            }

            $this->mongoLogService->activity($submitter->maba_id, 'maba', 'submit_realisasi', 'Maba mengajukan realisasi.', [
                'realisasi_id' => $realisasi->realisasi_id,
                'booking_id' => $bookingId,
            ]);

            return $realisasi;
        });
    }
}
