<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Membuang kolom 'file_path' dari tabel certifications.
 *
 * Kolom ini dirancang untuk menyimpan alamat berkas PDF sertifikat, tapi
 * pekerjaannya sudah diambil alih spatie/laravel-medialibrary lewat koleksi
 * 'pdfs' — di situlah unggahan, penghapusan, dan tautan unduhannya berjalan.
 *
 * Yang tertinggal cuma kolom yang tidak pernah diisi, tidak punya isian di
 * panel, dan tidak pernah dibaca satu tampilan pun. Kolom semacam itu berbahaya
 * bukan karena memakan tempat, tapi karena orang berikutnya akan menyangka ia
 * berarti sesuatu lalu menulis kode yang membacanya.
 *
 * Aman dijalankan: seluruh barisnya bernilai kosong sewaktu migrasi ini dibuat.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('certifications', 'file_path')) {
            return;
        }

        Schema::table('certifications', function (Blueprint $table) {
            $table->dropColumn('file_path');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('certifications', 'file_path')) {
            return;
        }

        Schema::table('certifications', function (Blueprint $table) {
            $table->string('file_path')->nullable();
        });
    }
};
