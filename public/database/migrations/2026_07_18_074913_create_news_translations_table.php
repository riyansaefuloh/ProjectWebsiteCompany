<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    //  Run the migrations.
    
    public function up(): void
    {
        Schema::create('news_translations', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('news_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('locale', 5);

            $table->string('title');

            $table->text('excerpt')->nullable();

            $table->longText('content');

            $table->string('meta_title')->nullable();

            $table->text('meta_description')->nullable();

            $table->unique(['news_id', 'locale']);
        });
    }

    
    //  Reverse the migrations.
    
    public function down(): void
    {
        Schema::dropIfExists('news_translations');
    }
};
