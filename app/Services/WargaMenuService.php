<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\KelompokWarga;
use App\Models\Warga;
use Illuminate\Console\Command;
use Throwable;

class WargaMenuService
{
    public function __construct(
        private readonly Command $command,
        private readonly BookingService $bookingService
    ) {
    }

    public function show(Warga $warga): void
    {
        while (true) {
            $this->command->info("\n=== Menu Warga: {$warga->nama} ===");

            $choice = $this->command->choice('Pilih menu', [
                'Lihat Kelompok Saya',
                'Lihat Request Booking ke Kelompok Saya',
                'Acc Booking',
                'Reject Booking',
                'Cancel Booking',
                'Lihat Jadwal Pertemuan',
                'Logout',
            ]);

            try {
                match ($choice) {
                    'Lihat Kelompok Saya' => $this->myGroup($warga),
                    'Lihat Request Booking ke Kelompok Saya' => $this->incomingBookings($warga),
                    'Acc Booking' => $this->accBooking($warga),
                    'Reject Booking' => $this->rejectBooking($warga),
                    'Cancel Booking' => $this->cancelBooking($warga),
                    'Lihat Jadwal Pertemuan' => $this->acceptedSchedules($warga),
                    'Logout' => null,
                };
            } catch (Throwable $e) {
                $this->command->error($e->getMessage());
            }

            if ($choice === 'Logout') {
                return;
            }
        }
    }

    private function myGroup(Warga $warga): void
    {
        $kelompok = KelompokWarga::with(['members.warga', 'perwakilan'])
            ->whereHas('members', fn ($q) => $q->where('warga_id', $warga->warga_id))
            ->first();

        if (!$kelompok) {
            $this->command->warn('Kamu belum tergabung dalam kelompok warga. Status: floating.');
            return;
        }

        $this->command->info("Kelompok: {$kelompok->kode_kelompok}");
        $this->command->info("Perwakilan: {$kelompok->perwakilan?->nama}");
        $this->command->info("Nomor WA: {$kelompok->nomor_wa_perwakilan}");
        $this->command->info("Status: {$kelompok->status}");
        $this->command->info("Rules: {$kelompok->rules}");

        $rows = $kelompok->members->map(fn ($m) => [
            $m->warga?->warga_id,
            $m->warga?->nama,
            $m->warga?->angkatan,
        ]);

        $this->command->table(['Warga ID', 'Nama', 'Angkatan'], $rows->toArray());
    }

    private function incomingBookings(Warga $warga): void
    {
        $kelompok = KelompokWarga::where('warga_id', $warga->warga_id)->first();

        if (!$kelompok) {
            $this->command->warn('Kamu bukan perwakilan kelompok.');
            return;
        }

        $rows = Booking::with(['creator', 'participants.maba'])
            ->where('kelompok_warga_id', $kelompok->kelompok_warga_id)
            ->orderBy('booking_id')
            ->get()
            ->map(fn ($b) => [
                $b->booking_id,
                $b->creator?->nama,
                $b->status,
                $b->participants->count(),
                $b->final_schedule,
                $b->final_location,
            ]);

        $this->command->table(
            ['Booking ID', 'Pembuat', 'Status', 'Jumlah Peserta', 'Jadwal', 'Lokasi'],
            $rows->toArray()
        );
    }

    private function accBooking(Warga $warga): void
    {
        $bookingId = (int) $this->command->ask('Masukkan Booking ID');
        $schedule = (string) $this->command->ask('Masukkan jadwal final');
        $location = (string) $this->command->ask('Masukkan lokasi final');
        $notes = $this->command->ask('Catatan warga, kosongkan jika tidak ada');

        $booking = $this->bookingService->decideBooking(
            $warga,
            $bookingId,
            'acc',
            $schedule,
            $location,
            null,
            $notes
        );

        $this->command->info("Booking {$booking->booking_id} berhasil di-acc.");
    }

    private function rejectBooking(Warga $warga): void
    {
        $bookingId = (int) $this->command->ask('Masukkan Booking ID');
        $reason = (string) $this->command->ask('Masukkan alasan reject');

        $booking = $this->bookingService->decideBooking(
            $warga,
            $bookingId,
            'rejected',
            null,
            null,
            $reason,
            null
        );

        $this->command->info("Booking {$booking->booking_id} berhasil di-reject.");
    }

    private function cancelBooking(Warga $warga): void
    {
        $bookingId = (int) $this->command->ask('Masukkan Booking ID');
        $reason = (string) $this->command->ask('Masukkan alasan cancel');

        $booking = $this->bookingService->decideBooking(
            $warga,
            $bookingId,
            'cancelled',
            null,
            null,
            $reason,
            null
        );

        $this->command->info("Booking {$booking->booking_id} berhasil di-cancel.");
    }

    private function acceptedSchedules(Warga $warga): void
    {
        $kelompok = KelompokWarga::whereHas('members', fn ($q) => $q->where('warga_id', $warga->warga_id))
            ->first();

        if (!$kelompok) {
            $this->command->warn('Kamu belum punya kelompok.');
            return;
        }

        $rows = Booking::where('kelompok_warga_id', $kelompok->kelompok_warga_id)
            ->whereIn('status', ['acc', 'completed'])
            ->orderBy('booking_id')
            ->get()
            ->map(fn ($b) => [
                $b->booking_id,
                $b->status,
                $b->final_schedule,
                $b->final_location,
            ]);

        $this->command->table(
            ['Booking ID', 'Status', 'Jadwal', 'Lokasi'],
            $rows->toArray()
        );
    }
}
