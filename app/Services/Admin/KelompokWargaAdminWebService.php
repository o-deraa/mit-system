<?php

namespace App\Services\Admin;

use App\Models\KelompokWarga;
use App\Models\KelompokWargaMember;
use App\Models\Warga;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class KelompokWargaAdminWebService
{
    public function create(array $data): KelompokWarga
    {
        return KelompokWarga::create([
            'kode_kelompok' => $data['kode_kelompok'],
            'rules' => $data['rules'] ?? null,
            'status' => 'draft',
        ]);
    }

    public function update(KelompokWarga $group, array $data): KelompokWarga
    {
        $targetStatus = $data['status'] ?? $group->status;

        $group->update([
            'kode_kelompok' => $data['kode_kelompok'],
            'rules' => $data['rules'] ?? null,
        ]);

        if ($targetStatus === 'final') {
            $this->finalize($group->refresh());
        } else {
            $group->update(['status' => 'draft']);
        }

        return $group->refresh();
    }

    public function addMember(KelompokWarga $group, array $data): KelompokWargaMember
    {
        return DB::transaction(function () use ($group, $data) {
            $wargaId = (int) $data['warga_id'];
            $isPerwakilan = (bool) ($data['is_perwakilan'] ?? false);
            $nomorWa = $data['nomor_wa'] ?? null;

            $warga = Warga::findOrFail($wargaId);

            if ($warga->status !== 'active') {
                throw new RuntimeException('Warga inactive tidak bisa dimasukkan ke kelompok.');
            }

            $alreadyInAnyGroup = KelompokWargaMember::where('warga_id', $wargaId)->exists();

            if ($alreadyInAnyGroup) {
                throw new RuntimeException('Warga ini sudah tergabung dalam kelompok lain.');
            }

            $memberCount = KelompokWargaMember::where('kelompok_warga_id', $group->kelompok_warga_id)->count();

            if ($memberCount >= 4) {
                throw new RuntimeException('Maksimal anggota kelompok warga adalah 4.');
            }

            if ($isPerwakilan) {
                if (trim((string) $nomorWa) === '') {
                    throw new RuntimeException('Nomor WA wajib diisi untuk perwakilan.');
                }

                KelompokWargaMember::where('kelompok_warga_id', $group->kelompok_warga_id)
                    ->update([
                        'is_perwakilan' => false,
                        'nomor_wa' => null,
                    ]);
            } else {
                $nomorWa = null;
            }

            return KelompokWargaMember::create([
                'kelompok_warga_id' => $group->kelompok_warga_id,
                'warga_id' => $wargaId,
                'is_perwakilan' => $isPerwakilan,
                'nomor_wa' => $nomorWa,
            ]);
        });
    }

    public function removeMember(KelompokWargaMember $member): void
    {
        DB::transaction(function () use ($member) {
            $groupId = $member->kelompok_warga_id;

            if ($member->is_perwakilan) {
                $member->delete();

                $remainingCount = KelompokWargaMember::where('kelompok_warga_id', $groupId)->count();

                if ($remainingCount > 0) {
                    throw new RuntimeException('Tidak bisa menghapus perwakilan sebelum menunjuk perwakilan baru.');
                }

                return;
            }

            $member->delete();
        });
    }

    public function setRepresentative(KelompokWargaMember $member, string $nomorWa): void
    {
        DB::transaction(function () use ($member, $nomorWa) {
            if (trim($nomorWa) === '') {
                throw new RuntimeException('Nomor WA wajib diisi untuk perwakilan.');
            }

            KelompokWargaMember::where('kelompok_warga_id', $member->kelompok_warga_id)
                ->update([
                    'is_perwakilan' => false,
                    'nomor_wa' => null,
                ]);

            $member->update([
                'is_perwakilan' => true,
                'nomor_wa' => $nomorWa,
            ]);
        });
    }

    public function finalize(KelompokWarga $group): void
    {
        $memberCount = KelompokWargaMember::where('kelompok_warga_id', $group->kelompok_warga_id)->count();

        if ($memberCount < 2 || $memberCount > 4) {
            throw new RuntimeException('Kelompok final harus memiliki 2 sampai 4 anggota.');
        }

        $representativeCount = KelompokWargaMember::where('kelompok_warga_id', $group->kelompok_warga_id)
            ->where('is_perwakilan', true)
            ->count();

        if ($representativeCount !== 1) {
            throw new RuntimeException('Kelompok final wajib memiliki tepat 1 perwakilan.');
        }

        $representative = KelompokWargaMember::where('kelompok_warga_id', $group->kelompok_warga_id)
            ->where('is_perwakilan', true)
            ->first();

        if (!$representative || trim((string) $representative->nomor_wa) === '') {
            throw new RuntimeException('Perwakilan wajib memiliki nomor WA.');
        }

        $group->update([
            'status' => 'final',
        ]);
    }
}
