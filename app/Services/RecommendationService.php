<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\KelompokWarga;
use App\Models\Maba;
use App\Models\MabaKelompokHistory;
use App\Models\WeeklyAvailability;
use App\Repositories\BookingRepository;
use RuntimeException;

class RecommendationService
{
    public function __construct(
        private BookingRepository $bookingRepository,
        private MongoLogService $mongoLogService
    ) {}

    public function recommend(int $requestedByMabaId, array $nrpList): array
    {
        $nrpList = array_values(array_filter(array_unique(array_map('trim', $nrpList))));

        if (count($nrpList) < 1 || count($nrpList) > 4) {
            throw new RuntimeException('Input NRP harus 1 sampai 4 maba.');
        }

        $mabas = Maba::whereIn('nrp', $nrpList)
            ->where('status', 'active')
            ->get();

        if ($mabas->count() !== count($nrpList)) {
            throw new RuntimeException('Ada NRP maba yang tidak ditemukan atau inactive.');
        }

        $week = $this->bookingRepository->activeWeek();

        if (!$week) {
            throw new RuntimeException('Tidak ada minggu aktif.');
        }

        $results = WeeklyAvailability::with('group.representative')
            ->where('week_id', $week->week_id)
            ->where('is_available', true)
            ->get()
            ->map(function (WeeklyAvailability $availability) use ($mabas, $week) {
                $groupId = $availability->kelompok_warga_id;
                $queue = $this->bookingRepository->activeQueueCount($week->week_id, $groupId);
                $maxQueue = (int) $availability->session_count;

                $score = 40;
                $reasons = ['Kelompok tersedia pada minggu aktif.'];

                $alreadyMet = MabaKelompokHistory::whereIn('maba_id', $mabas->pluck('maba_id'))
                    ->where('kelompok_warga_id', $groupId)
                    ->exists();

                if ($alreadyMet) {
                    $score -= 100;
                    $reasons[] = 'Ada maba yang sudah pernah bertemu kelompok ini.';
                } else {
                    $score += 30;
                    $reasons[] = 'Semua maba belum pernah bertemu kelompok ini.';
                }

                $hasActiveSameGroup = false;

                foreach ($mabas as $maba) {
                    if ($this->bookingRepository->mabaHasActiveBookingSameGroup($maba->maba_id, $groupId)) {
                        $hasActiveSameGroup = true;
                        break;
                    }
                }

                if ($hasActiveSameGroup) {
                    $score -= 100;
                    $reasons[] = 'Ada maba yang sudah punya booking aktif ke kelompok ini.';
                }

                if ($queue >= $maxQueue) {
                    $score -= 100;
                    $reasons[] = "Queue aktif kelompok sudah penuh: {$queue}/{$maxQueue}.";
                } else {
                    $score += 20;
                    $reasons[] = "Queue masih tersedia: {$queue}/{$maxQueue}.";
                }

                $completedCount = Booking::where('kelompok_warga_id', $groupId)
                    ->where('status', 'completed')
                    ->count();

                if ($completedCount <= 1) {
                    $score += 10;
                    $reasons[] = 'Kelompok relatif jarang dipilih.';
                }

                return [
                    'kelompok_warga_id' => $groupId,
                    'kode_kelompok' => $availability->group?->kode_kelompok,
                    'perwakilan' => $availability->group?->representative?->nama,
                    'wa' => $availability->group?->nomor_wa_perwakilan,
                    'score' => $score,
                    'queue_count' => $queue,
                    'max_queue' => $maxQueue,
                    'sisa_queue' => max(0, $maxQueue - $queue),
                    'session_mode' => $availability->session_mode,
                    'reasons' => $reasons,
                ];
            })
            ->sortByDesc('score')
            ->values()
            ->take(5)
            ->toArray();

        $this->mongoLogService->recommendation($requestedByMabaId, $nrpList, $results, $results);

        return $results;
    }
}
