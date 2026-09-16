<?php

namespace App\Services;

use App\Models\Inquiry;
use App\Models\Setting;

class WhatsAppService
{
    /**
     * Generate WhatsApp deep-link dengan prefilled text berdasarkan data Inquiry.
     */
    public static function generateLink(Inquiry $inquiry, ?string $targetPhoneNumber = null): string
    {
        // Ambil nomor WA dari Settings DB, fallback ke config/default jika kosong (termasuk string kosong)
        $dbPhone = Setting::where('key', 'whatsapp_number')->value('value');
        $phone = $targetPhoneNumber ?: ($dbPhone ?: config('app.whatsapp_number', '6289670475275'));

        // Bersihkan karakter non-digit dari nomor telepon (misal hapus '+', '-', ' ')
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        // Ambil nama produk jika ada relasi ke produk
        $productName = $inquiry->product ? $inquiry->product->translated_name : 'General Export Inquiry';

        // Format pesan otomatis (Professional Export Inquiry Template)
        $message = "Hello Sales Team,\n\n";
        $message .= "I would like to inquire about *{$productName}*.\n\n";
        $message .= " *Buyer Details:*\n";
        $message .= "• Name: {$inquiry->name}\n";
        $message .= "• Company: {$inquiry->company}\n";
        $message .= "• Country: {$inquiry->country_code}\n";
        if ($inquiry->volume) {
            $message .= "• Quantity/Volume: {$inquiry->volume}\n";
        }
        if ($inquiry->incoterms) {
            $message .= "• Preferred Incoterms: {$inquiry->incoterms}\n";
        }
        $message .= "\n *Message:*\n\"{$inquiry->message}\"\n\n";
        $message .= "Thank you!";

        // Encode teks pesan agar valid di URL
        $encodedMessage = urlencode($message);

        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }
}
