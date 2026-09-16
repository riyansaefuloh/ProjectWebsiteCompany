<?php

namespace Database\Seeders;

use App\Models\Gallery;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    public function run(): void
    {
        // Hanya membuat album galeri dengan kolom 'name'
        $gallery = Gallery::create([
            'name' => 'Coffee Processing Facility',
        ]);

        // Karena data file gambar asli disimpan di tabel 'media' Spatie,
        // di tabel gallery_items kita cukup membuat record kosong penampung id.
        $gallery->items()->create();
        $gallery->items()->create();
    }
}
