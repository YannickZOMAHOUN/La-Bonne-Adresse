<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Galerie photos
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etablissement_id')->constrained()->onDelete('cascade');
            $table->string('url');
            $table->string('legende')->nullable();
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });

        // Services proposés par l'établissement
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etablissement_id')->constrained()->onDelete('cascade');
            $table->string('libelle');  // ex: "WiFi", "Climatisation", "Parking"
            $table->string('emoji')->default('✅');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
        Schema::dropIfExists('photos');
    }
};
