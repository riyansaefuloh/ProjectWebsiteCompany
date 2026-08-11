<div>
    <div style="background: #e2e8f0; padding: 40px; text-align: center; margin-bottom: 30px;">
        <h1>{{ __('site.page_contact') }}</h1>
        <p>{{ __('site.page_contact_sub') }}</p>
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
                <h2 style="color: #10b981;">{{ __('site.inquiry_success') }}</h2>
                <p style="margin-bottom: 20px;">{{ __('site.inquiry_thank_you') }}</p>
                
                @if($whatsappUrl)
                    <div class="frontend-task" style="margin-bottom: 15px;">
                        [FRONTEND TASK: Berikan tombol WA yang menarik. URL sudah di-generate secara dinamis oleh backend.]
                    </div>
                    <a href="{{ $whatsappUrl }}" target="_blank" style="display: inline-block; padding: 12px 25px; background: #25d366; color: white; font-weight: bold; text-decoration: none; border-radius: 6px;">
                        {{ __('site.chat_whatsapp') }}
                    </a>
                @endif
                
                <div style="margin-top: 30px;">
                    <button wire:click="$set('isSubmitted', false)" style="background: none; border: none; color: #2563eb; cursor: pointer; text-decoration: underline;">{{ __('site.send_another') }}</button>
                </div>
            </div>
        @else
            <form wire:submit.prevent="executeRecaptcha" style="display: flex; flex-direction: column; gap: 15px;">
            
            <!-- Honeypot anti-spam field (hidden) -->
            <input type="text" wire:model="website_hp" style="display:none !important;" tabindex="-1" autocomplete="off">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label><strong>{{ __('site.field_name') }} *</strong></label>
                    <input type="text" wire:model="name" required style="width: 100%; padding: 8px; box-sizing: border-box;">
                    @error('name') <span style="color:red; font-size:12px;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label><strong>{{ __('site.field_company') }} *</strong></label>
                    <input type="text" wire:model="company" required style="width: 100%; padding: 8px; box-sizing: border-box;">
                    @error('company') <span style="color:red; font-size:12px;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label><strong>{{ __('site.field_email') }} *</strong></label>
                    <input type="email" wire:model="email" required style="width: 100%; padding: 8px; box-sizing: border-box;">
                    @error('email') <span style="color:red; font-size:12px;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label><strong>{{ __('site.field_phone') }}</strong></label>
                    <input type="text" wire:model="phone" style="width: 100%; padding: 8px; box-sizing: border-box;">
                </div>
            </div>

            <div>
                <label><strong>{{ __('site.field_country') }} *</strong></label>
                <input type="text" wire:model="country_code" required maxlength="2" placeholder="{{ __('site.field_country_ph') }}" style="width: 100%; padding: 8px; box-sizing: border-box;">
                @error('country_code') <span style="color:red; font-size:12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label><strong>{{ __('site.field_product') }}</strong></label>
                <select wire:model="product_id" style="width: 100%; padding: 8px; box-sizing: border-box;">
                    <option value="">{{ __('site.field_product_ph') }}</option>
                    @foreach($products ?? [] as $product)
                        <option value="{{ $product->id }}">{{ $product->translated_name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label><strong>{{ __('site.field_volume') }}</strong></label>
                    <input type="text" wire:model="volume" placeholder="{{ __('site.field_volume_ph') }}" style="width: 100%; padding: 8px; box-sizing: border-box;">
                </div>
                <div>
                    <label><strong>{{ __('site.field_incoterms') }}</strong></label>
                    <select wire:model="incoterms" style="width: 100%; padding: 8px; box-sizing: border-box;">
                        <option value="">{{ __('site.field_incoterms_ph') }}</option>
                        <option value="FOB">FOB</option>
                        <option value="CIF">CIF</option>
                        <option value="EXW">EXW</option>
                    </select>
                </div>
            </div>

            <div>
                <label><strong>{{ __('site.field_message') }} *</strong></label>
                <textarea wire:model="message" rows="5" required style="width: 100%; padding: 8px; box-sizing: border-box;"></textarea>
                @error('message') <span style="color:red; font-size:12px;">{{ $message }}</span> @enderror
            </div>

            <!-- Frontend task for Recaptcha -->
            <div class="frontend-task">
                [FRONTEND TASK: Script Google reCAPTCHA v3 sudah terpasang. Pastikan RECAPTCHA_SITE_KEY ada di .env]
            </div>

            @if(env('RECAPTCHA_SITE_KEY'))
                <script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY') }}"></script>
            @endif
            <script>
                document.addEventListener('livewire:initialized', () => {
                    Livewire.on('request-recaptcha', () => {
                        @if(env('RECAPTCHA_SITE_KEY'))
                            // Jika key ada, gunakan reCAPTCHA
                            grecaptcha.ready(function() {
                                grecaptcha.execute('{{ env('RECAPTCHA_SITE_KEY') }}', {action: 'inquiry'}).then(function(token) {
                                    @this.call('submit', token);
                                });
                            });
                        @else
                            // Jika key kosong (mode testing lokal), bypass reCAPTCHA
                            @this.call('submit', 'dummy-token-for-local-testing');
                        @endif
                    });
                });
            </script>

            <button type="submit" style="padding: 12px 20px; background: #2563eb; color: white; border: none; font-weight: bold; cursor: pointer; border-radius: 4px; font-size: 16px;">
                <span wire:loading.remove wire:target="executeRecaptcha, submit">{{ __('site.btn_submit') }}</span>
                <span wire:loading wire:target="executeRecaptcha, submit">{{ __('site.btn_processing') }}</span>
            </button>
        </form>
        @endif
    </div>
</div>
