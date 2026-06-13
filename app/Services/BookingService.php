<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Models\KelompokWarga;
use App\Models\Maba;
use App\Models\MabaKelompokHistory;
use App\Models\Warga;
use App\Models\WeeklyAvailability;
use App\Repositories\BookingRepository;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BookingService
{
    public const MAX_ACTIVE_QUEUE = 3;

    public function __construct(
        private BookingRepository $bookingRepository,
        private MongoLogService $mongoLogService
    ) {}

    public function createBooking(Maba $maba, int $kelompokWargaId): Booking
    {
        return DB::transaction(function () use ($maba, $kelompokWargaId) {
            $week = $this->bookingRepository->activeWeek();
            if (!$week) {
                throw new RuntimeException('Tidak ada minggu MIT yang aktif.');
            }

            if ($maba->status !== 'active') {
                throw new RuntimeException('Maba inactive tidak bisa membuat booking.');
            }

            $hasMet = MabaKelompokHistory::where('maba_id', $maba->maba_id)
                ->where('kelompok_warga_id', $kelompokWargaId)
                ->exists();
            if ($hasMet) {
                throw new RuntimeException('Maba sudah pernah bertemu kelompok warga ini, jadi tidak boleh booking lagi.');
            }

            if ($this->bookingRepository->mabaHasActiveBookingSameGroup($maba->maba_id, $kelompokWargaId)) {
                throw new RuntimeException('Maba sudah memiliki booking aktif pada kelompok warga yang sama.');
            }

            $availability = WeeklyAvailability::where('week_id', $week->week_id)
                ->where('kelompok_warga_id', $kelompokWargaId)
                ->where('is_available', true)
                ->first();
            if (!$availability) {
                throw new RuntimeException('Kelompok warga tidak tersedia pada minggu aktif.');
            }

            $queueCount = $this->bookingRepository->activeQueueCount($week->week_id, $kelompokWargaId);
            $maxQueue = $this->maxActiveQueueForAvailability($availability);

            if ($queueCount >= $maxQueue) {
                throw new RuntimeException("Queue aktif kelompok warga sudah penuh: {$queueCount}/{$maxQueue}.");
            }

            $booking = Booking::create([
                'week_id' => $week->week_id,
                'kelompok_warga_id' => $kelompokWargaId,
                'created_by_maba_id' => $maba->maba_id,
                'status' => 'pending',
            ]);

            BookingParticipant::create([
                'booking_id' => $booking->booking_id,
                'maba_id' => $maba->maba_id,
                'status' => 'joined',
                'joined_at' => now(),
            ]);

            $this->mongoLogService->activity($maba->maba_id, 'maba', 'create_booking', 'Maba membuat request booking.', [
                'booking_id' => $booking->booking_id,
                'kelompok_warga_id' => $kelompokWargaId,
            ]);

            return $booking;
        });
    }

    public function acceptBooking(Warga $warga, int $bookingId, string $schedule, string $location, ?string $notes = null): Booking
    {
        return DB::transaction(function () use ($warga, $bookingId, $schedule, $location, $notes) {
            $booking = Booking::lockForUpdate()->findOrFail($bookingId);
            $group = KelompokWarga::findOrFail($booking->kelompok_warga_id);

            if ($group->warga_id !== $warga->warga_id) {
                throw new RuntimeException('Hanya perwakilan kelompok yang boleh accept booking.');
            }

            if ($booking->status !== 'pending') {
                throw new RuntimeException('Hanya booking pending yang bisa di-accept.');
            }

            if (trim($schedule) === '' || trim($location) === '') {
                throw new RuntimeException('Jadwal final dan lokasi final wajib diisi.');
            }

            $booking->update([
                'status' => 'accepted',
                'final_schedule' => $schedule,
                'final_location' => $location,
                'warga_notes' => $notes,
                'decided_by_warga_id' => $warga->warga_id,
                'decided_at' => now(),
            ]);

            $this->mongoLogService->activity($warga->warga_id, 'warga', 'accept_booking', 'Warga accept booking.', [
                'booking_id' => $bookingId,
            ]);

            return $booking;
        });
    }

    public function cancelBooking(Warga $warga, int $bookingId, string $reason): Booking
    {
        return DB::transaction(function () use ($warga, $bookingId, $reason) {
            $booking = Booking::lockForUpdate()->findOrFail($bookingId);
            $group = KelompokWarga::findOrFail($booking->kelompok_warga_id);

            if ($group->warga_id !== $warga->warga_id) {
                throw new RuntimeException('Hanya perwakilan kelompok yang boleh cancel booking.');
            }

            if (!in_array($booking->status, ['pending', 'accepted'], true)) {
                throw new RuntimeException('Booking ini tidak bisa dibatalkan.');
            }

            if (trim($reason) === '') {
                throw new RuntimeException('Alasan cancelled wajib diisi.');
            }

            $booking->update([
                'status' => 'cancelled',
                'cancelled_reason' => $reason,
                'decided_by_warga_id' => $warga->warga_id,
                'decided_at' => now(),
            ]);

            $this->mongoLogService->activity($warga->warga_id, 'warga', 'cancel_booking', 'Warga cancel booking.', [
                'booking_id' => $bookingId,
                'reason' => $reason,
            ]);

            return $booking;
        });
    }

    public function joinBooking(Maba $maba, int $bookingId): BookingParticipant
    {
        return DB::transaction(function () use ($maba, $bookingId) {
            $booking = Booking::lockForUpdate()->findOrFail($bookingId);

            if ($booking->status !== 'accepted') {
                throw new RuntimeException('Maba hanya bisa join booking accepted.');
            }

            if (MabaKelompokHistory::where('maba_id', $maba->maba_id)->where('kelompok_warga_id', $booking->kelompok_warga_id)->exists()) {
                throw new RuntimeException('Maba sudah pernah bertemu kelompok warga ini.');
            }

            if ($this->bookingRepository->mabaHasActiveBookingSameGroup($maba->maba_id, $booking->kelompok_warga_id)) {
                throw new RuntimeException('Maba sudah memiliki booking aktif pada kelompok warga yang sama.');
            }

            $availability = WeeklyAvailability::where('week_id', $booking->week_id)
                ->where('kelompok_warga_id', $booking->kelompok_warga_id)
                ->firstOrFail();

            $participantCount = $this->bookingRepository->activeParticipantCount($booking->booking_id);
            if ($participantCount >= $availability->session_mode) {
                throw new RuntimeException('Booking sudah penuh.');
            }

            $participant = BookingParticipant::create([
                'booking_id' => $booking->booking_id,
                'maba_id' => $maba->maba_id,
                'status' => 'joined',
                'joined_at' => now(),
            ]);

            $this->mongoLogService->activity($maba->maba_id, 'maba', 'join_booking', 'Maba join booking accepted.', [
                'booking_id' => $bookingId,
            ]);

            return $participant;
        });
    }

    private function maxActiveQueueForAvailability(WeeklyAvailability $availability): int
    {
        return (int) $availability->session_count;
    }

    public function acceptBookingWithoutSchedule(Warga $warga, int $bookingId, ?string $notes = null): Booking
    {
        return DB::transaction(function () use ($warga, $bookingId, $notes) {
            $booking = Booking::lockForUpdate()->findOrFail($bookingId);
            $group = KelompokWarga::findOrFail($booking->kelompok_warga_id);

            if ($group->warga_id !== $warga->warga_id) {
                throw new RuntimeException('Hanya perwakilan kelompok yang boleh accept booking.');
            }

            if ($booking->status !== 'pending') {
                throw new RuntimeException('Hanya booking pending yang bisa di-accept.');
            }

            $booking->update([
                'status' => 'accepted',
                'warga_notes' => $notes,
                'decided_by_warga_id' => $warga->warga_id,
                'decided_at' => now(),
            ]);

            $this->mongoLogService->activity($warga->warga_id, 'warga', 'accept_booking', 'Warga accept booking tanpa jadwal final.', [
                'booking_id' => $bookingId,
            ]);

            return $booking;
        });
    }

    public function updateFinalScheduleByMaba(Maba $maba, int $bookingId, string $schedule, string $location): Booking
    {
        return DB::transaction(function () use ($maba, $bookingId, $schedule, $location) {
            $booking = Booking::lockForUpdate()->findOrFail($bookingId);

            if ($booking->status !== 'accepted') {
                throw new RuntimeException('Jadwal final hanya bisa diisi untuk booking accepted.');
            }

            $isParticipant = BookingParticipant::where('booking_id', $booking->booking_id)
                ->where('maba_id', $maba->maba_id)
                ->where('status', 'joined')
                ->exists();

            if (!$isParticipant) {
                throw new RuntimeException('Hanya peserta booking yang boleh mengisi jadwal final.');
            }

            if (trim($schedule) === '' || trim($location) === '') {
                throw new RuntimeException('Jadwal final dan lokasi final wajib diisi.');
            }

            $booking->update([
                'final_schedule' => $schedule,
                'final_location' => $location,
            ]);

            $this->mongoLogService->activity($maba->maba_id, 'maba', 'update_final_schedule', 'Maba mengisi jadwal dan lokasi final booking.', [
                'booking_id' => $booking->booking_id,
                'final_schedule' => $schedule,
                'final_location' => $location,
            ]);

            return $booking;
        });
    }

    public function leaveBooking(Maba $maba, int $bookingId): BookingParticipant
    {
        return DB::transaction(function () use ($maba, $bookingId) {
            $booking = Booking::lockForUpdate()->findOrFail($bookingId);

            if (!in_array($booking->status, ['pending', 'accepted'], true)) {
                throw new RuntimeException('Maba hanya bisa keluar dari booking pending atau accepted.');
            }

            $participant = BookingParticipant::where('booking_id', $booking->booking_id)
                ->where('maba_id', $maba->maba_id)
                ->where('status', 'joined')
                ->lockForUpdate()
                ->first();

            if (!$participant) {
                throw new RuntimeException('Data peserta aktif tidak ditemukan.');
            }

            $participant->update([
                'status' => 'left',
                'left_at' => now(),
            ]);

            $this->mongoLogService->activity($maba->maba_id, 'maba', 'leave_booking', 'Maba keluar dari booking.', [
                'booking_id' => $booking->booking_id,
            ]);

            return $participant;
        });
    }
}
