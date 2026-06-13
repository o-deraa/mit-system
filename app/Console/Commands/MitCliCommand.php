<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\KelompokWarga;
use App\Models\KelompokWargaMember;
use App\Models\Maba;
use App\Models\MitWeek;
use App\Models\Mongo\ActivityLog;
use App\Models\Mongo\RecommendationLog;
use App\Models\Mongo\RevisionHistory;
use App\Models\PasswordResetRequest;
use App\Models\Realisasi;
use App\Models\Warga;
use App\Services\AdminDataService;
use App\Services\AuthCliService;
use App\Services\BookingQueryService;
use App\Services\BookingService;
use App\Services\PasswordResetService;
use App\Services\RecommendationService;
use App\Services\RealisasiService;
use App\Services\TtdService;
use App\Services\VerificationService;
use Illuminate\Console\Command;
use Throwable;

class MitCliCommand extends Command
{
    protected $signature = 'mit:cli';
    protected $description = 'MIT Management System CLI';

    public function handle(
        AuthCliService $auth,
        AdminDataService $adminDataService,
        BookingQueryService $bookingQueryService,
        BookingService $bookingService,
        TtdService $ttdService,
        RealisasiService $realisasiService,
        VerificationService $verificationService,
        RecommendationService $recommendationService,
        PasswordResetService $passwordResetService
    ): int {
        while (true) {
            $choice = $this->askMenu('MIT MANAGEMENT SYSTEM', [
                1 => 'Login',
                2 => 'Lihat Informasi Sistem',
                0 => 'Keluar',
            ]);

            if ($choice === 0) {
                $this->info('Program selesai.');
                return self::SUCCESS;
            }

            $this->clearScreen();

            if ($choice === 2) {
                $this->headerBox('INFORMASI SISTEM MIT');
                $this->line('Sistem CLI untuk booking MIT, realisasi, verifikasi TTD, progress, dan log MongoDB.');
                $this->pauseToSameMenu('menu utama');
                continue;
            }

            $role = $this->askMenu('LOGIN', [
                1 => 'Admin',
                2 => 'Warga',
                3 => 'Maba',
                0 => 'Kembali',
            ]);

            if ($role === 0) {
                continue;
            }

            $this->clearScreen();

            if ($role === 1) {
                $username = $this->askRequiredString('Username admin');
                if ($username === null) {
                    continue;
                }

                $password = (string) $this->secret('Password admin');
                $admin = $auth->loginAdmin($username, $password);

                if (!$admin) {
                    $this->error('Login admin gagal.');
                    $this->pauseToSameMenu('menu login');
                    continue;
                }

                $this->adminMenu($admin, $adminDataService, $verificationService, $passwordResetService);
                continue;
            }

            if ($role === 2) {
                $nrp = $this->askRequiredString('NRP Warga');
                if ($nrp === null) {
                    continue;
                }

                $password = (string) $this->secret('Password');
                $warga = $auth->loginWarga($nrp, $password);

                if (!$warga) {
                    $this->error('Login warga gagal.');
                    $this->pauseToSameMenu('menu login');
                    continue;
                }

                $this->wargaMenu($warga, $bookingService);
                continue;
            }

            if ($role === 3) {
                $nrp = $this->askRequiredString('NRP Maba');
                if ($nrp === null) {
                    continue;
                }

                $password = (string) $this->secret('Password');
                $maba = $auth->loginMaba($nrp, $password);

                if (!$maba) {
                    $this->error('Login maba gagal.');
                    $this->pauseToSameMenu('menu login');
                    continue;
                }

                $this->mabaMenu(
                    $maba,
                    $bookingQueryService,
                    $bookingService,
                    $ttdService,
                    $realisasiService,
                    $recommendationService
                );
            }
        }
    }

    private function adminMenu(
        array $admin,
        AdminDataService $adminDataService,
        VerificationService $verificationService,
        PasswordResetService $passwordResetService
    ): void {
        while (true) {
            $choice = $this->askMenu('MENU ADMIN', [
                1 => 'Manajemen Maba',
                2 => 'Manajemen Warga',
                3 => 'Bentuk Kelompok Warga',
                4 => 'Manajemen Kelompok Warga',
                5 => 'Monitoring Kelompok Warga',
                6 => 'Manajemen Minggu MIT',
                7 => 'Monitoring Booking',
                8 => 'Monitoring Realisasi',
                9 => 'Verifikasi Tanda Tangan',
                10 => 'Lihat Log MongoDB',
                11 => 'Proses Reset Password',
                0 => 'Logout',
            ]);

            if ($choice === 0) {
                return;
            }

            $this->clearScreen();

            try {
                match ($choice) {
                    1 => $this->adminMabaMenu($admin, $adminDataService),
                    2 => $this->adminWargaMenu($admin, $adminDataService),
                    3 => $this->formKelompokWargaCli($admin, $adminDataService),
                    4 => $this->adminGroupMenu($admin, $adminDataService),
                    5 => $this->showGroupsWithMembers(),
                    6 => $this->adminWeekMenu($admin, $adminDataService),
                    7 => $this->showAllBookings(),
                    8 => $this->showAllRealisasi(),
                    9 => $this->verifyCli($admin, $verificationService),
                    10 => $this->showMongoLogs(),
                    11 => $this->processResetPasswordCli($admin, $passwordResetService),
                };
            } catch (Throwable $e) {
                $this->error($e->getMessage());
            }

            // Untuk opsi langsung dari MENU ADMIN, Enter dan 0 sama-sama kembali ke MENU ADMIN.
            if (in_array($choice, [3, 5, 7, 8, 10, 11], true)) {
                $this->pauseToSameMenu('menu admin');
            }
        }
    }

    private function adminMabaMenu(array $admin, AdminDataService $service): void
    {
        while (true) {
            $choice = $this->askMenu('MANAJEMEN MABA', [
                1 => 'Tambah Maba',
                2 => 'Lihat Semua Maba',
                3 => 'Ubah Maba',
                4 => 'Nonaktifkan Maba',
                5 => 'Hapus Maba Jika Belum Terpakai',
                0 => 'Kembali',
            ]);

            if ($choice === 0) {
                return;
            }

            $this->clearScreen();

            try {
                match ($choice) {
                    1 => $this->createMabaCli($admin, $service),
                    2 => $this->showAllMabaCli(),
                    3 => $this->updateMabaCli($admin, $service),
                    4 => $this->deactivateMabaCli($admin, $service),
                    5 => $this->deleteMabaCli($admin, $service),
                };
            } catch (Throwable $e) {
                $this->error($e->getMessage());
            }

            if ($this->pauseToMainOrPrevious('menu admin', 'menu manajemen maba')) {
                return;
            }
        }
    }

    private function createMabaCli(array $admin, AdminDataService $service): void
    {
        $this->headerBox('TAMBAH MABA');

        $nama = $this->askRequiredString('Nama, atau 0 untuk kembali');
        if ($nama === null) {
            return;
        }

        $nrp = $this->askRequiredString('NRP, atau 0 untuk kembali');
        if ($nrp === null) {
            return;
        }

        $maba = $service->createMaba($admin['identifier'], $nama, $nrp);

        $this->info("Maba ditambahkan. ID: {$maba->maba_id}. Password default mengikuti NRP.");
    }

    private function showAllMabaCli(): void
    {
        $this->headerBox('DAFTAR MABA');

        $rows = Maba::orderBy('maba_id')
            ->get()
            ->map(fn ($m) => [$m->maba_id, $m->nama, $m->nrp, $m->status])
            ->toArray();

        if (count($rows) === 0) {
            $this->warn('Belum ada data maba.');
            return;
        }

        $this->table(['ID', 'Nama', 'NRP', 'Status'], $rows);
    }

    private function updateMabaCli(array $admin, AdminDataService $service): void
    {
        $this->headerBox('UBAH MABA');

        $id = $this->askRequiredInt('Maba ID, atau 0 untuk kembali');
        if ($id === null) {
            return;
        }

        $maba = Maba::findOrFail($id);

        $nama = $this->askOptionalString("Nama baru [{$maba->nama}], atau 0 untuk kembali");
        if ($nama === null) {
            return;
        }

        $nrp = $this->askOptionalString("NRP baru [{$maba->nrp}], atau 0 untuk kembali");
        if ($nrp === null) {
            return;
        }

        $statusChoice = $this->askMenu('STATUS MABA', [
            1 => 'active',
            2 => 'inactive',
            0 => 'Kembali',
        ]);

        if ($statusChoice === 0) {
            return;
        }

        $status = $statusChoice === 1 ? 'active' : 'inactive';

        $service->updateMaba(
            $admin['identifier'],
            $id,
            $nama === '' ? $maba->nama : $nama,
            $nrp === '' ? $maba->nrp : $nrp,
            $status
        );

        $this->info('Data maba berhasil diubah.');
    }

    private function deactivateMabaCli(array $admin, AdminDataService $service): void
    {
        $this->headerBox('NONAKTIFKAN MABA');

        $id = $this->askRequiredInt('Maba ID, atau 0 untuk kembali');
        if ($id === null) {
            return;
        }

        $service->deactivateMaba($admin['identifier'], $id);
        $this->info('Maba berhasil dinonaktifkan.');
    }

    private function deleteMabaCli(array $admin, AdminDataService $service): void
    {
        $this->headerBox('HAPUS MABA JIKA BELUM TERPAKAI');

        $id = $this->askRequiredInt('Maba ID, atau 0 untuk kembali');
        if ($id === null) {
            return;
        }

        $service->deleteMabaIfUnused($admin['identifier'], $id);
        $this->info('Maba berhasil dihapus.');
    }

    private function adminWargaMenu(array $admin, AdminDataService $service): void
    {
        while (true) {
            $choice = $this->askMenu('MANAJEMEN WARGA', [
                1 => 'Tambah Warga',
                2 => 'Lihat Semua Warga',
                3 => 'Ubah Warga',
                4 => 'Nonaktifkan Warga',
                5 => 'Hapus Warga Jika Belum Terpakai',
                0 => 'Kembali',
            ]);

            if ($choice === 0) {
                return;
            }

            $this->clearScreen();

            try {
                match ($choice) {
                    1 => $this->createWargaCli($admin, $service),
                    2 => $this->showAllWargaCli(),
                    3 => $this->updateWargaCli($admin, $service),
                    4 => $this->deactivateWargaCli($admin, $service),
                    5 => $this->deleteWargaCli($admin, $service),
                };
            } catch (Throwable $e) {
                $this->error($e->getMessage());
            }

            if ($this->pauseToMainOrPrevious('menu admin', 'menu manajemen warga')) {
                return;
            }
        }
    }

    private function createWargaCli(array $admin, AdminDataService $service): void
    {
        $this->headerBox('TAMBAH WARGA');

        $nama = $this->askRequiredString('Nama, atau 0 untuk kembali');
        if ($nama === null) {
            return;
        }

        $nrp = $this->askRequiredString('NRP, atau 0 untuk kembali');
        if ($nrp === null) {
            return;
        }

        $angkatanChoice = $this->askMenu('ANGKATAN WARGA', [
            1 => '2022',
            2 => '2023',
            3 => '2024',
            0 => 'Kembali',
        ]);

        if ($angkatanChoice === 0) {
            return;
        }

        $angkatan = match ($angkatanChoice) {
            1 => 2022,
            2 => 2023,
            3 => 2024,
        };

        $warga = $service->createWarga($admin['identifier'], $nama, $nrp, $angkatan);

        $this->info("Warga ditambahkan. ID: {$warga->warga_id}. Password default mengikuti NRP.");
    }

    private function showAllWargaCli(): void
    {
        $this->headerBox('DAFTAR WARGA');

        $rows = Warga::orderBy('angkatan')
            ->orderBy('nama')
            ->get()
            ->map(fn ($w) => [$w->warga_id, $w->nama, $w->nrp, $w->angkatan, $w->status])
            ->toArray();

        if (count($rows) === 0) {
            $this->warn('Belum ada data warga.');
            return;
        }

        $this->table(['ID', 'Nama', 'NRP', 'Angkatan', 'Status'], $rows);
    }

    private function updateWargaCli(array $admin, AdminDataService $service): void
    {
        $this->headerBox('UBAH WARGA');

        $id = $this->askRequiredInt('Warga ID, atau 0 untuk kembali');
        if ($id === null) {
            return;
        }

        $warga = Warga::findOrFail($id);

        $nama = $this->askOptionalString("Nama baru [{$warga->nama}], atau 0 untuk kembali");
        if ($nama === null) {
            return;
        }

        $nrp = $this->askOptionalString("NRP baru [{$warga->nrp}], atau 0 untuk kembali");
        if ($nrp === null) {
            return;
        }

        $angkatanChoice = $this->askMenu('ANGKATAN WARGA', [
            1 => '2022',
            2 => '2023',
            3 => '2024',
            0 => 'Kembali',
        ]);

        if ($angkatanChoice === 0) {
            return;
        }

        $statusChoice = $this->askMenu('STATUS WARGA', [
            1 => 'active',
            2 => 'inactive',
            0 => 'Kembali',
        ]);

        if ($statusChoice === 0) {
            return;
        }

        $angkatan = match ($angkatanChoice) {
            1 => 2022,
            2 => 2023,
            3 => 2024,
        };

        $status = $statusChoice === 1 ? 'active' : 'inactive';

        $service->updateWarga(
            $admin['identifier'],
            $id,
            $nama === '' ? $warga->nama : $nama,
            $nrp === '' ? $warga->nrp : $nrp,
            $angkatan,
            $status
        );

        $this->info('Data warga berhasil diubah.');
    }

    private function deactivateWargaCli(array $admin, AdminDataService $service): void
    {
        $this->headerBox('NONAKTIFKAN WARGA');

        $id = $this->askRequiredInt('Warga ID, atau 0 untuk kembali');
        if ($id === null) {
            return;
        }

        $service->deactivateWarga($admin['identifier'], $id);
        $this->info('Warga berhasil dinonaktifkan.');
    }

    private function deleteWargaCli(array $admin, AdminDataService $service): void
    {
        $this->headerBox('HAPUS WARGA JIKA BELUM TERPAKAI');

        $id = $this->askRequiredInt('Warga ID, atau 0 untuk kembali');
        if ($id === null) {
            return;
        }

        $service->deleteWargaIfUnused($admin['identifier'], $id);
        $this->info('Warga berhasil dihapus.');
    }

    private function formKelompokWargaCli(array $admin, AdminDataService $service): void
    {
        $this->headerBox('BENTUK KELOMPOK WARGA');
        $this->line('Daftar warga active yang masih floating:');

        $availableWarga = Warga::where('status', 'active')
            ->whereDoesntHave('groupMembership')
            ->orderBy('angkatan')
            ->orderBy('nama')
            ->get();

        $this->table(
            ['Warga ID', 'Nama', 'NRP', 'Angkatan'],
            $availableWarga->map(fn ($w) => [$w->warga_id, $w->nama, $w->nrp, $w->angkatan])->toArray()
        );

        if ($availableWarga->isEmpty()) {
            $this->warn('Tidak ada warga active yang masih floating.');
            return;
        }

        $representativeId = $this->askRequiredInt('Warga ID perwakilan, atau 0 untuk kembali');
        if ($representativeId === null) {
            return;
        }

        $nomorWa = $this->askRequiredString('Nomor WA perwakilan, atau 0 untuk kembali');
        if ($nomorWa === null) {
            return;
        }

        $rules = $this->askOptionalString('Rules kelompok, kosongkan jika tidak ada');
        if ($rules === null) {
            return;
        }

        $statusChoice = $this->askMenu('STATUS KELOMPOK', [
            1 => 'draft',
            2 => 'final',
            0 => 'Batal',
        ]);

        if ($statusChoice === 0) {
            $this->warn('Pembentukan kelompok dibatalkan.');
            return;
        }

        $status = $statusChoice === 1 ? 'draft' : 'final';

        $group = $service->formKelompokWarga(
            $admin['identifier'],
            $representativeId,
            $nomorWa,
            $rules,
            $status
        );

        $this->info("Kelompok warga berhasil dibentuk. Kode kelompok: {$group->kode_kelompok}");
        $this->info('Perwakilan otomatis masuk sebagai anggota kelompok.');
    }

    private function adminGroupMenu(array $admin, AdminDataService $service): void
    {
        while (true) {
            $choice = $this->askMenu('MANAJEMEN KELOMPOK WARGA', [
                1 => 'Tambah Anggota Kelompok',
                2 => 'Kurangi Anggota Kelompok',
                3 => 'Lihat Detail Kelompok',
                0 => 'Kembali',
            ]);

            if ($choice === 0) {
                return;
            }

            $this->clearScreen();

            try {
                match ($choice) {
                    1 => $this->addGroupMemberCli($admin, $service),
                    2 => $this->removeGroupMemberCli($admin, $service),
                    3 => $this->showGroupsWithMembers(),
                };
            } catch (Throwable $e) {
                $this->error($e->getMessage());
            }

            if ($this->pauseToMainOrPrevious('menu admin', 'menu manajemen kelompok warga')) {
                return;
            }
        }
    }

    private function addGroupMemberCli(array $admin, AdminDataService $service): void
    {
        $this->headerBox('TAMBAH ANGGOTA KELOMPOK');
        $this->showGroupsWithMembers();

        $kelompokId = $this->askRequiredInt('Kelompok Warga ID, atau 0 untuk kembali');
        if ($kelompokId === null) {
            return;
        }

        $wargaId = $this->askRequiredInt('Warga ID yang akan ditambahkan, atau 0 untuk kembali');
        if ($wargaId === null) {
            return;
        }

        $service->addGroupMember($admin['identifier'], $kelompokId, $wargaId);

        $this->info('Anggota kelompok berhasil ditambahkan.');
    }

    private function removeGroupMemberCli(array $admin, AdminDataService $service): void
    {
        $this->headerBox('KURANGI ANGGOTA KELOMPOK');
        $this->showGroupsWithMembers();

        $memberId = $this->askRequiredInt('Member ID yang akan dikurangi, atau 0 untuk kembali');
        if ($memberId === null) {
            return;
        }

        $service->removeGroupMember($admin['identifier'], $memberId);

        $this->info('Anggota kelompok berhasil dikurangi.');
    }

    private function adminWeekMenu(array $admin, AdminDataService $service): void
    {
        while (true) {
            $choice = $this->askMenu('MANAJEMEN MINGGU MIT', [
                1 => 'Tambah Minggu MIT',
                2 => 'Lihat Semua Minggu MIT',
                3 => 'Start MIT Week',
                4 => 'Tutup MIT Week Active',
                0 => 'Kembali',
            ]);

            if ($choice === 0) {
                return;
            }

            $this->clearScreen();

            try {
                match ($choice) {
                    1 => $this->createMitWeekCli($admin, $service),
                    2 => $this->showAllMitWeeksCli(),
                    3 => $this->startMitWeekCli($admin, $service),
                    4 => $this->closeActiveMitWeekCli($admin, $service),
                };
            } catch (Throwable $e) {
                $this->error($e->getMessage());
            }

            if ($this->pauseToMainOrPrevious('menu admin', 'menu manajemen minggu mit')) {
                return;
            }
        }
    }

    private function createMitWeekCli(array $admin, AdminDataService $service): void
    {
        $this->headerBox('TAMBAH MINGGU MIT');

        $weekNumber = $this->askRequiredInt('Minggu ke, atau 0 untuk kembali');
        if ($weekNumber === null) {
            return;
        }

        $startDate = $this->askRequiredString('Tanggal awal, format YYYY-MM-DD, atau 0 untuk kembali');
        if ($startDate === null) {
            return;
        }

        $endDate = $this->askRequiredString('Tanggal akhir, format YYYY-MM-DD, atau 0 untuk kembali');
        if ($endDate === null) {
            return;
        }

        $week = $service->createMitWeek($admin['identifier'], $weekNumber, $startDate, $endDate);

        $this->info("Minggu MIT berhasil ditambahkan. Week ID: {$week->week_id}");
    }

    private function showAllMitWeeksCli(): void
    {
        $this->headerBox('DAFTAR MINGGU MIT');

        $weeks = MitWeek::orderBy('week_number')->get();

        $this->table(
            ['Week ID', 'Minggu', 'Start', 'End', 'Status', 'Availability Input'],
            $weeks->map(fn ($w) => [
                $w->week_id,
                $w->week_number,
                $w->start_date,
                $w->end_date,
                $w->status,
                $w->availability_input_status,
            ])->toArray()
        );
    }

    private function startMitWeekCli(array $admin, AdminDataService $service): void
    {
        $this->headerBox('START MIT WEEK');
        $this->showAllMitWeeksCli();

        $weekId = $this->askRequiredInt('Week ID yang akan di-start, atau 0 untuk kembali');
        if ($weekId === null) {
            return;
        }

        $week = $service->startMitWeek($admin['identifier'], $weekId);

        $this->info("MIT Week {$week->week_number} berhasil di-start.");
    }

    private function closeActiveMitWeekCli(array $admin, AdminDataService $service): void
    {
        $this->headerBox('TUTUP MIT WEEK ACTIVE');

        $confirm = strtolower(trim((string) $this->ask('Tutup MIT Week active? y/n')));

        if ($confirm !== 'y') {
            $this->warn('Proses dibatalkan.');
            return;
        }

        $service->closeActiveMitWeek($admin['identifier']);

        $this->info('MIT Week active berhasil ditutup.');
    }

    private function verifyCli(array $admin, VerificationService $service): void
    {
        while (true) {
            $choice = $this->askMenu('VERIFIKASI TANDA TANGAN', [
                1 => 'Lihat Request Pending Berdasarkan Minggu',
                0 => 'Kembali',
            ]);

            if ($choice === 0) {
                return;
            }

            $this->clearScreen();

            try {
                $this->processVerificationByWeekCli($admin, $service);
            } catch (Throwable $e) {
                $this->error($e->getMessage());
            }

            if ($this->pauseToMainOrPrevious('menu admin', 'menu verifikasi tanda tangan')) {
                return;
            }
        }
    }

    private function processVerificationByWeekCli(array $admin, VerificationService $service): void
    {
        $this->headerBox('REQUEST VERIFIKASI TTD');

        $weekNumber = $this->askRequiredInt('Masukkan minggu MIT yang ingin dicek, atau 0 untuk kembali');
        if ($weekNumber === null) {
            return;
        }

        $requests = $service->pendingRequestsByWeekNumber($weekNumber);

        if (count($requests) === 0) {
            $this->warn('Tidak ada request verifikasi TTD pending pada minggu ini.');
            return;
        }

        $this->table([
            'Verification ID',
            'Realisasi ID',
            'Booking ID',
            'Nama Maba',
            'NRP',
            'Kelompok',
            'TTD 2022',
            'TTD 2023',
            'TTD 2024',
            'Total Klaim',
            'Foto',
            'Submitted At',
        ], array_map(fn ($r) => [
            $r['verification_id'],
            $r['realisasi_id'],
            $r['booking_id'],
            $r['nama_maba'],
            $r['nrp_maba'],
            $r['kode_kelompok'],
            $r['claimed_ttd_2022'],
            $r['claimed_ttd_2023'],
            $r['claimed_ttd_2024'],
            $r['claimed_total'],
            $r['foto_path'],
            $r['submitted_at'],
        ], $requests));

        $verificationId = $this->askRequiredInt('Pilih Verification ID yang akan diverifikasi, atau 0 untuk kembali');
        if ($verificationId === null) {
            return;
        }

        $detail = $service->detailRequest($verificationId);

        $this->clearScreen();
        $this->headerBox('DETAIL REQUEST VERIFIKASI');

        $this->line('Nama Maba       : ' . $detail['maba']['nama']);
        $this->line('NRP Maba        : ' . $detail['maba']['nrp']);
        $this->line('Minggu MIT      : ' . $detail['week']['week_number']);
        $this->line('Booking ID      : ' . $detail['booking']['id']);
        $this->line('Kelompok Warga  : Kelompok ' . $detail['kelompok']['kode_kelompok']);
        $this->line('Jadwal Final    : ' . $detail['booking']['final_schedule']);
        $this->line('Lokasi Final    : ' . $detail['booking']['final_location']);
        $this->newLine();

        $this->line('Klaim TTD Maba:');
        $this->line('TTD 2022        : ' . $detail['claimed']['ttd_2022']);
        $this->line('TTD 2023        : ' . $detail['claimed']['ttd_2023']);
        $this->line('TTD 2024        : ' . $detail['claimed']['ttd_2024']);
        $this->line('Total           : ' . $detail['claimed']['total']);
        $this->newLine();

        $this->line('Bukti Foto:');
        $this->line('File Name       : ' . ($detail['upload']['file_name'] ?? '-'));
        $this->line('File Path       : ' . ($detail['upload']['file_path'] ?? '-'));
        $this->line('Mime Type       : ' . ($detail['upload']['mime_type'] ?? '-'));
        $this->line('Notes           : ' . ($detail['upload']['notes'] ?? '-'));

        $decision = $this->askMenu('KEPUTUSAN VERIFIKASI', [
            1 => 'verified',
            2 => 'revision',
            3 => 'rejected',
            0 => 'Batal',
        ]);

        if ($decision === 0) {
            $this->warn('Verifikasi dibatalkan.');
            return;
        }

        $status = match ($decision) {
            1 => 'verified',
            2 => 'revision',
            3 => 'rejected',
        };

        $v2022 = 0;
        $v2023 = 0;
        $v2024 = 0;

        if ($status === 'verified') {
            $useClaim = strtoupper(trim((string) $this->ask('Gunakan angka klaim maba sebagai angka verified? Y/N')));

            if ($useClaim === 'Y') {
                $v2022 = $detail['claimed']['ttd_2022'];
                $v2023 = $detail['claimed']['ttd_2023'];
                $v2024 = $detail['claimed']['ttd_2024'];
            } else {
                $input2022 = $this->askRequiredInt('Verified TTD 2022, atau 0 untuk kembali');
                if ($input2022 === null) {
                    return;
                }

                $input2023 = $this->askRequiredInt('Verified TTD 2023, atau 0 untuk kembali');
                if ($input2023 === null) {
                    return;
                }

                $input2024 = $this->askRequiredInt('Verified TTD 2024, atau 0 untuk kembali');
                if ($input2024 === null) {
                    return;
                }

                $v2022 = $input2022;
                $v2023 = $input2023;
                $v2024 = $input2024;
            }
        }

        $comment = (string) $this->ask('Komentar admin');

        $service->verifyRequestById(
            $admin['identifier'],
            $verificationId,
            $status,
            $v2022,
            $v2023,
            $v2024,
            $comment
        );

        $this->info('Request verifikasi TTD berhasil diproses.');
    }

    private function wargaMenu(Warga $warga, BookingService $bookingService): void
    {
        while (true) {
            $choice = $this->askMenu('MENU WARGA', [
                1 => 'Lihat Kelompok Saya',
                2 => 'Lihat Booking Masuk',
                3 => 'Accept Booking',
                4 => 'Cancel Booking',
                0 => 'Logout',
            ]);

            if ($choice === 0) {
                return;
            }

            $this->clearScreen();

            try {
                match ($choice) {
                    1 => $this->showWargaGroupCli($warga),
                    2 => $this->showIncomingBookingsForWargaCli($warga),
                    3 => $this->acceptBookingCli($warga, $bookingService),
                    4 => $this->cancelBookingCli($warga, $bookingService),
                };
            } catch (Throwable $e) {
                $this->error($e->getMessage());
            }

            $this->pauseToSameMenu('menu warga');
        }
    }

    private function showWargaGroupCli(Warga $warga): void
    {
        $this->headerBox('KELOMPOK SAYA');

        $member = KelompokWargaMember::with('group.members.warga')
            ->where('warga_id', $warga->warga_id)
            ->first();

        if (!$member) {
            $this->warn('Warga ini belum masuk kelompok. Hubungi admin.');
            return;
        }

        $group = $member->group;

        $this->info("Kelompok {$group->kode_kelompok} | Perwakilan ID: {$group->warga_id}");
        $this->table(
            ['Member ID', 'Nama', 'NRP', 'Angkatan'],
            $group->members->map(fn ($m) => [
                $m->member_id,
                $m->warga?->nama,
                $m->warga?->nrp,
                $m->warga?->angkatan,
            ])->toArray()
        );
    }

    private function showIncomingBookingsForWargaCli(Warga $warga): void
    {
        $this->headerBox('BOOKING MASUK');

        $group = KelompokWarga::where('warga_id', $warga->warga_id)->first();

        if (!$group) {
            $this->warn('Hanya perwakilan yang bisa melihat booking masuk sebagai pengambil keputusan.');
            return;
        }

        $bookings = Booking::where('kelompok_warga_id', $group->kelompok_warga_id)
            ->whereIn('status', ['pending', 'accepted'])
            ->get();

        $this->table(
            ['ID', 'Status', 'Jadwal', 'Lokasi'],
            $bookings->map(fn ($b) => [
                $b->booking_id,
                $b->status,
                $b->final_schedule,
                $b->final_location,
            ])->toArray()
        );
    }

    private function acceptBookingCli(Warga $warga, BookingService $bookingService): void
    {
        $this->headerBox('ACCEPT BOOKING');

        $bookingId = $this->askRequiredInt('Booking ID, atau 0 untuk kembali');
        if ($bookingId === null) {
            return;
        }

        $schedule = $this->askRequiredString('Final schedule, contoh 2026-06-11 14:00:00, atau 0 untuk kembali');
        if ($schedule === null) {
            return;
        }

        $location = $this->askRequiredString('Final location, atau 0 untuk kembali');
        if ($location === null) {
            return;
        }

        $notes = $this->askOptionalString('Catatan warga, kosongkan jika tidak ada');
        if ($notes === null) {
            return;
        }

        $bookingService->acceptBooking($warga, $bookingId, $schedule, $location, $notes);

        $this->info('Booking berhasil accepted.');
    }

    private function cancelBookingCli(Warga $warga, BookingService $bookingService): void
    {
        $this->headerBox('CANCEL BOOKING');

        $bookingId = $this->askRequiredInt('Booking ID, atau 0 untuk kembali');
        if ($bookingId === null) {
            return;
        }

        $reason = $this->askRequiredString('Alasan cancelled, atau 0 untuk kembali');
        if ($reason === null) {
            return;
        }

        $bookingService->cancelBooking($warga, $bookingId, $reason);

        $this->info('Booking berhasil cancelled.');
    }

    private function mabaMenu(
        Maba $maba,
        BookingQueryService $queryService,
        BookingService $bookingService,
        TtdService $ttdService,
        RealisasiService $realisasiService,
        RecommendationService $recommendationService
    ): void {
        while (true) {
            $choice = $this->askMenu('MENU MABA', [
                1 => 'Lihat Profil',
                2 => 'Lihat Progress TTD',
                3 => 'Lihat Kelompok Warga Tersedia',
                4 => 'Buat Request Booking',
                5 => 'Gabung Booking Accepted',
                6 => 'Lihat Booking Saya',
                7 => 'Ajukan Realisasi',
                8 => 'Rekomendasi Kelompok',
                0 => 'Logout',
            ]);

            if ($choice === 0) {
                return;
            }

            $this->clearScreen();

            try {
                match ($choice) {
                    1 => $this->showMabaProfileCli($maba),
                    2 => $this->showMabaProgressCli($maba, $ttdService),
                    3 => $this->showAvailableGroupsForMabaCli($maba, $queryService),
                    4 => $this->createBookingCli($maba, $bookingService),
                    5 => $this->joinAcceptedBookingCli($maba, $queryService, $bookingService),
                    6 => $this->showMyBookingsCli($maba),
                    7 => $this->submitRealisasiCli($maba, $realisasiService),
                    8 => $this->recommendationCli($maba, $recommendationService),
                };
            } catch (Throwable $e) {
                $this->error($e->getMessage());
            }

            $this->pauseToSameMenu('menu maba');
        }
    }

    private function showMabaProfileCli(Maba $maba): void
    {
        $this->headerBox('PROFIL MABA');

        $this->line("Nama: {$maba->nama}");
        $this->line("NRP : {$maba->nrp}");
    }

    private function showMabaProgressCli(Maba $maba, TtdService $ttdService): void
    {
        $this->headerBox('PROGRESS TTD');

        $p = $ttdService->progress($maba->maba_id);

        $this->table(['Kategori', 'Valid', 'Target', 'Kurang'], [
            ['2022', $p['total_2022'], $p['target_2022'], $p['kurang_2022']],
            ['2023', $p['total_2023'], $p['target_2023'], $p['kurang_2023']],
            ['2024', $p['total_2024'], $p['target_2024'], $p['kurang_2024']],
            ['Total', $p['total'], $p['target_total'], $p['kurang_total']],
        ]);
    }

    private function showAvailableGroupsForMabaCli(Maba $maba, BookingQueryService $queryService): void
    {
        $this->headerBox('KELOMPOK WARGA TERSEDIA');

        $rows = $queryService->availableGroupsForMaba($maba);

        $this->table(
            ['Group ID', 'Kode', 'Perwakilan', 'WA', 'Mode', 'Queue', 'Sisa Queue', 'Sisa Slot Accepted', 'Status Untuk Maba'],
            array_map(fn ($r) => [
                $r['kelompok_warga_id'],
                $r['kode_kelompok'],
                $r['perwakilan'],
                $r['wa'],
                $r['session_mode'],
                $r['queue_aktif'] . '/3',
                $r['sisa_queue'],
                $r['sisa_slot_booking_accepted'],
                $r['catatan_validasi'],
            ], $rows)
        );
    }

    private function createBookingCli(Maba $maba, BookingService $bookingService): void
    {
        $this->headerBox('BUAT REQUEST BOOKING');

        $kelompokId = $this->askRequiredInt('Kelompok Warga ID, atau 0 untuk kembali');
        if ($kelompokId === null) {
            return;
        }

        $booking = $bookingService->createBooking($maba, $kelompokId);

        $this->info("Booking dibuat. ID: {$booking->booking_id}");
    }

    private function joinAcceptedBookingCli(
        Maba $maba,
        BookingQueryService $queryService,
        BookingService $bookingService
    ): void {
        $this->headerBox('GABUNG BOOKING ACCEPTED');

        $rows = $queryService->joinableAcceptedBookingsForMaba($maba);

        $this->table(
            ['Booking ID', 'Kelompok', 'Perwakilan', 'Jadwal', 'Lokasi', 'Peserta', 'Sisa Slot'],
            array_map(fn ($r) => [
                $r['booking_id'],
                $r['kode_kelompok'],
                $r['perwakilan'],
                $r['final_schedule'],
                $r['final_location'],
                $r['peserta'] . '/' . $r['kapasitas'],
                $r['sisa_slot'],
            ], $rows)
        );

        if (count($rows) === 0) {
            $this->warn('Tidak ada booking accepted yang bisa kamu gabung.');
            return;
        }

        $bookingId = $this->askRequiredInt('Booking ID yang ingin digabung, atau 0 untuk kembali');
        if ($bookingId === null) {
            return;
        }

        $bookingService->joinBooking($maba, $bookingId);

        $this->info('Berhasil join booking accepted.');
    }

    private function showMyBookingsCli(Maba $maba): void
    {
        $this->headerBox('BOOKING SAYA');

        $bookings = Booking::with('group')
            ->whereHas('participants', fn ($q) => $q->where('maba_id', $maba->maba_id))
            ->get();

        $this->table(
            ['ID', 'Kelompok', 'Status', 'Jadwal', 'Lokasi'],
            $bookings->map(fn ($b) => [
                $b->booking_id,
                $b->group?->kode_kelompok,
                $b->status,
                $b->final_schedule,
                $b->final_location,
            ])->toArray()
        );
    }

    private function submitRealisasiCli(Maba $maba, RealisasiService $realisasiService): void
    {
        $this->headerBox('AJUKAN REALISASI');

        $bookingId = $this->askRequiredInt('Booking ID accepted, atau 0 untuk kembali');
        if ($bookingId === null) {
            return;
        }

        $data = [
            'meeting_held' => true,
            'is_warga_as_planned' => $this->confirm('Warga hadir sesuai rencana?', true),
            'absent_warga_notes' => $this->ask('Catatan warga tidak hadir'),
            'additional_warga_notes' => $this->ask('Catatan warga tambahan'),
            'general_notes' => $this->ask('Catatan umum'),
            'present_maba_ids' => $this->parseCsvIds((string) $this->ask('Maba ID present, pisah koma')),
            'absent_maba_ids' => $this->parseCsvIds((string) $this->ask('Maba ID absent, pisah koma')),
            'replacements' => $this->parseReplacement((string) $this->ask('Replacement old:new, pisah koma')),
            'claimed_ttd_2022' => (int) $this->ask('Claimed TTD 2022'),
            'claimed_ttd_2023' => (int) $this->ask('Claimed TTD 2023'),
            'claimed_ttd_2024' => (int) $this->ask('Claimed TTD 2024'),
            'upload_bukti' => [
                'file_name' => $this->ask('Nama file bukti'),
                'file_path' => $this->ask('Path file bukti'),
                'mime_type' => 'image/jpeg',
                'file_size' => 0,
            ],
        ];

        $realisasi = $realisasiService->submit($maba, $bookingId, $data);

        $this->info("Realisasi diajukan. ID: {$realisasi->realisasi_id}");
    }

    private function recommendationCli(Maba $maba, RecommendationService $recommendationService): void
    {
        $this->headerBox('REKOMENDASI KELOMPOK');

        $nrpInput = $this->askRequiredString('Masukkan 1-4 NRP, pisah koma, atau 0 untuk kembali');
        if ($nrpInput === null) {
            return;
        }

        $result = $recommendationService->recommend(
            $maba->maba_id,
            array_map('trim', explode(',', $nrpInput))
        );

        $this->table(
            ['Group ID', 'Kode', 'Score', 'Queue', 'Sisa Queue', 'Mode'],
            array_map(fn ($r) => [
                $r['kelompok_warga_id'],
                $r['kode_kelompok'],
                $r['score'],
                $r['queue_count'] . '/3',
                $r['sisa_queue'],
                $r['session_mode'],
            ], $result)
        );
    }

    private function showGroupsWithMembers(): void
    {
        $this->headerBox('DAFTAR KELOMPOK WARGA');

        $groups = KelompokWarga::with(['representative', 'members.warga'])
            ->orderBy('kode_kelompok')
            ->get();

        if ($groups->isEmpty()) {
            $this->warn('Belum ada kelompok warga.');
            return;
        }

        foreach ($groups as $group) {
            $this->newLine();
            $this->info("Kelompok {$group->kode_kelompok} | ID {$group->kelompok_warga_id} | Status {$group->status}");
            $this->line(
                "Perwakilan: {$group->representative?->nama} | " .
                "NRP: {$group->representative?->nrp} | " .
                "Angkatan: {$group->representative?->angkatan} | " .
                "WA: {$group->nomor_wa_perwakilan}"
            );

            $this->table(
                ['Member ID', 'Nama', 'NRP', 'Angkatan'],
                $group->members->map(fn ($m) => [
                    $m->member_id,
                    $m->warga?->nama,
                    $m->warga?->nrp,
                    $m->warga?->angkatan,
                ])->toArray()
            );
        }
    }

    private function showAllBookings(): void
    {
        $this->headerBox('SEMUA BOOKING');

        $bookings = Booking::with('group')->latest()->get();

        $this->table(
            ['ID', 'Kelompok', 'Status', 'Jadwal', 'Lokasi'],
            $bookings->map(fn ($b) => [
                $b->booking_id,
                $b->group?->kode_kelompok,
                $b->status,
                $b->final_schedule,
                $b->final_location,
            ])->toArray()
        );
    }

    private function showAllRealisasi(): void
    {
        $this->headerBox('SEMUA REALISASI');

        $items = Realisasi::latest()->get();

        $this->table(
            ['ID', 'Booking ID', 'Status', 'Submitted At'],
            $items->map(fn ($r) => [
                $r->realisasi_id,
                $r->booking_id,
                $r->status,
                $r->submitted_at,
            ])->toArray()
        );
    }

    private function showMongoLogs(): void
    {
        $this->headerBox('LOG MONGODB');

        $this->line('Activity Logs       : ' . ActivityLog::count());
        $this->line('Recommendation Logs : ' . RecommendationLog::count());
        $this->line('Revision Histories  : ' . RevisionHistory::count());
    }

    private function processResetPasswordCli(array $admin, PasswordResetService $service): void
    {
        $this->headerBox('PROSES RESET PASSWORD');

        $requests = PasswordResetRequest::where('status', 'pending')->get();

        $this->table(
            ['ID', 'Type', 'NRP', 'Status'],
            $requests->map(fn ($r) => [
                $r->reset_id,
                $r->requester_type,
                $r->nrp,
                $r->status,
            ])->toArray()
        );

        if ($requests->isEmpty()) {
            $this->warn('Tidak ada request reset password pending.');
            return;
        }

        $resetId = $this->askRequiredInt('Reset ID, atau 0 untuk kembali');
        if ($resetId === null) {
            return;
        }

        $decision = $this->askMenu('KEPUTUSAN RESET PASSWORD', [
            1 => 'approved',
            2 => 'rejected',
            0 => 'Kembali',
        ]);

        if ($decision === 0) {
            return;
        }

        $status = $decision === 1 ? 'approved' : 'rejected';
        $notes = (string) $this->ask('Catatan');

        $service->process($resetId, $status, $admin['identifier'], $notes);

        $this->info('Reset password selesai diproses.');
    }

    private function askMenu(string $title, array $items): int
    {
        $this->clearScreen();
        $this->headerBox($title);

        foreach ($items as $number => $label) {
            $this->line($number . '. ' . $label);
        }

        $this->separator();

        while (true) {
            $input = trim((string) $this->ask('Pilih menu'));

            if ($input === '') {
                $this->error('Pilihan tidak boleh kosong.');
                continue;
            }

            if (!ctype_digit($input)) {
                $this->error('Pilihan harus berupa angka.');
                continue;
            }

            $choice = (int) $input;

            if (!array_key_exists($choice, $items)) {
                $this->error('Pilihan tidak tersedia.');
                continue;
            }

            return $choice;
        }
    }

    private function clearScreen(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            system('cls');
            return;
        }

        system('clear');
    }

    private function headerBox(string $title): void
    {
        $this->line('========================================');
        $this->line(str_pad($title, 40, ' ', STR_PAD_BOTH));
        $this->line('========================================');
    }

    private function separator(): void
    {
        $this->line('----------------------------------------');
    }

    /**
     * Return true = keluar dari submenu ke menu role utama.
     * Return false = tetap di submenu sebelumnya.
     */
    private function pauseToMainOrPrevious(string $mainMenuName, string $previousMenuName): bool
    {
        $this->separator();
        $this->line("Tekan Enter untuk melanjutkan ke {$mainMenuName}.");
        $this->line("Ketik 0 untuk kembali ke {$previousMenuName}.");

        $input = trim((string) $this->ask('Input'));

        return $input !== '0';
    }

    private function pauseToSameMenu(string $menuName): void
    {
        $this->separator();
        $this->line("Tekan Enter untuk kembali ke {$menuName}.");
        $this->line("Ketik 0 untuk kembali ke {$menuName}.");

        $this->ask('Input');
    }

    private function askRequiredString(string $label): ?string
    {
        while (true) {
            $input = trim((string) $this->ask($label));

            if ($input === '0') {
                return null;
            }

            if ($input === '') {
                $this->warn('Input tidak boleh kosong. Ketik 0 untuk kembali.');
                continue;
            }

            return $input;
        }
    }

    private function askOptionalString(string $label): ?string
    {
        $input = trim((string) $this->ask($label));

        if ($input === '0') {
            return null;
        }

        return $input;
    }

    private function askRequiredInt(string $label): ?int
    {
        while (true) {
            $input = trim((string) $this->ask($label));

            if ($input === '0') {
                return null;
            }

            if ($input === '') {
                $this->warn('Input tidak boleh kosong. Ketik 0 untuk kembali.');
                continue;
            }

            if (!ctype_digit($input)) {
                $this->warn('Input harus berupa angka. Ketik 0 untuk kembali.');
                continue;
            }

            return (int) $input;
        }
    }

    private function parseCsvIds(string $value): array
    {
        if (trim($value) === '') {
            return [];
        }

        return array_values(array_filter(array_map('intval', explode(',', $value))));
    }

    private function parseReplacement(string $value): array
    {
        $result = [];

        if (trim($value) === '') {
            return $result;
        }

        foreach (explode(',', $value) as $pair) {
            [$old, $new] = array_pad(explode(':', $pair), 2, null);

            if ($old && $new) {
                $result[(int) $old] = (int) $new;
            }
        }

        return $result;
    }
}
