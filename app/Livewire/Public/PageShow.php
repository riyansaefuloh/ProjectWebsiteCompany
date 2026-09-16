<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Page;
use Illuminate\Support\Str;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;

class PageShow extends Component
{
    public $page;

    public function mount($slug): void
    {
        $this->page = Page::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $appName  = config('app.name');
        $locale   = app()->getLocale();
        $title    = $this->page->translated_title ?? $this->page->slug;
        $content  = strip_tags($this->page->translated_content ?? '');
        $shortDesc = mb_substr($content, 0, 160) ?: 'Read more about us on ' . $appName;

        SEOMeta::setTitle($title . ' - ' . $appName);
        SEOMeta::setDescription($shortDesc);
        SEOMeta::setCanonical(route('page.show', $slug));

        OpenGraph::setTitle($title . ' - ' . $appName);
        OpenGraph::setDescription($shortDesc);
        OpenGraph::setUrl(route('page.show', $slug));
        OpenGraph::setType('website');

        TwitterCard::setTitle($title . ' - ' . $appName);
        TwitterCard::setDescription($shortDesc);
    }

    #[Layout('components.layouts.public')]
    public function render()
    {
        $otherPages = Page::where('status', 'published')
            ->whereNotIn('slug', ['about-us', 'hero', $this->page->slug])
            ->get();

        [$isi, $daftarIsi] = $this->bedahIsi($this->page->translated_content);

        return view('livewire.public.page-show', [
            'otherPages' => $otherPages,
            'isi'        => $isi,
            'daftarIsi'  => $daftarIsi,
        ]);
    }

    /**
     * Menyuntikkan id ke tiap <h2> dan mengumpulkannya jadi daftar isi.
     *
     * Halaman ini menampung dokumen yang panjangnya tidak diketahui — kebijakan
     * privasi, syarat layanan, apa pun yang ditulis dari panel. Sepuluh pasal
     * tanpa daftar isi berarti menggulir sampai ketemu, dan yang dicari orang di
     * halaman begini biasanya SATU pasal, bukan seluruhnya.
     *
     * Dipisah di sini, bukan di Blade: yang dikerjakan membedah HTML, dan Blade
     * cuma boleh menggambar. Daftarnya juga dipakai untuk memutuskan susunan
     * halamannya — satu judul tidak perlu daftar isi.
     *
     * @return array{0: string, 1: list<array{id: string, teks: string}>}
     */
    private function bedahIsi(?string $html): array
    {
        $html = trim((string) $html);

        if ($html === '') {
            return ['', []];
        }

        /* Dom\HTMLDocument, bukan DOMDocument: yang lama mengurai HTML sebagai
           Latin-1 kecuali disuapi penanda encoding, dan "Café" di judul pasal
           akan keluar rusak. Yang ini UTF-8 sejak awal. */
        $dom = \Dom\HTMLDocument::createFromString($html, LIBXML_NOERROR);

        $daftar = [];
        $pakai  = [];

        foreach ($dom->querySelectorAll('h2') as $judul) {
            $teks = trim($judul->textContent);

            if ($teks === '') {
                continue;
            }

            /* Slug bisa bentrok — dua pasal boleh berjudul sama, dan dua id
               yang sama membuat tautan kedua mengantar ke pasal pertama. */
            $id = Str::slug($teks) ?: 'bagian';
            $pakai[$id] = ($pakai[$id] ?? 0) + 1;

            if ($pakai[$id] > 1) {
                $id .= '-' . $pakai[$id];
            }

            $judul->setAttribute('id', $id);
            $daftar[] = ['id' => $id, 'teks' => $teks];
        }

        return [$dom->body->innerHTML, $daftar];
    }
}
