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
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->string('numero');
            $table->enum('statut', ['En attente', 'En préparation', 'Prête pour livraison', 'En cours de livraison', 'Livrée', 'Annulée', 'Remboursée'])->default('En attente');
            $table->integer('prix');
            $table->string('commentaire');
            $table->boolean('isLivrable');

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
