<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Page;
use App\Models\Setting;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;

class About extends Component
{
    #[Layout('components.layouts.public')]
    public function render()
    {
        $page    = Page::where('slug', 'about-us')->first();
        $appName = config('app.name');

        $isiProfil = \App\Support\IsiHalaman::untuk('profile');

        $ringkas = $isiProfil('body', 'site.about_empty', [], $page?->translated_content);

        $desc = mb_substr(strip_tags($ringkas), 0, 160)
            ?: 'Learn about our company, our mission, and why we are a trusted coffee exporter from Indonesia.';

        SEOMeta::setTitle('About Us - ' . $appName);
        SEOMeta::setDescription($desc);
        SEOMeta::setCanonical(route('about'));

        OpenGraph::setTitle('About Us - ' . $appName);
        OpenGraph::setDescription($desc);
        OpenGraph::setUrl(route('about'));
        OpenGraph::setType('website');

        TwitterCard::setTitle('About Us - ' . $appName);
        TwitterCard::setDescription($desc);

        $settings = Setting::pluck('value', 'key')->toArray();

        $tersimpan = \App\Support\IsiHalaman::semua()['profile']['sections'] ?? null;

        $bagian = is_array($tersimpan) && $tersimpan !== []
            ? $tersimpan
            : \App\Livewire\Admin\PageIndex::PROFIL_BAWAAN;

        $bagian = array_values(array_filter(
            $bagian,
            fn ($b) => ($b['active'] ?? false) === true && isset($b['id'])
        ));

        usort($bagian, fn ($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));

        return view('livewire.public.about', [
            'isi'            => \App\Support\IsiHalaman::untuk('profile'),
            'profilSections' => $bagian,
            'page'           => $page,
            'settings'       => $settings,
        ]);
    }
}
