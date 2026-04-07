<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Créer la table menus
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etablissement_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('url');                    // chemin Storage
            $table->string('type')->default('image'); // 'image' ou 'pdf'
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });

        // Supprimer l'ancienne colonne menu si elle existe déjà
        if (Schema::hasColumn('etablissements', 'menu')) {
            Schema::table('etablissements', function (Blueprint $table) {
                $table->dropColumn('menu');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');

        Schema::table('etablissements', function (Blueprint $table) {
            $table->string('menu')->nullable()->after('fourchette_prix');
        });
    }
};
