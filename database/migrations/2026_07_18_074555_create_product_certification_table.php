<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    // Run the migrations.
    
    public function up(): void
    {
        Schema::create('product_certification', function (Blueprint $table) {

            $table->foreignUlid('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUlid('certification_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->primary([
                'product_id',
                'certification_id'
            ]);
        });
    }

    
    //  Reverse the migrations.
     
    public function down(): void
    {
        Schema::dropIfExists('product_certification');
    }
};
