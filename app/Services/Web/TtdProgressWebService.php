<?php

namespace App\Services\Web;

use App\Models\Maba;
use App\Models\VerificationResult;

class TtdProgressWebService
{
    public function progress(int $mabaId): array
    {
        $verified = VerificationResult::where('maba_id', $mabaId)
            ->where('status', 'verified')
            ->selectRaw('
                COALESCE(SUM(verified_ttd_2022), 0) as total_2022,
                COALESCE(SUM(verified_ttd_2023), 0) as total_2023,
                COALESCE(SUM(verified_ttd_2024), 0) as total_2024
            ')
            ->first();

        $total2022 = (int) $verified->total_2022;
        $total2023 = (int) $verified->total_2023;
        $total2024 = (int) $verified->total_2024;
        $total = $total2022 + $total2023 + $total2024;

        return [
            'total_2022' => $total2022,
            'target_2022' => 4,
            'kurang_2022' => max(0, 4 - $total2022),

            'total_2023' => $total2023,
            'target_2023' => 24,
            'kurang_2023' => max(0, 24 - $total2023),

            'total_2024' => $total2024,
            'target_2024' => 72,
            'kurang_2024' => max(0, 72 - $total2024),

            'total' => $total,
            'target_total' => 100,
            'kurang_total' => max(0, 100 - $total),
        ];
    }

    public function weeklyRecap(int $mabaId)
    {
        return VerificationResult::with(['week', 'realisasi.booking.group'])
            ->where('maba_id', $mabaId)
            ->orderBy('week_id')
            ->get()
            ->map(function (VerificationResult $result) {
                return [
                    'week_number' => $result->week?->week_number,
                    'status' => $result->status,
                    'claimed_2022' => $result->claimed_ttd_2022,
                    'claimed_2023' => $result->claimed_ttd_2023,
                    'claimed_2024' => $result->claimed_ttd_2024,
                    'verified_2022' => $result->verified_ttd_2022,
                    'verified_2023' => $result->verified_ttd_2023,
                    'verified_2024' => $result->verified_ttd_2024,
                    'admin_comment' => $result->admin_comment,
                    'kelompok' => $result->realisasi?->booking?->group?->kode_kelompok,
                ];
            });
    }

    public function historyGroups(int $mabaId)
    {
        $maba = Maba::with('histories')->findOrFail($mabaId);

        return $maba->histories()
            ->with(['group', 'week'])
            ->latest('created_at')
            ->get();
    }
}
