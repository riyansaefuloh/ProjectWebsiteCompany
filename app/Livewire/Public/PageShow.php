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

    private function bedahIsi(?string $html): array
    {
        $html = trim((string) $html);

        if ($html === '') {
            return ['', []];
        }

        $dom = \Dom\HTMLDocument::createFromString($html, LIBXML_NOERROR);

        $daftar = [];
        $pakai  = [];

        foreach ($dom->querySelectorAll('h2') as $judul) {
            $teks = trim($judul->textContent);

            if ($teks === '') {
                continue;
            }

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
