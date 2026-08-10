<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Download;
use Illuminate\Support\Facades\Storage;

class DownloadIndex extends Component
{
    public $email = '';
    public $selectedDownloadId = null;

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
