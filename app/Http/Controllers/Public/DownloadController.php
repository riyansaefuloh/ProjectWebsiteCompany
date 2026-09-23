<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Download;
use App\Models\Inquiry;
use App\Services\PdfCatalogService;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class DownloadController extends Controller
{
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

        return view('public.download-catalog');
    }

    public function downloadCatalog(Request $request, PdfCatalogService $pdfService): Response
    {
        $request->validate([
            'email' => 'required|email|max:150',
        ]);

        Inquiry::create([
            'name'         => 'Catalog Lead',
            'company'      => 'Unknown',
            'email'        => $request->input('email'),
            'country_code' => 'ID',
            'message'      => 'Downloaded Export Product Catalog PDF',
            'status'       => 'new',
            'ip_address'   => $request->ip(),
        ]);

        return $pdfService->generateCatalogPdf();
    }

    public function downloadFile(Request $request, Download $download)
    {
        if ($download->require_email && !$request->has('email')) {
            $request->validate([
                'email' => 'required|email',
                'name'  => 'nullable|string|max:100',
            ]);
        }

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

        $download->increment('download_count');

        if (Storage::disk('public')->exists($download->file_path)) {
            return Storage::disk('public')->download($download->file_path, $download->title . '.pdf');
        }

        return back()->with('error', 'File brochure not found.');
    }
}
