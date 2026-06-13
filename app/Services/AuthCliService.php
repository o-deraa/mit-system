<?php

namespace App\Services;

use App\Models\Maba;
use App\Models\Warga;

class AuthCliService
{
    public function loginAdmin(string $username, string $password): ?array
    {
        if ($username === env('MIT_ADMIN_USERNAME') && $password === env('MIT_ADMIN_PASSWORD')) {
            return [
                'role' => 'admin',
                'identifier' => env('MIT_ADMIN_IDENTIFIER', 'admin-demo'),
            ];
        }

        return null;
    }

    public function loginMaba(string $nrp, string $password): ?Maba
    {
        return Maba::where('nrp', $nrp)
            ->where('password', $password)
            ->where('status', 'active')
            ->first();
    }

    public function loginWarga(string $nrp, string $password): ?Warga
    {
        return Warga::where('nrp', $nrp)
            ->where('password', $password)
            ->where('status', 'active')
            ->first();
    }
}
