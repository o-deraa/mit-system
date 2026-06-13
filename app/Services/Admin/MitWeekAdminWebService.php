<?php

namespace App\Services\Admin;

use App\Models\MitWeek;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MitWeekAdminWebService
{
    public function create(array $data): MitWeek
    {
        return MitWeek::create([
            'week_number' => $data['week_number'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => 'upcoming',
            'availability_input_status' => 'closed',
        ]);
    }

    public function activate(int $weekId): MitWeek
    {
        return DB::transaction(function () use ($weekId) {
            $week = MitWeek::findOrFail($weekId);

            if ($week->status === 'completed') {
                throw new RuntimeException('Minggu yang sudah completed tidak bisa diaktifkan ulang.');
            }

            $activeExists = MitWeek::where('status', 'active')
                ->where('week_id', '!=', $weekId)
                ->exists();

            if ($activeExists) {
                throw new RuntimeException('Masih ada minggu MIT yang active. Tutup minggu active terlebih dahulu.');
            }

            $week->update([
                'status' => 'active',
            ]);

            return $week;
        });
    }

    public function close(int $weekId): MitWeek
    {
        $week = MitWeek::findOrFail($weekId);

        if ($week->status !== 'active') {
            throw new RuntimeException('Hanya minggu active yang bisa ditutup.');
        }

        $week->update([
            'status' => 'completed',
            'availability_input_status' => 'closed',
        ]);

        return $week;
    }

    public function toggleAvailabilityInput(int $weekId): MitWeek
    {
        $week = MitWeek::findOrFail($weekId);

        if ($week->status !== 'active') {
            throw new RuntimeException('Input ketersediaan hanya bisa dibuka/tutup pada minggu active.');
        }

        $week->update([
            'availability_input_status' => $week->availability_input_status === 'open' ? 'closed' : 'open',
        ]);

        return $week;
    }
}
