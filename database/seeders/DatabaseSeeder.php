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
        | 1. MABA - 129 data
        |--------------------------------------------------------------------------
        | NRP menggunakan format 5027251XXX.
        | XXX mengikuti nomor urut maba dari 001 sampai 129.
        |--------------------------------------------------------------------------
        */

        $mabas = [];

        $namaMaba = [
            1 => 'Evandra Raditya Fauzan',
            2 => 'Ferlin Erdina Sari',
            3 => 'Chilmi Muhammad Ulin Nuha',
            4 => 'Ni Putu Maqueenta Wijaya',
            5 => 'Umar',
            6 => 'Abhista Athallah Dyfan',
            7 => 'Arjunina Maqbulin Usman',
            8 => 'Rochmatul Auliyah',
            9 => 'Maulana Zaki Putra Zakaria',
            10 => 'Johannes Gilbert Hottonggi Panjaitan',
            11 => 'Revalinda Bunga Nayla Laksono',
            12 => 'Gede Satya Putra Aryanta',
            13 => 'Putri Agustin',
            14 => 'Naurah Sawestri',
            15 => 'Rafsanjani',
            16 => 'Clarissa',
            17 => 'Rizky Fauzan',
            18 => 'Nazwa Aulia Dwi Purnomo',
            19 => 'M. Rama Maulana Wafa',
            20 => 'Arteya Kumara',
            21 => 'Az Zahra Fiddien Al Farabi',
            22 => 'Muhammad Satrio Utomo',
            23 => 'Ahza Fakhrullah',
            24 => 'Muhammad Rafi Pramudya Putra',
            25 => 'Tafidah Hasna Mumtaza',
            26 => 'Maitasya Rohmatul Ula',
            27 => 'Rizki Muhammad Fadhli',
            28 => 'Faeyza Rusmawan',
            29 => 'Muhammad Rifqi Fathurrahman',
            30 => 'Muhammad Syadzili Abdul Muhyi',
            31 => 'Piramidiana Rachmatika',
            32 => 'Syarifa Dewi',
            33 => 'Daffa Ulhaq Fadhlurrahman',
            34 => 'Chen',
            35 => 'Chrisna Butarbutar',
            36 => 'Baik Dermawan Ganteng Kece Avv',
            37 => 'Sahira Bilqis Rivadito',
            38 => 'Rifqi As Shidiq',
            39 => 'La Satta Sitorus',
            40 => 'Sebastian Elroi Hasian Panjaitan',
            41 => 'M. Sabilil Haq',
            42 => 'Nayla Arsha Adyuta',
            43 => 'Asfia Fahmisan',
            44 => 'Arrumanta Ekna Luhkinasih',
            45 => 'Ahmad Nayottama Juliansyah',
            46 => 'Nayarfa Syamahira Dyananta',
            47 => 'Putri Permata Sabila',
            48 => 'M. Faris Roisul Azhar',
            49 => 'Ashkhabil Abror Budihardjo',
            50 => 'Arthur Tamajaya',
            51 => 'Manudinata',
            52 => 'Muhammad Syihan Zhafiri',
            53 => 'Reyhan',
            54 => 'Sharliz Sigit',
            55 => 'Dira Anugerah',
            56 => 'Rahmadina',
            57 => 'Riezco Eka Bayu Witantra',
            58 => 'Azita Zahwa Zahida Asmoro',
            59 => 'Salsabila Rafa Syafira',
            60 => 'Danish',
            61 => 'I Ketut Weda Adikusuma',
            62 => 'Ilham Wahyudi',
            63 => 'Dewa Ngakan Gede Wira Adhimukti',
            64 => 'I Made Tobby Anantha Adiwijaya',
            65 => 'Muhammad Faturrahman',
            66 => 'Setyo Ragil',
            67 => 'Muhammad Yusuf',
            68 => 'Halimah Mulia',
            69 => 'Audya Yuniarini',
            70 => 'Ahmad Maulana Bahyshidqi',
            71 => 'Muhammad Atallah Mas`udi',
            72 => 'I Made Gyanendra Anand Wisnawa',
            73 => 'Nabilla Nafisatus Zuhro',
            74 => 'Mahrinza Redouane Zakariyah',
            75 => 'Aisha Hanifa',
            76 => 'Muhammad Hugo Rayandra Esmid',
            77 => 'Dwi Muslim',
            78 => 'Najla Tufailah',
            79 => 'Raffa Al Azmi',
            80 => 'Adi Satrio',
            81 => 'Anargya Shafa Setiyadi Putra',
            82 => 'Fiorellin Ilona',
            83 => 'D`qhaizhar Ari Dhiaulhaq',
            84 => 'Muhammad Nadhif Pasya Ikhsan',
            85 => 'Marvelino Davas',
            86 => 'Muhammad Razzan Azizi Djauhari',
            87 => 'Muhammad Rifki Pribadi',
            88 => 'Alfarezy',
            89 => 'Nathania Tiara Wahyudi',
            90 => 'Pramudita Adi Putra',
            91 => 'Sulthan Daffa Al Hasyimi',
            92 => 'James Osamah',
            93 => 'Donnavie Aulia',
            94 => 'Hafiz Anugrah',
            95 => 'Nur Rizki Syahbana',
            96 => 'Suryapraba Laiasach',
            97 => 'Muhammad Salman Rifki Haq',
            98 => 'Jude Athala Yazid Sari',
            99 => 'Saktyaveshavatar Dharmesthabuddhi',
            100 => 'Muhammad Athasyah Enrizy',
            101 => 'Putu Putra Sakti Sadhana',
            102 => 'Achmad Rifqy Aqila',
            103 => 'Wahyu Yoga Wicaksono',
            104 => 'Khalifa Suryadinarta',
            105 => 'Artika Satriyo',
            106 => 'Bagus Harimurti',
            107 => 'Prayudya Rizky Ramadhani',
            108 => 'Iqlima Al Fairuz',
            109 => 'Syarifah Nailatur Rohma',
            110 => 'Bambang Nasarillah Kurniawan',
            111 => 'Zid Ilmi',
            112 => 'A. Algifari Rantiga Isdar',
            113 => 'Muhammad Ridwan',
            114 => 'Rahma Putri',
            115 => 'Fairuza',
            116 => 'Hanna Simanjuntak',
            117 => 'Muhamad Nasrulhaq',
            118 => 'Pradipta Airlangga Ramadhan',
            119 => 'Raditya Putra Purbono',
            120 => 'Patra Yudhistira Edwin',
            121 => 'Alif Ramzy Pasha Firdaus',
            122 => 'Anggun Eka Rizqy',
            123 => 'Syahzanani Al Mustofa',
            124 => 'Satria Tama',
            125 => 'Sahya Aryaguna',
            126 => 'Fadhilah Allayn',
            127 => 'Razana Aulia',
            128 => 'Atik Putri Matulina',
            129 => 'Ridho Zhafif',
        ];

        $inactiveMabaNumbers = [10, 99, 52, 86, 109];

        foreach ($namaMaba as $nomorUrut => $nama) {
            $nrpSuffix = str_pad((string) $nomorUrut, 3, '0', STR_PAD_LEFT);
            $nrp = '5027251' . $nrpSuffix;

            $mabas[] = Maba::create([
                'nama' => $nama,
                'nrp' => $nrp,
                'password' => $nrp,
                'status' => in_array($nomorUrut, $inactiveMabaNumbers, true) ? 'inactive' : 'active',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. WARGA - 285 data
        |--------------------------------------------------------------------------
        | Total:
        | - Angkatan 2023: 89 orang
        | - Angkatan 2024: 123 orang
        | - Angkatan 2022: 73 orang
        |
        | NRP menggunakan format 5027AA1BBB.
        | AA  = dua digit angkatan, contoh 22, 23, 24.
        | BBB = nomor urut warga pada angkatan tersebut.
        |--------------------------------------------------------------------------
        */

        $wargas = [];

        $jumlahWargaPerAngkatan = [
            2023 => 89,
            2024 => 123,
            2022 => 73,
        ];

        $namaWargaPerAngkatan = [
            2023 => [
                'Yusuf Wirawan Triyono',
                'Hanifah Ayu Amalia',
                'Nadira Lestari Nurfitriani',
                'Salma Ningrum Umarani',
                'Nadia Indriani Pertiwi',
                'Annisa Kusumawati Dwiyanti',
                'Bambang Fadillah Syahputra',
                'Agus Erlangga Susanto',
                'Delia Maharani Sari',
                'Kresna Febrianto Syahbani',
                'Nisa Hasanah Hapsari',
                'Livia Fitriana Rahayu',
                'Vanya Amalia Sukma',
                'Rangga Wibowo Amin',
                'Laras Zahira Puspitasari',
                'Vina Lestari Wardani',
                'Jasmine Sekar Maulida',
                'Nisa Kirana Ariyanti',
                'Maya Widyaningrum Kusumawardani',
                'Agus Rahardian Ramdani',
                'Sari Kartikasari Maharani',
                'Salman Fauzan Wijaya',
                'Farah Damayanti Syafira',
                'Bella Melati Ningsih',
                'Olivia Handayani Maulida',
                'Citra Kirana Rachmawati',
                'Ivan Prasetyo Pamungkas',
                'Maya Amalia Hapsari',
                'Ilham Wahyudi Adiputra',
                'Fajar Herlambang Rabbani',
                'Febri Hidayat Ramdani',
                'Shafa Kurniasari Nuraini',
                'Amira Hasanah Fadhilah',
                'Rafi Kusuma Kurnia',
                'Safira Puspita Nurhaliza',
                'Reza Hidayat Maheswara',
                'Bayu Pangestu Prabowo',
                'Niko Nugroho Mustofa',
                'Amira Puspita Wardani',
                'Gilang Wibowo Anugrah',
                'Sinta Hasanah Wahyuni',
                'Ivan Fadillah Susanto',
                'Citra Zahira Ramadhana',
                'Ulfa Maharani Puspitasari',
                'Wahyu Ardiansyah Nugroho',
                'Arif Setiawan Akbar',
                'Rachmat Pangestu Gunawan',
                'Andi Baskara Zuhri',
                'Salsa Kartikasari Salsabila',
                'Laila Ayu Putri',
                'Cahyo Pamungkas Wijaya',
                'Aldo Jayadi Rahardian',
                'Nadia Kartikasari Putri',
                'Sari Kurniasari Lestari',
                'Bianca Maulida Hapsari',
                'Erlangga Syahputra Nasution',
                'Amira Azzahra Ramadhana',
                'Aisyah Ramadhani Suryani',
                'Yuni Widyaningrum Maulida',
                'Aqila Aulia Maharani',
                'Febri Hakim Pangestu',
                'Pranata Laksono Anugrah',
                'Fania Yuliani Fadhilah',
                'Rendi Pratama Anugrah',
                'Lukman Utama Permana',
                'Dion Mulyadi Adiputra',
                'Sandy Lesmana Kusuma',
                'Damar Hakim Utomo',
                'Arif Maulana Rasyid',
                'Dimas Ardhana Basuki',
                'Taufik Pratama Syahbani',
                'Citra Lestari Syafira',
                'Putri Maulida Nugrahani',
                'Raisa Juwita Dwiyanti',
                'Kartika Kusumawati Sukma',
                'Satria Darmawan Wiratama',
                'Tegar Herlambang Prayoga',
                'Tito Kurniawan Fauzi',
                'Qonita Rahmawati Umarani',
                'Fitri Cahyani Nurfitriani',
                'Anwar Prakoso Cahyaputra',
                'Dian Amanda Maharani',
                'Firman Nurhadi Anugrah',
                'Karin Aulia Wardani',
                'Andi Fauzi Rabbani',
                'Pranata Fauzi Darmawan',
                'Rizal Hakim Prabowo',
                'Nabila Wulandari Ningsih',
                'Salsa Febriani Lestari',
            ],
            2024 => [
                'Reno Permana Zuhri',
                'Dian Ayu Kusumawardani',
                'Luthfi Maulana Zuhri',
                'Erlangga Rahardian Dewantara',
                'Zalfa Arianti Maulida',
                'Wulan Kusumawati Utami',
                'Eko Fauzan Susanto',
                'Yasmin Amanda Nuraini',
                'Bayu Prawira Firmansyah',
                'Naura Arianti Ariyanti',
                'Aldo Azzam Lesmana',
                'Niko Ramdani Syahbani',
                'Qonita Oktaviani Hasanah',
                'Elsa Fitriana Ningsih',
                'Aldo Ardhana Amin',
                'Malik Permana Maulana',
                'Iqbal Lesmana Pratama',
                'Riska Prameswari Maulida',
                'Ari Cahyadi Prakoso',
                'Qonita Yuliani Anggraini',
                'Usman Kusuma Febriansyah',
                'Yusuf Fauzan Kusuma',
                'Syifa Rahmawati Wulandari',
                'Icha Pertiwi Pangestika',
                'Vito Utama Julianto',
                'Tegar Baskara Prayoga',
                'Prita Puspita Puspitasari',
                'Salman Aditya Gustian',
                'Farhan Utama Ramadhani',
                'Hendra Ardiansyah Syahputra',
                'Aqila Fitriana Wahyuni',
                'Kresna Jatmiko Harimurti',
                'Tito Wibowo Herlambang',
                'Kevin Baskara Syahbani',
                'Rendi Utama Adiputra',
                'Karin Azzahra Salsabila',
                'Andi Pangestu Permana',
                'Indah Hasanah Nurhaliza',
                'Anisa Wulandari Suryani',
                'Sandy Pratama Rasyid',
                'Rendi Jayadi Yulianto',
                'Nanda Erlangga Effendi',
                'Zahra Kartikasari Syafira',
                'Yogi Setiawan Zuhri',
                'Intan Widyaningrum Salsabila',
                'Karin Kusumawati Kencana',
                'Dinda Safitri Permatasari',
                'Ayu Arianti Oktavia',
                'Indah Permatasari Safitri',
                'Bima Pamungkas Effendi',
                'Vito Nugraha Yulianto',
                'Hafiz Prasetyo Cahyono',
                'Raisa Fitriana Fadhilah',
                'Hendra Pradana Basuki',
                'Ridho Utama Maheswara',
                'Panji Wahyudi Zuhri',
                'Laila Melati Safitri',
                'Fajar Jatmiko Gunawan',
                'Wulan Rahmawati Nurhaliza',
                'Bianca Kirana Maulida',
                'Luthfi Febrianto Wisesa',
                'Vito Purnama Rabbani',
                'Farhan Purnama Kurnia',
                'Erlangga Wahyudi Susanto',
                'Joko Saputra Yulianto',
                'Usman Purnama Kusuma',
                'Arif Pangestu Yudhistira',
                'Zalfa Lestari Oktavia',
                'Riska Hasanah Aprilia',
                'Reza Mahardika Anugrah',
                'Ratna Widyaningrum Wahyuni',
                'Livia Melati Aprilia',
                'Vina Utami Jayanti',
                'Firman Nugroho Harimurti',
                'Usman Kurniawan Prawira',
                'Fathin Kusuma Yudhistira',
                'Prita Permatasari Maharani',
                'Yazid Kencana Rahman',
                'Adit Setiawan Pamungkas',
                'Dani Kencana Putra',
                'Aulia Zahira Ariyanti',
                'Laras Novitasari Putri',
                'Cahyo Hidayat Maheswara',
                'Syifa Febriani Dwiyanti',
                'Yogi Hakim Permana',
                'Haris Irawan Anugrah',
                'Dimas Baskara Amin',
                'Bella Kusumawati Nurfitriani',
                'Zaskia Wulandari Rachmawati',
                'Ayu Indriani Salsabila',
                'Malik Erlangga Putra',
                'Hanan Saputra Triyono',
                'Dika Lesmana Kurnia',
                'Delia Melati Mulyani',
                'Naufal Saputra Cahyaputra',
                'Zahra Safitri Nurhaliza',
                'Vina Damayanti Indriani',
                'Nisa Permatasari Nurhaliza',
                'Elsa Ramadhani Syafira',
                'Satria Wahyudi Adiputra',
                'Bella Kusumawati Permatasari',
                'Yuni Rahmawati Oktavia',
                'Maya Sekar Aprilia',
                'Rian Ramadhan Adiputra',
                'Firman Febrianto Gunawan',
                'Elvira Juwita Safitri',
                'Dewi Damayanti Salsabila',
                'Ulfa Arianti Puspitasari',
                'Rachmat Permana Gumilang',
                'Zidan Cahyadi Rahardian',
                'Taufik Lesmana Fauzi',
                'Kirana Puspita Rachmawati',
                'Icha Cahyani Nurhaliza',
                'Hanan Kencana Pratama',
                'Fahri Wahyudi Cahyaputra',
                'Sari Juwita Sukma',
                'Eko Jayadi Kartono',
                'Budi Wirawan Hidayat',
                'Damar Nugroho Tanjung',
                'Panji Kencana Hartanto',
                'Satria Darmawan Ardiansyah',
                'Prita Aulia Mulyani',
                'Laras Amanda Widyastuti',
            ],
            2022 => [
                'Raka Kencana Yudhistira',
                'Naufal Nugroho Harimurti',
                'Kirana Amalia Ramadhana',
                'Tiara Hasanah Nurfitriani',
                'Sinta Oktaviani Wulandari',
                'Aurel Damayanti Suryani',
                'Rizky Prawira Rahman',
                'Yazid Nugraha Pamungkas',
                'Panji Ramadhan Alamsyah',
                'Syifa Novitasari Rahayu',
                'Olivia Ayu Jayanti',
                'Kresna Saputra Iskandar',
                'Irvan Fauzan Firmansyah',
                'Dion Kurniawan Gustian',
                'Dedi Ardhana Febriansyah',
                'Rangga Hidayat Gumilang',
                'Nabila Handayani Azzahra',
                'Anisa Febriani Virdhani',
                'Eko Kurniawan Wijaya',
                'Salsa Widyaningrum Pertiwi',
                'Aulia Indriani Kencana',
                'Dewi Febriani Utami',
                'Yudha Febrianto Wicaksana',
                'Hafiz Jayadi Jatmika',
                'Rian Darmawan Fadillah',
                'Rachmat Kurniawan Mahendra',
                'Yoga Ananda Fauzi',
                'Laila Febriani Hasanah',
                'Nadira Prameswari Sukma',
                'Luthfi Febrianto Prakoso',
                'Gilang Prakoso Susanto',
                'Livia Yuliani Kurniawati',
                'Dani Hanif Adinata',
                'Hanifah Fitriana Ningsih',
                'Dika Irawan Wibowo',
                'Chika Anggraini Widyastuti',
                'Elvira Aulia Wahyuni',
                'Elvira Arianti Virdhani',
                'Joko Wijaya Akbar',
                'Riska Arianti Ramadhana',
                'Fitri Aulia Suryani',
                'Indah Damayanti Mardhiyah',
                'Salma Yuliani Hasanah',
                'Aurel Fitriana Umarani',
                'Dian Pertiwi Ariyanti',
                'Gita Melati Kusumawardani',
                'Delia Ramadhani Dwiyanti',
                'Nadia Wulandari Hasanah',
                'Salma Cahyani Nugrahani',
                'Tasya Anggraini Oktavia',
                'Reza Irawan Fauzi',
                'Icha Yuliani Mulyani',
                'Rizky Nurhadi Susanto',
                'Ulfa Indriani Ramadhana',
                'Elsa Utami Virdhani',
                'Hendra Mahardika Putra',
                'Hana Anggraini Umarani',
                'Malik Wirawan Darmawan',
                'Rizky Maulana Cahyaputra',
                'Cahyo Nugraha Sutanto',
                'Tegar Mulyadi Santoso',
                'Anisa Hapsari Widyastuti',
                'Olivia Ramadhani Safitri',
                'Pranata Prakoso Hidayat',
                'Rania Maulida Pangestika',
                'Galang Hidayatullah Gunawan',
                'Galang Febrianto Halim',
                'Nadira Hapsari Indriani',
                'Intan Azzahra Rahayu',
                'Aldi Aditama Mahendra',
                'Farhan Fauzan Fadhli',
                'Aisyah Novitasari Nurfitriani',
                'Bambang Hidayat Julianto',
            ],
        ];

        $semuaNamaWarga = array_merge(...array_values($namaWargaPerAngkatan));

        if (count($semuaNamaWarga) !== count(array_unique($semuaNamaWarga))) {
            throw new \RuntimeException('Seeder warga gagal: terdapat nama warga yang duplikat.');
        }

        foreach ($jumlahWargaPerAngkatan as $angkatan => $jumlahWarga) {
            $namaWargaAngkatan = $namaWargaPerAngkatan[$angkatan] ?? [];

            if (count($namaWargaAngkatan) !== $jumlahWarga) {
                throw new \RuntimeException('Seeder warga gagal: jumlah nama warga angkatan ' . $angkatan . ' tidak sesuai.');
            }

            $angkatanSuffix = substr((string) $angkatan, -2);

            for ($nomorUrut = 1; $nomorUrut <= $jumlahWarga; $nomorUrut++) {
                $nrpSuffix = str_pad((string) $nomorUrut, 3, '0', STR_PAD_LEFT);
                $nrp = '5027' . $angkatanSuffix . '1' . $nrpSuffix;

                $wargas[] = Warga::create([
                    'nama' => $namaWargaAngkatan[$nomorUrut - 1],
                    'nrp' => $nrp,
                    'angkatan' => $angkatan,
                    'password' => $nrp,
                    'status' => 'active',
                ]);
            }
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
        | 10. MONGO LOGS - masing-masing minimal 20 data
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
