<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Models\MitWeek;

class BookingRepository
{
    public function activeWeek(): ?MitWeek
    {
        return MitWeek::where('status', 'active')->first();
    }

    public function activeQueueCount(int $weekId, int $kelompokWargaId): int
    {
        return Booking::where('week_id', $weekId)
            ->where('kelompok_warga_id', $kelompokWargaId)
            ->whereIn('status', ['pending', 'accepted'])
            ->count();
    }

    public function activeParticipantCount(int $bookingId): int
    {
        return BookingParticipant::where('booking_id', $bookingId)
            ->whereIn('status', ['joined', 'present'])
            ->count();
    }

    public function mabaHasActiveBookingSameGroup(int $mabaId, int $kelompokWargaId): bool
    {
        return Booking::where('kelompok_warga_id', $kelompokWargaId)
            ->whereIn('status', ['pending', 'accepted'])
            ->whereHas('participants', function ($query) use ($mabaId) {
                $query->where('maba_id', $mabaId)
                    ->whereIn('status', ['joined', 'present']);
            })
            ->exists();
    }
}
