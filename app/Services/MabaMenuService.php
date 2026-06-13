<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Maba;
use App\Models\VerificationResult;
use Illuminate\Console\Command;
use Throwable;

class MabaMenuService
{
    public function __construct(
        private readonly Command $command,
        private readonly BookingService $bookingService,
        private readonly RealisasiService $realisasiService,
        private readonly ReportService $reportService
    ) {
    }

    public function show(Maba $maba): void
    {
        while (true) {
            $this->command->info("\n=== Menu Maba: {$maba->nama} ===");
            $choice = $this->command->choice('Pilih menu', [
                'Lihat Profil',
                'Lihat Target TTD',
                'Lihat Progress TTD',
                'Lihat Kelompok Warga Tersedia',
                'Buat Request Booking',
                'Gabung Booking yang Sudah Acc',
                'Lihat Booking Saya',
                'Ajukan Realisasi Pertemuan',
                'Lihat Status Verifikasi',
                'Logout',
            ]);

            try {
                match ($choice) {
                    'Lihat Profil' => $this->profile($maba),
                    'Lihat Target TTD' => $this->target(),
                    'Lihat Progress TTD' => $this->progress($maba),
                    'Lihat Kelompok Warga Tersedia' => $this->availableGroups(),
                    'Buat Request Booking' => $this->createBooking($maba),
                    'Gabung Booking yang Sudah Acc' => $this->joinBooking($maba),
                    'Lihat Booking Saya' => $this->myBookings($maba),
                    'Ajukan Realisasi Pertemuan' => $this->submitRealisasi($maba),
                    'Lihat Status Verifikasi' => $this->verificationStatus($maba),
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

    private function profile(Maba $maba): void
    {
        $this->command->table(
            ['Field', 'Value'],
            [
                ['Nama', $maba->nama],
                ['NRP', $maba->nrp],
                ['Status', $maba->status],
            ]
        );
    }

    private function target(): void
    {
        $this->command->table(
            ['Kategori', 'Target'],
            [
                ['TTD 2022', config('mit.target_ttd.2022')],
                ['TTD 2023', config('mit.target_ttd.2023')],
                ['TTD 2024', config('mit.target_ttd.2024')],
                ['Total', config('mit.target_ttd.total')],
            ]
        );
    }

    private function progress(Maba $maba): void
    {
        $p = $this->reportService->progressMaba($maba);

        $this->command->table(
            ['Kategori', 'Valid', 'Target', 'Kurang'],
            [
                ['2022', $p['ttd_2022'], $p['target_2022'], $p['kurang_2022']],
                ['2023', $p['ttd_2023'], $p['target_2023'], $p['kurang_2023']],
                ['2024', $p['ttd_2024'], $p['target_2024'], $p['kurang_2024']],
                ['Total', $p['total_ttd'], $p['target_total'], $p['kurang_total']],
            ]
        );
    }

    private function availableGroups(): void
    {
        $rows = $this->bookingService->availableGroups()->map(function ($availability) {
            return [
                $availability->kelompok_warga_id,
                $availability->kelompokWarga->kode_kelompok,
                $availability->kelompokWarga->perwakilan?->nama,
                $availability->session_mode,
                $availability->session_count,
                $availability->notes,
            ];
        });

        $this->command->table(
            ['ID Kelompok', 'Kode', 'Perwakilan', 'Mode Sesi', 'Jumlah Sesi', 'Catatan'],
            $rows->toArray()
        );
    }

    private function createBooking(Maba $maba): void
    {
        $kelompokId = (int) $this->command->ask('Masukkan ID kelompok_warga');

        $booking = $this->bookingService->createBooking($maba, $kelompokId);

        $this->command->info("Booking berhasil dibuat. Booking ID: {$booking->booking_id}, status: {$booking->status}");
    }

    private function joinBooking(Maba $maba): void
    {
        $bookingId = (int) $this->command->ask('Masukkan Booking ID');

        $participant = $this->bookingService->joinBooking($maba, $bookingId);

        $this->command->info("Berhasil join booking. Participant ID: {$participant->booking_participant_id}");
    }

    private function myBookings(Maba $maba): void
    {
        $rows = Booking::with('kelompokWarga')
            ->where('created_by_maba_id', $maba->maba_id)
            ->orWhereHas('participants', function ($q) use ($maba) {
                $q->where('maba_id', $maba->maba_id);
            })
            ->get()
            ->map(fn ($b) => [
                $b->booking_id,
                $b->kelompokWarga?->kode_kelompok,
                $b->status,
                $b->final_schedule,
                $b->final_location,
            ]);

        $this->command->table(
            ['Booking ID', 'Kelompok', 'Status', 'Jadwal', 'Lokasi'],
            $rows->toArray()
        );
    }

    private function submitRealisasi(Maba $maba): void
    {
        $bookingId = (int) $this->command->ask('Masukkan Booking ID');

        $presentInput = (string) $this->command->ask('Masukkan maba_id yang present, pisahkan koma. Contoh: 1,2,3');
        $absentInput = (string) $this->command->ask('Masukkan maba_id yang absent, pisahkan koma. Kosongkan jika tidak ada');

        $presentIds = $this->parseIds($presentInput);
        $absentIds = $this->parseIds($absentInput);

        $replacedPairs = [];
        if ($this->command->confirm('Ada peserta yang replaced/digantikan?', false)) {
            while (true) {
                $oldId = (int) $this->command->ask('maba_id lama yang digantikan');
                $newId = (int) $this->command->ask('maba_id pengganti');
                $replacedPairs[$oldId] = $newId;

                if (!$this->command->confirm('Tambah replacement lagi?', false)) {
                    break;
                }
            }
        }

        $claimed2022 = (int) $this->command->ask('Input TTD 2022');
        $claimed2023 = (int) $this->command->ask('Input TTD 2023');
        $claimed2024 = (int) $this->command->ask('Input TTD 2024');

        $absentWargaNotes = $this->command->ask('Catatan warga resmi tidak hadir, kosongkan jika tidak ada');
        $additionalWargaNotes = $this->command->ask('Catatan warga tambahan hadir, kosongkan jika tidak ada');
        $generalNotes = $this->command->ask('Catatan umum, kosongkan jika tidak ada');

        $realisasi = $this->realisasiService->submitRealisasi(
            $bookingId,
            $maba,
            $presentIds,
            $absentIds,
            $replacedPairs,
            $claimed2022,
            $claimed2023,
            $claimed2024,
            $absentWargaNotes,
            $additionalWargaNotes,
            $generalNotes
        );

        $this->command->info("Realisasi berhasil diajukan. Realisasi ID: {$realisasi->realisasi_id}");
    }

    private function verificationStatus(Maba $maba): void
    {
        $rows = VerificationResult::where('maba_id', $maba->maba_id)
            ->with('week')
            ->get()
            ->map(fn ($v) => [
                $v->verification_id,
                $v->week?->week_number,
                $v->status,
                $v->claimed_ttd_2022 + $v->claimed_ttd_2023 + $v->claimed_ttd_2024,
                $v->verified_ttd_2022 + $v->verified_ttd_2023 + $v->verified_ttd_2024,
                $v->admin_comment,
            ]);

        $this->command->table(
            ['Verification ID', 'Minggu', 'Status', 'Claimed Total', 'Verified Total', 'Komentar'],
            $rows->toArray()
        );
    }

    private function parseIds(string $input): array
    {
        if (trim($input) === '') {
            return [];
        }

        return collect(explode(',', $input))
            ->map(fn ($id) => (int) trim($id))
            ->filter(fn ($id) => $id > 0)
            ->values()
            ->toArray();
    }
}
