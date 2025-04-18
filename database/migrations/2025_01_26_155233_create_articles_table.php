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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('description')->nullable();
            $table->integer('prix');
            $table->integer('prixPromotion')->nullable();

            $table->boolean('isPromotion')->default(false);
            $table->integer('pourcentageReduction')->default(0);

            $table->boolean('madeInGabon')->default(false);

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('categorie');
            $table->string('image_principale')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
