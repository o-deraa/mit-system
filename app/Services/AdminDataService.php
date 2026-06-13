<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Models\KelompokWarga;
use App\Models\KelompokWargaMember;
use App\Models\Maba;
use App\Models\MitWeek;
use App\Models\Warga;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AdminDataService
{
    public function __construct(private MongoLogService $mongoLogService) {}

    public function createMaba(string $adminIdentifier, string $nama, string $nrp): Maba
    {
        $nama = trim($nama);
        $nrp = trim($nrp);

        if ($nama === '' || $nrp === '') {
            throw new RuntimeException('Nama dan NRP maba wajib diisi.');
        }

        if (Maba::where('nrp', $nrp)->exists()) {
            throw new RuntimeException('NRP maba sudah terdaftar.');
        }

        $maba = Maba::create([
            'nama' => $nama,
            'nrp' => $nrp,
            'password' => $nrp,
            'status' => 'active',
        ]);

        $this->mongoLogService->activity(null, 'admin', 'create_maba', 'Admin menambahkan data maba.', [
            'admin' => $adminIdentifier,
            'maba_id' => $maba->maba_id,
        ]);

        return $maba;
    }

    public function updateMaba(string $adminIdentifier, int $mabaId, string $nama, string $nrp, string $status): Maba
    {
        $nama = trim($nama);
        $nrp = trim($nrp);

        if ($nama === '' || $nrp === '') {
            throw new RuntimeException('Nama dan NRP maba wajib diisi.');
        }

        if (!in_array($status, ['active', 'inactive'], true)) {
            throw new RuntimeException('Status maba tidak valid.');
        }

        $duplicate = Maba::where('nrp', $nrp)
            ->where('maba_id', '!=', $mabaId)
            ->exists();

        if ($duplicate) {
            throw new RuntimeException('NRP maba sudah digunakan oleh data lain.');
        }

        $maba = Maba::findOrFail($mabaId);
        $maba->update([
            'nama' => $nama,
            'nrp' => $nrp,
            'status' => $status,
        ]);

        $this->mongoLogService->activity(null, 'admin', 'update_maba', 'Admin mengubah data maba.', [
            'admin' => $adminIdentifier,
            'maba_id' => $maba->maba_id,
        ]);

        return $maba;
    }

    public function deactivateMaba(string $adminIdentifier, int $mabaId): void
    {
        $maba = Maba::findOrFail($mabaId);
        $maba->update(['status' => 'inactive']);

        $this->mongoLogService->activity(null, 'admin', 'deactivate_maba', 'Admin menonaktifkan maba.', [
            'admin' => $adminIdentifier,
            'maba_id' => $mabaId,
        ]);
    }

    public function deleteMabaIfUnused(string $adminIdentifier, int $mabaId): void
    {
        $used = Booking::where('created_by_maba_id', $mabaId)->exists()
            || BookingParticipant::where('maba_id', $mabaId)->exists();

        if ($used) {
            throw new RuntimeException('Maba sudah punya relasi booking/participant. Gunakan nonaktifkan, bukan hapus fisik.');
        }

        Maba::findOrFail($mabaId)->delete();

        $this->mongoLogService->activity(null, 'admin', 'delete_maba', 'Admin menghapus data maba yang belum terpakai.', [
            'admin' => $adminIdentifier,
            'maba_id' => $mabaId,
        ]);
    }

    public function createWarga(string $adminIdentifier, string $nama, string $nrp, int $angkatan): Warga
    {
        $nama = trim($nama);
        $nrp = trim($nrp);

        if ($nama === '' || $nrp === '') {
            throw new RuntimeException('Nama dan NRP warga wajib diisi.');
        }

        if (!in_array($angkatan, [2022, 2023, 2024], true)) {
            throw new RuntimeException('Angkatan warga harus 2022, 2023, atau 2024.');
        }

        if (Warga::where('nrp', $nrp)->exists()) {
            throw new RuntimeException('NRP warga sudah terdaftar.');
        }

        $warga = Warga::create([
            'nama' => $nama,
            'nrp' => $nrp,
            'angkatan' => $angkatan,
            'password' => $nrp,
            'status' => 'active',
        ]);

        $this->mongoLogService->activity(null, 'admin', 'create_warga', 'Admin menambahkan data warga.', [
            'admin' => $adminIdentifier,
            'warga_id' => $warga->warga_id,
        ]);

        return $warga;
    }

    public function updateWarga(string $adminIdentifier, int $wargaId, string $nama, string $nrp, int $angkatan, string $status): Warga
    {
        $nama = trim($nama);
        $nrp = trim($nrp);

        if ($nama === '' || $nrp === '') {
            throw new RuntimeException('Nama dan NRP warga wajib diisi.');
        }

        if (!in_array($angkatan, [2022, 2023, 2024], true)) {
            throw new RuntimeException('Angkatan warga harus 2022, 2023, atau 2024.');
        }

        if (!in_array($status, ['active', 'inactive'], true)) {
            throw new RuntimeException('Status warga tidak valid.');
        }

        $duplicate = Warga::where('nrp', $nrp)
            ->where('warga_id', '!=', $wargaId)
            ->exists();

        if ($duplicate) {
            throw new RuntimeException('NRP warga sudah digunakan oleh data lain.');
        }

        $warga = Warga::findOrFail($wargaId);
        $warga->update([
            'nama' => $nama,
            'nrp' => $nrp,
            'angkatan' => $angkatan,
            'status' => $status,
        ]);

        $this->mongoLogService->activity(null, 'admin', 'update_warga', 'Admin mengubah data warga.', [
            'admin' => $adminIdentifier,
            'warga_id' => $wargaId,
        ]);

        return $warga;
    }

    public function deactivateWarga(string $adminIdentifier, int $wargaId): void
    {
        Warga::findOrFail($wargaId)->update(['status' => 'inactive']);

        $this->mongoLogService->activity(null, 'admin', 'deactivate_warga', 'Admin menonaktifkan warga.', [
            'admin' => $adminIdentifier,
            'warga_id' => $wargaId,
        ]);
    }

    public function deleteWargaIfUnused(string $adminIdentifier, int $wargaId): void
    {
        $used = KelompokWarga::where('warga_id', $wargaId)->exists()
            || KelompokWargaMember::where('warga_id', $wargaId)->exists();

        if ($used) {
            throw new RuntimeException('Warga sudah masuk kelompok/perwakilan. Gunakan nonaktifkan, bukan hapus fisik.');
        }

        Warga::findOrFail($wargaId)->delete();

        $this->mongoLogService->activity(null, 'admin', 'delete_warga', 'Admin menghapus data warga yang belum terpakai.', [
            'admin' => $adminIdentifier,
            'warga_id' => $wargaId,
        ]);
    }

    public function formKelompokWarga(string $adminIdentifier, int $representativeWargaId, string $nomorWa, ?string $rules, string $status = 'final'): KelompokWarga
    {
        return DB::transaction(function () use ($adminIdentifier, $representativeWargaId, $nomorWa, $rules, $status) {
            $nomorWa = trim($nomorWa);

            if (!in_array($status, ['draft', 'final'], true)) {
                throw new RuntimeException('Status kelompok tidak valid.');
            }

            if ($nomorWa === '') {
                throw new RuntimeException('Nomor WA perwakilan wajib diisi.');
            }

            $representative = Warga::where('status', 'active')->findOrFail($representativeWargaId);

            if (KelompokWargaMember::where('warga_id', $representative->warga_id)->exists()) {
                throw new RuntimeException('Warga perwakilan sudah menjadi anggota kelompok lain.');
            }

            $nextCode = ((int) KelompokWarga::max('kode_kelompok')) + 1;

            $group = KelompokWarga::create([
                'kode_kelompok' => $nextCode,
                'warga_id' => $representative->warga_id,
                'nomor_wa_perwakilan' => $nomorWa,
                'rules' => $rules,
                'status' => $status,
            ]);

            KelompokWargaMember::create([
                'kelompok_warga_id' => $group->kelompok_warga_id,
                'warga_id' => $representative->warga_id,
            ]);

            $this->mongoLogService->activity(null, 'admin', 'form_kelompok_warga', 'Admin membentuk kelompok warga.', [
                'admin' => $adminIdentifier,
                'kelompok_warga_id' => $group->kelompok_warga_id,
                'kode_kelompok' => $group->kode_kelompok,
                'representative_warga_id' => $representative->warga_id,
            ]);

            return $group;
        });
    }

    public function addGroupMember(string $adminIdentifier, int $kelompokWargaId, int $wargaId): void
    {
        DB::transaction(function () use ($adminIdentifier, $kelompokWargaId, $wargaId) {
            $group = KelompokWarga::lockForUpdate()->findOrFail($kelompokWargaId);
            $warga = Warga::where('status', 'active')->findOrFail($wargaId);

            $memberCount = KelompokWargaMember::where('kelompok_warga_id', $group->kelompok_warga_id)->count();
            if ($memberCount >= 4) {
                throw new RuntimeException('Kelompok warga maksimal 4 anggota.');
            }

            if (KelompokWargaMember::where('warga_id', $warga->warga_id)->exists()) {
                throw new RuntimeException('Warga sudah menjadi anggota kelompok lain.');
            }

            KelompokWargaMember::create([
                'kelompok_warga_id' => $group->kelompok_warga_id,
                'warga_id' => $warga->warga_id,
            ]);

            $this->mongoLogService->activity(null, 'admin', 'add_group_member', 'Admin menambahkan anggota kelompok warga.', [
                'admin' => $adminIdentifier,
                'kelompok_warga_id' => $group->kelompok_warga_id,
                'warga_id' => $warga->warga_id,
            ]);
        });
    }

    public function removeGroupMember(string $adminIdentifier, int $memberId): void
    {
        DB::transaction(function () use ($adminIdentifier, $memberId) {
            $member = KelompokWargaMember::lockForUpdate()->findOrFail($memberId);
            $group = KelompokWarga::lockForUpdate()->findOrFail($member->kelompok_warga_id);

            if ((int) $group->warga_id === (int) $member->warga_id) {
                throw new RuntimeException('Perwakilan tidak boleh langsung dihapus dari anggota. Ubah perwakilan terlebih dahulu.');
            }

            $activeBookingExists = Booking::where('kelompok_warga_id', $group->kelompok_warga_id)
                ->whereIn('status', ['pending', 'accepted'])
                ->exists();

            if ($activeBookingExists) {
                throw new RuntimeException('Kelompok masih memiliki booking aktif. Jangan ubah anggota sampai queue aktif selesai.');
            }

            $oldWargaId = $member->warga_id;
            $member->delete();

            $this->mongoLogService->activity(null, 'admin', 'remove_group_member', 'Admin mengurangi anggota kelompok warga.', [
                'admin' => $adminIdentifier,
                'kelompok_warga_id' => $group->kelompok_warga_id,
                'removed_warga_id' => $oldWargaId,
            ]);
        });
    }

    public function createMitWeek(string $adminIdentifier, int $weekNumber, string $startDate, string $endDate): MitWeek
    {
        if (MitWeek::where('week_number', $weekNumber)->exists()) {
            throw new RuntimeException('Nomor minggu MIT sudah ada.');
        }

        $week = MitWeek::create([
            'week_number' => $weekNumber,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'upcoming',
            'availability_input_status' => 'closed',
        ]);

        $this->mongoLogService->activity(null, 'admin', 'create_mit_week', 'Admin menambahkan minggu MIT.', [
            'admin' => $adminIdentifier,
            'week_id' => $week->week_id,
            'week_number' => $week->week_number,
        ]);

        return $week;
    }

    public function startMitWeek(string $adminIdentifier, int $weekId): MitWeek
    {
        return DB::transaction(function () use ($adminIdentifier, $weekId) {
            $week = MitWeek::lockForUpdate()->findOrFail($weekId);

            if ($week->status === 'completed') {
                throw new RuntimeException('Minggu MIT yang sudah completed tidak boleh di-start ulang pada alur normal.');
            }

            MitWeek::where('status', 'active')->update([
                'status' => 'completed',
                'availability_input_status' => 'closed',
            ]);

            $week->update([
                'status' => 'active',
                'availability_input_status' => 'open',
            ]);

            $this->mongoLogService->activity(null, 'admin', 'start_mit_week', 'Admin memulai MIT week.', [
                'admin' => $adminIdentifier,
                'week_id' => $week->week_id,
                'week_number' => $week->week_number,
            ]);

            return $week;
        });
    }

    public function closeActiveMitWeek(string $adminIdentifier): void
    {
        $week = MitWeek::where('status', 'active')->first();
        if (!$week) {
            throw new RuntimeException('Tidak ada minggu MIT yang sedang active.');
        }

        $week->update([
            'status' => 'completed',
            'availability_input_status' => 'closed',
        ]);

        $this->mongoLogService->activity(null, 'admin', 'close_mit_week', 'Admin menutup MIT week active.', [
            'admin' => $adminIdentifier,
            'week_id' => $week->week_id,
            'week_number' => $week->week_number,
        ]);
    }
}