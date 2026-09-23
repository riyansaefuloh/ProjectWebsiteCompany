<?php

namespace App\Services;

use App\Models\Inquiry;
use App\Models\Setting;

class WhatsAppService
{
    // Generate WhatsApp deep-link
    public static function generateLink(Inquiry $inquiry, ?string $targetPhoneNumber = null): string
    {
        // Nomor WA dari Setting/Config
        $dbPhone = Setting::where('key', 'whatsapp_number')->value('value');
        $phone = $targetPhoneNumber ?: ($dbPhone ?: config('app.whatsapp_number', '6289670475275'));

        // Format nomor telepon
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        // Nama produk inquiry
        $productName = $inquiry->product ? $inquiry->product->translated_name : 'General Export Inquiry';

        // Template pesan otomatis
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

        // Encode URL
        $encodedMessage = urlencode($message);

        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }
}
