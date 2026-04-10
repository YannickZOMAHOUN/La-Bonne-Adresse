<?php

namespace Database\Seeders;

use App\Models\Categorie;
use App\Models\Etablissement;
use App\Models\User;
use App\Models\Ville;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BeninAddressesImportSeeder extends Seeder
{
    public function run(): void
    {
        $villes = $this->seedVilles();
        $categories = $this->seedCategories();
        $owner = $this->seedOwner();

        $total = 0;

        foreach ($this->catalogue() as $villeSlug => $parCategorie) {
            foreach ($parCategorie as $categorieSlug => $items) {
                foreach ($items as $item) {
                    $ville = $villes[$villeSlug];
                    $categorie = $categories[$categorieSlug];
                    $slug = Str::slug($item['nom'] . '-' . $villeSlug . '-' . $categorieSlug);
                    $photoPrincipale = $item['photos'][0]['url'] ?? null;

                    $etablissement = Etablissement::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'user_id'         => $owner->id,
                            'ville_id'        => $ville->id,
                            'categorie_id'    => $categorie->id,
                            'nom'             => $item['nom'],
                            'slug'            => $slug,
                            'description'     => $this->makeDescription($categorie->slug, $ville->nom, $item['adresse']),
                            'adresse'         => $item['adresse'],
                            'telephone'       => $item['telephone'] ?? null,
                            'whatsapp'        => $item['telephone'] ?? null,
                            'email'           => null,
                            'site_web'        => $item['site_web'] ?? null,
                            'latitude'        => null,
                            'longitude'       => null,
                            'horaires'        => $item['horaires'] ?? null,
                            'fourchette_prix' => $item['fourchette_prix'] ?? null,
                            'photo_principale' => $photoPrincipale,
                            'statut'          => 'actif',
                            'en_vedette'      => $item['en_vedette'] ?? false,
                        ]
                    );

                    $this->syncPhotos($etablissement->id, $item['photos'] ?? []);
                    $total++;
                }
            }
        }

        $this->command->info('✅ Seeder BeninAddressesImportSeeder exécuté avec succès.');
        $this->command->info("📦 {$total} fiches importées dans les 6 villes.");
        $this->command->info('🖼️ Des photos ont été ajoutées quand une image publique exploitable a été retrouvée.');
    }

    // -------------------------------------------------------------------------
    // Seed helpers
    // -------------------------------------------------------------------------

    private function seedVilles(): array
    {
        $definitions = [
            'cotonou' => [
                'nom'         => 'Cotonou',
                'emoji'       => '🌊',
                'description' => 'Capitale économique du Bénin.',
            ],
            'porto-novo' => [
                'nom'         => 'Porto-Novo',
                'emoji'       => '🏛️',
                'description' => 'Capitale administrative du Bénin.',
            ],
            'abomey-calavi' => [
                'nom'         => 'Abomey-Calavi',
                'emoji'       => '🏘️',
                'description' => "Grande commune de l'Atlantique, distincte d'Abomey.",
            ],
            'bohicon-abomey' => [
                'nom'         => 'Bohicon/Abomey',
                'emoji'       => '🏛️',
                'description' => "Zone historique et commerciale du Zou, regroupant des adresses de Bohicon et d'Abomey.",
            ],
            'parakou' => [
                'nom'         => 'Parakou',
                'emoji'       => '🌄',
                'description' => "Grande ville du Nord et carrefour d'affaires du Bénin.",
            ],
            'ouidah' => [
                'nom'         => 'Ouidah',
                'emoji'       => '🌴',
                'description' => 'Ville historique et balnéaire du littoral béninois.',
            ],
        ];

        $villes = [];

        foreach ($definitions as $slug => $data) {
            $villes[$slug] = Ville::updateOrCreate(
                ['slug' => $slug],
                array_merge($data, ['slug' => $slug, 'active' => true])
            );
        }

        return $villes;
    }

    private function seedCategories(): array
    {
        $definitions = [
            'restaurants' => [
                'nom'         => 'Restaurants',
                'emoji'       => '🍽️',
                'description' => 'Restaurants, maquis, grills et cafés.',
            ],
            'hotels' => [
                'nom'         => 'Hôtels',
                'emoji'       => '🏨',
                'description' => 'Hôtels, guest houses et resorts.',
            ],
            'appartements-meubles' => [
                'nom'         => 'Appartements meublés',
                'emoji'       => '🏠',
                'description' => 'Appartements, résidences et locations meublées.',
            ],
        ];

        $categories = [];

        foreach ($definitions as $slug => $data) {
            $categories[$slug] = Categorie::updateOrCreate(
                ['slug' => $slug],
                array_merge($data, ['slug' => $slug, 'active' => true])
            );
        }

        return $categories;
    }

    private function seedOwner(): User
    {
        return User::updateOrCreate(
            ['email' => 'import@bonnesadresses.bj'],
            [
                'nom'      => 'Import Bonnes Adresses',
                'telephone' => null,
                'password' => Hash::make('Import@2026!'),
                'role'     => 'proprietaire',
                'statut'   => 'actif',
            ]
        );
    }

    // -------------------------------------------------------------------------
    // Photo sync
    // -------------------------------------------------------------------------

    private function syncPhotos(int $etablissementId, array $photos): void
    {
        DB::table('photos')->where('etablissement_id', $etablissementId)->delete();

        foreach ($photos as $index => $photo) {
            DB::table('photos')->insert([
                'etablissement_id' => $etablissementId,
                'url'              => $photo['url'],
                'legende'          => $photo['legende'] ?? null,
                'ordre'            => $index,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }
    }

    // -------------------------------------------------------------------------
    // Description factory
    // -------------------------------------------------------------------------

    private function makeDescription(string $categorieSlug, string $ville, string $adresse): string
    {
        return match ($categorieSlug) {
            'hotels'               => "Hôtel référencé à {$ville}, situé à {$adresse}.",
            'restaurants'          => "Restaurant référencé à {$ville}, situé à {$adresse}.",
            'appartements-meubles' => "Appartement meublé référencé à {$ville}, situé à {$adresse}.",
            default                => "Adresse référencée à {$ville}, située à {$adresse}.",
        };
    }

    // -------------------------------------------------------------------------
    // Catalogue
    // -------------------------------------------------------------------------

    private function catalogue(): array
    {
        return [

            // =================================================================
            // COTONOU
            // =================================================================
            'cotonou' => [
                'hotels' => [
                    [
                        'nom'       => 'Novotel Cotonou Orisha',
                        'adresse'   => 'Boulevard de la Marina, Cotonou, Benin',
                        'telephone' => '+229 21305662',
                        'site_web'  => 'https://all.accor.com/lien_externe.svlt?goto=fiche_hotel&code_hotel=1826&merchantid=seo-maps-BJ-1826&sourceid=aw-cen&utm_medium=seo%20maps&utm_source=google%20Maps&utm_campaign=seo%20maps',
                    ],
                    [
                        'nom'       => 'Sofitel Cotonou Marina Hotel & Spa',
                        'adresse'   => '1820 Boulevard de la Marina, Cotonou, Benin',
                        'telephone' => '+229 01 20 90 04 61',
                        'site_web'  => 'http://sofitel.accor.com/hotels/B845?merchantid=seo-maps-0-B845&sourceid=aw-cen&utm_source=google+Maps&utm_medium=seo+maps&utm_campaign=seo+maps&utm_term=Sofitel-Launch',
                    ],
                    [
                        'nom'       => 'Golden Tulip Diplomate Cotonou',
                        'adresse'   => 'N° 90 rue 12.017 - Boulevard de la Marina, Cotonou, Benin',
                        'telephone' => '+229 01 98 30 02 00',
                        'site_web'  => 'http://www.goldentuliplediplomatecotonou.com/',
                    ],
                    [
                        'nom'       => 'Azalaï Hôtel de la Plage',
                        'adresse'   => 'Quartier Ganhi - Cotonou, Cotonou, Benin',
                        'telephone' => '+229 01 21 31 72 00',
                        'site_web'  => 'https://www.azalai.com/azalaihotelcotonou/',
                    ],
                    [
                        'nom'       => 'Bénin Royal Hôtel',
                        'adresse'   => 'Lot 398 quartier Maro-Militaire, Vons face Toxi Labo, Av. Germain Olory Togbé, Cotonou, Benin',
                        'telephone' => '+229 01 65 89 89 89',
                        'site_web'  => 'https://www.beninroyalhotel.com/en/',
                    ],
                    [
                        'nom'       => 'Maison Rouge Cotonou',
                        'adresse'   => 'Boulevard de la Marina, Cotonou, Benin',
                        'telephone' => '+229 01 65 12 69 89',
                        'site_web'  => 'http://hotel-benin-maison-rouge-cotonou.com/',
                    ],
                    [
                        'nom'       => 'Hotel du Lac',
                        'adresse'   => 'Cotonou, Benin',
                        'telephone' => '+229 21331919',
                        'site_web'  => 'http://www.hoteldulacbenin.com/',
                    ],
                    [
                        'nom'       => 'Sun Beach Hôtel',
                        'adresse'   => 'Fidjrossè Calvaire, Cotonou, Benin',
                        'telephone' => '+229 21302690',
                        'site_web'  => 'https://sites.google.com/view/sun-beach-hotel/',
                    ],
                    [
                        'nom'       => 'LE KARE EBENE',
                        'adresse'   => 'Place de Martyrs, Cotonou, Benin',
                        'telephone' => '+229 01 64 40 40 40',
                        'site_web'  => 'http://www.lekareebene.com/',
                    ],
                    [
                        'nom'       => 'Hôtel Tahiti benin',
                        'adresse'   => 'Rue 1531, Cotonou, Benin',
                        'telephone' => '+229 97111777',
                        'site_web'  => 'https://sites.google.com/view/hotel-tahiti-benin/',
                    ],
                ],
                'restaurants' => [
                    [
                        'nom'       => 'ESCOBAR',
                        'adresse'   => '9C49+92, Cotonou, Benin',
                        'telephone' => '+229 01 97 77 77 77',
                    ],
                    [
                        'nom'       => 'Face À La Mer',
                        'adresse'   => '03 BP 68 Fidjrossè Route des pêches, Cotonou, Benin',
                        'telephone' => '+229 90390303',
                        'site_web'  => 'http://www.facealamer.bj/',
                    ],
                    [
                        'nom'       => 'Ya-Hala',
                        'adresse'   => '9C6P+78C, Rue 108, Cotonou, Benin',
                        'telephone' => '+229 66555999',
                    ],
                    [
                        'nom'       => 'La Pirogue',
                        'adresse'   => 'Rue 104B, Cotonou, Benin',
                        'telephone' => '+229 91954444',
                        'site_web'  => 'https://www.canva.com/design/DAFUukMpZcg/ZBhlbYiHtRAH8uBgYr4kgQ/edit?utm_content=DAFUukMpZcg&utm_campaign=designshare&utm_medium=link2&utm_source=sharebutton',
                    ],
                    [
                        'nom'       => 'Le Lieu Unique',
                        'adresse'   => '08 Bp, 67 avenue de la francophonie, Cotonou, Benin',
                        'telephone' => '+229 01 67 54 98 58',
                    ],
                    [
                        'nom'       => 'L\'Imprévu',
                        'adresse'   => 'Face à ECOBANK direction general, Ganhi, Cotonou, Benin',
                        'telephone' => '+229 01 66 97 40 40',
                    ],
                    [
                        'nom'       => 'Paulcuisine',
                        'adresse'   => '995C+MV8, Cotonou, Benin',
                        'telephone' => '+229 01 66 57 09 91',
                    ],
                    [
                        'nom'       => 'La Cabane du Pêcheur',
                        'adresse'   => '982V+339, Cotonou, Benin',
                        'telephone' => '+229 01 95 55 00 03',
                    ],
                    [
                        'nom'       => 'Bangkok Terrasse',
                        'adresse'   => 'Rue 449, Cotonou, Benin',
                        'telephone' => '+229 01 21 30 37 86',
                    ],
                    [
                        'nom'       => 'L\'insolite',
                        'adresse'   => '9C86+6J9, Cotonou, Benin',
                        'telephone' => '+229 01 67 26 01 75',
                    ],
                ],
                'appartements-meubles' => [
                    [
                        'nom'       => 'Appartement meublé à Cotonou',
                        'adresse'   => 'Cotonou, Benin',
                        'telephone' => '+229 01 97 20 11 07',
                    ],
                    [
                        'nom'       => 'Résidence NC - Appartements meublés',
                        'adresse'   => '229 Rue 2470, Cotonou, Benin',
                        'telephone' => '+229 01 95 95 08 93',
                        'site_web'  => 'https://www.residence-nc.com/',
                    ],
                    [
                        'nom'       => 'JAAK RESIDENCE (OFFICIEL)',
                        'adresse'   => 'Cotonou, Benin',
                        'telephone' => '+229 01 97 69 33 33',
                        'site_web'  => 'http://jaakresidence.jaakgroup.com/',
                    ],
                    [
                        'nom'       => 'Residence meublée D.E.F',
                        'adresse'   => '9929+8W6, Cotonou, Benin',
                        'telephone' => '+229 01 56 56 53 21',
                    ],
                    [
                        'nom'       => 'Ola guest house (Résidence) appartement meublé',
                        'adresse'   => '229 Akpakpa, Cotonou, Benin',
                        'telephone' => '+229 61160654',
                    ],
                    [
                        'nom'       => 'Résidences et appartements meublés Naris - Cotonou Fidjrossè',
                        'adresse'   => 'Unnamed Road, Cotonou, Benin',
                        'telephone' => '+229 40935757',
                    ],
                    [
                        'nom'       => 'FLIMI RESIDENCE',
                        'adresse'   => '3267 Agla Figaro, Cotonou, Benin',
                        'telephone' => '+229 47101014',
                        'site_web'  => 'https://www.flimiresidence.com/',
                    ],
                    [
                        'nom'     => 'Appartements meublés "La Grâce"',
                        'adresse' => '99MR+XQ6, 072BP 041, Cotonou, Benin',
                    ],
                    [
                        'nom'       => 'RÉSIDENCE AHA',
                        'adresse'   => 'Cotonou, Benin',
                        'telephone' => '+229 51003444',
                    ],
                    [
                        'nom'       => 'Les Ombrelles Appart\'Hotel (LOAH)',
                        'adresse'   => 'Rue 2521A, Cotonou, Benin',
                        'telephone' => '+229 55818181',
                    ],
                ],
            ],

            // =================================================================
            // PORTO-NOVO
            // =================================================================
            'porto-novo' => [
                'hotels' => [
                    [
                        'nom'       => 'ART RESIDENCE Hotel',
                        'adresse'   => 'ilot 113 rue 40 Avakpa Kpodji, Porto-Novo, Benin',
                        'telephone' => '+229 01 91 71 64 64',
                        'site_web'  => 'https://artresidence.bj/',
                    ],
                    [
                        'nom'       => 'TOUR EIFFEL HOTEL BENIN',
                        'adresse'   => 'B.P. 1946 COTONOU, BENIN, Porto-Novo, Benin',
                        'telephone' => '+229 01 97 60 98 82',
                        'site_web'  => 'http://www.toureiffelhotel.com/',
                    ],
                    [
                        'nom'       => 'Freedom Palace Hôtel',
                        'adresse'   => 'Agbokou Avakpa, Porto-Novo, Benin',
                        'telephone' => '+229 01 62 13 14 11',
                    ],
                    [
                        'nom'     => 'HOTEL AYELAWADJE 2',
                        'adresse' => 'Porto-Novo, Benin',
                    ],
                    [
                        'nom'     => 'Hotel Dona',
                        'adresse' => 'Porto-Novo, Benin',
                    ],
                    [
                        'nom'       => 'MB-Group Hotel',
                        'adresse'   => 'FJF6+6VX, Porto-Novo, Benin',
                        'telephone' => '+229 63100564',
                    ],
                    [
                        'nom'       => 'Hotel Novella Planet',
                        'adresse'   => 'Immeuble HOUNSINOU, quartier Houinmè, Porto-Novo, Benin',
                        'telephone' => '+229 01 50 02 92 08',
                    ],
                    [
                        'nom'       => 'Queen\'s Hotel',
                        'adresse'   => '1ère Rue à gauche après l[\'Eglise Catholique, Djassin-Houinvié, Porto-Novo, Benin',
                        'telephone' => '+229 95050885',
                    ],
                    [
                        'nom'       => 'Hotel Les Oliviers',
                        'adresse'   => '01BP1939, Porto-Novo, Benin',
                        'telephone' => '+229 97723284',
                    ],
                    [
                        'nom'       => 'Hôtel Résidence Santa Claudia',
                        'adresse'   => 'Lot 327M, Porto-Novo, Benin',
                        'telephone' => '+229 53545350',
                        'site_web'  => 'https://www.hrsantaclaudia.com/',
                    ],
                ],
                'restaurants' => [
                    [
                        'nom'       => 'Restaurant ART RESIDENCE - Porto Novo',
                        'adresse'   => 'Immeuble abritant Coris Bank ilot 113 rue 40 Agbokou Avakpa, Porto-Novo, Benin',
                        'telephone' => '+229 01 91 71 64 64',
                        'site_web'  => 'https://artresidence.bj/',
                    ],
                    [
                        'nom'       => 'African Foodseum',
                        'adresse'   => '01 BP 1091, Porto-Novo, Benin',
                        'telephone' => '+229 01 62 74 76 28',
                    ],
                    [
                        'nom'       => 'L\'Endroit by Olabissi',
                        'adresse'   => 'FJF8+3WF, Ave William Ponty, Porto-Novo, Benin',
                        'telephone' => '+229 01 60 13 56 56',
                    ],
                    [
                        'nom'       => 'Rehoboth',
                        'adresse'   => 'Porto-Novo, Benin',
                        'telephone' => '+229 01 94 60 88 28',
                    ],
                    [
                        'nom'       => 'O\'tentiQ Café Bar Restaurant',
                        'adresse'   => 'GJ5G+M9W, Bd du Cinquantenaire, Porto-Novo, Benin',
                        'telephone' => '+229 01 94 19 57 40',
                    ],
                    [
                        'nom'       => 'DÊGUÊ PALACE Restaurant',
                        'adresse'   => '3ème Arrondissement, Porto-Novo, Benin',
                        'telephone' => '+229 97169206',
                    ],
                    [
                        'nom'       => 'BIS CAFE',
                        'adresse'   => 'Voie pavée derrière Reso Atao, Porto-Novo, Benin',
                        'telephone' => '+229 96177042',
                    ],
                    [
                        'nom'       => 'L\'ESCALE ZEVOUGNON',
                        'adresse'   => 'FJ4C+M9, Porto-Novo, Benin',
                        'telephone' => '+229 01 40 11 63 79',
                    ],
                    [
                        'nom'       => 'Restaurant Meilleurs Saveurs',
                        'adresse'   => 'FJPC+467, Porto-Novo, Benin',
                        'telephone' => '+229 61456410',
                    ],
                    [
                        'nom'     => 'Restaurant Légend\'aire',
                        'adresse' => 'FJC7+3CR, Porto-Novo, Benin',
                    ],
                ],
                'appartements-meubles' => [
                    [
                        'nom'       => 'Résidence Les 6 Roses',
                        'adresse'   => 'Quartier Houinmè, Porto-Novo, Benin',
                        'telephone' => '+229 01 42 34 95 49',
                        'site_web'  => 'https://tr.ee/6mIuHj',
                    ],
                    [
                        'nom'     => 'Ma maison',
                        'adresse' => 'GHGV+H2, Porto-Novo, Benin',
                    ],
                    [
                        'nom'       => 'LES RESIDENCES SALAH 229',
                        'adresse'   => '6.500807, 2.623518, Porto-Novo, Benin',
                        'telephone' => '+229 01 97 25 21 20',
                    ],
                    [
                        'nom'       => 'Lagoon Residence',
                        'adresse'   => 'Quartier Gouako, Danto, Porto-Novo, Benin',
                        'telephone' => '+229 01 63 29 97 85',
                        'site_web'  => 'https://www.lagoonresidence-rentbenin.com/',
                    ],
                    [
                        'nom'       => 'Ivy Residence',
                        'adresse'   => 'Porto-Novo, Benin',
                        'telephone' => '+229 90808642',
                        'site_web'  => 'https://residenceivy.com/',
                    ],
                    [
                        'nom'       => 'Résidence SENAKPON',
                        'adresse'   => 'BP : 01, Porto-Novo, Benin',
                        'telephone' => '+229 01 96 08 30 09',
                    ],
                    [
                        'nom'     => 'Maison Porto Novo',
                        'adresse' => 'FMX6+82, Porto-Novo, Benin',
                    ],
                    [
                        'nom'     => 'Résidence Porto novo',
                        'adresse' => 'FJH4+RC3, Porto-Novo, Benin',
                    ],
                    [
                        'nom'       => 'RESIDENCE HINTENOR GUESTHOUSE',
                        'adresse'   => 'GJ69+H2R, Porto-Novo, Benin',
                        'telephone' => '+229 62220004',
                        'site_web'  => 'https://hintenor.com/',
                    ],
                    [
                        'nom'     => 'Gbodjè Dupont home',
                        'adresse' => 'FJHC+C4W, Porto-Novo, Benin',
                    ],
                ],
            ],

            // =================================================================
            // ABOMEY-CALAVI
            // =================================================================
            'abomey-calavi' => [
                'hotels' => [
                    [
                        'nom'       => 'Hotel Les Arcades',
                        'adresse'   => 'Quartier ZOCA, Abomey-Calavi, Benin',
                        'telephone' => '+229 01 69 20 21 20',
                        'site_web'  => 'https://www.lesarcadeshotel.com/',
                    ],
                    [
                        'nom'       => 'Hotel Paramondo',
                        'adresse'   => 'Godomey Togoudo, Route de IITA, Abomey-Calavi, Benin',
                        'telephone' => '+229 01 62 65 85 24',
                    ],
                    [
                        'nom'       => 'Hotel Village Vacances Assouka',
                        'adresse'   => '02 BP 1832, Abomey-Calavi, Benin',
                        'telephone' => '+229 01 94 32 97 36',
                    ],
                    [
                        'nom'       => 'Hotel Germain - Ganvié Holiday Resort',
                        'adresse'   => 'Quartier Havè - Ganvié II, Abomey-Calavi, Benin',
                        'telephone' => '+229 95573320',
                    ],
                    [
                        'nom'       => 'Paradisia Hôtel',
                        'adresse'   => 'Rue RNIE 1, Quartier Godomey, Abomey-Calavi, Benin',
                        'telephone' => '+229 96975757',
                        'site_web'  => 'https://www.paradisiahotel.bj/',
                    ],
                    [
                        'nom'       => 'Résidences ASKÉ',
                        'adresse'   => 'BP 2909, Abomey-Calavi, Benin',
                        'telephone' => '+229 97984360',
                    ],
                    [
                        'nom'       => 'Voyage Afrique Benin',
                        'adresse'   => '1731, Abomey-Calavi, Benin',
                        'telephone' => '+229 97218760',
                        'site_web'  => 'http://www.voyageafriquebenin.org/',
                    ],
                    [
                        'nom'       => 'Hotel Residence La Paix',
                        'adresse'   => 'F8GR+2RP, Abomey-Calavi, Benin',
                        'telephone' => '+229 97064348',
                    ],
                    [
                        'nom'       => 'Hôtel La Résidence Calavi',
                        'adresse'   => 'Aitchedji, Abomey-Calavi, Benin',
                        'telephone' => '+229 01 44 96 22 26',
                    ],
                    [
                        'nom'       => 'Résidence Turquoise',
                        'adresse'   => 'Cité Arconville, Abomey-Calavi, Benin',
                        'telephone' => '+229 97756052',
                        'site_web'  => 'https://www.booking.com/hotel/bj/residence-turquoise.fr.html',
                    ],
                ],
                'restaurants' => [
                    [
                        'nom'       => 'Les Plats De Anna',
                        'adresse'   => 'Abomey-Calavi, Benin',
                        'telephone' => '+229 01 62 50 86 87',
                    ],
                    [
                        'nom'       => 'JN Restaurant Epicerie Fine',
                        'adresse'   => 'Bidossessi, Abomey-Calavi, Benin',
                        'telephone' => '+229 01 53 28 68 32',
                        'site_web'  => 'https://jn-restaurant-epicerie-fine2.odoo.com/page-d-accueil',
                    ],
                    [
                        'nom'       => 'Restaurant le Caviar',
                        'adresse'   => 'Rue derrière l\'hôpital CHIC, Abomey-Calavi, Benin',
                        'telephone' => '+229 01 91 11 48 48',
                    ],
                    [
                        'nom'       => 'Tasty Grills',
                        'adresse'   => '001, Abomey-Calavi, Benin',
                        'telephone' => '+229 40664053',
                    ],
                    [
                        'nom'       => 'New Land Lounge Bar - Restaurant',
                        'adresse'   => '2ème rue à droite, Rte de Maria Gléta, Abomey-Calavi, Benin',
                        'telephone' => '+229 91273636',
                    ],
                    [
                        'nom'       => 'Le Grand Café Calavi',
                        'adresse'   => 'F9C4+685, RNIE2, Abomey-Calavi, Benin',
                        'telephone' => '+229 01 67 11 71 16',
                    ],
                    [
                        'nom'     => 'Chez Ares',
                        'adresse' => 'C8RF+3HM, Unnamed Road, Abomey-Calavi, Benin',
                    ],
                    [
                        'nom'       => 'Le Fouquet',
                        'adresse'   => 'F8JW+PHF, Abomey-Calavi, Benin',
                        'telephone' => '+229 01 94 94 21 70',
                    ],
                    [
                        'nom'       => 'La Tendance Restaurant',
                        'adresse'   => 'Abomey-Calavi, Benin',
                        'telephone' => '+229 01 90 27 77 10',
                    ],
                    [
                        'nom'       => 'Restaurant Coquette',
                        'adresse'   => 'Bidossessi, Abomey-Calavi, Benin',
                        'telephone' => '+229 57131336',
                    ],
                ],
                'appartements-meubles' => [
                    [
                        'nom'       => 'Appart Meublé',
                        'adresse'   => 'Abomey-Calavi, Benin',
                        'telephone' => '+229 96779662',
                    ],
                    [
                        'nom'       => 'Appart Meublé Le Privilège',
                        'adresse'   => 'Abomey-Calavi, Benin',
                        'telephone' => '+229 90414051',
                    ],
                    [
                        'nom'       => 'KET\'S GUEST HOUSE APPARTEMENT MEUBLÉS',
                        'adresse'   => 'Abomey-Calavi, Benin',
                        'telephone' => '+229 56535653',
                        'site_web'  => 'https://www.facebook.com/profile.php?id=100085123341954',
                    ],
                    [
                        'nom'       => 'Résidence Welfare',
                        'adresse'   => 'Abomey-Calavi, Benin',
                        'telephone' => '+229 98912030',
                    ],
                    [
                        'nom'       => 'Résidence AJM (Appartement Meublé)',
                        'adresse'   => 'Abomey-Calavi, Benin',
                        'telephone' => '+229 01 61 84 19 81',
                    ],
                    [
                        'nom'     => 'Luxury furnished apartment - Abomey-Calavi, quartier Zopah',
                        'adresse' => 'Quartier Zopah, Abomey-Calavi, Benin',
                    ],
                    [
                        'nom'     => 'Appartement de 3 Pièces Cosy Avec un Balcon à 10 Minutes de Super U Erevan',
                        'adresse' => 'Abomey-Calavi, Benin',
                    ],
                    [
                        'nom'     => 'Well-located apartment in Cité Arconville, Abomey-Calavi - Benin',
                        'adresse' => 'Cité Arconville, Abomey-Calavi, Benin',
                    ],
                    [
                        'nom'     => 'Résidence Schilo - appartement équipé avec piscine privée et rooftop',
                        'adresse' => 'Abomey-Calavi, Benin',
                    ],
                    [
                        'nom'     => 'Adole Guest House - Two-Bedroom Apartment',
                        'adresse' => 'Abomey-Calavi, Benin',
                    ],
                ],
            ],

            // =================================================================
            // BOHICON / ABOMEY
            // =================================================================
            'bohicon-abomey' => [
                'hotels' => [
                    [
                        'nom'       => 'Hôtel La Majesté',
                        'adresse'   => '53X9+6MW, quartier Zakpo, route du nord après carrefour Zakpo, Bohicon, Bénin',
                        'telephone' => '+229 01 95 08 51 56',
                        'site_web'  => 'https://www.princeshotelsbenin.com/hotellamajestebohicon',
                    ],
                    [
                        'nom'       => 'Chez Sabine',
                        'adresse'   => '310 rue du château Djimè, Abomey, Bénin',
                        'telephone' => '+229 63136464',
                        'site_web'  => 'https://sites.google.com/view/chambres-dhote-chez-sabine/',
                    ],
                    [
                        'nom'       => 'Reine Hotel',
                        'adresse'   => 'Bohicon, Bénin',
                        'telephone' => '+229 01 96 44 46 51',
                        'site_web'  => 'http://reinehotel.bj/',
                    ],
                    [
                        'nom'        => 'Bois Vert Hôtel',
                        'adresse'    => '5XFR+74, Abomey, Bénin',
                        'telephone'  => '+229 01 66 65 20 44',
                        'site_web'   => 'https://boisverthotel.netlify.app/',
                        'en_vedette' => true,
                        'photos'     => [
                            [
                                'url'     => 'https://sspark.genspark.ai/cfimages?u1=0eYYagJF2qUWMgm%2F09NyDGKTHGlNAsqHVMkBWpeJj22vwdpotq3ESYoTHkoqwm%2Bo%2BXhpPMgF8pxpjCeJ8BUCyckCDZWi39cwi9czBZhFjwx37OY%3D&u2=IjLGfkRhX3BisuPV&width=2560',
                                'legende' => 'Bois Vert Hôtel - visuel public indexé du lieu',
                            ],
                        ],
                    ],
                    [
                        'nom'       => 'Hôtel des Princes',
                        'adresse'   => 'RNIE4, Bohicon, Bénin',
                        'telephone' => '+229 01 96 04 90 55',
                    ],
                    [
                        'nom'       => 'Hôtel Saint JO Bohicon',
                        'adresse'   => 'Bohicon, Bénin',
                        'telephone' => '+229 01 91 06 23 23',
                    ],
                    [
                        'nom'     => 'Abomey Events Hotel',
                        'adresse' => '5XHQ+CX, Abomey, Bénin',
                    ],
                    [
                        'nom'       => 'Hotel Tennessee',
                        'adresse'   => '534F+9H9, RNIE2, Bohicon, Bénin',
                        'telephone' => '+229 01 94 64 74 66',
                        'photos'    => [
                            [
                                'url'     => 'https://sspark.genspark.ai/cfimages?u1=7TxmoSH896Qd5%2F2Ls8F72emj917cHadwbwdqof%2BZWUhGCoIdXumQxamuoiSBC6ii9hzQBauybPV6tSwY0QLAxDRVU%2FHd7HhO5AZ433sGoMpz&u2=Re2fQs1WNS7b%2FbdE&width=2560',
                                'legende' => 'Hotel Tennessee - visuel public indexé du lieu',
                            ],
                        ],
                    ],
                    [
                        'nom'       => 'Ekagnon Hôtel Abomey',
                        'adresse'   => '5273+63R, Abomey, Bénin',
                        'telephone' => '+229 01 44 99 98 88',
                        'site_web'  => 'https://www.ekagnon.com/',
                    ],
                    [
                        'nom'       => 'Sun City Hotel',
                        'adresse'   => '5XXC+M33, Abomey, Bénin',
                        'telephone' => '+229 95097075',
                    ],
                ],
                'restaurants' => [
                    [
                        'nom'       => 'LE SKY BAR RESTAURANT',
                        'adresse'   => 'RNIE2, Bohicon, Bénin',
                        'telephone' => '+229 01 98 30 30 30',
                    ],
                    [
                        'nom'       => 'Cafeteria La Racine',
                        'adresse'   => 'RNIE2, Bohicon, Bénin',
                        'telephone' => '+229 67070706',
                    ],
                    [
                        'nom'       => 'Bar Restaurant LES 6 RUES de Bohicon',
                        'adresse'   => '08, Bohicon, Bénin',
                        'telephone' => '+229 97376853',
                    ],
                    [
                        'nom'       => 'Bar Restaurant Rose_Lid',
                        'adresse'   => '02 BP 373, Bohicon, Bénin',
                        'telephone' => '+229 01 96 54 54 91',
                    ],
                    [
                        'nom'       => 'Café Resto chez Nick',
                        'adresse'   => '53FJ+358, Bohicon, Bénin',
                        'telephone' => '+229 01 97 90 08 27',
                    ],
                    [
                        'nom'       => 'Restaurant La Table',
                        'adresse'   => 'Agblomè-lébi, Abomey, Bénin',
                        'telephone' => '+229 01 97 13 06 03',
                    ],
                    [
                        'nom'       => 'Magui Bar Les Saveurs de Marguerite',
                        'adresse'   => '5XPX+55, Abomey, Bénin',
                        'telephone' => '+229 01 95 62 55 99',
                    ],
                    [
                        'nom'       => 'Café royal',
                        'adresse'   => '52M7+8XP, Abomey, Bénin',
                        'telephone' => '+229 01 67 01 01 99',
                    ],
                    [
                        'nom'       => 'Restaurant Wakanu',
                        'adresse'   => '5XQJ+XG3, Abomey, Bénin',
                        'telephone' => '+229 01 64 61 21 61',
                    ],
                    [
                        'nom'       => 'La TAVERNE d\'Abomey',
                        'adresse'   => 'CEG 3 ABOMEY (SOTA), Abomey, Bénin',
                        'telephone' => '+229 01 95 38 81 17',
                    ],
                ],
                'appartements-meubles' => [
                    [
                        'nom'       => 'LYS\'APPARTS',
                        'adresse'   => 'BP 01, Bohicon, Bénin',
                        'telephone' => '+229 01 62 56 57 58',
                    ],
                    [
                        'nom'       => 'Résidence Gerardo DEHA',
                        'adresse'   => '6332+43, Bohicon, Bénin',
                        'telephone' => '+229 01 96 46 43 18',
                    ],
                    [
                        'nom'        => 'Résidence Le Confort',
                        'adresse'    => 'Agbadjagon, Bohicon, Bénin',
                        'site_web'   => 'https://xn--rsidenceleconfort-btb.com/',
                        'fourchette_prix' => '15 000 - 25 000 FCFA/nuit',
                        'en_vedette' => true,
                        'photos'     => [
                            [
                                'url'     => 'https://sspark.genspark.ai/cfimages?u1=kfjCSqAfLfPOwz3Sb%2BYswe7mQtsAVhL62xj1pG2MqDS%2BNQUCeoGmbKOXBlUhCTIuXl3yZM5nt4NYETXdBkJ2o6Kqax4dmNx7EqJYM07sKoF%2B8Uw43XdgGPX95mY1lQyhqtU3s3Ii%2FEC4OHbIhsY%3D&u2=s0zwbzAfAdKZF1dL&width=1024',
                                'legende' => 'Résidence Le Confort - salle à manger moderne',
                            ],
                            [
                                'url'     => 'https://sspark.genspark.ai/cfimages?u1=iBkLqGI1S4A2%2B0vQVuSQhIlMByQeQzisRnRdRhmsyUEjCr301Ra7WVsaFIR%2FxpYMC1UfhD0ISljA%2FiGhVGDRlk7e4Bh8RsoFSzlFtmaUzqE2DWKucpFpkRR3jT9ikqfdkb30rmDUiTr0e181UI0%3D&u2=a2vL4kledNBuEaMp&width=1024',
                                'legende' => 'Résidence Le Confort - chambre confortable',
                            ],
                        ],
                    ],
                    [
                        'nom'     => 'Appartement meublé Bohicon (CoinAfrique)',
                        'adresse' => 'Zone calme, Bohicon, Bénin',
                        'site_web' => 'https://bj.coinafrique.com/annonce/appartements-meubles/location-appartement-meuble-bohicon-3864874',
                    ],
                    [
                        'nom'     => 'Perfect Residence Bohicon',
                        'adresse' => 'Bohicon/Abomey, Bénin',
                        'site_web' => 'https://www.airbnb.com/rooms/1129033650625575829',
                    ],
                    [
                        'nom'     => 'Quiet apartment in Bohicon',
                        'adresse' => 'Bohicon, cœur du Bénin',
                        'site_web' => 'https://www.airbnb.com/rooms/711638238758920426',
                    ],
                    [
                        'nom'     => 'Villa Pere Hospice - Studio Apartment',
                        'adresse' => 'Bohicon, Bénin',
                        'site_web' => 'https://www.airbnb.com/rooms/1642246971978829086',
                    ],
                    [
                        'nom'       => 'Appartement meuble 1 chambre salon - Bohicon',
                        'adresse'   => 'Bohicon, Bénin',
                        'telephone' => '+229 0165945357',
                        'site_web'  => 'https://www.facebook.com/cossi.ludovic.3/videos/appartement-meubl%C3%A9-disponible-%C3%A0-bohicon-%C3%A0-12k-seulement-la-nuit%C3%A9e-t%C3%A9l-0165945357/1164879168967804/',
                    ],
                    [
                        'nom'     => 'Appartement meuble de standing - 2 chambres',
                        'adresse' => 'Abomey, Bénin',
                        'site_web' => 'https://www.booking.com/hotel/bj/appartement-meuble-de-standing.fr.html',
                    ],
                    [
                        'nom'     => 'Appartement meublé Bohicon Centre',
                        'adresse' => '5395+4W, Bohicon, Bénin',
                    ],
                ],
            ],

            // =================================================================
            // PARAKOU
            // =================================================================
            'parakou' => [
                'hotels' => [
                    [
                        'nom'       => 'Parakou\'s Paradise',
                        'adresse'   => 'Route de Transa, Parakou, Bénin',
                        'telephone' => '+229 01 66 22 70 69',
                        'site_web'  => 'https://sites.google.com/view/parakouparadise/accueil',
                    ],
                    [
                        'nom'       => 'HOTEL IYA-OASIS',
                        'adresse'   => 'Parakou, Bénin',
                        'telephone' => '+229 98000000',
                    ],
                    [
                        'nom'       => 'Kobourou City Hotel',
                        'adresse'   => '9J8F+V4W, RNIE2, Parakou, Bénin',
                        'telephone' => '+229 01 62 01 22 00',
                        'site_web'  => 'http://kobouroucityhotel.com/',
                    ],
                    [
                        'nom'        => 'Almadies PRESTIGE',
                        'adresse'    => 'Parakou, Bénin',
                        'telephone'  => '+229 01 41 76 17 03',
                        'site_web'   => 'https://www.almadiesprestige.com/',
                        'en_vedette' => true,
                        'photos'     => [
                            [
                                'url'     => 'https://sspark.genspark.ai/cfimages?u1=RAZeEkQQCrWHBjz9ARvpsGGX0cekejUzUGoAke0a%2F5dcNgLQPLb36anga0zKOO0eA68PMcExWBVAh%2Bunu8wNn%2FcSz3Wn4SyGlljXIGexzooOYkMD6zeI4FToS2TCHOjRnpUuyNCvt27kG6ANWTe8IXZ21X7dj3vd%2B37uhvBGlB9caKCQEjEp2NswYT43lyIyfGsOk3X6c365WfS%2FoeuhcNV57Q%3D%3D&u2=4vDya%2BeK6aBngl1a&width=1024',
                                'legende' => 'Almadies PRESTIGE - façade et piscine',
                            ],
                            [
                                'url'     => 'https://sspark.genspark.ai/cfimages?u1=d%2FltZKKOVUbmz7JysyEL7K0oT9LIb6solLoKJYXHLxwMLaFWY8pakYengEXaeyQHjbgb7jyY%2BbrKHdA84vKo5cN1HffWswd8I%2FO47ZBAv5LdYDQpxnNY4aZ8rsLUCQ6uuiCQ29wlyg%2FGaFqzh9DhEbKwpPwv23IliJCQfrfm7d8WZeDNWkxs7TD34Y5vOqU%3D&u2=cYjc6zqf5Sig8fjJ&width=1024',
                                'legende' => 'Almadies PRESTIGE - piscine à débordement',
                            ],
                        ],
                    ],
                    [
                        'nom'       => 'Hotel les Routiers',
                        'adresse'   => '8JXF+4XX, route du Niger quartier Tranza, Parakou, Bénin',
                        'telephone' => '+229 95976091',
                    ],
                    [
                        'nom'       => 'Benin Metropole Hôtel',
                        'adresse'   => 'Parakou, Bénin',
                        'telephone' => '+229 46717177',
                        'site_web'  => 'http://beninmetropole.site/',
                    ],
                    [
                        'nom'       => 'Luxury Suites',
                        'adresse'   => '100 m après Carrefour Papini, route de Nima, Parakou, Bénin',
                        'telephone' => '+229 97131397',
                        'site_web'  => 'http://luxurysuites.homes/',
                    ],
                    [
                        'nom'       => 'Ola Luxury Hotel',
                        'adresse'   => 'Carrefour Guy Riobé en allant vers Don Bosco, Parakou, Bénin',
                        'telephone' => '+229 01 45 01 00 00',
                    ],
                    [
                        'nom'       => 'Le Majestic Hotel',
                        'adresse'   => '8JW8+Q26, Parakou, Bénin',
                        'telephone' => '+229 66311017',
                        'site_web'  => 'http://lemajestichotel.com/',
                    ],
                    [
                        'nom'       => 'La Lune du Monde',
                        'adresse'   => 'Quartier Tibona, près du commissariat, Parakou, Bénin',
                        'telephone' => '+229 51135513',
                    ],
                ],
                'restaurants' => [
                    [
                        'nom'       => 'O\'Grill Restaurant',
                        'adresse'   => '8JFV+QPV, Parakou, Bénin',
                        'telephone' => '+229 46078535',
                    ],
                    [
                        'nom'       => 'Cave du Nord Parakou',
                        'adresse'   => 'Parakou, Bénin',
                        'telephone' => '+229 97576057',
                    ],
                    [
                        'nom'       => 'Nasuba Food',
                        'adresse'   => '03 BP 215, Parakou, Bénin',
                        'telephone' => '+229 01 51 94 94 79',
                        'site_web'  => 'http://www.nasubafood.com/',
                    ],
                    [
                        'nom'       => 'Lounge Bar Chez Nabil',
                        'adresse'   => '9J58+FQG, Parakou, Bénin',
                        'telephone' => '+229 01 61 71 39 39',
                    ],
                    [
                        'nom'       => 'Restaurant Le Secret De La Vielle Marmite',
                        'adresse'   => '02 BP 386, Parakou, Bénin',
                        'telephone' => '+229 66222939',
                    ],
                    [
                        'nom'       => 'Les bonnes saveurs',
                        'adresse'   => '9JHF+HR6, Parakou, Bénin',
                        'telephone' => '+229 97320340',
                    ],
                    [
                        'nom'       => 'MAQUIS LE MONO',
                        'adresse'   => '8JVF+6XG, route de Transa, Parakou, Bénin',
                        'telephone' => '+229 01 96 33 79 29',
                    ],
                    [
                        'nom'       => 'Dody\'s Pizza & Pasta',
                        'adresse'   => '9JGJ+W36, route sans nom, Parakou, Bénin',
                        'telephone' => '+229 62777777',
                        'site_web'  => 'https://www.facebook.com/Dodys-Pizza-Pasta-1719236721655505/',
                    ],
                    [
                        'nom'       => 'La Belle Creole',
                        'adresse'   => '02BP299, Parakou, Bénin',
                        'telephone' => '+229 94709422',
                    ],
                    [
                        'nom'     => 'Restaurant le moment',
                        'adresse' => '8JW5+MJC, Parakou, Bénin',
                    ],
                ],
                'appartements-meubles' => [
                    [
                        'nom'       => 'Peace Résidence',
                        'adresse'   => '8HRH+76, Parakou, Bénin',
                        'telephone' => '+229 61616732',
                    ],
                    [
                        'nom'       => 'Innova Appart',
                        'adresse'   => 'Parakou, Bénin',
                        'telephone' => '+229 55253393',
                        'site_web'  => 'https://www.facebook.com/profile.php?id=100064141027325',
                    ],
                    [
                        'nom'       => 'Luxury Suites Résidence Meublée',
                        'adresse'   => '100 m après Carrefour Papini, route de Nima, Parakou, Bénin',
                        'telephone' => '+229 97131397',
                        'site_web'  => 'http://luxurysuites.homes/',
                    ],
                    [
                        'nom'       => 'Guest House / Residence Meublée',
                        'adresse'   => '9JPF+G32, Parakou, Bénin',
                        'telephone' => '+229 96560454',
                    ],
                    [
                        'nom'       => 'Guest House (OLS)',
                        'adresse'   => 'Parakou, Bénin',
                        'telephone' => '+229 44272769',
                        'site_web'  => 'https://gh.eprosat.com/',
                    ],
                    [
                        'nom'     => 'Banikani',
                        'adresse' => '8M82+HC3, Parakou, Bénin',
                    ],
                    [
                        'nom'     => 'Maison à louer',
                        'adresse' => '8JGV+QQ8, Parakou, Bénin',
                    ],
                    [
                        'nom'       => 'Résidence Fifamè',
                        'adresse'   => 'Parakou, Bénin',
                        'telephone' => '+229 01 96 82 89 00',
                    ],
                    [
                        'nom'     => 'Feel Well Guest House',
                        'adresse' => 'Parakou, Bénin',
                        'site_web' => 'https://fr.hotels.com/ho1112424032/feel-well-guest-house-parakou-benin/',
                    ],
                    [
                        'nom'     => 'Guest House Nol Palace',
                        'adresse' => 'Quartier Okedama, en face de l\'HIA, Parakou, Bénin',
                        'site_web' => 'https://www.facebook.com/p/Guest-House-Nol-Palace-61558458956272/',
                    ],
                ],
            ],

            // =================================================================
            // OUIDAH
            // =================================================================
            'ouidah' => [
                'hotels' => [
                    [
                        'nom'        => 'La Casa Del Papa',
                        'adresse'    => '827P+JJ3, Ouidah Plage, Ouidah, Bénin',
                        'telephone'  => '+229 95953904',
                        'site_web'   => 'https://www.casadelpapa.com/en',
                        'en_vedette' => true,
                        'photos'     => [
                            [
                                'url'     => 'https://sspark.genspark.ai/cfimages?u1=K9IAWJMBiERWcPcPFMJWtzFzM1wcOoFEd3t0I9U8ctTuzxYmBrUAYQHk%2FUkM%2BZ8QHNmWfy17uq%2FJp4NZ0Ak9EeySCIVOKR1l1mie%2BRROfkxmLu9rQ%2FDl1m%2FaOizWZxUkj2L719uSg1Whudd%2BU3A50TvNdE2%2F8bovs4ZEluv83BgGjnJlEbCtzjvYBwZh97aBZu4%3D&u2=8%2F6sQBEb%2Bqh5iYQz&width=1024',
                                'legende' => 'Casa del Papa - chambre et resort en bord de mer',
                            ],
                            [
                                'url'     => 'https://sspark.genspark.ai/cfimages?u1=8H39qZtwWJlvhX9JZG01khkqGR5ePYZAN%2FVW2PZXiQH78OhVusaHihS%2FUR3Rq20qh1stFa4YRDP%2FSAUAzdNlkyFMJCcbIClVpEXDelpmPqm06PXzE8TP1bZH%2F5sibA%3D%3D&u2=Cg4RwZcyXC6%2BNUPM&width=1024',
                                'legende' => 'Casa del Papa - vue générale du resort',
                            ],
                        ],
                    ],
                    [
                        'nom'     => 'Résidence MGA hôtel bar restaurant',
                        'adresse' => 'RNIE1, Ouidah, Bénin',
                    ],
                    [
                        'nom'       => 'Bel Ami sur Pilotis',
                        'adresse'   => 'BP27 Ouidah, Bénin',
                        'telephone' => '+229 96182194',
                        'site_web'  => 'http://tourisme-tokpa-dome.com/',
                    ],
                    [
                        'nom'       => 'Le Village d\'Hélène - Natura Resort',
                        'adresse'   => 'Rives du lac Toho, Ouidah, Bénin',
                        'telephone' => '+229 40288888',
                        'site_web'  => 'https://lvh.bj/',
                    ],
                    [
                        'nom'       => 'RIO HÔTEL OUIDAH',
                        'adresse'   => 'Ouidah Kpassè, Ouidah, Bénin',
                        'telephone' => '+229 01 91 72 65 25',
                    ],
                    [
                        'nom'       => 'GUEST HOUSE ILÉ-IFÈ',
                        'adresse'   => 'Ouidah, Bénin',
                        'telephone' => '+229 97409807',
                        'site_web'  => 'https://sites.google.com/view/ouidah/accueil',
                    ],
                    [
                        'nom'       => 'Native Hotels Ouidah',
                        'adresse'   => 'Avenue de France, Ouidah, Bénin',
                        'telephone' => '+229 01 45 46 42 88',
                        'site_web'  => 'https://nativehotels.net/',
                    ],
                    [
                        'nom'       => 'Domaine de la Palmeraie',
                        'adresse'   => 'Ouidah, Bénin',
                        'telephone' => '+229 01 98 42 08 20',
                    ],
                    [
                        'nom'       => 'HOTEL DK',
                        'adresse'   => '937X+GP, quartier Tovè 2, Ouidah, Bénin',
                        'telephone' => '+229 40962380',
                    ],
                    [
                        'nom'       => 'Residence des Provinces',
                        'adresse'   => 'La résidence des Provinces, Ouidah, Bénin',
                        'telephone' => '+229 97602355',
                    ],
                ],
                'restaurants' => [
                    [
                        'nom'       => 'Hakuna Matata Restaurant Grillade Ouidah',
                        'adresse'   => '932V+79M, Ouidah, Bénin',
                        'telephone' => '+229 01 96 09 58 23',
                    ],
                    [
                        'nom'       => 'Alôzô',
                        'adresse'   => '936P+936, Ouidah, Bénin',
                        'telephone' => '+229 01 67 58 03 03',
                        'site_web'  => 'http://www.alozo-art.com/',
                        'photos'    => [
                            [
                                'url'     => 'https://sspark.genspark.ai/cfimages?u1=oa%2BR%2FBDJiWfhC9IgZ13wv5iRmqiJx%2F1VaIRHaGe%2Bt72NdDgNTu5QDDNNzF4aD18KVLgPAqNHZyt3oc7Bv7cGr6r0Q%2BcgGT2bBEy2l38ZqnkR7t%2BXF0Eq94eUT7DZ3WmtDCy0UreeUMcxT1iBm5kbzC22EoQ43EnzCg%3D%3D&u2=eeC66mxhR33SFyFN&width=2560',
                                'legende' => 'Restaurant Alôzô Terrasse - visuel public indexé du lieu',
                            ],
                        ],
                    ],
                    [
                        'nom'       => 'Kolè',
                        'adresse'   => 'Rue en face école publique Kpassè, Ouidah, Bénin',
                        'telephone' => '+229 01 97 66 56 40',
                    ],
                    [
                        'nom'       => 'Les Délices de la Côte',
                        'adresse'   => '101, Ouidah, Bénin',
                        'telephone' => '+229 01 61 62 92 51',
                    ],
                    [
                        'nom'       => 'Restaurant Citro Grill',
                        'adresse'   => 'Place Chacha, Ouidah, Bénin',
                        'telephone' => '+229 01 97 73 29 57',
                    ],
                    [
                        'nom'       => 'Le Goût Supérieur',
                        'adresse'   => 'Rue van Vollenhoven, Ouidah, Bénin',
                        'telephone' => '+229 01 91 34 51 35',
                    ],
                    [
                        'nom'       => 'Le Couvert de la Bahia Ouidah',
                        'adresse'   => 'Ouidah, Bénin',
                        'telephone' => '+229 01 96 15 79 66',
                    ],
                    [
                        'nom'       => 'Blue Moon bar & grill',
                        'adresse'   => 'BP544 Gomey, Ouidah, Bénin',
                        'telephone' => '+229 01 95 53 50 51',
                    ],
                    [
                        'nom'       => 'Restaurant L\'Amicale',
                        'adresse'   => 'Atinkamey en face de chez colo 1, Ouidah, Bénin',
                        'telephone' => '+229 01 62 08 54 90',
                    ],
                    [
                        'nom'     => 'La cabane du CCRI',
                        'adresse' => '934G+F68, Avenue de France, Ouidah, Bénin',
                    ],
                ],
                'appartements-meubles' => [
                    [
                        'nom'       => 'RESIDENCE SAINT-GALL-H',
                        'adresse'   => 'Ouidah, Bénin',
                        'telephone' => '+229 01 59 92 52 56',
                    ],
                    [
                        'nom'        => 'Les Résidences DA SOLI',
                        'adresse'    => 'Ouidah, Bénin',
                        'telephone'  => '+229 01 92 27 21 21',
                        'en_vedette' => true,
                        'photos'     => [
                            [
                                'url'     => 'https://sspark.genspark.ai/cfimages?u1=zXUzm3gc0l5H1zn%2BgYN199nWlEFBBZ1uY0YHhhhHbmzdUBqKpay9TYQXDRqvh4D%2BwSXVcaFLxKDwv9vOVE0EitJWUCV8DmjBNKAWgAtji%2FoKCbIRGSA2zewgc%2BWZ%2B6MO6UPac3TGQKBmvqoQNw%2B%2BSFTFJdvAf3Y%3D&u2=rDnEBLeIajNRm0y1&width=2560',
                                'legende' => 'Les Résidences DA SOLI - visuel public indexé du lieu',
                            ],
                        ],
                    ],
                    [
                        'nom'     => 'RÉSIDENCE BLANCHE',
                        'adresse' => 'Ouidah, Bénin',
                    ],
                    [
                        'nom'     => 'Maison Ouidah',
                        'adresse' => '932R+HRQ, Ouidah, Bénin',
                    ],
                    [
                        'nom'     => 'Maison fleurie Ouidah',
                        'adresse' => 'Ouidah, Bénin',
                    ],
                    [
                        'nom'     => 'Appartement climatisé 3 pièces avec Wifi à Ouidah',
                        'adresse' => 'Ouidah, Bénin',
                        'site_web' => 'https://br.bluepillow.com/search/6811c1fc6178c7268f2dd687?dest=bkng&cat=House&lat=6.35861&lng=2.09809&language=pt',
                    ],
                    [
                        'nom'     => 'Maison GBEDEDJI Ignace',
                        'adresse' => '93HF+QXC, Ouidah, Bénin',
                    ],
                    [
                        'nom'     => 'Ouidah Appartement Complexe 935Q+49',
                        'adresse' => '935Q+49, Ouidah, Bénin',
                    ],
                    [
                        'nom'     => 'Ouidah Appartement Complexe 6°21\'45"N 2°5\'53"E',
                        'adresse' => '6°21\'45" N, 2°5\'53" E, Ouidah, Bénin',
                    ],
                    [
                        'nom'     => 'Le Petit Cocon de Ouidah',
                        'adresse' => 'Ouidah, Bénin',
                        'site_web' => 'https://www.booking.com/hotel/bj/le-petit-cocon-de-ouidah.html',
                    ],
                ],
            ],

        ]; // end catalogue()
    }
}
