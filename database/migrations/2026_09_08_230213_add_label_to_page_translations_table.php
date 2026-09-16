<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Label kecil di atas judul halaman statis — "Legal", "Informasi", apa pun.
 *
 * Di page_translations, bukan di pages: labelnya kata yang dibaca pengunjung,
 * jadi ia harus bisa berbeda antara EN dan ID sama seperti judul dan isinya.
 *
 * nullable: halaman yang tidak diberi label tidak menggambar apa-apa di sana,
 * bukan menggambar label kosong.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_translations', function (Blueprint $table) {
            $table->string('label', 60)->nullable()->after('locale');
        });
    }

    public function down(): void
    {
        Schema::table('page_translations', function (Blueprint $table) {
            $table->dropColumn('label');
        });
    }
};
