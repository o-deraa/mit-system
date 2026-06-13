<?php

namespace App\Services\Admin;

use App\Models\Warga;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class WargaAdminWebService
{
    public function create(array $data): Warga
    {
        return Warga::create([
            'nama' => $data['nama'],
            'nrp' => $data['nrp'],
            'angkatan' => $data['angkatan'],
            'password' => Hash::make($data['password'] ?? $data['nrp']),
            'status' => $data['status'] ?? 'active',
        ]);
    }

    public function update(int $wargaId, array $data): Warga
    {
        $warga = Warga::findOrFail($wargaId);

        $warga->update([
            'nama' => $data['nama'],
            'nrp' => $data['nrp'],
            'angkatan' => $data['angkatan'],
            'status' => $data['status'],
        ]);

        if (!empty($data['password'])) {
            $warga->update([
                'password' => Hash::make($data['password']),
            ]);
        }

        return $warga;
    }

    public function deleteIfSafe(int $wargaId): void
    {
        $warga = Warga::findOrFail($wargaId);

        $used = DB::table('kelompok_warga')->where('warga_id', $wargaId)->exists()
            || DB::table('kelompok_warga_member')->where('warga_id', $wargaId)->exists()
            || DB::table('booking')->where('decided_by_warga_id', $wargaId)->exists();

        if ($used) {
            throw new RuntimeException('Warga tidak bisa dihapus karena sudah menjadi perwakilan/anggota kelompok atau pernah memproses booking. Gunakan status inactive.');
        }

        $warga->delete();
    }
}
