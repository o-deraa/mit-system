<?php

namespace App\Repositories;

use App\Models\KelompokWarga;

class KelompokWargaRepository
{
    public function getNextKodeKelompok(): int
    {
        return ((int) KelompokWarga::max('kode_kelompok')) + 1;
    }

    public function findByKode(int $kode): ?KelompokWarga
    {
        return KelompokWarga::where('kode_kelompok', $kode)->first();
    }

    public function finalGroups()
    {
        return KelompokWarga::with(['representative', 'members.warga'])
            ->where('status', 'final')
            ->orderBy('kode_kelompok')
            ->get();
    }
}
