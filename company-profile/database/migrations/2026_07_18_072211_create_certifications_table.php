<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Run the migrations.
     
    public function up(): void
    {
        Schema::create('certifications', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->string('slug')->unique();
            $table->string('issuer');
            $table->string('certificate_number')->nullable();

            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();

            $table->string('file_path')->nullable();

            $table->string('status')->default('active');
            $table->integer('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    
    // Reverse the migrations.
    
    public function down(): void
    {
        Schema::dropIfExists('certifications');
    }
};
