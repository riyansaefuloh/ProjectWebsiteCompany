<div>
    @if ($isSubmitted)
        <div style="background-color: #d1fae5; border: 1px solid #10b981; color: #065f46; padding: 20px; border-radius: 8px;">
            <h3>Thank You! Your Inquiry Has Been Submitted.</h3>
            <p>We have sent a confirmation email to <strong>{{ $email }}</strong>.</p>
            
            @if($whatsappUrl)
                <a href="{{ $whatsappUrl }}" target="_blank" style="display: inline-block; background-color: #25D366; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 10px;">
                    💬 Chat Directly via WhatsApp
                </a>
            @endif
        </div>
    @else
        <form wire:submit.prevent="submit">
            <!-- Honeypot anti-spam (tersembunyi) -->
            <input type="text" wire:model="website_hp" style="display:none !important;" tabindex="-1" autocomplete="off">

            <div>
                <label>Full Name *</label>
                <input type="text" wire:model="name" required>
                @error('name') <span style="color:red">{{ $message }}</span> @enderror
            </div>

            <div>
                <label>Company Name *</label>
                <input type="text" wire:model="company" required>
                @error('company') <span style="color:red">{{ $message }}</span> @enderror
            </div>

            <div>
                <label>Email Address *</label>
                <input type="email" wire:model="email" required>
                @error('email') <span style="color:red">{{ $message }}</span> @enderror
            </div>

            <div>
                <label>Country Code (ISO 2-letter e.g. US, DE, JP) *</label>
                <input type="text" wire:model="country_code" maxlength="2" required>
                @error('country_code') <span style="color:red">{{ $message }}</span> @enderror
            </div>

            <div>
                <label>Phone / WhatsApp</label>
                <input type="text" wire:model="phone">
            </div>

            <div>
                <label>Estimated Quantity / Volume</label>
                <input type="text" wire:model="volume" placeholder="e.g. 1 x 20ft container">
            </div>

            <div>
                <label>Preferred Incoterms</label>
                <input type="text" wire:model="incoterms" placeholder="e.g. FOB, CIF">
            </div>

            <div>
                <label>Message / Specifications *</label>
                <textarea wire:model="message" rows="4" required></textarea>
                @error('message') <span style="color:red">{{ $message }}</span> @enderror
            </div>

            <button type="submit" style="margin-top: 10px; padding: 10px 20px; background: #2563eb; color: white; border: none; cursor: pointer;">
                Submit Request for Quotation (RFQ)
            </button>
        </form>
    @endif
</div>
