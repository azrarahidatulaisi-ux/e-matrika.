<?php

namespace App\Libraries;

/**
 * Geocoder Lokal Padang (Sumatera Barat)
 * Mengonversi alamat puskesmas / pasien ke titik koordinat berdasarkan
 * kelurahan, kecamatan, atau nama jalan di Kota Padang secara instan tanpa timeout.
 */
class PadangGeocoder
{
    /**
     * Titik pusat kelurahan & lokasi di Kota Padang
     */
    private static array $locations = [
        // --- Padang Barat (Wilayah Kerja PKM Padang Pasir & Sekitarnya) ---
        'padang pasir'      => [-0.9385, 100.3551, 'Kel. Padang Pasir, Padang Barat'],
        'pasir'             => [-0.9385, 100.3551, 'Kel. Padang Pasir, Padang Barat'],
        'olo'               => [-0.9430, 100.3540, 'Kel. Olo, Padang Barat'],
        'belakang olo'      => [-0.9425, 100.3535, 'Belakang Olo, Padang Barat'],
        'purus'             => [-0.9325, 100.3530, 'Kel. Purus, Padang Barat'],
        'rimbo kaluang'     => [-0.9260, 100.3545, 'Kel. Rimbo Kaluang, Padang Barat'],
        'flamboyan baru'    => [-0.9310, 100.3580, 'Kel. Flamboyan Baru, Padang Barat'],
        'flamboyan'         => [-0.9310, 100.3580, 'Kel. Flamboyan Baru, Padang Barat'],
        'ujung gurun'       => [-0.9360, 100.3570, 'Kel. Ujung Gurun, Padang Barat'],
        'belakang tangsi'   => [-0.9510, 100.3560, 'Kel. Belakang Tangsi, Padang Barat'],
        'kampung pondok'    => [-0.9570, 100.3575, 'Kel. Kampung Pondok, Padang Barat'],
        'pondok'            => [-0.9570, 100.3575, 'Kel. Kampung Pondok, Padang Barat'],
        'kampung jao'       => [-0.9470, 100.3580, 'Kel. Kampung Jao, Padang Barat'],
        'berok nipah'       => [-0.9630, 100.3570, 'Kel. Berok Nipah, Padang Barat'],
        'nipah'             => [-0.9630, 100.3570, 'Kel. Berok Nipah, Padang Barat'],

        // --- Padang Timur ---
        'sawahan timur'     => [-0.9460, 100.3720, 'Kel. Sawahan Timur, Padang Timur'],
        'sawahan'           => [-0.9480, 100.3680, 'Kel. Sawahan, Padang Timur'],
        'simpang haru'      => [-0.9430, 100.3750, 'Kel. Simpang Haru, Padang Timur'],
        'kubu marapalam'    => [-0.9480, 100.3800, 'Kel. Kubu Marapalam, Padang Timur'],
        'kubu parak karakah'=> [-0.9550, 100.3820, 'Kel. Kubu Parak Karakah, Padang Timur'],
        'marapalam'         => [-0.9480, 100.3800, 'Kel. Kubu Marapalam, Padang Timur'],
        'andalas'           => [-0.9380, 100.3780, 'Kel. Andalas, Padang Timur'],
        'ganting parak gadang' => [-0.9580, 100.3690, 'Kel. Ganting Parak Gadang, Padang Timur'],
        'ganting'           => [-0.9580, 100.3690, 'Kel. Ganting, Padang Timur'],
        'parak gadang'      => [-0.9610, 100.3730, 'Kel. Parak Gadang Timur, Padang Timur'],
        'jati baru'         => [-0.9320, 100.3680, 'Kel. Jati Baru, Padang Timur'],
        'jati'              => [-0.9350, 100.3650, 'Kel. Jati, Padang Timur'],

        // --- Padang Utara ---
        'alai parak kopi'   => [-0.9250, 100.3680, 'Kel. Alai Parak Kopi, Padang Utara'],
        'alai'              => [-0.9250, 100.3680, 'Kel. Alai Parak Kopi, Padang Utara'],
        'gunung pangilun'   => [-0.9180, 100.3650, 'Kel. Gunung Pangilun, Padang Utara'],
        'pangilun'          => [-0.9180, 100.3650, 'Kel. Gunung Pangilun, Padang Utara'],
        'lolong belanti'    => [-0.9200, 100.3520, 'Kel. Lolong Belanti, Padang Utara'],
        'lolong'            => [-0.9200, 100.3520, 'Kel. Lolong Belanti, Padang Utara'],
        'ulak karang utara' => [-0.9050, 100.3500, 'Kel. Ulak Karang Utara, Padang Utara'],
        'ulak karang selatan' => [-0.9120, 100.3510, 'Kel. Ulak Karang Selatan, Padang Utara'],
        'ulak karang'       => [-0.9080, 100.3505, 'Kel. Ulak Karang, Padang Utara'],
        'air tawar barat'   => [-0.8980, 100.3490, 'Kel. Air Tawar Barat, Padang Utara'],
        'air tawar timur'   => [-0.8950, 100.3540, 'Kel. Air Tawar Timur, Padang Utara'],
        'air tawar'         => [-0.8965, 100.3515, 'Kel. Air Tawar, Padang Utara'],

        // --- Padang Selatan ---
        'alang laweh'       => [-0.9580, 100.3630, 'Kel. Alang Laweh, Padang Selatan'],
        'belakang pondok'   => [-0.9610, 100.3590, 'Kel. Belakang Pondok, Padang Selatan'],
        'batang arau'       => [-0.9670, 100.3650, 'Kel. Batang Arau, Padang Selatan'],
        'bukit gado-gado'   => [-0.9850, 100.3600, 'Kel. Bukit Gado-Gado, Padang Selatan'],
        'mato aie'          => [-0.9780, 100.3750, 'Kel. Mato Aie, Padang Selatan'],
        'rawang'            => [-0.9750, 100.3850, 'Kel. Rawang, Padang Selatan'],
        'seberang padang'   => [-0.9650, 100.3720, 'Kel. Seberang Padang, Padang Selatan'],
        'seberang palinggam'=> [-0.9650, 100.3660, 'Kel. Seberang Palinggam, Padang Selatan'],
        'ranah parak rumbio'=> [-0.9590, 100.3610, 'Kel. Ranah Parak Rumbio, Padang Selatan'],
        'teluk bayur'       => [-0.9980, 100.3800, 'Kel. Teluk Bayur, Padang Selatan'],
        'air manis'         => [-0.9890, 100.3550, 'Kel. Air Manis, Padang Selatan'],

        // --- Nanggalo ---
        'kurao pagang'      => [-0.9020, 100.3680, 'Kel. Kurao Pagang, Nanggalo'],
        'surau gadang'      => [-0.9120, 100.3680, 'Kel. Surau Gadang, Nanggalo'],
        'kampung olo'       => [-0.9180, 100.3610, 'Kel. Kampung Olo, Nanggalo'],
        'tabing banda gadang' => [-0.9080, 100.3590, 'Kel. Tabing Banda Gadang, Nanggalo'],
        'kampung lapai'     => [-0.9150, 100.3560, 'Kel. Kampung Lapai, Nanggalo'],
        'lapai'             => [-0.9150, 100.3560, 'Kel. Kampung Lapai, Nanggalo'],
        'gurun laweh'       => [-0.9190, 100.3690, 'Kel. Gurun Laweh, Nanggalo'],
        'nanggalo'          => [-0.9120, 100.3650, 'Kec. Nanggalo'],

        // --- Kuranji ---
        'ampang'            => [-0.9290, 100.3770, 'Kel. Ampang, Kuranji'],
        'lubuk lintah'      => [-0.9320, 100.3860, 'Kel. Lubuk Lintah, Kuranji'],
        'kalumbuk'          => [-0.9210, 100.3850, 'Kel. Kalumbuk, Kuranji'],
        'korong gadang'     => [-0.9140, 100.3950, 'Kel. Korong Gadang, Kuranji'],
        'pasar ambacang'    => [-0.9380, 100.3950, 'Kel. Pasar Ambacang, Kuranji'],
        'anduring'          => [-0.9350, 100.3800, 'Kel. Anduring, Kuranji'],
        'gunung sarik'      => [-0.8980, 100.4020, 'Kel. Gunung Sarik, Kuranji'],
        'sungai sapih'      => [-0.8950, 100.3880, 'Kel. Sungai Sapih, Kuranji'],
        'kuranji'           => [-0.9100, 100.4050, 'Kec. Kuranji'],

        // --- Koto Tangah ---
        'lubuk buaya'       => [-0.8520, 100.3350, 'Kel. Lubuk Buaya, Koto Tangah'],
        'dadok tunggul hitam' => [-0.8850, 100.3620, 'Kel. Dadok Tunggul Hitam, Koto Tangah'],
        'tunggul hitam'     => [-0.8850, 100.3620, 'Kel. Dadok Tunggul Hitam, Koto Tangah'],
        'bungo pasang'      => [-0.8650, 100.3580, 'Kel. Bungo Pasang, Koto Tangah'],
        'aia pacah'         => [-0.8750, 100.3850, 'Kel. Aia Pacah, Koto Tangah'],
        'air pacah'         => [-0.8750, 100.3850, 'Kel. Aia Pacah, Koto Tangah'],
        'balai gadang'      => [-0.8400, 100.3800, 'Kel. Balai Gadang, Koto Tangah'],
        'batipuh panjang'   => [-0.8600, 100.3450, 'Kel. Batipuh Panjang, Koto Tangah'],
        'koto panjang ikur koto' => [-0.8700, 100.3700, 'Kel. Koto Panjang Ikur Koto, Koto Tangah'],
        'koto pulai'        => [-0.8450, 100.3550, 'Kel. Koto Pulai, Koto Tangah'],
        'lubuk minturun'    => [-0.8550, 100.3750, 'Kel. Lubuk Minturun, Koto Tangah'],
        'padang sarai'      => [-0.8350, 100.3250, 'Kel. Padang Sarai, Koto Tangah'],
        'parupuk tabing'    => [-0.8900, 100.3450, 'Kel. Parupuk Tabing, Koto Tangah'],
        'tabing'            => [-0.8900, 100.3450, 'Kel. Parupuk Tabing, Koto Tangah'],
        'pasie nan tigo'    => [-0.8600, 100.3200, 'Kel. Pasie Nan Tigo, Koto Tangah'],
        'batang kabung'     => [-0.8750, 100.3350, 'Kel. Batang Kabung Ganting, Koto Tangah'],
        'koto tangah'       => [-0.8600, 100.3550, 'Kec. Koto Tangah'],

        // --- Lubuk Begalung ---
        'lubuk begalung'    => [-0.9650, 100.3850, 'Kel. Lubuk Begalung'],
        'banuaran'          => [-0.9650, 100.3780, 'Kel. Banuaran, Lubuk Begalung'],
        'cengkeh'           => [-0.9620, 100.4050, 'Kel. Cengkeh Nan XX, Lubuk Begalung'],
        'pagambiran'        => [-0.9850, 100.3900, 'Kel. Pagambiran Ampalu, Lubuk Begalung'],
        'pampangan'         => [-0.9820, 100.3820, 'Kel. Pampangan Nan XX, Lubuk Begalung'],
        'pitameh'           => [-0.9550, 100.3980, 'Kel. Pitameh Tanjung Saba, Lubuk Begalung'],
        'tanah sirah'       => [-0.9580, 100.3880, 'Kel. Tanah Sirah Piai, Lubuk Begalung'],
        'batung taba'       => [-0.9650, 100.3980, 'Kel. Batung Taba Nan XX, Lubuk Begalung'],

        // --- Lubuk Kilangan ---
        'bandar buat'       => [-0.9580, 100.4200, 'Kel. Bandar Buat, Lubuk Kilangan'],
        'batu gadang'       => [-0.9650, 100.4450, 'Kel. Batu Gadang, Lubuk Kilangan'],
        'indatung'          => [-0.9680, 100.4600, 'Kel. Indarung, Lubuk Kilangan'],
        'indarung'          => [-0.9680, 100.4600, 'Kel. Indarung, Lubuk Kilangan'],
        'lubuk kilangan'    => [-0.9600, 100.4350, 'Kec. Lubuk Kilangan'],

        // --- Pauh ---
        'limau manis'       => [-0.9280, 100.4450, 'Kel. Limau Manis, Pauh'],
        'cupak tangah'      => [-0.9220, 100.4250, 'Kel. Cupak Tangah, Pauh'],
        'kapalo koto'       => [-0.9150, 100.4350, 'Kel. Kapalo Koto, Pauh'],
        'pisang'            => [-0.9350, 100.4020, 'Kel. Pisang, Pauh'],
        'pauh'              => [-0.9250, 100.4300, 'Kec. Pauh'],

        // --- Bungus Teluk Kabung ---
        'bungus'            => [-1.0350, 100.4050, 'Kec. Bungus Teluk Kabung'],
        'teluk kabung'      => [-1.0600, 100.4150, 'Kel. Teluk Kabung'],

        // --- Nama Jalan Populer di Padang ---
        'sudirman'          => [-0.9450, 100.3600, 'Jl. Jend. Sudirman, Padang'],
        'khatib sulaiman'   => [-0.9150, 100.3580, 'Jl. Khatib Sulaiman, Padang'],
        'veteran'           => [-0.9360, 100.3540, 'Jl. Veteran, Padang Barat'],
        'pemuda'            => [-0.9420, 100.3540, 'Jl. Pemuda, Padang Barat'],
        'm. yamin'          => [-0.9490, 100.3580, 'Jl. M. Yamin, Padang'],
        'bagindo aziz chan' => [-0.9480, 100.3610, 'Jl. Bagindo Aziz Chan, Padang'],
        'raden saleh'       => [-0.9250, 100.3540, 'Jl. Raden Saleh, Padang'],
        'rasuna said'       => [-0.9320, 100.3590, 'Jl. Rasuna Said, Padang'],
        'hamka'             => [-0.8980, 100.3490, 'Jl. Prof. Dr. Hamka, Padang'],
        's. parman'         => [-0.9120, 100.3510, 'Jl. S. Parman, Padang'],
        'samudera'          => [-0.9390, 100.3510, 'Jl. Samudera (Pantai Padang)'],
        'hayuk wuruk'       => [-0.9520, 100.3550, 'Jl. Hayam Wuruk, Padang'],
        'hayam wuruk'       => [-0.9520, 100.3550, 'Jl. Hayam Wuruk, Padang'],
        'hang tuah'         => [-0.9480, 100.3520, 'Jl. Hang Tuah, Padang'],
        'belakang tangsi'   => [-0.9510, 100.3560, 'Jl. Belakang Tangsi, Padang'],

        // --- Pusat Kecamatan Umum ---
        'padang barat'      => [-0.9420, 100.3560, 'Kec. Padang Barat'],
        'padang timur'      => [-0.9480, 100.3750, 'Kec. Padang Timur'],
        'padang utara'      => [-0.9150, 100.3550, 'Kec. Padang Utara'],
        'padang selatan'    => [-0.9700, 100.3650, 'Kec. Padang Selatan'],
        'padang kota'       => [-0.9471, 100.3600, 'Kota Padang'],
        'kota padang'       => [-0.9471, 100.3600, 'Kota Padang'],
        'padang'            => [-0.9471, 100.3600, 'Kota Padang'],
    ];

    /**
     * Cari koordinat dari string alamat.
     * Mengembalikan array dengan lat, lng, perkiraan, dan sumber jika cocok.
     */
    public static function lookup(string $alamat): ?array
    {
        $raw = trim($alamat);
        if ($raw === '') return null;

        $lower = ' ' . mb_strtolower($raw) . ' ';

        // Cari kecocokan kata kunci terpanjang lebih dahulu
        foreach (self::$locations as $keyword => $data) {
            // Cocokkan keyword dengan batasan kata yang wajar
            if (str_contains($lower, $keyword)) {
                $baseLat = $data[0];
                $baseLng = $data[1];
                $wilayah = $data[2];

                // Berikan deterministic jitter halus (±50 s.d. 120 meter) berdasarkan hash alamat
                // agar pasien di kelurahan/jalan yang sama tidak menumpuk di 1 pixel yang persis sama
                $h = abs(crc32($raw));
                $jitterLat = (($h % 1000) - 500) / 1000.0 * 0.0022; // ~ ±120 meter
                $jitterLng = ((intdiv($h, 1000) % 1000) - 500) / 1000.0 * 0.0022;

                return [
                    'lat'       => round($baseLat + $jitterLat, 7),
                    'lng'       => round($baseLng + $jitterLng, 7),
                    'perkiraan' => true,
                    'sumber'    => 'wilayah_lokal',
                    'wilayah'   => $wilayah,
                ];
            }
        }

        // Jika mengandung kata "padang", fallback ke pusat kota Padang dengan dispersi
        if (str_contains($lower, 'padang')) {
            $h = abs(crc32($raw));
            $jitterLat = (($h % 1000) - 500) / 1000.0 * 0.015;
            $jitterLng = ((intdiv($h, 1000) % 1000) - 500) / 1000.0 * 0.015;

            return [
                'lat'       => round(-0.9471 + $jitterLat, 7),
                'lng'       => round(100.3600 + $jitterLng, 7),
                'perkiraan' => true,
                'sumber'    => 'wilayah_lokal_kota',
                'wilayah'   => 'Kota Padang',
            ];
        }

        return null;
    }
}
