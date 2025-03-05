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
            // $table->integer('quantité');
            // $table->boolean('isDisponible')->default(true);
            $table->boolean('isPromotion')->default(false);
            $table->integer('pourcentageReduction')->default(0);

            $table->boolean('madeInGabon')->default(false);

            $table->string('type'); // Type de l'article (ex: chaussures, vêtements...)
            $table->json('caracteristiques'); // Stocke les caractéristiques spécifiques à chaque type


            $table->foreignId('boutique_id')->constrained('boutiques')->onDelete('cascade');
            $table->foreignId('sous_categorie_id')->constrained('sous_categories')->onDelete('cascade');


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
