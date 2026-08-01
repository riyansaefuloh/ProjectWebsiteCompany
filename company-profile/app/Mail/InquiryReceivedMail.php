<?php

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InquiryReceivedMail extends Mailable implements ShouldQueue
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
            subject: "[NEW RFQ] Export Inquiry from {$this->inquiry->company} ({$this->inquiry->country_code})",
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: "
                <h2>New Export Inquiry Received</h2>
                <p>You have received a new inquiry from the website:</p>
                <ul>
                    <li><strong>Buyer Name:</strong> {$this->inquiry->name}</li>
                    <li><strong>Company:</strong> {$this->inquiry->company}</li>
                    <li><strong>Email:</strong> {$this->inquiry->email}</li>
                    <li><strong>Country Code:</strong> {$this->inquiry->country_code}</li>
                    <li><strong>Phone/WA:</strong> " . ($this->inquiry->phone ?? '-') . "</li>
                    <li><strong>Product:</strong> " . ($this->inquiry->product ? $this->inquiry->product->translated_name : 'General Inquiry') . "</li>
                    <li><strong>Estimated Volume:</strong> " . ($this->inquiry->volume ?? '-') . "</li>
                    <li><strong>Incoterms:</strong> " . ($this->inquiry->incoterms ?? '-') . "</li>
                </ul>
                <hr>
                <p><strong>Message:</strong></p>
                <p><em>\"{$this->inquiry->message}\"</em></p>
                <hr>
                <p>Please log in to the admin panel to process and quote this inquiry.</p>
            ",
        );
    }
}
