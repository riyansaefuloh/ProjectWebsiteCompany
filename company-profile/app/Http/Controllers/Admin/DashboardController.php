<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use App\Models\Inquiry;
use App\Models\News;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // =============================================
        // 1. STATISTIK INQUIRY (PRD Bab 8.1)
        // =============================================
        $totalInquiries       = Inquiry::count();
        $newInquiries         = Inquiry::where('status', 'new')->count();
        $processingInquiries  = Inquiry::where('status', 'processing')->count();
        $quotedInquiries      = Inquiry::where('status', 'quoted')->count();
        $closedInquiries      = Inquiry::where('status', 'closed')->count();
        $rejectedInquiries    = Inquiry::where('status', 'rejected')->count();

        // 5 Inquiry terbaru untuk ditampilkan di tabel ringkasan
        $latestInquiries = Inquiry::with('product')
            ->latest()
            ->limit(5)
            ->get();

        // =============================================
        // 2. STATISTIK PRODUK (PRD Bab 8.1)
        // =============================================
        $totalProducts    = Product::where('status', 'published')->count();
        $draftProducts    = Product::where('status', 'draft')->count();
        $featuredProducts = Product::where('is_featured', true)->count();

        // =============================================
        // 3. STATISTIK KONTEN
        // =============================================
        $totalNews = News::where('status', 'published')->count();

        // =============================================
        // 4. ⚠️ ALERT SERTIFIKAT KEDALUWARSA (PRD Bab 8.4)
        // =============================================
        $expiredCerts = Certification::with('translations')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', Carbon::now())
            ->where('status', 'active')
            ->orderBy('expires_at', 'asc')
            ->get();

        $expiringSoonCerts = Certification::with('translations')
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [Carbon::now(), Carbon::now()->addDays(90)])
            ->orderBy('expires_at', 'asc')
            ->get();

        // =============================================
        // 5. 📊 GRAFIK INQUIRY PER BULAN (PRD Bab 8.1)
        // Data 12 bulan terakhir untuk line/bar chart
        // =============================================
        $awal = Carbon::now()->subMonths(11)->startOfMonth();

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
            'newInquiries',
            'processingInquiries',
            'quotedInquiries',
            'closedInquiries',
            'rejectedInquiries',
            'latestInquiries',
            // Product stats
            'totalProducts',
            'draftProducts',
            'featuredProducts',
            // Content stats
            'totalNews',
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
