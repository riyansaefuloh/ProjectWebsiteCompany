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
     * Tampilkan form email sebelum download katalog (GET).
     */
    public function showCatalogForm()
    {
        return response('<html><body style="font-family:sans-serif;max-width:400px;margin:60px auto;padding:20px">
            <h2>Download Export Product Catalog</h2>
            <p>Masukkan email Anda untuk mengunduh katalog produk kami.</p>
            <form method="POST" action="' . route('download.catalog') . '">
                ' . csrf_field() . '
                <label>Email <span style="color:red">*</span></label><br>
                <input type="email" name="email" required 
                    style="width:100%;padding:8px;margin:8px 0 16px;border:1px solid #ccc;border-radius:4px">
                <button type="submit" 
                    style="background:#2563eb;color:white;padding:10px 20px;border:none;border-radius:4px;cursor:pointer;width:100%">
                    📥 Download Katalog PDF
                </button>
            </form>
        </body></html>', 200, ['Content-Type' => 'text/html']);
    }

    /**
     * Download Katalog PDF Dinamis dengan Lead Capture Gate.
     * PRD Bab 7.9: "Buyer memasukkan email sebelum mengunduh katalog"
     */
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
