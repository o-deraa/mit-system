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
        return DB::transaction(function () use ($data) {
            $representativeId = (int) $data['warga_id'];
            $memberIds = array_map('intval', $data['member_ids'] ?? []);

            $allMemberIds = array_values(array_unique(array_merge([$representativeId], $memberIds)));

            if (count($allMemberIds) < 2) {
                throw new RuntimeException('Kelompok warga minimal berisi 2 warga termasuk perwakilan.');
            }

            if (count($allMemberIds) > 4) {
                throw new RuntimeException('Kelompok warga maksimal berisi 4 warga.');
            }

            $activeCount = Warga::whereIn('warga_id', $allMemberIds)
                ->where('status', 'active')
                ->count();

            if ($activeCount !== count($allMemberIds)) {
                throw new RuntimeException('Semua anggota kelompok harus warga active.');
            }

            $alreadyJoined = KelompokWargaMember::whereIn('warga_id', $allMemberIds)->exists();

            if ($alreadyJoined) {
                throw new RuntimeException('Salah satu warga sudah tergabung dalam kelompok lain.');
            }

            $nextKode = ((int) KelompokWarga::max('kode_kelompok')) + 1;

            $group = KelompokWarga::create([
                'kode_kelompok' => $nextKode,
                'warga_id' => $representativeId,
                'nomor_wa_perwakilan' => $data['nomor_wa_perwakilan'],
                'rules' => $data['rules'] ?? null,
                'status' => $data['status'] ?? 'final',
            ]);

            foreach ($allMemberIds as $wargaId) {
                KelompokWargaMember::create([
                    'kelompok_warga_id' => $group->kelompok_warga_id,
                    'warga_id' => $wargaId,
                ]);
            }

            return $group;
        });
    }

    public function update(int $kelompokWargaId, array $data): KelompokWarga
    {
        $group = KelompokWarga::findOrFail($kelompokWargaId);

        $group->update([
            'nomor_wa_perwakilan' => $data['nomor_wa_perwakilan'],
            'rules' => $data['rules'] ?? null,
            'status' => $data['status'],
        ]);

        return $group;
    }

    public function addMember(int $kelompokWargaId, int $wargaId): void
    {
        DB::transaction(function () use ($kelompokWargaId, $wargaId) {
            $group = KelompokWarga::with('members')->findOrFail($kelompokWargaId);
            $warga = Warga::findOrFail($wargaId);

            if ($warga->status !== 'active') {
                throw new RuntimeException('Warga harus active.');
            }

            if ($group->members()->count() >= 4) {
                throw new RuntimeException('Kelompok sudah mencapai maksimal 4 anggota.');
            }

            if (KelompokWargaMember::where('warga_id', $wargaId)->exists()) {
                throw new RuntimeException('Warga sudah tergabung dalam kelompok lain.');
            }

            KelompokWargaMember::create([
                'kelompok_warga_id' => $kelompokWargaId,
                'warga_id' => $wargaId,
            ]);
        });
    }

    public function removeMember(int $memberId): void
    {
        DB::transaction(function () use ($memberId) {
            $member = KelompokWargaMember::with('group')->findOrFail($memberId);
            $group = $member->group;

            if ((int) $group->warga_id === (int) $member->warga_id) {
                throw new RuntimeException('Perwakilan kelompok tidak boleh langsung dihapus dari anggota.');
            }

            if ($group->members()->count() <= 2) {
                throw new RuntimeException('Kelompok warga minimal harus memiliki 2 anggota.');
            }

            $member->delete();
        });
    }

    public function deleteIfSafe(int $kelompokWargaId): void
    {
        $used = DB::table('weekly_availability')->where('kelompok_warga_id', $kelompokWargaId)->exists()
            || DB::table('booking')->where('kelompok_warga_id', $kelompokWargaId)->exists()
            || DB::table('maba_kelompok_history')->where('kelompok_warga_id', $kelompokWargaId)->exists();

        if ($used) {
            throw new RuntimeException('Kelompok tidak bisa dihapus karena sudah dipakai pada availability/booking/riwayat.');
        }

        KelompokWarga::findOrFail($kelompokWargaId)->delete();
    }
}
