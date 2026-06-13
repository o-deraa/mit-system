<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Models\KelompokWarga;
use App\Models\KelompokWargaMember;
use App\Models\Maba;
use App\Models\MabaKelompokHistory;
use App\Models\MitWeek;
use App\Models\Mongo\ActivityLog;
use App\Models\Mongo\RecommendationLog;
use App\Models\Mongo\RevisionHistory;
use App\Models\Mongo\UploadBuktiLog;
use App\Models\PasswordResetRequest;
use App\Models\Realisasi;
use App\Models\VerificationResult;
use App\Models\Warga;
use App\Models\WeeklyAvailability;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $maba1 = Maba::create(['nama' => 'Dera', 'nrp' => '5027251001', 'password' => '5027251001', 'status' => 'active']);
        $maba2 = Maba::create(['nama' => 'Faqih', 'nrp' => '5027251002', 'password' => '5027251002', 'status' => 'active']);
        $maba3 = Maba::create(['nama' => 'Hasan', 'nrp' => '5027251003', 'password' => '5027251003', 'status' => 'active']);
        $maba4 = Maba::create(['nama' => 'Ira', 'nrp' => '5027251004', 'password' => '5027251004', 'status' => 'active']);
        $maba5 = Maba::create(['nama' => 'Jihan', 'nrp' => '5027251005', 'password' => '5027251005', 'status' => 'active']);

        $w1 = Warga::create(['nama' => 'Raka', 'nrp' => '5027242001', 'angkatan' => 2024, 'password' => '5027242001', 'status' => 'active']);
        $w2 = Warga::create(['nama' => 'Andi', 'nrp' => '5027242002', 'angkatan' => 2024, 'password' => '5027242002', 'status' => 'active']);
        $w3 = Warga::create(['nama' => 'Sinta', 'nrp' => '5027232001', 'angkatan' => 2023, 'password' => '5027232001', 'status' => 'active']);
        $w4 = Warga::create(['nama' => 'Bagas', 'nrp' => '5027222001', 'angkatan' => 2022, 'password' => '5027222001', 'status' => 'active']);
        $w5 = Warga::create(['nama' => 'Dimas', 'nrp' => '5027242003', 'angkatan' => 2024, 'password' => '5027242003', 'status' => 'active']);
        $w6 = Warga::create(['nama' => 'Naya', 'nrp' => '5027232002', 'angkatan' => 2023, 'password' => '5027232002', 'status' => 'active']);
        $w7 = Warga::create(['nama' => 'Tegar', 'nrp' => '5027222002', 'angkatan' => 2022, 'password' => '5027222002', 'status' => 'active']);
        $w8 = Warga::create(['nama' => 'Lala', 'nrp' => '5027242004', 'angkatan' => 2024, 'password' => '5027242004', 'status' => 'active']);

        $week1 = MitWeek::create(['week_number' => 1, 'start_date' => '2026-06-08', 'end_date' => '2026-06-14', 'status' => 'active', 'availability_input_status' => 'open']);
        $week2 = MitWeek::create(['week_number' => 2, 'start_date' => '2026-06-15', 'end_date' => '2026-06-21', 'status' => 'upcoming', 'availability_input_status' => 'closed']);

        $g1 = KelompokWarga::create(['kode_kelompok' => 1, 'warga_id' => $w1->warga_id, 'nomor_wa_perwakilan' => '081111111111', 'rules' => 'Tepat waktu.', 'status' => 'final']);
        $g2 = KelompokWarga::create(['kode_kelompok' => 2, 'warga_id' => $w3->warga_id, 'nomor_wa_perwakilan' => '082222222222', 'rules' => 'Bawa buku MIT.', 'status' => 'final']);
        $g3 = KelompokWarga::create(['kode_kelompok' => 3, 'warga_id' => $w5->warga_id, 'nomor_wa_perwakilan' => '083333333333', 'rules' => 'Konfirmasi via WA.', 'status' => 'final']);

        foreach ([[$g1,$w1],[$g1,$w2],[$g1,$w4],[$g2,$w3],[$g2,$w6],[$g2,$w7],[$g3,$w5],[$g3,$w8]] as [$g, $w]) {
            KelompokWargaMember::create(['kelompok_warga_id' => $g->kelompok_warga_id, 'warga_id' => $w->warga_id]);
        }

        WeeklyAvailability::create(['week_id' => $week1->week_id, 'kelompok_warga_id' => $g1->kelompok_warga_id, 'is_available' => true, 'session_mode' => 4, 'session_count' => 3, 'notes' => 'Bisa sore.']);
        WeeklyAvailability::create(['week_id' => $week1->week_id, 'kelompok_warga_id' => $g2->kelompok_warga_id, 'is_available' => true, 'session_mode' => 4, 'session_count' => 3, 'notes' => 'Bisa siang.']);
        WeeklyAvailability::create(['week_id' => $week1->week_id, 'kelompok_warga_id' => $g3->kelompok_warga_id, 'is_available' => true, 'session_mode' => 6, 'session_count' => 3, 'notes' => 'Bisa weekend.']);

        $b1 = Booking::create(['week_id' => $week1->week_id, 'kelompok_warga_id' => $g1->kelompok_warga_id, 'created_by_maba_id' => $maba1->maba_id, 'status' => 'pending']);
        BookingParticipant::create(['booking_id' => $b1->booking_id, 'maba_id' => $maba1->maba_id, 'status' => 'joined', 'joined_at' => now()]);

        $b2 = Booking::create([
            'week_id' => $week1->week_id,
            'kelompok_warga_id' => $g2->kelompok_warga_id,
            'created_by_maba_id' => $maba2->maba_id,
            'status' => 'accepted',
            'final_schedule' => '2026-06-11 14:00:00',
            'final_location' => 'Kantin Departemen',
            'decided_by_warga_id' => $w3->warga_id,
            'decided_at' => now(),
        ]);
        BookingParticipant::create(['booking_id' => $b2->booking_id, 'maba_id' => $maba2->maba_id, 'status' => 'joined', 'joined_at' => now()]);

        $b3 = Booking::create([
            'week_id' => $week1->week_id,
            'kelompok_warga_id' => $g3->kelompok_warga_id,
            'created_by_maba_id' => $maba4->maba_id,
            'status' => 'completed',
            'final_schedule' => '2026-06-10 10:00:00',
            'final_location' => 'Gazebo ITS',
            'decided_by_warga_id' => $w5->warga_id,
            'decided_at' => now(),
        ]);
        BookingParticipant::create(['booking_id' => $b3->booking_id, 'maba_id' => $maba4->maba_id, 'status' => 'present', 'joined_at' => now()]);

        $r1 = Realisasi::create([
            'booking_id' => $b3->booking_id,
            'week_id' => $week1->week_id,
            'submitted_by_maba_id' => $maba4->maba_id,
            'realisasi_is_meeting_held' => true,
            'is_warga_as_planned' => true,
            'status' => 'verified',
            'submitted_at' => now(),
            'verified_at' => now(),
            'verified_by_admin_identifier' => 'admin-demo',
        ]);
        VerificationResult::create([
            'realisasi_id' => $r1->realisasi_id,
            'maba_id' => $maba4->maba_id,
            'week_id' => $week1->week_id,
            'claimed_ttd_2022' => 1,
            'claimed_ttd_2023' => 2,
            'claimed_ttd_2024' => 5,
            'verified_ttd_2022' => 1,
            'verified_ttd_2023' => 2,
            'verified_ttd_2024' => 5,
            'status' => 'verified',
            'verified_by_admin_identifier' => 'admin-demo',
            'verified_at' => now(),
        ]);
        MabaKelompokHistory::create(['maba_id' => $maba4->maba_id, 'kelompok_warga_id' => $g3->kelompok_warga_id, 'week_id' => $week1->week_id, 'booking_id' => $b3->booking_id, 'created_at' => now()]);

        PasswordResetRequest::create(['requester_type' => 'maba', 'requester_id' => $maba5->maba_id, 'nrp' => $maba5->nrp, 'new_password' => 'passwordbaru', 'status' => 'pending', 'requested_at' => now()]);

        ActivityLog::create(['user_id' => null, 'role' => 'admin', 'action' => 'seed_demo', 'description' => 'Seeder demo dibuat.', 'metadata' => ['source' => 'DatabaseSeeder'], 'ip_address' => '127.0.0.1', 'created_at' => now()]);
        UploadBuktiLog::create(['realisasi_id' => $r1->realisasi_id, 'maba_id' => $maba4->maba_id, 'file_name' => 'bukti_demo.jpg', 'file_path' => 'uploads/bukti_demo.jpg', 'mime_type' => 'image/jpeg', 'file_size' => 12345, 'notes' => 'Dummy bukti.', 'created_at' => now()]);
        RecommendationLog::create(['requested_by_maba_id' => $maba1->maba_id, 'input_nrp_list' => [$maba1->nrp], 'recommended_groups' => [], 'scoring_detail' => [], 'created_at' => now()]);
        RevisionHistory::create(['realisasi_id' => $r1->realisasi_id, 'admin_identifier' => 'admin-demo', 'old_status' => 'pending', 'new_status' => 'verified', 'notes' => 'Seeder verified.', 'changed_fields' => [], 'created_at' => now()]);
    }
}
