<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InquiryExportController extends Controller
{
    /**
     * Export data Inquiry ke format CSV.
     */
    public function export(): StreamedResponse
    {
        $fileName = 'export_inquiries_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'ULID', 'Date', 'Buyer Name', 'Company', 'Email', 
            'Country Code', 'Phone', 'Product', 'Volume', 
            'Incoterms', 'Status', 'IP Address', 'Message'
        ];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');

            // Tambahkan BOM agar UTF-8 terbaca dengan benar di Microsoft Excel
            fputs($file, "\xEF\xBB\xBF");

            // Write header CSV
            fputcsv($file, $columns);

            // Fetch data inquiry dengan eager loading relasi product
            Inquiry::with('product.translations')
                ->latest()
                ->chunk(100, function ($inquiries) use ($file) {
                    foreach ($inquiries as $inquiry) {
                        $productName = $inquiry->product ? $inquiry->product->translated_name : 'General Inquiry';

                        fputcsv($file, [
                            $inquiry->id,
                            $inquiry->created_at->format('Y-m-d H:i:s'),
                            $inquiry->name,
                            $inquiry->company,
                            $inquiry->email,
                            $inquiry->country_code,
                            $inquiry->phone ?? '-',
                            $productName,
                            $inquiry->volume ?? '-',
                            $inquiry->incoterms ?? '-',
                            strtoupper($inquiry->status),
                            $inquiry->ip_address,
                            $inquiry->message
                        ]);
                    }
                });

            fclose($file);
        };

        return response()->streamDownload($callback, $fileName, $headers);
    }
}
