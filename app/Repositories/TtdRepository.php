<?php

namespace App\Repositories;

use App\Models\VerificationResult;
use Illuminate\Support\Facades\DB;

class TtdRepository
{
    public function verifiedSumByMaba(int $mabaId): object
    {
        return VerificationResult::where('maba_id', $mabaId)
            ->where('status', 'verified')
            ->selectRaw('
                COALESCE(SUM(verified_ttd_2022), 0) AS total_2022,
                COALESCE(SUM(verified_ttd_2023), 0) AS total_2023,
                COALESCE(SUM(verified_ttd_2024), 0) AS total_2024,
                COALESCE(SUM(verified_ttd_2022 + verified_ttd_2023 + verified_ttd_2024), 0) AS total
            ')
            ->first();
    }

    public function weeklyRecapByMaba(int $mabaId)
    {
        return VerificationResult::query()
            ->join('mit_week', 'mit_week.week_id', '=', 'verification_result.week_id')
            ->where('verification_result.maba_id', $mabaId)
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
}
