<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Product Catalog - {{ $companyName }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        .header { text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { color: #1e3a8a; margin: 0; font-size: 24px; text-transform: uppercase; }
        .header p { margin: 5px 0 0 0; color: #666; font-size: 11px; }
        .product-card { border: 1px solid #e5e7eb; border-radius: 6px; padding: 15px; margin-bottom: 20px; page-break-inside: avoid; }
        .product-title { font-size: 16px; font-weight: bold; color: #1d4ed8; margin: 0 0 10px 0; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .spec-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .spec-table th, .spec-table td { border: 1px solid #d1d5db; padding: 6px 10px; font-size: 11px; text-align: left; }
        .spec-table th { background-color: #f3f4f6; color: #374151; }
        .badge { background-color: #dbeafe; color: #1e40af; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: bold; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 10px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $companyName }}</h1>
        <p>EXPORT PRODUCT CATALOG — {{ $date }}</p>
    </div>

    @foreach($products as $product)
        <div class="product-card">
            <div class="product-title">
                {{ $product->translated_name ?? 'Product Name' }}
                @if($product->category)
                    <span class="badge">{{ $product->category->translated_name }}</span>
                @endif
            </div>

            <p><strong>Description:</strong> {{ $product->translated_description ?? '-' }}</p>

            <table class="spec-table">
                <tr>
                    <th>HS Code</th>
                    <td>{{ $product->hs_code }}</td>
                    <th>MOQ</th>
                    <td>{{ $product->moq }}</td>
                </tr>
                <tr>
                    <th>Supply Capacity</th>
                    <td>{{ $product->supply_capacity }}</td>
                    <th>Packaging</th>
                    <td>{{ $product->packaging }}</td>
                </tr>
                <tr>
                    <th>Origin</th>
                    <td>{{ $product->origin }}</td>
                    <th>Incoterms</th>
                    <td>{{ $product->incoterms }}</td>
                </tr>
                @if($product->indicative_price)
                <tr>
                    <th>Indicative Price</th>
                    <td colspan="3">{{ $product->currency }} {{ number_format($product->indicative_price, 2) }}</td>
                </tr>
                @endif
            </table>
        </div>
    @endforeach

    <div class="footer">
        Generated automatically by {{ $companyName }} Export System. For inquiries: contact us via website.
    </div>

</body>
</html>
