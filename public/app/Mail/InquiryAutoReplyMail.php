<?php

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InquiryAutoReplyMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Inquiry $inquiry;

    public function __construct(Inquiry $inquiry)
    {
        $this->inquiry = $inquiry;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Thank you for your inquiry - " . config('app.name', 'Exporter Company'),
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: "
                <div style='font-family: sans-serif; line-height: 1.6; color: #333;'>
                    <h2>Thank You for Reaching Out to Us!</h2>
                    <p>Dear <strong>{$this->inquiry->name}</strong>,</p>
                    <p>We have successfully received your Request for Quotation (RFQ) regarding <strong>" . ($this->inquiry->product ? $this->inquiry->product->translated_name : 'our export products') . "</strong>.</p>
                    <p>Our Export Sales Team is reviewing your requirements and will get back to you with detailed specifications, pricing, and terms within 24 hours.</p>
                    <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
                    <p style='font-size: 14px; color: #666;'>
                        Need an urgent response? You can also contact our export manager directly on WhatsApp.
                    </p>
                    <p>Best Regards,<br><strong>Export Sales Team</strong><br>" . config('app.name') . "</p>
                </div>
            ",
        );
    }
}
