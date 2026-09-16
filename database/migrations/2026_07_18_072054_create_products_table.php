<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {

            $table->ulid('id')->primary();

            $table->foreignUlid('category_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('slug')->unique();

            $table->string('hs_code');

            $table->string('moq');

            $table->string('supply_capacity');

            $table->string('packaging');

            $table->string('origin');

            $table->decimal('indicative_price', 15, 2)->nullable();

            $table->string('currency', 3)->default('USD');

            $table->string('incoterms');

            $table->boolean('is_featured')->default(false);

            $table->enum('status', [
                'draft',
                'published'
            ])->default('draft');

            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};