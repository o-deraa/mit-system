<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Models\Maba;
use App\Models\MabaKelompokHistory;
use App\Models\WeeklyAvailability;
use App\Repositories\BookingRepository;

class BookingQueryService
{
    public function __construct(private BookingRepository $bookingRepository) {}

    public function availableGroupsForMaba(Maba $maba): array
    {
        $week = $this->bookingRepository->activeWeek();
        if (!$week) {
            return [];
        }

        return WeeklyAvailability::with(['group.representativeMember.warga'])
            ->where('week_id', $week->week_id)
            ->where('is_available', true)
            ->get()
            ->map(function (WeeklyAvailability $availability) use ($maba, $week) {
                $groupId = $availability->kelompok_warga_id;
                $queueCount = $this->bookingRepository->activeQueueCount($week->week_id, $groupId);
                $hasMet = MabaKelompokHistory::where('maba_id', $maba->maba_id)
                    ->where('kelompok_warga_id', $groupId)
                    ->exists();
                $hasActiveSameGroup = $this->bookingRepository->mabaHasActiveBookingSameGroup($maba->maba_id, $groupId);

                $acceptedSlotLeft = Booking::where('week_id', $week->week_id)
                    ->where('kelompok_warga_id', $groupId)
                    ->where('status', 'accepted')
                    ->get()
                    ->sum(function (Booking $booking) use ($availability) {
                        $count = BookingParticipant::where('booking_id', $booking->booking_id)
                            ->whereIn('status', ['joined', 'present'])
                            ->count();
                        return max(0, $availability->session_mode - $count);
                    });

                return [
                    'kelompok_warga_id' => $groupId,
                    'kode_kelompok' => $availability->group?->kode_kelompok,
                    'perwakilan' => $availability->group?->representativeMember?->warga?->nama,
                    'wa' => $availability->group?->representativeMember?->nomor_wa,
                    'session_mode' => $availability->session_mode,
                    'session_count' => $availability->session_count,
                    'queue_aktif' => $queueCount,
                    'sisa_queue' => max(0, $availability->session_count - $queueCount),
                    'sisa_slot_booking_accepted' => $acceptedSlotLeft,
                    'boleh_booking_baru' => !$hasMet && !$hasActiveSameGroup && $queueCount < $availability->session_count,
                    'catatan_validasi' => $hasMet
                        ? 'Sudah pernah bertemu kelompok ini'
                        : ($hasActiveSameGroup ? 'Sudah punya booking aktif ke kelompok ini' : 'Boleh request booking baru'),
                ];
            })
            ->values()
            ->toArray();
    }

    public function joinableAcceptedBookingsForMaba(Maba $maba): array
    {
        $week = $this->bookingRepository->activeWeek();

        if (!$week) {
            return [];
        }

        return Booking::with(['group.representativeMember.warga'])
            ->where('week_id', $week->week_id)
            ->where('status', 'accepted')
            ->get()
            ->map(function (Booking $booking) use ($maba) {
                $availability = WeeklyAvailability::where('week_id', $booking->week_id)
                    ->where('kelompok_warga_id', $booking->kelompok_warga_id)
                    ->first();

                if (!$availability) {
                    return null;
                }

                $participantCount = $this->bookingRepository->activeParticipantCount($booking->booking_id);
                $sisaSlot = max(0, $availability->session_mode - $participantCount);

                $hasMet = MabaKelompokHistory::where('maba_id', $maba->maba_id)
                    ->where('kelompok_warga_id', $booking->kelompok_warga_id)
                    ->exists();

                $alreadyJoined = BookingParticipant::where('booking_id', $booking->booking_id)
                    ->where('maba_id', $maba->maba_id)
                    ->whereIn('status', ['joined', 'present'])
                    ->exists();

                if ($sisaSlot <= 0 || $hasMet || $alreadyJoined) {
                    return null;
                }

                return [
                    'booking_id' => $booking->booking_id,
                    'kode_kelompok' => $booking->group?->kode_kelompok,
                    'perwakilan' => $booking->group?->representativeMember?->warga?->nama,
                    'final_schedule' => $booking->final_schedule,
                    'final_location' => $booking->final_location,
                    'peserta' => $participantCount,
                    'kapasitas' => $availability->session_mode,
                    'sisa_slot' => $sisaSlot,
                ];
            })
            ->filter()
            ->values()
            ->toArray();
    }
}
