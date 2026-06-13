<?php

namespace App\Services;

use App\Models\Maba;
use App\Models\Warga;
use Illuminate\Support\Facades\Hash;

class WebAuthService
{
    public function loginAdmin(string $username, string $password): ?array
    {
        $adminUsername = (string) config('mit.admin.username');
        $adminPassword = (string) config('mit.admin.password');

        if (!hash_equals($adminUsername, $username)) {
            return null;
        }

        if (!hash_equals($adminPassword, $password)) {
            return null;
        }

        return [
            'role' => 'admin',
            'id' => config('mit.admin.identifier'),
            'name' => 'Admin MIT',
            'identifier' => config('mit.admin.identifier'),
        ];
    }

    public function loginWarga(string $nrp, string $password): ?Warga
    {
        $warga = Warga::where('nrp', $nrp)->first();

        if (!$warga) {
            return null;
        }

        if (($warga->status ?? null) !== 'active') {
            return null;
        }

        if (!$this->passwordMatches($password, (string) $warga->password)) {
            return null;
        }

        return $warga;
    }

    public function loginMaba(string $nrp, string $password): ?Maba
    {
        $maba = Maba::where('nrp', $nrp)->first();

        if (!$maba) {
            return null;
        }

        if (($maba->status ?? null) !== 'active') {
            return null;
        }

        if (!$this->passwordMatches($password, (string) $maba->password)) {
            return null;
        }

        return $maba;
    }

    private function passwordMatches(string $plainPassword, string $storedPassword): bool
    {
        if ($storedPassword === '') {
            return false;
        }

        /*
        * Untuk data dummy lama, password bisa masih plaintext.
        * Jangan langsung Hash::check(), karena Laravel bisa throw error
        * jika storedPassword bukan hash Bcrypt/Argon yang valid.
        */
        if (
            str_starts_with($storedPassword, '$2y$') ||
            str_starts_with($storedPassword, '$2a$') ||
            str_starts_with($storedPassword, '$2b$') ||
            str_starts_with($storedPassword, '$argon2i$') ||
            str_starts_with($storedPassword, '$argon2id$')
        ) {
            return Hash::check($plainPassword, $storedPassword);
        }

        /*
        * Fallback khusus demo:
        * password plaintext di database dibandingkan langsung.
        */
        return hash_equals($storedPassword, $plainPassword);
    }
}
