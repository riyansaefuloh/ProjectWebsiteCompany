<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Download;
use App\Models\Inquiry;
use App\Models\Product;
use App\Services\PdfCatalogService;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class DownloadController extends Controller
{
    /**
     * Tampilkan form email sebelum download katalog (GET).
     */
    public function showCatalogForm()
    {
        $appName = config('app.name');

        SEOMeta::setTitle(__('site.offline_catalog') . ' - ' . $appName);
        SEOMeta::setDescription(__('site.offline_catalog_sub'));
        SEOMeta::setCanonical(route('download.catalog.form'));

        OpenGraph::setTitle(__('site.offline_catalog') . ' - ' . $appName);
        OpenGraph::setDescription(__('site.offline_catalog_sub'));
        OpenGraph::setUrl(route('download.catalog.form'));
        OpenGraph::setType('website');

        TwitterCard::setTitle(__('site.offline_catalog') . ' - ' . $appName);
        TwitterCard::setDescription(__('site.offline_catalog_sub'));

        return view('public.download-catalog', [
            'productCount'  => Product::where('status', 'published')->count(),
            'categoryCount' => Category::where('status', 'active')->count(),
        ]);
    }

    public function downloadCatalog(Request $request, PdfCatalogService $pdfService): Response
    {
        // 1. Validasi email (Lead Capture Gate — PRD Bab 7.9)
        $request->validate([
            'email' => 'required|email|max:150',
        ]);

        // 2. Simpan lead buyer ke tabel inquiries
        Inquiry::create([
            'name'         => 'Catalog Lead',
            'company'      => 'Unknown',
            'email'        => $request->input('email'),
            'country_code' => 'ID',
            'message'      => 'Downloaded Export Product Catalog PDF',
            'status'       => 'new',
            'ip_address'   => $request->ip(),
        ]);

        // 3. Generate dan return PDF katalog
        return $pdfService->generateCatalogPdf();
    }

    /**
     * Download File Brosur/Dokumen dengan Lead Capture Gate (Opsional Email).
     */
    public function downloadFile(Request $request, Download $download)
    {
        if ($download->require_email && !$request->has('email')) {
            $request->validate([
                'email' => 'required|email',
                'name'  => 'nullable|string|max:100',
            ]);
        }

        // 2. Simpan lead buyer jika email dikirimkan
        if ($request->filled('email')) {
            Inquiry::create([
                'name'         => $request->input('name', 'Download Lead'),
                'company'      => 'Download Lead Gate',
                'email'        => $request->input('email'),
                'country_code' => 'US',
                'message'      => "Downloaded file: {$download->title}",
                'status'       => 'new',
                'ip_address'   => $request->ip(),
            ]);
        }

        // 3. Increment statistik download_count
        $download->increment('download_count');

        // 4. Download file dari Storage
        if (Storage::disk('public')->exists($download->file_path)) {
            return Storage::disk('public')->download($download->file_path, $download->title . '.pdf');
        }

        return back()->with('error', 'File brochure not found.');
    }
}
