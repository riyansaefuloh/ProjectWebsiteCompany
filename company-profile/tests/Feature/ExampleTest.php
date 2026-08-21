<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /*
     * RefreshDatabase WAJIB.
     *
     * Halaman masuk membaca tabel settings untuk mengambil logo dan nama
     * perusahaan. Tanpa migrasi, uji ini mati dengan "no such table: settings"
     * — bukan karena halamannya rusak, tapi karena basis datanya kosong.
     */
    use RefreshDatabase;

    /**
     * Halaman masuk panel admin terbuka.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->followingRedirects()->get('/login')->assertStatus(200);
    }
}
