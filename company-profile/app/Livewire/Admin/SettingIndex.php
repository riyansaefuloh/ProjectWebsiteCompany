<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Setting;

class SettingIndex extends Component
{
    // Settings Fields
    public string $whatsapp_number = '';
    public string $contact_email = '';
    public string $company_address = '';
    public string $google_analytics_id = '';
    public string $brand_color = '#2563eb';

    public function mount(): void
    {
        // Load existing settings from DB (key-value)
        $this->whatsapp_number = Setting::where('key', 'whatsapp_number')->value('value') ?? '6281234567890';
        $this->contact_email = Setting::where('key', 'contact_email')->value('value') ?? 'sales@exportercompany.com';
        $this->company_address = Setting::where('key', 'company_address')->value('value') ?? 'Jakarta, Indonesia';
        $this->google_analytics_id = Setting::where('key', 'google_analytics_id')->value('value') ?? '';
        $this->brand_color = Setting::where('key', 'brand_color')->value('value') ?? '#2563eb';
    }

    protected function rules(): array
    {
        return [
            'whatsapp_number'     => 'required|string|max:30',
            'contact_email'       => 'required|email|max:100',
            'company_address'     => 'required|string|max:500',
            'google_analytics_id' => 'nullable|string|max:50',
            'brand_color'         => 'required|string|max:10',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $settings = [
            'whatsapp_number'     => $this->whatsapp_number,
            'contact_email'       => $this->contact_email,
            'company_address'     => $this->company_address,
            'google_analytics_id' => $this->google_analytics_id,
            'brand_color'         => $this->brand_color,
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        session()->flash('message', 'Global settings updated successfully!');
    }

    public function render()
    {
        return view('livewire.admin.setting-index');
    }
}
