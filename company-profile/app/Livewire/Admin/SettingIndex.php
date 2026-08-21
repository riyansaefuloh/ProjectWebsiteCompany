<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingIndex extends Component
{
    use WithFileUploads;
    // Settings Fields
    public string $company_name = '';
    public string $whatsapp_number = '';

    /*
     * Nomor telepon biasa, terpisah dari WhatsApp.
     *
     * Kunci 'company_phone' sudah lama dipakai kaki situs dan formulir
     * inquiry, tapi tidak punya isian di panel ini — jadi satu-satunya cara
     * mengubahnya adalah lewat basis data langsung.
     */
    public string $company_phone = '';

    public string $contact_email = '';
    public string $company_address = '';
    public ?string $google_map_url = null;
    public ?string $google_analytics_id = null;

    public string $timezone = 'Asia/Jakarta';
    public string $facebook_url = '';
    public string $instagram_url = '';
    public string $linkedin_url = '';
    
    public $logo;
    public $favicon;
    public ?string $existing_logo = null;
    public ?string $existing_favicon = null;

    public function mount(): void
    {
        // Load existing settings from DB (key-value)
        $this->company_name = Setting::where('key', 'company_name')->value('value') ?? 'PT. Indo Export Global';
        $this->whatsapp_number = Setting::where('key', 'whatsapp_number')->value('value') ?? '6289670475275';
        $this->company_phone = Setting::where('key', 'company_phone')->value('value') ?? '';
        $this->contact_email = Setting::where('key', 'contact_email')->value('value') ?? 'arjunapandawa088@gmail.com';
        $this->company_address = Setting::where('key', 'company_address')->value('value') ?? 'Jl. Jenderal Sudirman No. 123, Jakarta, Indonesia';
        $this->google_map_url = Setting::where('key', 'google_map_url')->value('value');
        $this->google_analytics_id = Setting::where('key', 'google_analytics_id')->value('value') ?? '';

        $this->timezone = Setting::where('key', 'timezone')->value('value') ?? 'Asia/Jakarta';
        $this->facebook_url = Setting::where('key', 'facebook_url')->value('value') ?? '';
        $this->instagram_url = Setting::where('key', 'instagram_url')->value('value') ?? '';
        $this->linkedin_url = Setting::where('key', 'linkedin_url')->value('value') ?? '';
        
        $this->existing_logo = Setting::where('key', 'logo')->value('value');
        $this->existing_favicon = Setting::where('key', 'favicon')->value('value');
    }

    protected function rules(): array
    {
        return [
            'company_name'        => 'required|string|max:255',
            'whatsapp_number'     => 'required|string|max:30',
            'company_phone'       => 'nullable|string|max:30',
            'contact_email'       => 'required|email|max:255',
            'company_address'     => 'required|string|max:500',
            'google_map_url'      => 'nullable|url|max:1000',
            'google_analytics_id' => 'nullable|string|max:50',
            'timezone'            => 'required|string|max:50',
            'facebook_url'        => 'nullable|url|max:255',
            'instagram_url'       => 'nullable|url|max:255',
            'linkedin_url'        => 'nullable|url|max:255',
            'logo'                => 'nullable|image|max:2048',
            'favicon'             => 'nullable|image|max:1024',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $settings = [
            'company_name'        => $this->company_name,
            'whatsapp_number'     => $this->whatsapp_number,
            'company_phone'       => $this->company_phone,
            'contact_email'       => $this->contact_email,
            'company_address'     => $this->company_address,
            'google_map_url'      => $this->google_map_url,
            'google_analytics_id' => $this->google_analytics_id,
            'timezone'            => $this->timezone,
            'facebook_url'        => $this->facebook_url,
            'instagram_url'       => $this->instagram_url,
            'linkedin_url'        => $this->linkedin_url,
        ];

        if ($this->logo) {
            $logoPath = $this->logo->store('settings', 'public');
            $settings['logo'] = $logoPath;
            $this->existing_logo = $logoPath;
        }

        if ($this->favicon) {
            $faviconPath = $this->favicon->store('settings', 'public');
            $settings['favicon'] = $faviconPath;
            $this->existing_favicon = $faviconPath;
        }

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        session()->flash('message', 'Global settings updated successfully!');
    }

    // [KOMEN] Menggunakan folder components/layouts/app.blade.php
    #[Layout('components.layouts.app')]
    public function render()
    {
        /*
         * Dibaca APA ADANYA, tidak disimpan dan tidak diubah dari sini.
         *
         * Situs publik membaca 'contact_email' lebih dulu — yaitu isian di
         * halaman ini — dan baru jatuh ke 'company_email' kalau kosong. Kunci
         * kedua itu masih menyimpan alamat lamanya dan tidak punya isian di
         * halaman ini, jadi ia tak terlihat sampai isian di atas dikosongkan.
         *
         * Ditarik supaya keadaan itu bisa disebut di layarnya, bukan jadi
         * kejutan sesudah menekan Simpan.
         */
        return view('livewire.admin.setting-index', [
            'emailSitus' => Setting::where('key', 'company_email')->value('value'),
        ]);
    }
}
