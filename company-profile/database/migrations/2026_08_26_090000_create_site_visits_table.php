<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /*
     * Catatan kunjungan situs publik.
     *
     * Satu baris per halaman yang dibuka. Diisi oleh
     * App\Http\Middleware\RecordSiteVisit, yang HANYA dipasang pada kelompok
     * rute publik — kunjungan staf ke panel admin bukan lalu lintas situs.
     *
     * Kuncinya bigint berurut, bukan ULID seperti tabel lain di proyek ini.
     * Tabel ini satu-satunya yang isinya bertambah tiap halaman dibuka, bukan
     * tiap kali orang menekan Simpan; kunci yang menaik berurutan membuat
     * penambahan barisnya selalu jatuh di ujung indeks alih-alih menyisip di
     * tengah, dan indeksnya sendiri seperempat lebih ringan.
     *
     * Tidak ada alamat IP, nama, atau apa pun yang menunjuk ke satu orang.
     * Kolom `visitor` hanya sidik jari satu arah untuk memisahkan "seribu
     * halaman dibuka satu orang" dari "seribu orang membuka satu halaman".
     */
    public function up(): void
    {
        Schema::create('site_visits', function (Blueprint $table) {
            $table->id();

            $table->string('path');

            // sha256 — 64 aksara heksadesimal, panjangnya tetap.
            $table->string('visitor', 64);

            $table->timestamp('visited_at');

            // Dasbor selalu bertanya "berapa bulan ini" — penyaringnya waktu.
            $table->index('visited_at');

            // Untuk menghitung pengunjung unik dalam satu rentang waktu.
            $table->index(['visitor', 'visited_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_visits');
    }
};
