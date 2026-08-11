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
    public string $brand_color = '#2563eb';
    
    public string $timezone = 'Asia/Jakarta';
    public string $facebook_url = '';
    public string $instagram_url = '';
    public string $linkedin_url = '';
    
    public $logo;
    public $favicon;
    public ?string $existing_logo = null;
    public ?string $existing_favicon = null;

    public array $home_sections = [];

    public function mount(): void
    {
        // Load existing settings from DB (key-value)
        $this->company_name = Setting::where('key', 'company_name')->value('value') ?? 'PT. Indo Export Global';
        $this->whatsapp_number = Setting::where('key', 'whatsapp_number')->value('value') ?? '6289670475275';
        $this->contact_email = Setting::where('key', 'contact_email')->value('value') ?? 'arjunapandawa088@gmail.com';
        $this->company_address = Setting::where('key', 'company_address')->value('value') ?? 'Jl. Jenderal Sudirman No. 123, Jakarta, Indonesia';
        $this->google_map_url = Setting::where('key', 'google_map_url')->value('value');
        $this->google_analytics_id = Setting::where('key', 'google_analytics_id')->value('value') ?? '';
        $this->brand_color = Setting::where('key', 'brand_color')->value('value') ?? '#2563eb';
        
        $this->timezone = Setting::where('key', 'timezone')->value('value') ?? 'Asia/Jakarta';
        $this->facebook_url = Setting::where('key', 'facebook_url')->value('value') ?? '';
        $this->instagram_url = Setting::where('key', 'instagram_url')->value('value') ?? '';
        $this->linkedin_url = Setting::where('key', 'linkedin_url')->value('value') ?? '';
        
        $this->existing_logo = Setting::where('key', 'logo')->value('value');
        $this->existing_favicon = Setting::where('key', 'favicon')->value('value');

        // Load or initialize Home Sections JSON
        $sectionsJson = Setting::where('key', 'home_sections')->value('value');
        if ($sectionsJson) {
            $this->home_sections = json_decode($sectionsJson, true);
            
            // Check if downloads is missing and inject it
            $hasDownloads = false;
            foreach ($this->home_sections as $sec) {
                if ($sec['id'] === 'downloads') $hasDownloads = true;
            }
            if (!$hasDownloads) {
                $this->home_sections[] = ["id" => "downloads", "name" => "Catalogs & Downloads", "active" => true, "order" => count($this->home_sections) + 1];
                Setting::updateOrCreate(['key' => 'home_sections'], ['value' => json_encode($this->home_sections)]);
            }

            // Sort by order just in case
            usort($this->home_sections, fn($a, $b) => $a['order'] <=> $b['order']);
        } else {
            $this->home_sections = [
                ["id" => "hero", "name" => "Hero Slider", "active" => true, "order" => 1],
                ["id" => "about", "name" => "About Us", "active" => true, "order" => 2],
                ["id" => "products", "name" => "Our Products", "active" => true, "order" => 3],
                ["id" => "export-markets", "name" => "Export Markets", "active" => true, "order" => 4],
                ["id" => "certifications", "name" => "Certifications", "active" => true, "order" => 5],
                ["id" => "gallery", "name" => "Gallery", "active" => true, "order" => 6],
                ["id" => "downloads", "name" => "Catalogs & Downloads", "active" => true, "order" => 7],
                ["id" => "news", "name" => "Latest News", "active" => true, "order" => 8],
                ["id" => "contact", "name" => "Contact Us", "active" => true, "order" => 9]
            ];
            Setting::updateOrCreate(['key' => 'home_sections'], ['value' => json_encode($this->home_sections)]);
        }
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
            'brand_color'         => 'required|string|max:10',
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
            'contact_email'       => $this->contact_email,
            'company_address'     => $this->company_address,
            'google_map_url'      => $this->google_map_url,
            'google_analytics_id' => $this->google_analytics_id,
            'brand_color'         => $this->brand_color,
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

    // Home Section Management Methods
    public function toggleSectionActive($id)
    {
        foreach ($this->home_sections as &$section) {
            if ($section['id'] === $id) {
                $section['active'] = !$section['active'];
                break;
            }
        }
        $this->saveSections();
    }

    public function moveSectionUp($id)
    {
        $index = $this->findSectionIndex($id);
        if ($index > 0) {
            // Swap orders
            $tempOrder = $this->home_sections[$index]['order'];
            $this->home_sections[$index]['order'] = $this->home_sections[$index - 1]['order'];
            $this->home_sections[$index - 1]['order'] = $tempOrder;
            $this->saveSections();
        }
    }

    public function moveSectionDown($id)
    {
        $index = $this->findSectionIndex($id);
        if ($index !== null && $index < count($this->home_sections) - 1) {
            // Swap orders
            $tempOrder = $this->home_sections[$index]['order'];
            $this->home_sections[$index]['order'] = $this->home_sections[$index + 1]['order'];
            $this->home_sections[$index + 1]['order'] = $tempOrder;
            $this->saveSections();
        }
    }

    private function findSectionIndex($id)
    {
        foreach ($this->home_sections as $index => $section) {
            if ($section['id'] === $id) return $index;
        }
        return null;
    }

    private function saveSections()
    {
        // Re-sort array by order before saving
        usort($this->home_sections, fn($a, $b) => $a['order'] <=> $b['order']);
        // Normalize order to 1,2,3...
        foreach ($this->home_sections as $index => &$sec) {
            $sec['order'] = $index + 1;
        }
        Setting::updateOrCreate(['key' => 'home_sections'], ['value' => json_encode($this->home_sections)]);
    }

    // [KOMEN] Menggunakan folder components/layouts/app.blade.php
    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.admin.setting-index');
    }
}
