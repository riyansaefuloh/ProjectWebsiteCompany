<?php

namespace App\Services;

use App\Models\Product;
use App\Models\News;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class JsonLdService
{
    public static function organizationSchema(): array
    {
        $logoPath = Setting::where('key', 'logo')->value('value');
        $logoUrl = $logoPath ? Storage::disk('public')->url($logoPath) : asset('logo.png');

        $socials = [];
        if ($fb = Setting::where('key', 'facebook_url')->value('value')) $socials[] = $fb;
        if ($ig = Setting::where('key', 'instagram_url')->value('value')) $socials[] = $ig;
        if ($in = Setting::where('key', 'linkedin_url')->value('value')) $socials[] = $in;

        return [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => config('app.name', 'Exporter Company'),
            'url'      => url('/'),
            'logo'     => $logoUrl,
            'sameAs'   => $socials
        ];
    }

    public static function productSchema(Product $product): array
    {
        return [
            '@context'    => 'https://schema.org',
            '@type'       => 'Product',
            'name'        => $product->translated_name,
            'description' => $product->translated_description,
            'image'       => $product->getFirstMediaUrl('gallery', 'webp') ?: null,
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

    public static function articleSchema(News $news): array
    {
        return [
            '@context'      => 'https://schema.org',
            '@type'         => 'Article',
            'headline'      => $news->translated_title,
            'image'         => $news->getFirstMediaUrl('covers', 'webp') ?: null,
            'datePublished' => $news->published_at ? $news->published_at->toIso8601String() : $news->created_at->toIso8601String(),
            'author'        => [
                '@type' => 'Person',
                'name'  => $news->author ? $news->author->name : 'Export Team'
            ]
        ];
    }

    public static function breadcrumbSchema(array $items): array
    {
        $listElements = [];

        foreach ($items as $position => $item) {
            $element = [
                '@type'    => 'ListItem',
                'position' => $position + 1,
                'name'     => $item['name'],
            ];

            if (!empty($item['url'])) {
                $element['item'] = $item['url'];
            }

            $listElements[] = $element;
        }

        return [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $listElements,
        ];
    }

    // ============================================================
    // HELPER — Breadcrumb siap pakai per halaman
    // ============================================================

    public static function productBreadcrumb(Product $product): array
    {
        return self::breadcrumbSchema([
            ['name' => 'Home',     'url' => url('/')],
            ['name' => 'Products', 'url' => route('products.index')],
            ['name' => $product->translated_name],
        ]);
    }

    public static function newsBreadcrumb(News $news): array
    {
        return self::breadcrumbSchema([
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'News', 'url' => route('news.index')],
            ['name' => $news->translated_title],
        ]);
    }

    public static function aboutBreadcrumb(): array
    {
        return self::breadcrumbSchema([
            ['name' => 'Home',     'url' => url('/')],
            ['name' => 'About Us'],
        ]);
    }
}
