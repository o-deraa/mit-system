<?php

namespace App\Repositories;

use App\Models\Maba;

class MabaRepository
{
    public function findActiveByNrp(string $nrp): ?Maba
    {
        return Maba::where('nrp', $nrp)->where('status', 'active')->first();
    }

    public function search(string $keyword)
    {
        return Maba::where('nama', 'like', "%{$keyword}%")
            ->orWhere('nrp', 'like', "%{$keyword}%")
            ->orderBy('nama')
            ->get();
    }
}
