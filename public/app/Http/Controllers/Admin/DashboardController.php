<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\SiteVisit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // =============================================
        // 1. STATISTIK INQUIRY (PRD Bab 8.1)
        // =============================================
        /*
         * Satu hitungan saja: jumlah sepanjang waktu, untuk baris keterangan
         * kecil di kartu ringkasan. Angka BULANANNYA tidak dihitung di sini —
         * ia sudah tersedia di $chartMonthData di bawah tanpa kueri tambahan.
         *
         * Lima hitungan per-status yang dulu ada — baru, diproses, ditawar,
         * selesai, ditolak — dibuang bersama lima kartu ringkasannya, dan tidak
         * dikembalikan: pertanyaan "inquiry sedang di tahap mana" dijawab jauh
         * lebih tepat oleh tabel Inquiry terbaru di bawah.
         *
         * Jumlah inquiry baru untuk lencana lonceng dihitung sendiri di
         * layouts/app.blade.php, jadi ia tidak bergantung pada yang di sini.
         */
        $totalInquiries = Inquiry::count();

        /*
         * 5 Inquiry terbaru untuk ditampilkan di tabel ringkasan.
         *
         * assignedSales IKUT dimuat di muka. Sebelumnya hanya 'product' yang
         * dimuat, sementara kolom "Ditangani" memanggil $inquiry->assignedSales
         * di setiap baris — satu kueri tambahan untuk SETIAP baris yang sudah
         * ditugaskan. Selama hampir tidak ada inquiry yang ditugaskan, gejalanya
         * tidak terlihat; ia baru muncul persis ketika timnya mulai bekerja
         * dengan benar, yaitu saat paling tidak enak untuk ditemukan.
         */
        $latestInquiries = Inquiry::with(['product', 'assignedSales'])
            ->latest()
            ->limit(5)
            ->get();

        // =============================================
        // 2. STATISTIK PRODUK (PRD Bab 8.1)
        // =============================================
        $totalProductsAll = Product::count();
        $totalProducts    = Product::where('status', 'published')->count();
        $draftProducts    = Product::where('status', 'draft')->count();
        $featuredProducts = Product::where('is_featured', true)->count();

        // =============================================
        // 3b. KUNJUNGAN SITUS PUBLIK
        // =============================================
        /*
         * Yang dihitung BARISNYA — satu baris per halaman yang dibuka, jadi
         * angkanya kunjungan halaman. Kolom `visitor` ada untuk memisahkan
         * "seribu halaman dibuka satu orang" dari "seribu orang membuka satu
         * halaman", tapi itu pertanyaan yang berbeda dan belum ditanyakan.
         *
         * DUA BULAN diambil sekaligus dalam satu kueri lalu dipisah di PHP —
         * dua jendela yang bersambungan tidak perlu menempuh indeks yang sama
         * dua kali. Dikelompokkan di PHP, bukan lewat fungsi tanggal basis
         * data: fungsinya berbeda-beda antar mesin, dan jendelanya sudah
         * dibatasi dua bulan sehingga barisnya tidak pernah banyak. Alasan yang
         * sama dipakai grafik inquiry per bulan di bawah.
         */
        $totalVisits = SiteVisit::count();

        $awalBulanIni  = Carbon::now()->startOfMonth();
        $awalBulanLalu = $awalBulanIni->copy()->subMonth();

        $kunjunganPerBulan = SiteVisit::where('visited_at', '>=', $awalBulanLalu)
            ->pluck('visited_at')
            ->groupBy(fn ($waktu) => Carbon::parse($waktu)->format('Y-m'))
            ->map->count();

        $visitsThisMonth = (int) ($kunjunganPerBulan[$awalBulanIni->format('Y-m')] ?? 0);
        $visitsLastMonth = (int) ($kunjunganPerBulan[$awalBulanLalu->format('Y-m')] ?? 0);

        // =============================================
        // 4. ⚠️ ALERT SERTIFIKAT KEDALUWARSA (PRD Bab 8.4)
        // =============================================
        // 'media' ikut dimuat di muka sejak kartu peringatan di dasbor
        // menampilkan logo tiap sertifikasi. Tanpa ini, getFirstMedia('logos')
        // menembak database sekali untuk SETIAP baris — sepuluh sertifikasi
        // yang perlu diurus jadi sepuluh kueri tambahan di setiap kali dasbor
        // dibuka, dan jumlahnya tumbuh seiring bertambahnya sertifikasi.
        $expiredCerts = Certification::with(['translations', 'media'])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', Carbon::now())
            ->where('status', 'active')
            ->orderBy('expires_at', 'asc')
            ->get();

        $expiringSoonCerts = Certification::with(['translations', 'media'])
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [Carbon::now(), Carbon::now()->addDays(90)])
            ->orderBy('expires_at', 'asc')
            ->get();

        // =============================================
        // 5. 📊 GRAFIK INQUIRY PER BULAN (PRD Bab 8.1)
        // Data 12 bulan terakhir untuk line/bar chart
        // =============================================
        /*
         * startOfMonth() DULU, baru subMonths(11) — urutannya penting.
         *
         * Terbalik, pengurangannya berjalan di tanggal hari ini. Pada tanggal
         * 31, "31 Agustus 2026 dikurangi 11 bulan" jatuh ke 31 September 2025,
         * tanggal yang tidak ada, dan Carbon melubernya jadi 1 Oktober 2025.
         * Jendelanya bergeser sebulan: September 2025 hilang dari grafik, dan
         * September 2026 — bulan yang BELUM TERJADI — ikut masuk sebagai nol.
         *
         * Yang lebih merugikan, kartu "Inquiry bulan ini" membaca petak
         * terakhir jendela ini. Petak itu jadi bulan depan, jadi angkanya
         * selalu 0 di tiap tanggal 31 — dan "vs bulan lalu" membandingkan
         * bulan ini dengan bulan lalu yang keliru.
         */
        $awal = Carbon::now()->startOfMonth()->subMonths(11);

        /*
         * Dikelompokkan lewat PHP, bukan TO_CHAR di basis data.
         *
         * TO_CHAR hanya ada di PostgreSQL — kueri ini dulu mati begitu
         * dijalankan di sqlite, dan itu salah satu sebab uji dasbor tidak
         * pernah bisa jalan. Jumlah inquiry setahun terlalu sedikit untuk
         * membuat pengelompokan di PHP jadi soal.
         */
        $hitungan = Inquiry::where('created_at', '>=', $awal)
            ->pluck('created_at')
            ->groupBy(fn ($tanggal) => Carbon::parse($tanggal)->format('Y-m'))
            ->map->count();

        /*
         * Duabelas bulan digambar SEMUANYA, termasuk yang nol.
         *
         * Sebelumnya hanya bulan yang ada inquiry-nya yang masuk. Bulan kosong
         * tidak muncul sebagai nol — ia lenyap dari sumbunya, sehingga garisnya
         * menyambungkan Maret langsung ke Agustus seolah keduanya bersebelahan.
         * Trennya jadi terbaca naik padahal datar.
         */
        $chartMonthLabels = [];
        $chartMonthData   = [];

        for ($i = 0; $i < 12; $i++) {
            $bulan = $awal->copy()->addMonths($i);

            $chartMonthLabels[] = $bulan->format('M Y');       // contoh: "Aug 2026"
            $chartMonthData[]   = (int) ($hitungan[$bulan->format('Y-m')] ?? 0);
        }

        // =============================================
        // 6. 🌍 GRAFIK INQUIRY PER NEGARA (PRD Bab 8.1)
        // Top 10 negara pengirim inquiry
        // =============================================
        $inquiryPerCountry = Inquiry::select(
                'country_code',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('country_code')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Format untuk Chart.js: { labels: [...], data: [...] }
        $chartCountryLabels = $inquiryPerCountry->pluck('country_code')
            ->map(fn($code) => strtoupper($code))
            ->toArray();
        $chartCountryData = $inquiryPerCountry->pluck('total')->toArray();

        return view('admin.dashboard', compact(
            // Inquiry stats
            'totalInquiries',
            'latestInquiries',
            // Product stats
            'totalProductsAll',
            'totalProducts',
            'draftProducts',
            'featuredProducts',
            // Kunjungan situs publik
            'totalVisits',
            'visitsThisMonth',
            'visitsLastMonth',
            // Certification alerts
            'expiredCerts',
            'expiringSoonCerts',
            // Chart data — inquiry per bulan
            'chartMonthLabels',
            'chartMonthData',
            // Chart data — inquiry per negara
            'chartCountryLabels',
            'chartCountryData',
        ));
    }
}
