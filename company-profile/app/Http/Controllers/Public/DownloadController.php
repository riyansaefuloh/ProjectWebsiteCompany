<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Download;
use App\Models\Inquiry;
use App\Services\PdfCatalogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class DownloadController extends Controller
{
    /**
     * Download Katalog PDF Dinamis langsung.
     */
    public function downloadCatalog(PdfCatalogService $pdfService): Response
    {
        return $pdfService->generateCatalogPdf();
    }

    /**
     * Download File Brosur/Dokumen dengan Lead Capture Gate (Opsional Email).
     */
    public function downloadFile(Request $request, Download $download)
    {
        // 1. Jika file butuh email (require_email = true) dan email belum diisi di request
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
