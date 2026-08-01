<div style="font-family: sans-serif; padding: 20px; max-width: 600px;">
    <h2>Global Website Settings</h2>

    @if (session()->has('message'))
        <div style="background-color: #d1fae5; color: #065f46; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="save">
        <div style="margin-bottom: 15px;">
            <label><strong>WhatsApp Sales Number (International Format e.g. 6281234567890) *</strong></label>
            <input type="text" wire:model="whatsapp_number" style="width: 100%; padding: 8px;" required>
            @error('whatsapp_number') <span style="color:red">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 15px;">
            <label><strong>Official Contact Email *</strong></label>
            <input type="email" wire:model="contact_email" style="width: 100%; padding: 8px;" required>
            @error('contact_email') <span style="color:red">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 15px;">
            <label><strong>Company Address *</strong></label>
            <textarea wire:model="company_address" rows="3" style="width: 100%; padding: 8px;" required></textarea>
            @error('company_address') <span style="color:red">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 15px;">
            <label><strong>Google Analytics / GTM ID (e.g. G-XXXXXXX)</strong></label>
            <input type="text" wire:model="google_analytics_id" style="width: 100%; padding: 8px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label><strong>Brand Primary Color *</strong></label>
            <input type="color" wire:model="brand_color" style="height: 40px; width: 100px;">
        </div>

        <button type="submit" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            Save Settings
        </button>
    </form>
</div>
