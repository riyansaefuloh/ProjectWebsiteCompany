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
     * Jam operasional. SATU isian untuk Senin–Sabtu, bukan tiga isian terpisah.
     *
     * Yang lama memisahkan Senin–Jumat, Sabtu, dan Minggu — tiga baris yang
     * pada praktiknya selalu diisi jam yang sama, lalu digambar tiga kali di
     * kaki situs dan halaman kontak. Satu rentang yang berlaku sepanjang
     * minggu kerja lebih jujur, dan tidak bisa jadi tidak konsisten dengan
     * dirinya sendiri.
     */
    public string $hours_weekly = '';

    public function mount(): void
    {
        $nilai = Setting::pluck('value', 'key');

        $this->company_name    = $nilai['company_name'] ?? 'PT. Indo Export Global';
        $this->whatsapp_number = $nilai['whatsapp_number'] ?? '';
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

        /*
         * Kunci lama hours_weekday dipakai sebagai benih supaya jam yang sudah
         * terlanjur diisi tidak hilang begitu isiannya disatukan. Sesudah sekali
         * Simpan, save() di bawah mengosongkan ketiga kunci lamanya.
         */
        $this->hours_weekly = ($nilai['hours_weekly'] ?? '') ?: ($nilai['hours_weekday'] ?? '');

        $this->existing_logo    = $nilai['logo'] ?? null;
        $this->existing_favicon = $nilai['favicon'] ?? null;
    }

    protected function rules(): array
    {
        return [
            'company_name'        => 'required|string|max:255',
            'whatsapp_number'     => 'required|string|max:30',
            'contact_email'       => 'required|email|max:255',
            'company_address'     => 'required|string|max:500',
            'google_map_url'      => 'nullable|url|max:1000',
            'google_analytics_id' => 'nullable|string|max:50',
            'timezone'            => 'required|string|max:50',
            'facebook_url'        => 'nullable|url|max:255',
            'instagram_url'       => 'nullable|url|max:255',
            'linkedin_url'        => 'nullable|url|max:255',
            'hours_weekly'        => 'nullable|string|max:60',
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
            /*
             * Kunci telepon DIKOSONGKAN, bukan dibiarkan. Situs publik kini cuma
             * memakai WhatsApp; nilai yang tertinggal di sini tidak punya isian
             * lagi di panel, jadi ia tidak akan pernah bisa diperbaiki dari sana
             * kalau suatu saat ada yang membacanya kembali.
             */
            'company_phone'       => '',
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
            'hours_weekly'        => $this->hours_weekly,

            /*
             * Ketiga kunci lama DIKOSONGKAN, bukan dibiarkan. Kaki situs dan
             * halaman kontak masih membaca hours_weekday sebagai cadangan;
             * selama nilainya tertinggal di sana, jam lama tetap tergambar
             * berdampingan dengan jam baru dan dari panel keduanya tampak
             * baik-baik saja karena hanya satu yang punya isian.
             */
            'hours_weekday'       => '',
            'hours_saturday'      => '',
            'hours_sunday'        => '',
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

    public function deleteFile(string $type): void
    {
        if (!in_array($type, ['logo', 'favicon'])) {
            return;
        }

        $property = 'existing_' . $type;
        $path = $this->$property;

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        $this->$property = null;
        $this->$type = null;

        Setting::updateOrCreate(
            ['key' => $type],
            ['value' => null]
        );
        
        session()->flash('message', ucfirst($type) . ' berhasil dihapus.');
    }

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
