<div>
    <div style="background: #e2e8f0; padding: 40px; text-align: center; margin-bottom: 30px;">
        <h1>Contact Us / Wholesale Inquiry</h1>
        <p>Get in touch for bulk orders and export inquiries.</p>
        <div class="frontend-task">
            [FRONTEND TASK: Berikan styling Hero Banner khusus halaman Contact Us]
        </div>
    </div>

    <div style="max-width: 800px; margin: 0 auto; background: white; padding: 30px; border: 1px solid #ddd; border-radius: 8px;">
        <div class="frontend-task" style="margin-bottom: 20px;">
            [FRONTEND TASK: Styling form ini. Pastikan form ini benar-benar mengirim data melalui Livewire (wire:submit="submit"). Fungsi simpan ke database dan kirim email/WA sudah siap di Backend!]
        </div>

        @php
            $googleMapUrl = \App\Models\Setting::where('key', 'google_map_url')->value('value');
        @endphp

        @if($googleMapUrl)
            <div style="margin-bottom: 30px; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                <div class="frontend-task" style="margin: 0; padding: 10px; border-bottom: 1px solid #e2e8f0; border-radius: 0;">
                    [FRONTEND TASK: Ini adalah Peta Google Map (Iframe) yang URL-nya bisa diubah dinamis dari CMS Global Settings!]
                </div>
                <iframe src="{{ $googleMapUrl }}" width="100%" height="300" style="border:0; display: block;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        @endif

        @if($isSubmitted)
            <div style="text-align: center; padding: 40px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                <h2 style="color: #10b981;">Inquiry Sent Successfully!</h2>
                <p style="margin-bottom: 20px;">Thank you for reaching out. Our team will contact you shortly.</p>
                
                @if($whatsappUrl)
                    <div class="frontend-task" style="margin-bottom: 15px;">
                        [FRONTEND TASK: Berikan tombol WA yang menarik. URL sudah di-generate secara dinamis oleh backend.]
                    </div>
                    <a href="{{ $whatsappUrl }}" target="_blank" style="display: inline-block; padding: 12px 25px; background: #25d366; color: white; font-weight: bold; text-decoration: none; border-radius: 6px;">
                        💬 Chat with Us on WhatsApp
                    </a>
                @endif
                
                <div style="margin-top: 30px;">
                    <button wire:click="$set('isSubmitted', false)" style="background: none; border: none; color: #2563eb; cursor: pointer; text-decoration: underline;">Send another inquiry</button>
                </div>
            </div>
        @else
            <form wire:submit.prevent="submit" style="display: flex; flex-direction: column; gap: 15px;">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label><strong>Name *</strong></label>
                    <input type="text" wire:model="name" required style="width: 100%; padding: 8px; box-sizing: border-box;">
                    @error('name') <span style="color:red; font-size:12px;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label><strong>Company / Organization *</strong></label>
                    <input type="text" wire:model="company" required style="width: 100%; padding: 8px; box-sizing: border-box;">
                    @error('company') <span style="color:red; font-size:12px;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label><strong>Email *</strong></label>
                    <input type="email" wire:model="email" required style="width: 100%; padding: 8px; box-sizing: border-box;">
                    @error('email') <span style="color:red; font-size:12px;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label><strong>Phone / WhatsApp (Optional)</strong></label>
                    <input type="text" wire:model="phone" style="width: 100%; padding: 8px; box-sizing: border-box;">
                </div>
            </div>

            <div>
                <label><strong>Target Country (ISO-2 Code) *</strong></label>
                <input type="text" wire:model="country_code" required maxlength="2" placeholder="US, ID, JP..." style="width: 100%; padding: 8px; box-sizing: border-box;">
                @error('country_code') <span style="color:red; font-size:12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label><strong>Interested Product (Optional)</strong></label>
                <select wire:model="product_id" style="width: 100%; padding: 8px; box-sizing: border-box;">
                    <option value="">-- Select Product --</option>
                    @foreach($products ?? [] as $product)
                        <option value="{{ $product->id }}">{{ $product->translated_name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label><strong>Estimated Volume (Optional)</strong></label>
                    <input type="text" wire:model="volume" placeholder="e.g. 1x20ft container" style="width: 100%; padding: 8px; box-sizing: border-box;">
                </div>
                <div>
                    <label><strong>Preferred Incoterms (Optional)</strong></label>
                    <select wire:model="incoterms" style="width: 100%; padding: 8px; box-sizing: border-box;">
                        <option value="">-- Select --</option>
                        <option value="FOB">FOB</option>
                        <option value="CIF">CIF</option>
                        <option value="EXW">EXW</option>
                    </select>
                </div>
            </div>

            <div>
                <label><strong>Message / Inquiry Details *</strong></label>
                <textarea wire:model="message" rows="5" required style="width: 100%; padding: 8px; box-sizing: border-box;"></textarea>
                @error('message') <span style="color:red; font-size:12px;">{{ $message }}</span> @enderror
            </div>

            <!-- Frontend task for Recaptcha -->
            <div class="frontend-task">
                [FRONTEND TASK: Pasang Google reCAPTCHA v3 script di sini sesuai PRD 7.10]
            </div>

            <button type="submit" style="padding: 12px 20px; background: #2563eb; color: white; border: none; font-weight: bold; cursor: pointer; border-radius: 4px; font-size: 16px;">
                <span wire:loading.remove wire:target="submit">Submit Inquiry</span>
                <span wire:loading wire:target="submit">Processing...</span>
            </button>
        </form>
        @endif
    </div>
</div>
