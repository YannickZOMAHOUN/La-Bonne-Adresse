<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etablissements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('ville_id')->constrained()->onDelete('cascade');
            $table->foreignId('categorie_id')->constrained()->onDelete('cascade');

            $table->string('nom');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('adresse');
            $table->string('telephone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('site_web')->nullable();

            // Localisation Google Maps
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Horaires (format JSON simple)
            $table->json('horaires')->nullable();

            // Prix indicatif
            $table->string('fourchette_prix')->nullable(); // ex: "2000 - 5000 FCFA"

            // Photo principale
            $table->string('photo_principale')->nullable();

            // Statut : en_attente | actif | suspendu
            $table->enum('statut', ['en_attente', 'actif', 'suspendu'])->default('en_attente');

            // Mis en avant par l'admin
            $table->boolean('en_vedette')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etablissements');
    }
};
