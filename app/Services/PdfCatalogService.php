<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class PdfCatalogService
{
    private const SISI_FOTO = 288;

    public function generateCatalogPdf(?string $categoryId = null): Response
    {
        $query = Product::with(['translations', 'specifications', 'certifications.translations', 'category.translations', 'media'])
            ->where('status', 'published')
            ->orderBy('sort_order', 'asc');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->get();

        $pengaturan = Setting::pluck('value', 'key');

        $pdf = Pdf::loadView('pdf.catalog', [
            'products'    => $products,
            'foto'        => $products->mapWithKeys(
                fn ($p) => [$p->id => $this->fotoTersemat($p)]
            ),
            'companyName' => $pengaturan['company_name'] ?? config('app.name', 'Export Company'),
            'kontak'      => [
                'email'    => $pengaturan['contact_email'] ?? $pengaturan['company_email'] ?? '',
                'whatsapp' => $pengaturan['whatsapp_number'] ?? '',
                'alamat'   => $pengaturan['company_address'] ?? '',
            ],
            'date'        => date('F Y'),
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('Export_Product_Catalog_' . date('Y_m') . '.pdf');
    }

    private function fotoTersemat(Product $product): ?string
    {
        $media = $product->getFirstMedia('gallery');

        if (! $media) {
            return null;
        }

        return Cache::remember(
            "pdf-foto-{$media->id}-{$media->updated_at?->timestamp}",
            now()->addWeek(),
            function () use ($media) {
                foreach (['thumb', ''] as $konversi) {
                    $jalur = $konversi ? $media->getPath($konversi) : $media->getPath();

                    if (! is_file($jalur)) {
                        continue;
                    }

                    $info = @getimagesize($jalur);

                    if (! $info || ! in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
                        continue;
                    }

                    $asal = $info[2] === IMAGETYPE_PNG
                        ? @imagecreatefrompng($jalur)
                        : @imagecreatefromjpeg($jalur);

                    if (! $asal) {
                        continue;
                    }

                    [$lebar, $tinggi] = [imagesx($asal), imagesy($asal)];
                    $skala  = min(1, self::SISI_FOTO / max($lebar, $tinggi));
                    $kecil  = imagescale($asal, (int) round($lebar * $skala), (int) round($tinggi * $skala));

                    if (! $kecil) {
                        continue;
                    }

                    $putih = imagecreatetruecolor(imagesx($kecil), imagesy($kecil));
                    imagefill($putih, 0, 0, imagecolorallocate($putih, 255, 255, 255));
                    imagecopy($putih, $kecil, 0, 0, 0, 0, imagesx($kecil), imagesy($kecil));

                    ob_start();
                    imagejpeg($putih, null, 72);
                    $bita = ob_get_clean();

                    return 'data:image/jpeg;base64,' . base64_encode($bita);
                }

                return null;
            }
        );
    }
}
