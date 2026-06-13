<?php

namespace App\Services;

use App\Models\VerificationResult;

class TtdService
{
    public const TARGET_TTD_2022 = 4;
    public const TARGET_TTD_2023 = 24;
    public const TARGET_TTD_2024 = 72;
    public const TARGET_TTD_TOTAL = 100;

    public function progress(int $mabaId): array
    {
        $row = VerificationResult::where('maba_id', $mabaId)
            ->where('status', 'verified')
            ->selectRaw('COALESCE(SUM(verified_ttd_2022),0) as total_2022')
            ->selectRaw('COALESCE(SUM(verified_ttd_2023),0) as total_2023')
            ->selectRaw('COALESCE(SUM(verified_ttd_2024),0) as total_2024')
            ->first();

        $t2022 = (int) $row->total_2022;
        $t2023 = (int) $row->total_2023;
        $t2024 = (int) $row->total_2024;
        $total = $t2022 + $t2023 + $t2024;

        return [
            'total_2022' => $t2022,
            'target_2022' => self::TARGET_TTD_2022,
            'kurang_2022' => max(0, self::TARGET_TTD_2022 - $t2022),
            'total_2023' => $t2023,
            'target_2023' => self::TARGET_TTD_2023,
            'kurang_2023' => max(0, self::TARGET_TTD_2023 - $t2023),
            'total_2024' => $t2024,
            'target_2024' => self::TARGET_TTD_2024,
            'kurang_2024' => max(0, self::TARGET_TTD_2024 - $t2024),
            'total' => $total,
            'target_total' => self::TARGET_TTD_TOTAL,
            'kurang_total' => max(0, self::TARGET_TTD_TOTAL - $total),
        ];
    }
}
