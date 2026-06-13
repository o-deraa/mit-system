<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Maba;
use App\Models\MabaKelompokHistory;
use App\Models\VerificationResult;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function __construct(private TtdService $ttdService) {}

    public function progressPerMaba(string $nrp): array
    {
        $maba = Maba::where('nrp', $nrp)->firstOrFail();
        return [
            'maba' => $maba,
            'progress' => $this->ttdService->progress($maba->maba_id),
        ];
    }

    public function allMabaProgressSummary(): array
    {
        $mabaList = Maba::where('status', 'active')->get();
        $complete = 0;
        $total = 0;

        foreach ($mabaList as $maba) {
            $progress = $this->ttdService->progress($maba->maba_id);
            $total += $progress['total'];
            if ($progress['is_complete']) {
                $complete++;
            }
        }

        return [
            'jumlah_maba' => $mabaList->count(),
            'rata_rata_total' => $mabaList->count() > 0 ? round($total / $mabaList->count(), 2) : 0,
            'jumlah_memenuhi_target' => $complete,
            'jumlah_belum_memenuhi_target' => $mabaList->count() - $complete,
        ];
    }

    public function weeklyRecap()
    {
        return VerificationResult::query()
            ->join('mit_week', 'mit_week.week_id', '=', 'verification_result.week_id')
            ->where('verification_result.status', 'verified')
            ->groupBy('mit_week.week_id', 'mit_week.week_number')
            ->orderBy('mit_week.week_number')
            ->select([
                'mit_week.week_number',
                DB::raw('SUM(verified_ttd_2022) AS total_2022'),
                DB::raw('SUM(verified_ttd_2023) AS total_2023'),
                DB::raw('SUM(verified_ttd_2024) AS total_2024'),
                DB::raw('SUM(verified_ttd_2022 + verified_ttd_2023 + verified_ttd_2024) AS total'),
            ])
            ->get();
    }

    public function popularGroups()
    {
        return MabaKelompokHistory::query()
            ->join('kelompok_warga', 'kelompok_warga.kelompok_warga_id', '=', 'maba_kelompok_history.kelompok_warga_id')
            ->groupBy('kelompok_warga.kode_kelompok')
            ->orderByDesc(DB::raw('COUNT(*)'))
            ->select([
                'kelompok_warga.kode_kelompok',
                DB::raw('COUNT(*) AS jumlah_maba_pernah_bertemu'),
            ])
            ->get();
    }
}
