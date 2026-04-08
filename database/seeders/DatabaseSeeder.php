<?php

namespace Database\Seeders;

use App\Models\Ville;
use App\Models\Categorie;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Villes ────────────────────────────────────────────
        $villes = [
            [
                'nom' => 'Cotonou',
                'slug' => 'cotonou',
                'emoji' => '🌊',
                'description' => 'Capitale économique du Bénin'
            ],
            [
                'nom' => 'Bohicon/Abomey',
                'slug' => 'bohicon-abomey',
                'emoji' => '🏛️',
                'description' => 'Cœur historique du Bénin'
            ],
            [
                'nom' => 'Parakou',
                'slug' => 'parakou',
                'emoji' => '🌄',
                'description' => 'Capitale du Nord'
            ],
        ];
        foreach ($villes as $ville) {
            Ville::firstOrCreate(['slug' => $ville['slug']], $ville);
        }

        // ── Catégories ────────────────────────────────────────
        $categories = [
            [
                'nom' => 'Restaurants',
                'slug' => 'restaurants',
                'emoji' => '🍽️',
                'description' => 'Restaurants, maquis, fast-food'
            ],
            [
                'nom' => 'Hôtels',
                'slug' => 'hotels',
                'emoji' => '🏨',
                'description' => 'Hôtels et hébergements'
            ],
            [
                'nom' => 'Appartements meublés',
                'slug' => 'appartements-meubles',
                'emoji' => '🏠',
                'description' => 'Appartements et studios meublés'
            ],
        ];
        foreach ($categories as $cat) {
            Categorie::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        // ── Admin ─────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'admin@bonnesadresses.bj'],
            [
                'nom'      => 'Administrateur',
                'email'    => 'admin@bonnesadresses.bj',
                'password' => Hash::make('Admin@2025!'),
                'role'     => 'admin',
                'statut'   => 'actif',
            ]
        );

        $this->command->info('✅ Données initiales créées avec succès !');
        $this->command->info('📧 Admin : admin@bonnesadresses.bj / Admin@2025!');
        //$this->call(EstablishmentSeeder::class);
    }
}
