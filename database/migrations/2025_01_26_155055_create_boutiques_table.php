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
        Schema::create('boutiques', function (Blueprint $table) {
            $table->id();
            $table->string('adresse');
            $table->string('nom');
            $table->string('phone');
            $table->string('url_logo');
            $table->string('heure_ouverture');
            $table->string('heure_fermeture');
            $table->string('description');

            $table->boolean('is_active')->default(true);
            $table->integer('solde')->default(0);
            // $table->timestamp('trial_ends_at')->nullable();
            // $table->timestamp('next_payment_date')->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boutiques');
    }
};