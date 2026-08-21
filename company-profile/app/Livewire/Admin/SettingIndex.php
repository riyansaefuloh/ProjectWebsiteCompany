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

    /*
     * Jam operasional. Ketiganya sudah lama digambar kaki situs dan halaman
     * kontak, tapi tidak punya isian di panel — satu-satunya cara mengisinya
     * adalah lewat basis data langsung. Itu sebabnya blok "Jam operasional"
     * tidak pernah muncul di situs.
     */
    public string $hours_weekday = '';
    public string $hours_saturday = '';
    public string $hours_sunday = '';

    public function mount(): void
    {
        $nilai = Setting::pluck('value', 'key');

        $this->company_name    = $nilai['company_name'] ?? 'PT. Indo Export Global';
        $this->whatsapp_number = $nilai['whatsapp_number'] ?? '';
        $this->company_phone   = $nilai['company_phone'] ?? '';
        $this->company_address = $nilai['company_address'] ?? '';

        /*
         * TIDAK ADA alamat cadangan yang ditulis di kode.
         *
         * Sebelumnya baris ini berakhir dengan alamat Gmail pribadi seseorang
         * yang ditulis langsung di kode, dan alamat itu tampil di kaki situs
         * setiap kali kuncinya kosong. Cadangan semacam itu tidak pernah
         * kelihatan salah dari panel, karena isiannya tampak terisi wajar.
         *
         * Yang dipakai sekarang: isian ini, lalu kunci lama company_email
         * kalau isian ini belum pernah ada. Sesudah sekali Simpan, save() di
         * bawah menyamakan keduanya dan alamat bayangannya lenyap.
         */
        $this->contact_email = ($nilai['contact_email'] ?? '') ?: ($nilai['company_email'] ?? '');

        $this->google_map_url      = $nilai['google_map_url'] ?? null;
        $this->google_analytics_id = $nilai['google_analytics_id'] ?? '';
        $this->timezone            = $nilai['timezone'] ?? 'Asia/Jakarta';
        $this->facebook_url        = $nilai['facebook_url'] ?? '';
        $this->instagram_url       = $nilai['instagram_url'] ?? '';
        $this->linkedin_url        = $nilai['linkedin_url'] ?? '';

        $this->hours_weekday  = $nilai['hours_weekday'] ?? '';
        $this->hours_saturday = $nilai['hours_saturday'] ?? '';
        $this->hours_sunday   = $nilai['hours_sunday'] ?? '';

        $this->existing_logo    = $nilai['logo'] ?? null;
        $this->existing_favicon = $nilai['favicon'] ?? null;
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
            'hours_weekday'       => 'nullable|string|max:60',
            'hours_saturday'      => 'nullable|string|max:60',
            'hours_sunday'        => 'nullable|string|max:60',
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

            /*
             * Kunci lama ditulis dengan nilai yang SAMA, bukan dibiarkan.
             *
             * Beberapa bagian situs masih membacanya sebagai cadangan. Selama
             * isinya boleh berbeda, satu layar bisa memuat dua alamat — dan
             * dari panel keduanya tampak baik-baik saja karena hanya satu yang
             * punya isian. Menyamakannya di sini membuat perbedaan itu mustahil.
             */
            'company_email'       => $this->contact_email,

            'company_address'     => $this->company_address,
            'google_map_url'      => $this->google_map_url,
            'google_analytics_id' => $this->google_analytics_id,
            'timezone'            => $this->timezone,
            'facebook_url'        => $this->facebook_url,
            'instagram_url'       => $this->instagram_url,
            'linkedin_url'        => $this->linkedin_url,
            'hours_weekday'       => $this->hours_weekday,
            'hours_saturday'      => $this->hours_saturday,
            'hours_sunday'        => $this->hours_sunday,
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
         * Kunci lama 'company_email' dulu ditarik ke sini supaya bisa
         * diperingatkan di layar kalau isinya berbeda. Peringatan itu tidak
         * diperlukan lagi: save() sekarang menulis kedua kunci dengan nilai
         * yang sama, jadi keduanya tidak bisa lagi berbeda.
         */
        return view('livewire.admin.setting-index');
    }
}
