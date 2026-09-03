<?php

namespace Database\Seeders;

use App\Models\Inquiry;
use App\Models\SiteVisit;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Data contoh BULAN LALU, semata untuk melihat kartu ringkasan dasbor.
 *
 * Tiga kartu ringkasan menampilkan perubahan bulan ini terhadap bulan lalu.
 * Selama bulan lalu masih kosong, persentasenya tidak bisa dihitung — naik dari
 * nol ke berapa pun adalah pembagian dengan nol, bukan "naik seratus persen" —
 * sehingga kartunya menggambar garis pendek dan bentuk jadinya tidak pernah
 * terlihat. Seeder ini mengisi kekosongan itu.
 *
 * BUKAN bagian dari DatabaseSeeder, dan sengaja tidak didaftarkan di sana:
 * ia dijalankan sendiri saat dibutuhkan, tidak ikut terbawa tiap kali basis
 * data disemai ulang.
 *
 *     php artisan db:seed --class=ContohDataDasborSeeder
 *
 * Semua barisnya bisa dikenali dan dihapus kembali. Inquiry-nya memakai surel
 * berdomain @contoh.test — TLD yang dicadangkan dan tidak mungkin jadi alamat
 * sungguhan — dan kunjungannya bertanda pengunjung yang berawalan 'contoh'.
 *
 *     php artisan tinker
 *     >>> App\Models\Inquiry::where('email','like','%@contoh.test')->delete();
 *     >>> App\Models\SiteVisit::where('visitor','like','contoh%')->delete();
 *
 * Angkanya sengaja dipilih supaya KEDUA arah tren terlihat sekaligus: inquiry
 * bulan lalu dibuat lebih banyak dari bulan ini (persentase turun, merah), dan
 * kunjungan bulan lalu lebih sedikit (persentase naik, hijau). Satu kali muat
 * halaman cukup untuk menilai kedua keadaannya.
 */
class ContohDataDasborSeeder extends Seeder
{
    public function run(): void
    {
        $bulanLalu = Carbon::now()->startOfMonth()->subMonth();

        $this->inquiryBulanLalu($bulanLalu);
        $this->kunjunganBulanLalu($bulanLalu);
    }

    /**
     * Delapan inquiry tersebar di sepanjang bulan lalu.
     *
     * Tanggalnya disebar, bukan ditumpuk di satu hari: grafik "Inquiry per
     * bulan" mengelompokkan per bulan sehingga tidak peduli, tapi tabel
     * "Inquiry terbaru" mengurutkannya per tanggal — dan delapan baris bertanggal
     * sama akan berurutan acak di sana.
     */
    private function inquiryBulanLalu(Carbon $bulanLalu): void
    {
        $contoh = [
            ['Aiko Tanaka',     'Tanaka Trading Co.',    'JP', 'Mencari arabika Gayo grade 1, kontrak tahunan.'],
            ['Mark Doyle',      'Northbrook Coffee',     'AU', 'Butuh sampel robusta untuk uji cupping.'],
            ['Sofia Rossi',     'Caffe Rossi SRL',       'IT', 'Menanyakan ketersediaan dan harga FOB.'],
            ['Chen Wei',        'Golden Bean Ltd.',      'CN', 'Permintaan penawaran satu kontainer.'],
            ['Lukas Meyer',     'Meyer Rösterei',        'DE', 'Tertarik lot bersertifikat organik.'],
            ['Amir Haddad',     'Levant Importers',      'AE', 'Menanyakan jadwal pengiriman kuartal depan.'],
            ['Nadia Petrova',   'Volga Coffee House',    'RU', 'Butuh spesifikasi dan kadar air.'],
            ['James Okoro',     'Lagos Roastery',        'NG', 'Menanyakan MOQ dan syarat pembayaran.'],
        ];

        $status = ['new', 'processing', 'quoted', 'closed'];

        foreach ($contoh as $i => [$nama, $perusahaan, $negara, $pesan]) {
            $waktu = $bulanLalu->copy()
                ->addDays(2 + $i * 3)
                ->setTime(9 + ($i % 8), ($i * 7) % 60);

            $inquiry = Inquiry::create([
                'name'         => $nama,
                'company'      => $perusahaan,
                'email'        => str($nama)->slug('.') . '@contoh.test',
                'country_code' => $negara,
                'message'      => $pesan,
                'status'       => $status[$i % count($status)],
                'ip_address'   => '203.0.113.' . (10 + $i),   // blok TEST-NET-3, RFC 5737
            ]);

            /*
             * created_at disetel SESUDAH create(), bukan lewat mass assignment:
             * kolom itu tidak ada di $fillable, dan Eloquent akan menimpanya
             * dengan waktu sekarang saat barisnya dibuat.
             */
            $inquiry->forceFill([
                'created_at' => $waktu,
                'updated_at' => $waktu,
            ])->saveQuietly();
        }
    }

    /**
     * Tiga puluh kunjungan tersebar di sepanjang bulan lalu.
     *
     * Jumlahnya sengaja lebih SEDIKIT dari kunjungan bulan berjalan, supaya
     * persentase kartunya naik — pasangan dari inquiry yang dibuat turun.
     */
    private function kunjunganBulanLalu(Carbon $bulanLalu): void
    {
        $halaman = [
            '', 'products', 'products/kopi-arabika-gayo', 'about',
            'certifications', 'export-markets', 'news', 'inquiry',
        ];

        $hariDalamBulan = $bulanLalu->daysInMonth;

        for ($i = 0; $i < 30; $i++) {
            SiteVisit::create([
                'path'    => $halaman[$i % count($halaman)],

                /*
                 * Berawalan 'contoh' supaya barisnya bisa ditemukan kembali
                 * untuk dihapus. Panjangnya tetap 64 aksara, sama dengan sidik
                 * jari sungguhan yang dihasilkan RecordSiteVisit.
                 */
                'visitor' => str_pad('contoh' . $i, 64, '0'),

                'visited_at' => $bulanLalu->copy()
                    ->addDays($i % $hariDalamBulan)
                    ->setTime(8 + ($i % 12), ($i * 11) % 60),
            ]);
        }
    }
}
