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
        $inquiryPerMonth = Inquiry::select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month"),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy(DB::raw("TO_CHAR(created_at, 'YYYY-MM')"))
            ->orderBy('month', 'asc')
            ->get();

        // Format untuk Chart.js: { labels: [...], data: [...] }
        $chartMonthLabels = $inquiryPerMonth->pluck('month')->map(function ($m) {
            return Carbon::createFromFormat('Y-m', $m)->format('M Y'); // contoh: "Aug 2026"
        })->toArray();
        $chartMonthData = $inquiryPerMonth->pluck('total')->toArray();

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
