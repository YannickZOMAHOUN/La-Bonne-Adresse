<?php

namespace Database\Seeders;

use App\Models\Categorie;
use App\Models\Etablissement;
use App\Models\User;
use App\Models\Ville;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BeninAddressesImportSeeder extends Seeder
{
    public function run(): void
    {
        // Villes demandées par le client : Abomey-Calavi est distincte de Abomey.
        $villes = [
            'cotonou' => Ville::updateOrCreate(
                ['slug' => 'cotonou'],
                [
                    'nom' => 'Cotonou',
                    'emoji' => '🌊',
                    'description' => 'Capitale économique du Bénin',
                    'active' => true,
                ]
            ),
            'porto-novo' => Ville::updateOrCreate(
                ['slug' => 'porto-novo'],
                [
                    'nom' => 'Porto-Novo',
                    'emoji' => '🏛️',
                    'description' => 'Capitale administrative du Bénin',
                    'active' => true,
                ]
            ),
            'abomey-calavi' => Ville::updateOrCreate(
                ['slug' => 'abomey-calavi'],
                [
                    'nom' => 'Abomey-Calavi',
                    'emoji' => '🏘️',
                    'description' => 'Grande commune de l’Atlantique distincte d’Abomey',
                    'active' => true,
                ]
            ),
        ];

        $categories = [
            'restaurants' => Categorie::updateOrCreate(
                ['slug' => 'restaurants'],
                [
                    'nom' => 'Restaurants',
                    'emoji' => '🍽️',
                    'description' => 'Restaurants, maquis, fast-food',
                    'active' => true,
                ]
            ),
            'hotels' => Categorie::updateOrCreate(
                ['slug' => 'hotels'],
                [
                    'nom' => 'Hôtels',
                    'emoji' => '🏨',
                    'description' => 'Hôtels et hébergements',
                    'active' => true,
                ]
            ),
            'appartements-meubles' => Categorie::updateOrCreate(
                ['slug' => 'appartements-meubles'],
                [
                    'nom' => 'Appartements meublés',
                    'emoji' => '🏠',
                    'description' => 'Appartements et studios meublés',
                    'active' => true,
                ]
            ),
        ];

        // Compte technique d’import pour rattacher les 90 fiches.
        $owner = User::updateOrCreate(
            ['email' => 'import@bonnesadresses.bj'],
            [
                'nom' => 'Import Bonnes Adresses',
                'telephone' => null,
                'password' => Hash::make('Import@2026!'),
                'role' => 'proprietaire',
                'statut' => 'actif',
            ]
        );

        $catalogue = [
            'cotonou' => [
                'hotels' => [
                    ['nom' => 'Novotel Cotonou Orisha', 'adresse' => 'Boulevard de la Marina, Cotonou, Benin', 'telephone' => '+229 21305662', 'site_web' => 'https://all.accor.com/lien_externe.svlt?goto=fiche_hotel&code_hotel=1826&merchantid=seo-maps-BJ-1826&sourceid=aw-cen&utm_medium=seo%20maps&utm_source=google%20Maps&utm_campaign=seo%20maps'],
                    ['nom' => 'Sofitel Cotonou Marina Hotel & Spa', 'adresse' => '1820 Boulevard de la Marina, Cotonou, Benin', 'telephone' => '+229 01 20 90 04 61', 'site_web' => 'http://sofitel.accor.com/hotels/B845?merchantid=seo-maps-0-B845&sourceid=aw-cen&utm_source=google+Maps&utm_medium=seo+maps&utm_campaign=seo+maps&utm_term=Sofitel-Launch'],
                    ['nom' => 'Golden Tulip Diplomate Cotonou', 'adresse' => 'N° 90 rue 12.017 - Boulevard de la Marina, Cotonou, Benin', 'telephone' => '+229 01 98 30 02 00', 'site_web' => 'http://www.goldentuliplediplomatecotonou.com/'],
                    ['nom' => 'Azalaï Hôtel de la Plage', 'adresse' => 'Quartier Ganhi - Cotonou, Cotonou, Benin', 'telephone' => '+229 01 21 31 72 00', 'site_web' => 'https://www.azalai.com/azalaihotelcotonou/'],
                    ['nom' => 'Bénin Royal Hôtel', 'adresse' => 'Lot 398 quartier Maro-Militaire, Vons face Toxi Labo, Av. Germain Olory Togbé, Cotonou, Benin', 'telephone' => '+229 01 65 89 89 89', 'site_web' => 'https://www.beninroyalhotel.com/en/'],
                    ['nom' => 'Maison Rouge Cotonou', 'adresse' => 'Boulevard de la Marina, Cotonou, Benin', 'telephone' => '+229 01 65 12 69 89', 'site_web' => 'http://hotel-benin-maison-rouge-cotonou.com/'],
                    ['nom' => 'Hotel du Lac', 'adresse' => 'Cotonou, Benin', 'telephone' => '+229 21331919', 'site_web' => 'http://www.hoteldulacbenin.com/'],
                    ['nom' => 'Sun Beach Hôtel', 'adresse' => 'Fidjrossè Calvaire, Cotonou, Benin', 'telephone' => '+229 21302690', 'site_web' => 'https://sites.google.com/view/sun-beach-hotel/'],
                    ['nom' => 'LE KARE EBENE', 'adresse' => 'Place de Martyrs, Cotonou, Benin', 'telephone' => '+229 01 64 40 40 40', 'site_web' => 'http://www.lekareebene.com/'],
                    ['nom' => 'Hôtel Tahiti benin', 'adresse' => 'Rue 1531, Cotonou, Benin', 'telephone' => '+229 97111777', 'site_web' => 'https://sites.google.com/view/hotel-tahiti-benin/'],
                ],
                'restaurants' => [
                    ['nom' => 'ESCOBAR', 'adresse' => '9C49+92, Cotonou, Benin', 'telephone' => '+229 01 97 77 77 77'],
                    ['nom' => 'Face À La Mer', 'adresse' => '03 BP 68 Fidjrossè Route des pêches, Cotonou, Benin', 'telephone' => '+229 90390303', 'site_web' => 'http://www.facealamer.bj/'],
                    ['nom' => 'Ya-Hala', 'adresse' => '9C6P+78C, Rue 108, Cotonou, Benin', 'telephone' => '+229 66555999'],
                    ['nom' => 'La Pirogue', 'adresse' => 'Rue 104B, Cotonou, Benin', 'telephone' => '+229 91954444', 'site_web' => 'https://www.canva.com/design/DAFUukMpZcg/ZBhlbYiHtRAH8uBgYr4kgQ/edit?utm_content=DAFUukMpZcg&utm_campaign=designshare&utm_medium=link2&utm_source=sharebutton'],
                    ['nom' => 'Le Lieu Unique', 'adresse' => '08 Bp, 67 avenue de la francophonie, Cotonou, Benin', 'telephone' => '+229 01 67 54 98 58'],
                    ['nom' => 'L\'Imprévu', 'adresse' => 'Face à ECOBANK direction general, Ganhi, Cotonou, Benin', 'telephone' => '+229 01 66 97 40 40'],
                    ['nom' => 'Paulcuisine', 'adresse' => '995C+MV8, Cotonou, Benin', 'telephone' => '+229 01 66 57 09 91'],
                    ['nom' => 'La Cabane du Pêcheur', 'adresse' => '982V+339, Cotonou, Benin', 'telephone' => '+229 01 95 55 00 03'],
                    ['nom' => 'Bangkok Terrasse', 'adresse' => 'Rue 449, Cotonou, Benin', 'telephone' => '+229 01 21 30 37 86'],
                    ['nom' => 'L\'insolite', 'adresse' => '9C86+6J9, Cotonou, Benin', 'telephone' => '+229 01 67 26 01 75'],
                ],
                'appartements-meubles' => [
                    ['nom' => 'Appartement meublé à Cotonou', 'adresse' => 'Cotonou, Benin', 'telephone' => '+229 01 97 20 11 07'],
                    ['nom' => 'Résidence NC - Appartements meublés', 'adresse' => '229 Rue 2470, Cotonou, Benin', 'telephone' => '+229 01 95 95 08 93', 'site_web' => 'https://www.residence-nc.com/'],
                    ['nom' => 'JAAK RESIDENCE (OFFICIEL)', 'adresse' => 'Cotonou, Benin', 'telephone' => '+229 01 97 69 33 33', 'site_web' => 'http://jaakresidence.jaakgroup.com/'],
                    ['nom' => 'Residence meublée D.E.F', 'adresse' => '9929+8W6, Cotonou, Benin', 'telephone' => '+229 01 56 56 53 21'],
                    ['nom' => 'Ola guest house (Résidence) appartement meublé', 'adresse' => '229 Akpakpa, Cotonou, Benin', 'telephone' => '+229 61160654'],
                    ['nom' => 'Résidences et appartements meublés Naris - Cotonou Fidjrossè', 'adresse' => 'Unnamed Road, Cotonou, Benin', 'telephone' => '+229 40935757'],
                    ['nom' => 'FLIMI RESIDENCE', 'adresse' => '3267 Agla Figaro, Cotonou, Benin', 'telephone' => '+229 47101014', 'site_web' => 'https://www.flimiresidence.com/'],
                    ['nom' => 'Appartements meublés "La Grâce"', 'adresse' => '99MR+XQ6, 072BP 041, Cotonou, Benin'],
                    ['nom' => 'RÉSIDENCE AHA', 'adresse' => 'Cotonou, Benin', 'telephone' => '+229 51003444'],
                    ['nom' => 'Les Ombrelles Appart\'Hotel (LOAH)', 'adresse' => 'Rue 2521A, Cotonou, Benin', 'telephone' => '+229 55818181'],
                ],
            ],
            'porto-novo' => [
                'hotels' => [
                    ['nom' => 'ART RESIDENCE Hotel', 'adresse' => 'ilot 113 rue 40 Avakpa Kpodji, Porto-Novo, Benin', 'telephone' => '+229 01 91 71 64 64', 'site_web' => 'https://artresidence.bj/'],
                    ['nom' => 'TOUR EIFFEL HOTEL BENIN', 'adresse' => 'B.P. 1946 COTONOU, BENIN, Porto-Novo, Benin', 'telephone' => '+229 01 97 60 98 82', 'site_web' => 'http://www.toureiffelhotel.com/'],
                    ['nom' => 'Freedom Palace Hôtel', 'adresse' => 'Agbokou Avakpa, Porto-Novo, Benin', 'telephone' => '+229 01 62 13 14 11'],
                    ['nom' => 'HOTEL AYELAWADJE 2', 'adresse' => 'Porto-Novo, Benin'],
                    ['nom' => 'Hotel Dona', 'adresse' => 'Porto-Novo, Benin'],
                    ['nom' => 'MB-Group Hotel', 'adresse' => 'FJF6+6VX, Porto-Novo, Benin', 'telephone' => '+229 63100564'],
                    ['nom' => 'Hotel Novella Planet', 'adresse' => 'Immeuble HOUNSINOU, quartier Houinmè, Porto-Novo, Benin', 'telephone' => '+229 01 50 02 92 08'],
                    ['nom' => 'Queen\'s Hotel', 'adresse' => '1ère Rue à gauche après l’Eglise Catholique, Djassin-Houinvié, Porto-Novo, Benin', 'telephone' => '+229 95050885'],
                    ['nom' => 'Hotel Les Oliviers', 'adresse' => '01BP1939, Porto-Novo, Benin', 'telephone' => '+229 97723284'],
                    ['nom' => 'Hôtel Résidence Santa Claudia', 'adresse' => 'Lot 327M, Porto-Novo, Benin', 'telephone' => '+229 53545350', 'site_web' => 'https://www.hrsantaclaudia.com/'],
                ],
                'restaurants' => [
                    ['nom' => 'Restaurant ART RESIDENCE - Porto Novo', 'adresse' => 'Immeuble abritant Coris Bank ilot 113 rue 40 Agbokou Avakpa, Porto-Novo, Benin', 'telephone' => '+229 01 91 71 64 64', 'site_web' => 'https://artresidence.bj/'],
                    ['nom' => 'African Foodseum', 'adresse' => '01 BP 1091, Porto-Novo, Benin', 'telephone' => '+229 01 62 74 76 28'],
                    ['nom' => 'L\'Endroit by Olabissi', 'adresse' => 'FJF8+3WF, Ave William Ponty, Porto-Novo, Benin', 'telephone' => '+229 01 60 13 56 56'],
                    ['nom' => 'Rehoboth', 'adresse' => 'Porto-Novo, Benin', 'telephone' => '+229 01 94 60 88 28'],
                    ['nom' => 'O\'tentiQ Café Bar Restaurant', 'adresse' => 'GJ5G+M9W, Bd du Cinquantenaire, Porto-Novo, Benin', 'telephone' => '+229 01 94 19 57 40'],
                    ['nom' => 'DÊGUÊ PALACE Restaurant', 'adresse' => '3ème Arrondissement, Porto-Novo, Benin', 'telephone' => '+229 97169206'],
                    ['nom' => 'BIS CAFE', 'adresse' => 'Voie pavée derrière Reso Atao, Porto-Novo, Benin', 'telephone' => '+229 96177042'],
                    ['nom' => 'L’ESCALE ZEVOUGNON', 'adresse' => 'FJ4C+M9, Porto-Novo, Benin', 'telephone' => '+229 01 40 11 63 79'],
                    ['nom' => 'Restaurant Meilleurs Saveurs', 'adresse' => 'FJPC+467, Porto-Novo, Benin', 'telephone' => '+229 61456410'],
                    ['nom' => 'Restaurant Légend\'aire', 'adresse' => 'FJC7+3CR, Porto-Novo, Benin'],
                ],
                'appartements-meubles' => [
                    ['nom' => 'Résidence Les 6 Roses', 'adresse' => 'Quartier Houinmè, Porto-Novo, Benin', 'telephone' => '+229 01 42 34 95 49', 'site_web' => 'https://tr.ee/6mIuHj'],
                    ['nom' => 'Ma maison', 'adresse' => 'GHGV+H2, Porto-Novo, Benin'],
                    ['nom' => 'LES RESIDENCES SALAH 229', 'adresse' => '6.500807, 2.623518, Porto-Novo, Benin', 'telephone' => '+229 01 97 25 21 20'],
                    ['nom' => 'Lagoon Residence', 'adresse' => 'Quartier Gouako, Danto, Porto-Novo, Benin', 'telephone' => '+229 01 63 29 97 85', 'site_web' => 'https://www.lagoonresidence-rentbenin.com/'],
                    ['nom' => 'Ivy Residence', 'adresse' => 'Porto-Novo, Benin', 'telephone' => '+229 90808642', 'site_web' => 'https://residenceivy.com/'],
                    ['nom' => 'Résidence SENAKPON', 'adresse' => 'BP : 01, Porto-Novo, Benin', 'telephone' => '+229 01 96 08 30 09'],
                    ['nom' => 'Maison Porto Novo', 'adresse' => 'FMX6+82, Porto-Novo, Benin'],
                    ['nom' => 'Résidence Porto novo', 'adresse' => 'FJH4+RC3, Porto-Novo, Benin'],
                    ['nom' => 'RESIDENCE HINTENOR GUESTHOUSE', 'adresse' => 'GJ69+H2R, Porto-Novo, Benin', 'telephone' => '+229 62220004', 'site_web' => 'https://hintenor.com/'],
                    ['nom' => 'Gbodjè Dupont home', 'adresse' => 'FJHC+C4W, Porto-Novo, Benin'],
                ],
            ],
            'abomey-calavi' => [
                'hotels' => [
                    ['nom' => 'Hotel Les Arcades', 'adresse' => 'Quartier ZOCA, Abomey-Calavi, Benin', 'telephone' => '+229 01 69 20 21 20', 'site_web' => 'https://www.lesarcadeshotel.com/'],
                    ['nom' => 'Hotel Paramondo', 'adresse' => 'Godomey Togoudo, Route de IITA, Abomey-Calavi, Benin', 'telephone' => '+229 01 62 65 85 24'],
                    ['nom' => 'Hotel Village Vacances Assouka', 'adresse' => '02 BP 1832, Abomey-Calavi, Benin', 'telephone' => '+229 01 94 32 97 36'],
                    ['nom' => 'Hotel Germain - Ganvié Holiday Resort', 'adresse' => 'Quartier Havè - Ganvié II, Abomey-Calavi, Benin', 'telephone' => '+229 95573320'],
                    ['nom' => 'Paradisia Hôtel', 'adresse' => 'Rue RNIE 1, Quartier Godomey, Abomey-Calavi, Benin', 'telephone' => '+229 96975757', 'site_web' => 'https://www.paradisiahotel.bj/'],
                    ['nom' => 'Résidences ASKÉ', 'adresse' => 'BP 2909, Abomey-Calavi, Benin', 'telephone' => '+229 97984360'],
                    ['nom' => 'Voyage Afrique Benin', 'adresse' => '1731, Abomey-Calavi, Benin', 'telephone' => '+229 97218760', 'site_web' => 'http://www.voyageafriquebenin.org/'],
                    ['nom' => 'Hotel Residence La Paix', 'adresse' => 'F8GR+2RP, Abomey-Calavi, Benin', 'telephone' => '+229 97064348'],
                    ['nom' => 'Hôtel La Résidence Calavi', 'adresse' => 'Aitchedji, Abomey-Calavi, Benin', 'telephone' => '+229 01 44 96 22 26'],
                    ['nom' => 'Résidence Turquoise', 'adresse' => 'Cité Arconville, Abomey-Calavi, Benin', 'telephone' => '+229 97756052', 'site_web' => 'https://www.booking.com/hotel/bj/residence-turquoise.fr.html'],
                ],
                'restaurants' => [
                    ['nom' => 'Les Plats De Anna', 'adresse' => 'Abomey-Calavi, Benin', 'telephone' => '+229 01 62 50 86 87'],
                    ['nom' => 'JN Restaurant Epicerie Fine', 'adresse' => 'Bidossessi, Abomey-Calavi, Benin', 'telephone' => '+229 01 53 28 68 32', 'site_web' => 'https://jn-restaurant-epicerie-fine2.odoo.com/page-d-accueil'],
                    ['nom' => 'Restaurant le Caviar', 'adresse' => 'Rue derrière l\'hôpital CHIC, Abomey-Calavi, Benin', 'telephone' => '+229 01 91 11 48 48'],
                    ['nom' => 'Tasty Grills', 'adresse' => '001, Abomey-Calavi, Benin', 'telephone' => '+229 40664053'],
                    ['nom' => 'New Land Lounge Bar - Restaurant', 'adresse' => '2ème rue à droite, Rte de Maria Gléta, Abomey-Calavi, Benin', 'telephone' => '+229 91273636'],
                    ['nom' => 'Le Grand Café Calavi', 'adresse' => 'F9C4+685, RNIE2, Abomey-Calavi, Benin', 'telephone' => '+229 01 67 11 71 16'],
                    ['nom' => 'Chez Ares', 'adresse' => 'C8RF+3HM, Unnamed Road, Abomey-Calavi, Benin'],
                    ['nom' => 'Le Fouquet', 'adresse' => 'F8JW+PHF, Abomey-Calavi, Benin', 'telephone' => '+229 01 94 94 21 70'],
                    ['nom' => 'La Tendance Restaurant', 'adresse' => 'Abomey-Calavi, Benin', 'telephone' => '+229 01 90 27 77 10'],
                    ['nom' => 'Restaurant Coquette', 'adresse' => 'Bidossessi, Abomey-Calavi, Benin', 'telephone' => '+229 57131336'],
                ],
                'appartements-meubles' => [
                    ['nom' => 'Appart Meublé', 'adresse' => 'Abomey-Calavi, Benin', 'telephone' => '+229 96779662'],
                    ['nom' => 'Appart Meublé Le Privilège', 'adresse' => 'Abomey-Calavi, Benin', 'telephone' => '+229 90414051'],
                    ['nom' => 'KET\'S GUEST HOUSE APPARTEMENT MEUBLÉS', 'adresse' => 'Abomey-Calavi, Benin', 'telephone' => '+229 56535653', 'site_web' => 'https://www.facebook.com/profile.php?id=100085123341954'],
                    ['nom' => 'Résidence Welfare', 'adresse' => 'Abomey-Calavi, Benin', 'telephone' => '+229 98912030'],
                    ['nom' => 'Résidence AJM (Appartement Meublé)', 'adresse' => 'Abomey-Calavi, Benin', 'telephone' => '+229 01 61 84 19 81'],
                    ['nom' => 'Luxury furnished apartment - Abomey-Calavi, quartier Zopah', 'adresse' => 'Quartier Zopah, Abomey-Calavi, Benin'],
                    ['nom' => 'Appartement de 3 Pièces Cosy Avec un Balcon à 10 Minutes de Super U Erevan', 'adresse' => 'Abomey-Calavi, Benin'],
                    ['nom' => 'Well-located apartment in Cité Arconville, Abomey-Calavi - Benin', 'adresse' => 'Cité Arconville, Abomey-Calavi, Benin'],
                    ['nom' => 'Résidence Schilo - appartement équipé avec piscine privée et rooftop', 'adresse' => 'Abomey-Calavi, Benin'],
                    ['nom' => 'Adole Guest House - Two-Bedroom Apartment', 'adresse' => 'Abomey-Calavi, Benin'],
                ],
            ],
        ];

        foreach ($catalogue as $villeSlug => $parCategorie) {
            foreach ($parCategorie as $categorieSlug => $items) {
                foreach ($items as $item) {
                    $ville = $villes[$villeSlug];
                    $categorie = $categories[$categorieSlug];
                    $slug = Str::slug($item['nom'] . '-' . $villeSlug . '-' . $categorieSlug);

                    Etablissement::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'user_id' => $owner->id,
                            'ville_id' => $ville->id,
                            'categorie_id' => $categorie->id,
                            'nom' => $item['nom'],
                            'slug' => $slug,
                            'description' => $this->makeDescription($categorie->slug, $ville->nom, $item['adresse']),
                            'adresse' => $item['adresse'],
                            'telephone' => $item['telephone'] ?? null,
                            'whatsapp' => $item['telephone'] ?? null,
                            'email' => null,
                            'site_web' => $item['site_web'] ?? null,
                            'latitude' => null,
                            'longitude' => null,
                            'horaires' => null,
                            'fourchette_prix' => null,
                            'photo_principale' => null,
                            'statut' => 'actif',
                            'en_vedette' => false,
                        ]
                    );
                }
            }
        }

        $this->command->info('✅ Seeder BeninAddressesImportSeeder exécuté avec succès.');
        $this->command->info('📦 90 fiches importées : 10 hôtels, 10 restaurants et 10 appartements meublés dans chacune des 3 villes.');
    }

    private function makeDescription(string $categorieSlug, string $ville, string $adresse): string
    {
        return match ($categorieSlug) {
            'hotels' => "Hôtel référencé à {$ville}, situé à {$adresse}.",
            'restaurants' => "Restaurant référencé à {$ville}, situé à {$adresse}.",
            'appartements-meubles' => "Appartement meublé référencé à {$ville}, situé à {$adresse}.",
            default => "Adresse référencée à {$ville}, située à {$adresse}.",
        };
    }
}
