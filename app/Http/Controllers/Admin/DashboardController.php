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
        // 1. STATISTIK INQUIRY
        // =============================================
        $totalInquiries = Inquiry::count();

        $latestInquiries = Inquiry::with(['product', 'assignedSales'])
            ->latest()
            ->limit(5)
            ->get();

        // =============================================
        // 2. STATISTIK PRODUK
        // =============================================
        $totalProductsAll = Product::count();
        $totalProducts    = Product::where('status', 'published')->count();
        $draftProducts    = Product::where('status', 'draft')->count();
        $featuredProducts = Product::where('is_featured', true)->count();

        // =============================================
        // 3. KUNJUNGAN SITUS PUBLIK
        // =============================================
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
        // 4. ALERT SERTIFIKAT KEDALUWARSA
        // =============================================
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
        // 5. GRAFIK INQUIRY PER BULAN
        // =============================================
        $awal = Carbon::now()->startOfMonth()->subMonths(11);

        $hitungan = Inquiry::where('created_at', '>=', $awal)
            ->pluck('created_at')
            ->groupBy(fn ($tanggal) => Carbon::parse($tanggal)->format('Y-m'))
            ->map->count();

        $chartMonthLabels = [];
        $chartMonthData   = [];

        for ($i = 0; $i < 12; $i++) {
            $bulan = $awal->copy()->addMonths($i);

            $chartMonthLabels[] = $bulan->format('M Y');
            $chartMonthData[]   = (int) ($hitungan[$bulan->format('Y-m')] ?? 0);
        }

        // =============================================
        // 6. GRAFIK INQUIRY PER NEGARA
        // =============================================
        $inquiryPerCountry = Inquiry::select(
                'country_code',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('country_code')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $chartCountryLabels = $inquiryPerCountry->pluck('country_code')
            ->map(fn($code) => strtoupper($code))
            ->toArray();
        $chartCountryData = $inquiryPerCountry->pluck('total')->toArray();

        return view('admin.dashboard', compact(
            'totalInquiries',
            'latestInquiries',
            'totalProductsAll',
            'totalProducts',
            'draftProducts',
            'featuredProducts',
            'totalVisits',
            'visitsThisMonth',
            'visitsLastMonth',
            'expiredCerts',
            'expiringSoonCerts',
            'chartMonthLabels',
            'chartMonthData',
            'chartCountryLabels',
            'chartCountryData',
        ));
    }
}
