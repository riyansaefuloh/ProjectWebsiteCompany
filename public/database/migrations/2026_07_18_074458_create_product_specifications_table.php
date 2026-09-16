<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //  Run the migrations.
    
    public function up(): void
    {
        Schema::create('product_specifications', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('spec_key');
            $table->text('spec_value');

            $table->string('locale', 5)->nullable();

            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });
    }

    
    // Reverse the migrations.
     
    public function down(): void
    {
        Schema::dropIfExists('product_specifications');
    }
};
