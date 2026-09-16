<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Run the migrations.
     
    public function up(): void
    {
        Schema::create('export_market_translations', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('export_market_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('locale', 5);
            $table->string('name');
            $table->text('note')->nullable();

            $table->unique(['export_market_id', 'locale']);
        }); 
    }

    //  Reverse the migrations.
    
    public function down(): void
    {
        Schema::dropIfExists('export_market_translations');
    }
};
