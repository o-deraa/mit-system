<?php

namespace App\Services\Admin;

use App\Models\Maba;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class MabaAdminWebService
{
    public function create(array $data): Maba
    {
        return Maba::create([
            'nama' => $data['nama'],
            'nrp' => $data['nrp'],
            'password' => Hash::make($data['password'] ?? $data['nrp']),
            'status' => $data['status'] ?? 'active',
        ]);
    }

    public function update(int $mabaId, array $data): Maba
    {
        $maba = Maba::findOrFail($mabaId);

        $maba->update([
            'nama' => $data['nama'],
            'nrp' => $data['nrp'],
            'status' => $data['status'],
        ]);

        if (!empty($data['password'])) {
            $maba->update([
                'password' => Hash::make($data['password']),
            ]);
        }

        return $maba;
    }

    public function deleteIfSafe(int $mabaId): void
    {
        $maba = Maba::findOrFail($mabaId);

        $used = DB::table('booking')->where('created_by_maba_id', $mabaId)->exists()
            || DB::table('booking_participant')->where('maba_id', $mabaId)->exists()
            || DB::table('realisasi')->where('submitted_by_maba_id', $mabaId)->exists()
            || DB::table('verification_result')->where('maba_id', $mabaId)->exists()
            || DB::table('maba_kelompok_history')->where('maba_id', $mabaId)->exists();

        if ($used) {
            throw new RuntimeException('Maba tidak bisa dihapus karena sudah memiliki relasi booking/realisasi/verifikasi/riwayat. Gunakan status inactive.');
        }

        $maba->delete();
    }
}
