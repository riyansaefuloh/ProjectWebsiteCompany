<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class PdfCatalogService
{
    /** Sisi terpanjang foto tersemat, dalam piksel. */
    private const SISI_FOTO = 288;

    /**
     * Generate PDF Export Product Catalog.
     */
    public function generateCatalogPdf(?string $categoryId = null): Response
    {
        // Query produk aktif beserta relasi translasi, spesifikasi, dan sertifikasi
        $query = Product::with(['translations', 'specifications', 'certifications.translations', 'category.translations', 'media'])
            ->where('status', 'published')
            ->orderBy('sort_order', 'asc');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->get();

        /*
         * Nama dan kontak diambil dari Pengaturan, BUKAN dari config('app.name').
         *
         * Berkas ini yang dibawa pulang pembeli sesudah meninggalkan situs, dan
         * sampai sekarang kepalanya bertuliskan "LARAVEL" — nilai bawaan
         * APP_NAME yang tidak pernah diganti. Nama perusahaan yang sebenarnya
         * sudah lama ada di tabel settings, tempat yang sama dengan yang dibaca
         * bilah kepala dan kaki halaman situs.
         */
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

        // Atur ukuran kertas A4 portrait
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('Export_Product_Catalog_' . date('Y_m') . '.pdf');
    }

    /**
     * Foto produk sebagai data URI, dikecilkan lebih dulu.
     *
     * Dikecilkan, dan itu bukan penghalusan: menyematkan berkas asli apa
     * adanya menghasilkan katalog 13,5 MB untuk enam produk — berkas yang
     * ditolak hampir semua kotak surel, padahal justru lewat surel katalog ini
     * dikirim. Pada 288px sisi terpanjang, keenamnya jadi di bawah 400 KB, dan
     * petaknya sendiri cuma 108pt di kertas.
     *
     * Data URI, bukan jalur berkas: DomPDF memang bisa membaca berkas lokal,
     * tapi jalur Windows bergaris miring terbalik dan aturan chroot gagal
     * diam-diam — menyisakan petak kosong tanpa satu pun pesan.
     */
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

                    /* JPEG dan PNG saja: DomPDF tidak mengenal WebP, dan
                       koleksi 'gallery' menyimpan hasil konversi WebP
                       berdampingan dengan aslinya. */
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

                    /* Selalu keluar sebagai JPEG, termasuk yang asalnya PNG:
                       PNG berlatar tembus pandang akan menghitam di PDF, dan
                       foto produk tidak butuh saluran alfa. */
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
