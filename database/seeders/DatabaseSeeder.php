<?php

namespace Database\Seeders;

use Carbon\Carbon;
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
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Seeder;
use Throwable;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        EloquentModel::unguard();

        /*
        |--------------------------------------------------------------------------
        | 1. MABA - 40 data
        |--------------------------------------------------------------------------
        */

        $mabas = [];

        $namaMaba = [
            'Dera', 'Faqih', 'Hasan', 'Ira', 'Jihan',
            'Karin', 'Luthfi', 'Mira', 'Naufal', 'Ocha',
            'Putra', 'Qori', 'Rania', 'Satria', 'Tania',
            'Umar', 'Vina', 'Wahyu', 'Xavier', 'Yasmin',
            'Zaki', 'Alif', 'Bella', 'Candra', 'Dinda',
            'Edo', 'Fara', 'Gilang', 'Hana', 'Ilham',
            'Joko', 'Kezia', 'Laras', 'Malik', 'Nadia',
            'Oscar', 'Pram', 'Rizka', 'Seno', 'Tiara',
        ];

        foreach ($namaMaba as $index => $nama) {
            $nrp = '502725' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);

            $mabas[] = Maba::create([
                'nama' => $nama,
                'nrp' => $nrp,
                'password' => $nrp,
                'status' => $index % 17 === 0 ? 'inactive' : 'active',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. WARGA - 60 data
        |--------------------------------------------------------------------------
        | Dibuat 60 warga agar 20 kelompok bisa punya 3 anggota unik.
        | Karena di migration ada unique('warga_id') pada kelompok_warga_member,
        | satu warga hanya boleh masuk ke satu kelompok.
        |--------------------------------------------------------------------------
        */

        $wargas = [];

        $namaWarga = [
            'Raka', 'Andi', 'Sinta', 'Bagas', 'Dimas',
            'Naya', 'Tegar', 'Lala', 'Bima', 'Citra',
            'Dion', 'Elsa', 'Farhan', 'Gita', 'Hendra',
            'Indah', 'Jefri', 'Kirana', 'Lukman', 'Maya',
            'Niko', 'Olivia', 'Panji', 'Qila', 'Reno',
            'Salma', 'Tomi', 'Ulfa', 'Vito', 'Wulan',
            'Yudha', 'Zahra', 'Adit', 'Bunga', 'Cahya',
            'Devi', 'Erwin', 'Fitri', 'Galih', 'Hilda',
            'Irvan', 'Jasmine', 'Kevin', 'Livia', 'Maulana',
            'Nesya', 'Oki', 'Prita', 'Rafi', 'Salsa',
            'Tasya', 'Udin', 'Vanya', 'Wira', 'Yuni',
            'Zidan', 'Arga', 'Bianca', 'Chandra', 'Dewi',
        ];

        foreach ($namaWarga as $index => $nama) {
            $angkatan = match ($index % 3) {
                0 => 2024,
                1 => 2023,
                default => 2022,
            };

            $nrp = '5027' . substr((string) $angkatan, -2) . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);

            $wargas[] = Warga::create([
                'nama' => $nama,
                'nrp' => $nrp,
                'angkatan' => $angkatan,
                'password' => $nrp,
                'status' => $index % 19 === 0 ? 'inactive' : 'active',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 3. MIT WEEK - 20 data
        |--------------------------------------------------------------------------
        */

        $weeks = [];

        $baseStartDate = now()->startOfWeek()->subWeeks(2);

        for ($i = 1; $i <= 20; $i++) {
            $startDate = $baseStartDate->copy()->addWeeks($i - 1);
            $endDate = $startDate->copy()->addDays(6);

            $status = match (true) {
                $i < 3 => 'completed',
                $i === 3 => 'active',
                default => 'upcoming',
            };

            $weeks[] = MitWeek::create([
                'week_number' => $i,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'status' => $status,
                'availability_input_status' => $i === 3 ? 'open' : 'closed',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 4. KELOMPOK WARGA - 20 data
        |--------------------------------------------------------------------------
        */

        $groups = [];

        $rulesList = [
            'Tepat waktu dan konfirmasi minimal H-1.',
            'Bawa buku MIT dan alat tulis.',
            'Konfirmasi via WA perwakilan.',
            'Tidak menerima peserta tanpa booking.',
            'Maksimal toleransi keterlambatan 10 menit.',
            'Wajib mengisi jadwal final sebelum pertemuan.',
            'Pertemuan diutamakan sore hari.',
            'Boleh diskusi santai, tetap sopan.',
            'Peserta wajib hadir sesuai daftar booking.',
            'Jika batal, kabari secepatnya.',
            'Utamakan komunikasi lewat perwakilan.',
            'Jangan double booking kelompok yang sama.',
            'Peserta wajib membawa bukti buku MIT.',
            'Lokasi final harus jelas.',
            'Perubahan jadwal harus disepakati bersama.',
            'Tidak menerima tambahan mendadak tanpa konfirmasi.',
            'Realisasi wajib diajukan setelah pertemuan.',
            'Pastikan semua peserta tercatat.',
            'Warga tambahan dicatat di realisasi.',
            'Jika ada warga absen, tulis catatan realisasi.',
        ];

        for ($i = 1; $i <= 20; $i++) {
            $groups[] = KelompokWarga::create([
                'kode_kelompok' => $i,
                'rules' => $rulesList[$i - 1],
                'status' => $i <= 18 ? 'final' : 'draft',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 5. KELOMPOK WARGA MEMBER - 60 data
        |--------------------------------------------------------------------------
        | Setiap kelompok punya 3 anggota.
        | Anggota pertama selalu menjadi perwakilan.
        |--------------------------------------------------------------------------
        */

        $waBase = 81111111111;

        foreach ($groups as $groupIndex => $group) {
            for ($memberSlot = 0; $memberSlot < 3; $memberSlot++) {
                $wargaIndex = ($groupIndex * 3) + $memberSlot;
                $warga = $wargas[$wargaIndex];

                KelompokWargaMember::create([
                    'kelompok_warga_id' => $group->kelompok_warga_id,
                    'warga_id' => $warga->warga_id,
                    'is_perwakilan' => $memberSlot === 0,
                    'nomor_wa' => $memberSlot === 0
                        ? '0' . ((string) ($waBase + $groupIndex))
                        : null,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 6. WEEKLY AVAILABILITY - 80 data
        |--------------------------------------------------------------------------
        | 20 kelompok x 4 minggu awal.
        | Ada variasi tersedia/tidak tersedia, mode 4/6, dan jumlah sesi.
        |--------------------------------------------------------------------------
        */

        foreach (array_slice($weeks, 0, 4) as $weekIndex => $week) {
            foreach ($groups as $groupIndex => $group) {
                WeeklyAvailability::create([
                    'week_id' => $week->week_id,
                    'kelompok_warga_id' => $group->kelompok_warga_id,
                    'is_available' => ($groupIndex + $weekIndex) % 7 !== 0,
                    'session_mode' => $groupIndex % 4 === 0 ? 6 : 4,
                    'session_count' => $groupIndex % 4 === 0 ? 2 : 3,
                    'notes' => match (($groupIndex + $weekIndex) % 5) {
                        0 => 'Bisa sore.',
                        1 => 'Bisa siang.',
                        2 => 'Bisa weekend.',
                        3 => 'Perlu konfirmasi ulang.',
                        default => 'Jadwal fleksibel.',
                    },
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 7. BOOKING - 80 data
        |--------------------------------------------------------------------------
        | 20 pending
        | 20 accepted
        | 20 completed
        | 20 cancelled
        |--------------------------------------------------------------------------
        */

        $bookings = [];

        for ($i = 1; $i <= 80; $i++) {
            $week = $weeks[($i - 1) % 4];
            $group = $groups[($i - 1) % count($groups)];
            $creator = $mabas[($i - 1) % count($mabas)];

            $status = match (true) {
                $i <= 20 => 'pending',
                $i <= 40 => 'accepted',
                $i <= 60 => 'completed',
                default => 'cancelled',
            };

            $deciderGroupMember = KelompokWargaMember::where('kelompok_warga_id', $group->kelompok_warga_id)
                ->where('is_perwakilan', true)
                ->first();

            $bookingData = [
                'week_id' => $week->week_id,
                'kelompok_warga_id' => $group->kelompok_warga_id,
                'created_by_maba_id' => $creator->maba_id,
                'status' => $status,
                'final_schedule' => null,
                'final_location' => null,
                'cancelled_reason' => null,
                'warga_notes' => null,
                'decided_by_warga_id' => null,
                'decided_at' => null,
            ];

            if (in_array($status, ['accepted', 'completed'], true)) {
                $bookingData['final_schedule'] = $week->start_date->copy()
                    ->addDays(($i % 5) + 1)
                    ->setTime(9 + ($i % 8), 0, 0)
                    ->format('Y-m-d H:i:s');

                $bookingData['final_location'] = match ($i % 5) {
                    0 => 'Kantin Departemen',
                    1 => 'Gazebo ITS',
                    2 => 'Perpustakaan ITS',
                    3 => 'Taman Alumni',
                    default => 'Selasar Departemen',
                };

                $bookingData['warga_notes'] = match ($i % 4) {
                    0 => 'Peserta diminta datang tepat waktu.',
                    1 => 'Bawa buku MIT.',
                    2 => 'Konfirmasi ulang sebelum berangkat.',
                    default => 'Lokasi bisa berubah sesuai kondisi.',
                };

                $bookingData['decided_by_warga_id'] = $deciderGroupMember?->warga_id;
                $bookingData['decided_at'] = now()->subDays(rand(1, 10));
            }

            if ($status === 'cancelled') {
                $bookingData['cancelled_reason'] = match ($i % 4) {
                    0 => 'Jadwal tidak cocok.',
                    1 => 'Kelompok warga tidak tersedia.',
                    2 => 'Peserta membatalkan permintaan.',
                    default => 'Ada perubahan agenda.',
                };

                $bookingData['decided_by_warga_id'] = $deciderGroupMember?->warga_id;
                $bookingData['decided_at'] = now()->subDays(rand(1, 10));
            }

            $bookings[] = Booking::create($bookingData);
        }

        /*
        |--------------------------------------------------------------------------
        | 8. BOOKING PARTICIPANT - minimal 160 data
        |--------------------------------------------------------------------------
        | Pending    : joined
        | Accepted   : joined + left/replaced untuk tes keluar/ganti peserta
        | Completed  : present/absent
        | Cancelled  : joined/left
        |--------------------------------------------------------------------------
        */

        foreach ($bookings as $bookingIndex => $booking) {
            $participantCount = match ($booking->status) {
                'pending' => 1 + ($bookingIndex % 4),
                'accepted' => $bookingIndex % 3 === 0 ? 6 : 4,
                'completed' => $bookingIndex % 3 === 0 ? 6 : 4,
                'cancelled' => 2 + ($bookingIndex % 3),
                default => 1,
            };

            for ($slot = 0; $slot < $participantCount; $slot++) {
                $maba = $mabas[($bookingIndex + $slot) % count($mabas)];

                $participantStatus = 'joined';
                $replacedBy = null;
                $leftAt = null;

                if ($booking->status === 'accepted') {
                    if ($slot === $participantCount - 1 && $bookingIndex % 4 === 0) {
                        $participantStatus = 'left';
                        $leftAt = now()->subDays(1);
                    } elseif ($slot === $participantCount - 2 && $bookingIndex % 5 === 0) {
                        $participantStatus = 'replaced';
                        $replacement = $mabas[($bookingIndex + $slot + 10) % count($mabas)];
                        $replacedBy = $replacement->maba_id;
                        $leftAt = now()->subDays(1);
                    }
                }

                if ($booking->status === 'completed') {
                    $participantStatus = $slot === $participantCount - 1 && $bookingIndex % 4 === 0
                        ? 'absent'
                        : 'present';
                }

                if ($booking->status === 'cancelled') {
                    $participantStatus = $slot === 0 ? 'joined' : 'left';
                    $leftAt = $slot === 0 ? null : now()->subDays(2);
                }

                BookingParticipant::create([
                    'booking_id' => $booking->booking_id,
                    'maba_id' => $maba->maba_id,
                    'status' => $participantStatus,
                    'replaced_by_maba_id' => $replacedBy,
                    'joined_at' => now()->subDays(rand(1, 14)),
                    'left_at' => $leftAt,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 9. REALISASI + VERIFICATION RESULT + HISTORY
        |--------------------------------------------------------------------------
        | Dibuat untuk 20 booking completed.
        |--------------------------------------------------------------------------
        */

        $completedBookings = collect($bookings)
            ->where('status', 'completed')
            ->values();

        $realisasis = [];

        foreach ($completedBookings as $index => $booking) {
            $submitterParticipant = BookingParticipant::where('booking_id', $booking->booking_id)
                ->where('status', 'present')
                ->first();

            if (!$submitterParticipant) {
                continue;
            }

            $realisasiStatus = match ($index % 4) {
                0 => 'pending',
                1 => 'verified',
                2 => 'revision',
                default => 'rejected',
            };

            $realisasi = Realisasi::create([
                'booking_id' => $booking->booking_id,
                'week_id' => $booking->week_id,
                'submitted_by_maba_id' => $submitterParticipant->maba_id,
                'realisasi_is_meeting_held' => true,
                'is_warga_as_planned' => $index % 5 !== 0,
                'absent_warga_notes' => $index % 5 === 0 ? 'Ada satu warga yang tidak hadir.' : null,
                'additional_warga_notes' => $index % 6 === 0 ? 'Ada satu warga tambahan ikut sesi.' : null,
                'general_notes' => match ($index % 4) {
                    0 => 'Menunggu verifikasi admin.',
                    1 => 'Realisasi valid dan sudah diverifikasi.',
                    2 => 'Perlu revisi bukti atau jumlah TTD.',
                    default => 'Data realisasi ditolak karena tidak sesuai.',
                },
                'status' => $realisasiStatus,
                'submitted_at' => now()->subDays(rand(1, 10)),
                'verified_at' => $realisasiStatus === 'pending' ? null : now()->subDays(rand(0, 5)),
                'verified_by_admin_identifier' => $realisasiStatus === 'pending' ? null : 'admin-demo',
            ]);

            $realisasis[] = $realisasi;

            $presentParticipants = BookingParticipant::where('booking_id', $booking->booking_id)
                ->where('status', 'present')
                ->get();

            foreach ($presentParticipants as $participantIndex => $participant) {
                $claimed2022 = ($participantIndex + $index) % 2;
                $claimed2023 = 1 + (($participantIndex + $index) % 3);
                $claimed2024 = 3 + (($participantIndex + $index) % 5);

                $verified2022 = $realisasiStatus === 'verified' ? $claimed2022 : max(0, $claimed2022 - 1);
                $verified2023 = $realisasiStatus === 'verified' ? $claimed2023 : max(0, $claimed2023 - 1);
                $verified2024 = $realisasiStatus === 'verified' ? $claimed2024 : max(0, $claimed2024 - 2);

                VerificationResult::create([
                    'realisasi_id' => $realisasi->realisasi_id,
                    'maba_id' => $participant->maba_id,
                    'week_id' => $booking->week_id,
                    'claimed_ttd_2022' => $claimed2022,
                    'claimed_ttd_2023' => $claimed2023,
                    'claimed_ttd_2024' => $claimed2024,
                    'verified_ttd_2022' => $verified2022,
                    'verified_ttd_2023' => $verified2023,
                    'verified_ttd_2024' => $verified2024,
                    'status' => $realisasiStatus,
                    'admin_comment' => match ($realisasiStatus) {
                        'pending' => null,
                        'verified' => 'Data valid.',
                        'revision' => 'Jumlah TTD perlu disesuaikan.',
                        'rejected' => 'Bukti tidak sesuai.',
                        default => null,
                    },
                    'verified_by_admin_identifier' => $realisasiStatus === 'pending' ? null : 'admin-demo',
                    'verified_at' => $realisasiStatus === 'pending' ? null : now()->subDays(rand(0, 5)),
                ]);

                MabaKelompokHistory::firstOrCreate([
                    'maba_id' => $participant->maba_id,
                    'kelompok_warga_id' => $booking->kelompok_warga_id,
                ], [
                    'week_id' => $booking->week_id,
                    'booking_id' => $booking->booking_id,
                    'created_at' => now()->subDays(rand(1, 10)),
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 10. PASSWORD RESET REQUEST - 20 data
        |--------------------------------------------------------------------------
        */

        for ($i = 1; $i <= 20; $i++) {
            $isMaba = $i % 2 === 1;

            if ($isMaba) {
                $requester = $mabas[($i - 1) % count($mabas)];
                $requesterType = 'maba';
                $requesterId = $requester->maba_id;
            } else {
                $requester = $wargas[($i - 1) % count($wargas)];
                $requesterType = 'warga';
                $requesterId = $requester->warga_id;
            }

            PasswordResetRequest::create([
                'requester_type' => $requesterType,
                'requester_id' => $requesterId,
                'nrp' => $requester->nrp,
                'new_password' => 'passwordbaru' . $i,
                'status' => match ($i % 3) {
                    0 => 'pending',
                    1 => 'approved',
                    default => 'rejected',
                },
                'admin_notes' => match ($i % 3) {
                    0 => null,
                    1 => 'Reset disetujui.',
                    default => 'Data tidak sesuai.',
                },
                'requested_at' => now()->subDays($i),
                'processed_at' => $i % 3 === 0 ? null : now()->subDays(max(0, $i - 1)),
                'processed_by_admin_identifier' => $i % 3 === 0 ? null : 'admin-demo',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 11. MONGO LOGS - masing-masing minimal 20 data
        |--------------------------------------------------------------------------
        | Jika MongoDB belum aktif, bagian ini akan dilewati agar seeding MySQL
        | tetap berhasil.
        |--------------------------------------------------------------------------
        */

        try {
            ActivityLog::query()->delete();
            UploadBuktiLog::query()->delete();
            RecommendationLog::query()->delete();
            RevisionHistory::query()->delete();

            for ($i = 1; $i <= 20; $i++) {
                $actorMaba = $mabas[($i - 1) % count($mabas)];
                $realisasi = $realisasis[($i - 1) % count($realisasis)];

                ActivityLog::create([
                    'user_id' => $i % 4 === 0 ? null : $actorMaba->maba_id,
                    'role' => match ($i % 3) {
                        0 => 'admin',
                        1 => 'maba',
                        default => 'warga',
                    },
                    'action' => match ($i % 5) {
                        0 => 'seed_demo',
                        1 => 'create_booking',
                        2 => 'join_booking',
                        3 => 'submit_realisasi',
                        default => 'verify_ttd',
                    },
                    'description' => 'Log demo ke-' . $i,
                    'metadata' => [
                        'source' => 'DatabaseSeeder',
                        'index' => $i,
                    ],
                    'ip_address' => '127.0.0.' . $i,
                    'created_at' => now()->subMinutes($i * 5),
                ]);

                UploadBuktiLog::create([
                    'realisasi_id' => $realisasi->realisasi_id,
                    'maba_id' => $actorMaba->maba_id,
                    'file_name' => 'bukti_demo_' . $i . '.jpg',
                    'file_path' => 'uploads/bukti_demo_' . $i . '.jpg',
                    'mime_type' => 'image/jpeg',
                    'file_size' => 10000 + ($i * 250),
                    'notes' => 'Dummy bukti realisasi ke-' . $i,
                    'created_at' => now()->subMinutes($i * 6),
                ]);

                RecommendationLog::create([
                    'requested_by_maba_id' => $actorMaba->maba_id,
                    'input_nrp_list' => [
                        $actorMaba->nrp,
                        $mabas[$i % count($mabas)]->nrp,
                    ],
                    'recommended_groups' => [
                        [
                            'kelompok_warga_id' => $groups[($i - 1) % count($groups)]->kelompok_warga_id,
                            'kode_kelompok' => $groups[($i - 1) % count($groups)]->kode_kelompok,
                            'score' => 90 - ($i % 20),
                        ],
                        [
                            'kelompok_warga_id' => $groups[$i % count($groups)]->kelompok_warga_id,
                            'kode_kelompok' => $groups[$i % count($groups)]->kode_kelompok,
                            'score' => 80 - ($i % 15),
                        ],
                    ],
                    'scoring_detail' => [
                        'slot_score' => 40,
                        'history_score' => 30,
                        'availability_score' => 20,
                        'random_factor' => $i,
                    ],
                    'created_at' => now()->subMinutes($i * 7),
                ]);

                RevisionHistory::create([
                    'realisasi_id' => $realisasi->realisasi_id,
                    'admin_identifier' => 'admin-demo',
                    'old_status' => match ($i % 4) {
                        0 => 'pending',
                        1 => 'pending',
                        2 => 'revision',
                        default => 'pending',
                    },
                    'new_status' => match ($i % 4) {
                        0 => 'verified',
                        1 => 'revision',
                        2 => 'verified',
                        default => 'rejected',
                    },
                    'notes' => match ($i % 4) {
                        0 => 'Data sudah valid.',
                        1 => 'Perlu revisi jumlah TTD.',
                        2 => 'Revisi diterima.',
                        default => 'Bukti tidak sesuai.',
                    },
                    'changed_fields' => [
                        'status',
                        'admin_comment',
                        'verified_ttd_2022',
                        'verified_ttd_2023',
                        'verified_ttd_2024',
                    ],
                    'created_at' => now()->subMinutes($i * 8),
                ]);
            }
        } catch (Throwable $e) {
            $this->command?->warn('Seeder MongoDB dilewati: ' . $e->getMessage());
        }

        EloquentModel::reguard();
    }
}
