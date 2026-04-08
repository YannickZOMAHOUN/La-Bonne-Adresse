<?php

/*namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EstablishmentSeeder extends Seeder
{
    public function run()
    {
        $locations = [
            // ==========================================
            // COTONOU (30 Établissements)
            // ==========================================
            ['name' => 'Novotel Orisha', 'city' => 'Cotonou', 'type' => 'Hôtel', 'address' => 'Boulevard de la Marina'],
            ['name' => 'Golden Tulip Le Diplomate', 'city' => 'Cotonou', 'type' => 'Hôtel', 'address' => 'Gbégamey'],
            ['name' => 'Azalaï Hôtel', 'city' => 'Cotonou', 'type' => 'Hôtel', 'address' => 'Plage de Ganhi'],
            ['name' => 'Ibis Cotonou', 'city' => 'Cotonou', 'type' => 'Hôtel', 'address' => 'Boulevard de la Marina'],
            ['name' => 'Hôtel Sun Beach', 'city' => 'Cotonou', 'type' => 'Hôtel', 'address' => 'Fidjrossè'],
            ['name' => 'Bénin Royal Hôtel', 'city' => 'Cotonou', 'type' => 'Hôtel', 'address' => 'Marouvin'],
            ['name' => 'Maison Rouge', 'city' => 'Cotonou', 'type' => 'Hôtel', 'address' => 'Haie Vive'],
            ['name' => 'Hôtel du Lac', 'city' => 'Cotonou', 'type' => 'Hôtel', 'address' => 'Akpakpa'],
            ['name' => 'Résidence Hôtel Obama', 'city' => 'Cotonou', 'type' => 'Hôtel', 'address' => 'Aéroport'],
            ['name' => 'BJS Hôtel', 'city' => 'Cotonou', 'type' => 'Hôtel', 'address' => 'Agla'],

            ['name' => 'Le Panda', 'city' => 'Cotonou', 'type' => 'Restaurant', 'address' => 'Haie Vive'],
            ['name' => 'La Cabane du Pêcheur', 'city' => 'Cotonou', 'type' => 'Restaurant', 'address' => 'Plage Fidjrossè'],
            ['name' => 'Le Livingstone', 'city' => 'Cotonou', 'type' => 'Restaurant', 'address' => 'Haie Vive'],
            ['name' => 'Saveurs d’Afrique', 'city' => 'Cotonou', 'type' => 'Restaurant', 'address' => 'Cadjehoun'],
            ['name' => 'L’Imprévu', 'city' => 'Cotonou', 'type' => 'Restaurant', 'address' => 'Haie Vive'],
            ['name' => 'Le Teranga', 'city' => 'Cotonou', 'type' => 'Restaurant', 'address' => 'Ganhi'],
            ['name' => 'Shamiana', 'city' => 'Cotonou', 'type' => 'Restaurant', 'address' => 'Haie Vive'],
            ['name' => 'Wasabi', 'city' => 'Cotonou', 'type' => 'Restaurant', 'address' => 'Haie Vive'],
            ['name' => 'Pili Pili', 'city' => 'Cotonou', 'type' => 'Restaurant', 'address' => 'Fidjrossè'],
            ['name' => 'O’Poivre', 'city' => 'Cotonou', 'type' => 'Restaurant', 'address' => 'Cadjehoun'],

            ['name' => 'Résidence Les Cocotiers', 'city' => 'Cotonou', 'type' => 'Appartement', 'address' => 'Les Cocotiers'],
            ['name' => 'Blue Waves Furnished', 'city' => 'Cotonou', 'type' => 'Appartement', 'address' => 'Fidjrossè'],
            ['name' => 'Appartements Marina', 'city' => 'Cotonou', 'type' => 'Appartement', 'address' => 'Erevan'],
            ['name' => 'Sky Residence', 'city' => 'Cotonou', 'type' => 'Appartement', 'address' => 'Gbégamey'],
            ['name' => 'Residence Roméo', 'city' => 'Cotonou', 'type' => 'Appartement', 'address' => 'Haie Vive'],
            ['name' => 'Ebony Apartments', 'city' => 'Cotonou', 'type' => 'Appartement', 'address' => 'Zongo'],
            ['name' => 'Comfort Inn Benin', 'city' => 'Cotonou', 'type' => 'Appartement', 'address' => 'Agla'],
            ['name' => 'Espace Bienvenue', 'city' => 'Cotonou', 'type' => 'Appartement', 'address' => 'Ste Rita'],
            ['name' => 'Sweet Home Furnished', 'city' => 'Cotonou', 'type' => 'Appartement', 'address' => 'Akpakpa'],
            ['name' => 'Penthouse Cotonou', 'city' => 'Cotonou', 'type' => 'Appartement', 'address' => 'Haie Vive'],

            // ==========================================
            // BOHICON / ABOMEY (30 Établissements)
            // ==========================================
            ['name' => 'Hôtel Dako-Kpodji', 'city' => 'Bohicon', 'type' => 'Hôtel', 'address' => 'Quartier Dako'],
            ['name' => 'Hôtel Princesse Lydie', 'city' => 'Bohicon', 'type' => 'Hôtel', 'address' => 'Route de Parakou'],
            ['name' => 'Auberge d’Abomey', 'city' => 'Abomey', 'type' => 'Hôtel', 'address' => 'Musée Historique'],
            ['name' => 'Hôtel Singapour', 'city' => 'Bohicon', 'type' => 'Hôtel', 'address' => 'Face Gare'],
            ['name' => 'Le Palais Privé', 'city' => 'Abomey', 'type' => 'Hôtel', 'address' => 'Quartier Royal'],
            ['name' => 'Hôtel la Majesté', 'city' => 'Bohicon', 'type' => 'Hôtel', 'address' => 'Entrée ville'],
            ['name' => 'Hôtel Sun City Bohicon', 'city' => 'Bohicon', 'type' => 'Hôtel', 'address' => 'Gbégamey Bohicon'],
            ['name' => 'Hôtel Jeco', 'city' => 'Bohicon', 'type' => 'Hôtel', 'address' => 'Route Inter-états'],
            ['name' => 'Hotel La Détente', 'city' => 'Abomey', 'type' => 'Hôtel', 'address' => 'Zone Résidentielle'],
            ['name' => 'Auberge de la cité', 'city' => 'Abomey', 'type' => 'Hôtel', 'address' => 'Centre ville'],

            ['name' => 'Maquis Le Relais', 'city' => 'Bohicon', 'type' => 'Restaurant', 'address' => 'Carrefour Mouillé'],
            ['name' => 'La Verdure', 'city' => 'Bohicon', 'type' => 'Restaurant', 'address' => 'Quartier Agbanwémè'],
            ['name' => 'Restaurant Le Musée', 'city' => 'Abomey', 'type' => 'Restaurant', 'address' => 'Près du Palais'],
            ['name' => 'Chez Colette', 'city' => 'Bohicon', 'type' => 'Restaurant', 'address' => 'Marché de Bohicon'],
            ['name' => 'Saveurs du Plateau', 'city' => 'Abomey', 'type' => 'Restaurant', 'address' => 'Route de Djidja'],
            ['name' => 'L’Escale Gourmande', 'city' => 'Bohicon', 'type' => 'Restaurant', 'address' => 'Route de Cotonou'],
            ['name' => 'Maquis Béninois', 'city' => 'Abomey', 'type' => 'Restaurant', 'address' => 'Quartier Adandokpodji'],
            ['name' => 'Afro Food Bohicon', 'city' => 'Bohicon', 'type' => 'Restaurant', 'address' => 'Gare Routière'],
            ['name' => 'Le Palmier', 'city' => 'Bohicon', 'type' => 'Restaurant', 'address' => 'Centre Ville'],
            ['name' => 'La Paillote d’Abomey', 'city' => 'Abomey', 'type' => 'Restaurant', 'address' => 'Quartier Houndjro'],

            ['name' => 'Résidence Les Rois', 'city' => 'Abomey', 'type' => 'Appartement', 'address' => 'Quartier Palais'],
            ['name' => 'Appartements Plateau', 'city' => 'Bohicon', 'type' => 'Appartement', 'address' => 'Sodohomè'],
            ['name' => 'Villa Meublée Zou', 'city' => 'Bohicon', 'type' => 'Appartement', 'address' => 'Gbégnigan'],
            ['name' => 'Studio Confort Abomey', 'city' => 'Abomey', 'type' => 'Appartement', 'address' => 'Sogba'],
            ['name' => 'Résidence de la Paix', 'city' => 'Bohicon', 'type' => 'Appartement', 'address' => 'Lissezoun'],
            ['name' => 'Maison des Hôtes', 'city' => 'Abomey', 'type' => 'Appartement', 'address' => 'Djègbè'],
            ['name' => 'Logement VIP Bohicon', 'city' => 'Bohicon', 'type' => 'Appartement', 'address' => 'Zone administrative'],
            ['name' => 'Appartement du Centre', 'city' => 'Bohicon', 'type' => 'Appartement', 'address' => 'Agnonmé'],
            ['name' => 'Cité du Zou', 'city' => 'Bohicon', 'type' => 'Appartement', 'address' => 'Zakpo'],
            ['name' => 'Pavillon Royal', 'city' => 'Abomey', 'type' => 'Appartement', 'address' => 'Vidolè'],

            // ==========================================
            // PARAKOU (30 Établissements)
            // ==========================================
            ['name' => 'Hôtel Kobourou City', 'city' => 'Parakou', 'type' => 'Hôtel', 'address' => 'Quartier Albarika'],
            ['name' => 'Hôtel Les Routiers', 'city' => 'Parakou', 'type' => 'Hôtel', 'address' => 'Centre Ville'],
            ['name' => 'Tidjani Hôtel', 'city' => 'Parakou', 'type' => 'Hôtel', 'address' => 'Zone Industrielle'],
            ['name' => 'Hôtel La Cigale', 'city' => 'Parakou', 'type' => 'Hôtel', 'address' => 'Ladji Farani'],
            ['name' => 'Hôtel Kaba', 'city' => 'Parakou', 'type' => 'Hôtel', 'address' => 'Quartier Banikanni'],
            ['name' => 'Hôtel Baobab', 'city' => 'Parakou', 'type' => 'Hôtel', 'address' => 'Route de Malanville'],
            ['name' => 'Hôtel de l’Univers', 'city' => 'Parakou', 'type' => 'Hôtel', 'address' => 'Avenue Hubert Maga'],
            ['name' => 'La Résidence Parakou', 'city' => 'Parakou', 'type' => 'Hôtel', 'address' => 'Quartier Camp Adagbé'],
            ['name' => 'Hôtel Sero Kora', 'city' => 'Parakou', 'type' => 'Hôtel', 'address' => 'Aéroport Parakou'],
            ['name' => 'Hôtel Relax', 'city' => 'Parakou', 'type' => 'Hôtel', 'address' => 'Wansirou'],

            ['name' => 'Restaurant Le Robinson', 'city' => 'Parakou', 'type' => 'Restaurant', 'address' => 'Albarika'],
            ['name' => 'La Face Douce', 'city' => 'Parakou', 'type' => 'Restaurant', 'address' => 'Banikanni'],
            ['name' => 'Maquis Le Consulat', 'city' => 'Parakou', 'type' => 'Restaurant', 'address' => 'Ladji Farani'],
            ['name' => 'Le Gourmet', 'city' => 'Parakou', 'type' => 'Restaurant', 'address' => 'Centre ville'],
            ['name' => 'Saveurs du Nord', 'city' => 'Parakou', 'type' => 'Restaurant', 'address' => 'Près du Marché Arzéké'],
            ['name' => 'Le Patio', 'city' => 'Parakou', 'type' => 'Restaurant', 'address' => 'Quartier Zongo'],
            ['name' => 'Fast Food Borgou', 'city' => 'Parakou', 'type' => 'Restaurant', 'address' => 'Titirou'],
            ['name' => 'Chez Mama Bénin', 'city' => 'Parakou', 'type' => 'Restaurant', 'address' => 'Baka'],
            ['name' => 'L’Escale du Nord', 'city' => 'Parakou', 'type' => 'Restaurant', 'address' => 'Route de Kandi'],
            ['name' => 'Maquis Les Parakois', 'city' => 'Parakou', 'type' => 'Restaurant', 'address' => 'Tibona'],

            ['name' => 'Cité Kobourou', 'city' => 'Parakou', 'type' => 'Appartement', 'address' => 'Albarika'],
            ['name' => 'Résidence Alafia', 'city' => 'Parakou', 'type' => 'Appartement', 'address' => 'Zongo-Zenon'],
            ['name' => 'Studios Meublés Banikanni', 'city' => 'Parakou', 'type' => 'Appartement', 'address' => 'Banikanni'],
            ['name' => 'Villa Parakou VIP', 'city' => 'Parakou', 'type' => 'Appartement', 'address' => 'Camp Adagbé'],
            ['name' => 'Appartements Borgou', 'city' => 'Parakou', 'type' => 'Appartement', 'address' => 'Tranza'],
            ['name' => 'Le Refuge Meublé', 'city' => 'Parakou', 'type' => 'Appartement', 'address' => 'Okédama'],
            ['name' => 'Résidence Nord-Sud', 'city' => 'Parakou', 'type' => 'Appartement', 'address' => 'Sinagourou'],
            ['name' => 'Hébergement Plus', 'city' => 'Parakou', 'type' => 'Appartement', 'address' => 'Titirou'],
            ['name' => 'Maison Papillon', 'city' => 'Parakou', 'type' => 'Appartement', 'address' => 'Wansirou'],
            ['name' => 'Appartement Arzéké', 'city' => 'Parakou', 'type' => 'Appartement', 'address' => 'Centre Commercial'],
        ];

        foreach ($locations as $item) {
            DB::table('establishments')->insert([
                'name' => $item['name'],
                'slug' => Str::slug($item['name'] . '-' . $item['city']),
                'city' => $item['city'],
                'category' => $item['type'],
                'address' => $item['address'],
                'description' => "Bienvenue à " . $item['name'] . ". Une adresse de référence pour votre séjour ou vos repas à " . $item['city'] . ".",
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}*/
