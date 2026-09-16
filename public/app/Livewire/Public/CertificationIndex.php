<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Certification;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;

class CertificationIndex extends Component
{
    public function mount(): void
    {
        $appName = config('app.name');

        SEOMeta::setTitle('Our Certifications - ' . $appName);
        SEOMeta::setDescription('We hold internationally recognized certifications including Organic, Fair Trade, Rainforest Alliance, and food safety standards for our coffee export products.');
        SEOMeta::setCanonical(route('certifications.index'));

        OpenGraph::setTitle('Our Certifications - ' . $appName);
        OpenGraph::setDescription('International certifications held by ' . $appName . ' for coffee export quality assurance.');
        OpenGraph::setUrl(route('certifications.index'));
        OpenGraph::setType('website');

        TwitterCard::setTitle('Our Certifications - ' . $appName);
        TwitterCard::setDescription('Internationally certified coffee exporter from Indonesia.');
    }

    #[Layout('components.layouts.public')]
    public function render()
    {
        /*
         * berlaku(), bukan sekadar status aktif — cakupan yang sama dengan bilah
         * kepercayaan di beranda.
         *
         * Halaman ini dulu sengaja menampilkan yang kedaluwarsa lengkap dengan
         * label "Expired on", dengan alasan keterbukaan. Yang terjadi di layar
         * justru sebaliknya: sertifikat mati berdiri sederet dengan yang hidup,
         * sama besar dan sama menonjol, dan pembaca harus memeriksa tanggal satu
         * per satu untuk tahu mana yang masih berarti. Yang sudah lewat tanggal
         * bukan kabar yang perlu disampaikan di halaman kredensial — ia cuma
         * melemahkan yang masih berlaku.
         */
        $certifications = Certification::berlaku()
            ->with(['translations', 'media'])
            ->orderBy('sort_order')
            ->get();

        return view('livewire.public.certification-index', array_merge(compact('certifications'), [
            /* Isi kepala halaman ini bisa disunting dari menu Halaman;
               yang kosong jatuh ke teks bawaan di berkas bahasa. */
            'isi' => \App\Support\IsiHalaman::untuk('certifications'),
        ]));
    }
}
