<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    // Run the migrations.
    
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->string('name');
            $table->string('company');
            $table->string('email');
            $table->string('country_code', 2);

            $table->string('phone')->nullable();

            $table->foreignUlid('product_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('volume')->nullable();
            $table->string('incoterms')->nullable();

            $table->text('message');

            $table->enum('status', [
                'new',
                'processing',
                'quoted',
                'closed',
                'rejected'
            ])->default('new');

            $table->foreignUlid('assigned_to')
                ->nullable()
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->text('internal_note')->nullable();

            $table->string('ip_address', 45);

            $table->timestamps();
        });
    }

    
    // Reverse the migrations.
    
    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
