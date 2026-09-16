<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    //  Run the migrations.
    
    public function up(): void
    {
        Schema::create('downloads', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->string('title');

            $table->string('file_path');

            $table->boolean('require_email')
                ->default(false);

            $table->unsignedBigInteger('download_count')
                ->default(0);

            $table->integer('sort_order')
                ->default(0);

            $table->timestamps();
        });
    }

    
    //  Reverse the migrations.
    
    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};
