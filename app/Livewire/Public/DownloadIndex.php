<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Download;
use App\Models\Inquiry;
use Illuminate\Support\Facades\Storage;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;

class DownloadIndex extends Component
{
    public $email = '';
    public $selectedDownloadId = null;

    public function mount(): void
    {
        $appName = config('app.name');

        SEOMeta::setTitle('Catalogs & Downloads - ' . $appName);
        SEOMeta::setDescription('Download our product catalogs, export brochures, and specification sheets. Enter your email to access the full catalog of our premium Indonesian coffee exports.');
        SEOMeta::setCanonical(route('downloads.index'));

        OpenGraph::setTitle('Catalogs & Downloads - ' . $appName);
        OpenGraph::setDescription('Download our product catalogs and specification sheets for Indonesian coffee exports.');
        OpenGraph::setUrl(route('downloads.index'));
        OpenGraph::setType('website');

        TwitterCard::setTitle('Catalogs & Downloads - ' . $appName);
        TwitterCard::setDescription('Download our coffee export catalogs and product spec sheets.');
    }

    #[Layout('components.layouts.public')]
    public function render()
    {
        $downloads = Download::orderBy('sort_order')->get();
        return view('livewire.public.download-index', array_merge(compact('downloads'), [
            /* Isi kepala halaman ini bisa disunting dari menu Halaman;
               yang kosong jatuh ke teks bawaan di berkas bahasa. */
            'isi' => \App\Support\IsiHalaman::untuk('downloads'),
        ]));
    }

    public function download($id)
    {
        $download = Download::findOrFail($id);

        if ($download->require_email) {
            if (blank($this->email)) {
                $this->selectedDownloadId = $id;
                $this->addError('email', __('site.download_email_required'));
                return;
            }

            $this->validate(['email' => 'required|email|max:150']);
        }

        if (!Storage::disk('public')->exists($download->file_path)) {
            $this->addError('email', __('site.download_file_missing'));
            return;
        }

        if ($download->require_email) {
            $this->captureLead($download);
        }

        $download->increment('download_count');

        // Reset state
        $this->selectedDownloadId = null;
        $this->email = '';

        $filename = \Illuminate\Support\Str::slug($download->title) . '.pdf';
        return response()->download(Storage::disk('public')->path($download->file_path), $filename);
    }

    private function captureLead(Download $download): void
    {
        $note = 'Downloaded file: ' . $download->title;

        $alreadyLogged = Inquiry::where('email', $this->email)
            ->where('message', $note)
            ->exists();

        if ($alreadyLogged) {
            return;
        }

        Inquiry::create([
            'name'         => 'Download Lead',
            'company'      => 'Download Lead Gate',
            'email'        => $this->email,

            'country_code' => 'ZZ',

            'message'      => $note,
            'status'       => 'new',
            'ip_address'   => request()->ip(),
        ]);
    }
}
