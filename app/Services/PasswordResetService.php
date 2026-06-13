<?php

namespace App\Services;

use App\Models\Maba;
use App\Models\PasswordResetRequest;
use App\Models\Warga;
use RuntimeException;

class PasswordResetService
{
    public function request(string $type, string $nrp, string $newPassword): PasswordResetRequest
    {
        if (!in_array($type, ['maba', 'warga'], true)) {
            throw new RuntimeException('Requester type tidak valid.');
        }

        $model = $type === 'maba'
            ? Maba::where('nrp', $nrp)->firstOrFail()
            : Warga::where('nrp', $nrp)->firstOrFail();

        return PasswordResetRequest::create([
            'requester_type' => $type,
            'requester_id' => $type === 'maba' ? $model->maba_id : $model->warga_id,
            'nrp' => $nrp,
            'new_password' => $newPassword,
            'status' => 'pending',
            'requested_at' => now(),
        ]);
    }

    public function process(int $resetId, string $status, string $adminIdentifier, ?string $notes = null): void
    {
        if (!in_array($status, ['approved', 'rejected'], true)) {
            throw new RuntimeException('Status reset password tidak valid.');
        }

        $request = PasswordResetRequest::findOrFail($resetId);
        $request->update([
            'status' => $status,
            'admin_notes' => $notes,
            'processed_at' => now(),
            'processed_by_admin_identifier' => $adminIdentifier,
        ]);

        if ($status === 'approved') {
            if ($request->requester_type === 'maba') {
                Maba::where('maba_id', $request->requester_id)->update(['password' => $request->new_password]);
            } else {
                Warga::where('warga_id', $request->requester_id)->update(['password' => $request->new_password]);
            }
        }
    }
}
