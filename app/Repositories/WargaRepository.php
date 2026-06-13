<?php

namespace App\Repositories;

use App\Models\Warga;

class WargaRepository
{
    public function findActiveByNrp(string $nrp): ?Warga
    {
        return Warga::where('nrp', $nrp)->where('status', 'active')->first();
    }
}
