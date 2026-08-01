<?php

namespace App\Services;

use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PdfCatalogService
{
    /**
     * Generate PDF Export Product Catalog.
     */
    public function generateCatalogPdf(?string $categoryId = null): Response
    {
        // Query produk aktif beserta relasi translasi, spesifikasi, dan sertifikasi
        $query = Product::with(['translations', 'specifications', 'certifications.translations', 'category.translations'])
            ->where('status', 'published')
            ->orderBy('sort_order', 'asc');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->get();

        // Load view PDF catalog
        $pdf = Pdf::loadView('pdf.catalog', [
            'products'    => $products,
            'companyName' => config('app.name', 'Exporter Company'),
            'date'        => date('F Y'),
        ]);

        // Atur ukuran kertas A4 portrait
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('Export_Product_Catalog_' . date('Y_m') . '.pdf');
    }
}

