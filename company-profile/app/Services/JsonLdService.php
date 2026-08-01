<?php

namespace App\Services;

use App\Models\Product;
use App\Models\News;

class JsonLdService
{
    /**
     * Schema.org Organization (Dipasang di Beranda & About)
     */
    public static function organizationSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => config('app.name', 'Exporter Company'),
            'url'      => url('/'),
            'logo'     => asset('logo.png'),
            'sameAs'   => [
                'https://facebook.com',
                'https://linkedin.com',
                'https://instagram.com'
            ]
        ];
    }

    /**
     * Schema.org Product (Dipasang di Detail Produk)
     */
    public static function productSchema(Product $product): array
    {
        return [
            '@context'    => 'https://schema.org',
            '@type'       => 'Product',
            'name'        => $product->translated_name,
            'description' => $product->translated_description,
            'sku'         => $product->hs_code,
            'mpn'         => $product->id,
            'offers'      => [
                '@type'         => 'Offer',
                'priceCurrency' => $product->currency ?? 'USD',
                'price'         => $product->indicative_price ?? '0.00',
                'availability'  => 'https://schema.org/InStock',
                'seller'        => [
                    '@type' => 'Organization',
                    'name'  => config('app.name')
                ]
            ]
        ];
    }

    /**
     * Schema.org Article (Dipasang di Detail Berita)
     */
    public static function articleSchema(News $news): array
    {
        return [
            '@context'      => 'https://schema.org',
            '@type'         => 'Article',
            'headline'      => $news->translated_title,
            'datePublished' => $news->published_at ? $news->published_at->toIso8601String() : $news->created_at->toIso8601String(),
            'author'        => [
                '@type' => 'Person',
                'name'  => $news->author ? $news->author->name : 'Export Team'
            ]
        ];
    }
}
