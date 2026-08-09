<div style="font-family: sans-serif; padding: 20px; max-width: 600px;">
    <h2>Global Website Settings</h2>

    @if (session()->has('message'))
        <div style="background-color: #d1fae5; color: #065f46; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="save">
        <div style="margin-bottom: 15px;">
            <label><strong>WhatsApp Sales Number (International Format e.g. 6281234567890) </strong></label>
            <input type="text" wire:model="whatsapp_number" style="width: 100%; padding: 8px;" >
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
            <label><strong>Google Maps Embed URL (Optional)</strong></label>
            <input type="url" wire:model="google_map_url" style="width: 100%; padding: 8px;" placeholder="https://www.google.com/maps/embed?...">
            <p style="font-size: 12px; color: #6b7280; margin-top: 4px;">Paste the src URL from Google Maps Embed (starts with https://www.google.com/maps/embed)</p>
            @error('google_map_url') <span style="color:red">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 15px;">
            <label><strong>Google Analytics / GTM ID (e.g. G-XXXXXXX)</strong></label>
            <input type="text" wire:model="google_analytics_id" style="width: 100%; padding: 8px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label><strong>Brand Primary Color *</strong></label>
            <input type="color" wire:model="brand_color" style="height: 40px; width: 100px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label><strong>Timezone *</strong></label>
            <select wire:model="timezone" style="width: 100%; padding: 8px;" required>
                <option value="Asia/Jakarta">Asia/Jakarta (WIB)</option>
                <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                <option value="UTC">UTC</option>
            </select>
        </div>

        <hr style="margin: 20px 0; border: 1px solid #e5e7eb;">

        <h3>Social Media Links</h3>
        <div style="margin-bottom: 15px;">
            <label>Facebook URL</label>
            <input type="url" wire:model="facebook_url" style="width: 100%; padding: 8px;">
        </div>
        <div style="margin-bottom: 15px;">
            <label>Instagram URL</label>
            <input type="url" wire:model="instagram_url" style="width: 100%; padding: 8px;">
        </div>
        <div style="margin-bottom: 15px;">
            <label>LinkedIn URL</label>
            <input type="url" wire:model="linkedin_url" style="width: 100%; padding: 8px;">
        </div>

        <hr style="margin: 20px 0; border: 1px solid #e5e7eb;">

        <h3>Branding Assets</h3>
        <div style="margin-bottom: 15px;">
            <label><strong>Company Logo</strong></label><br>
            @if($existing_logo)
                <img src="{{ Storage::url($existing_logo) }}" style="height: 50px; margin-bottom: 10px; background: #eee; padding: 5px;">
            @endif
            <input type="file" wire:model="logo" accept="image/*" style="width: 100%; padding: 8px;">
            @error('logo') <span style="color:red">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 25px;">
            <label><strong>Favicon</strong></label><br>
            @if($existing_favicon)
                <img src="{{ Storage::url($existing_favicon) }}" style="height: 32px; margin-bottom: 10px; background: #eee; padding: 5px;">
            @endif
            <input type="file" wire:model="favicon" accept="image/*" style="width: 100%; padding: 8px;">
            @error('favicon') <span style="color:red">{{ $message }}</span> @enderror
        </div>

        <button type="submit" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            Save Settings
        </button>
    </form>

    <hr style="margin: 30px 0; border: 1px solid #e5e7eb;">

    <h2>Home Page Sections Order</h2>
    <p style="font-size: 14px; color: #6b7280; margin-bottom: 15px;">
        Manage the order and visibility of sections on the homepage. Changes here are saved instantly.
    </p>

    <div style="border: 1px solid #d1d5db; border-radius: 6px; overflow: hidden;">
        <table border="0" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f3f4f6; text-align: left;">
                    <th style="border-bottom: 1px solid #d1d5db; width: 60px;">Order</th>
                    <th style="border-bottom: 1px solid #d1d5db;">Section Identifier</th>
                    <th style="border-bottom: 1px solid #d1d5db;">Display Name</th>
                    <th style="border-bottom: 1px solid #d1d5db; width: 100px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($home_sections as $sec)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="text-align: center;">
                            <button wire:click="moveSectionUp('{{ $sec['id'] }}')" style="padding: 2px 5px; cursor: pointer; border: 1px solid #ccc; background: white;">▲</button><br>
                            <span style="font-weight: bold; font-size: 14px; display: inline-block; margin: 4px 0;">{{ $sec['order'] }}</span><br>
                            <button wire:click="moveSectionDown('{{ $sec['id'] }}')" style="padding: 2px 5px; cursor: pointer; border: 1px solid #ccc; background: white;">▼</button>
                        </td>
                        <td><code>{{ $sec['id'] }}</code></td>
                        <td>{{ $sec['name'] }}</td>
                        <td>
                            <button wire:click="toggleSectionActive('{{ $sec['id'] }}')" style="padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; background: {{ $sec['active'] ? '#10b981' : '#6b7280' }}; color: white; width: 100%;">
                                {{ $sec['active'] ? 'Active' : 'Hidden' }}
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
