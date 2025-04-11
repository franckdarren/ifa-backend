<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('image_articles', function (Blueprint $table) {
            $table->id();
            $table->string('url_photo');
            $table->foreignId('variation_id')->nullable()->constrained('variations')->onDelete('cascade');

            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_articles');
    }
};