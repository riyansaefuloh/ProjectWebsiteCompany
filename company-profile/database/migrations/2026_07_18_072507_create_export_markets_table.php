<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Run the migrations.
     
    public function up(): void
    {
        Schema::create('export_markets', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->string('country_code', 2);
            $table->string('region');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });
    }

    // Reverse the migrations.
     
    public function down(): void
    {
        Schema::dropIfExists('export_markets');
    }
};
