<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Download;
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
        return view('livewire.public.download-index', compact('downloads'));
    }

    public function download($id)
    {
        $download = Download::findOrFail($id);

        if ($download->require_email && empty($this->email)) {
            $this->selectedDownloadId = $id;
            $this->addError('email', 'Email is required to download this file.');
            return;
        }

        // Increment download count
        $download->increment('download_count');

        // Reset state
        $this->selectedDownloadId = null;
        $this->email = '';

        // If the physical file doesn't exist, show an error message
        if (!Storage::disk('public')->exists($download->file_path)) {
            $this->addError('email', 'The requested file is not available on the server.');
            return;
        }

        $filename = \Illuminate\Support\Str::slug($download->title) . '.pdf';
        return response()->download(Storage::disk('public')->path($download->file_path), $filename);
    }
}
