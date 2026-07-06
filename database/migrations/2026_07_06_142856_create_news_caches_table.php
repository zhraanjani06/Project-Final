<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('news_caches', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 3)->index();
            $table->string('title', 500);
            $table->text('description')->nullable();
            $table->text('url');
            $table->string('source_name')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('sentiment')->default('Neutral'); // Positive, Negative, Neutral
            $table->integer('sentiment_score')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_caches');
    }
};
