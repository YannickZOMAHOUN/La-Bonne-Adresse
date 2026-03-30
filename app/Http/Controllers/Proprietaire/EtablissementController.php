<?php

namespace App\Http\Controllers\Proprietaire;

use App\Http\Controllers\Controller;
use App\Models\Ville;
use App\Models\Categorie;
use App\Models\Etablissement;
use App\Models\Service;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EtablissementController extends Controller
{
    // ── Formats d'image acceptés ───────────────────────────────
    const PHOTO_MIMES    = 'jpg,jpeg,png,webp';
    const PHOTO_MAX_KB   = 3072;   // 3 Mo
    const GALERIE_MAX    = 6;

    // ── Jours de la semaine (pour les horaires) ────────────────
    const JOURS = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];

    /**
     * Dashboard propriétaire
     */
    public function dashboard()
    {
        $etablissements = auth()->user()->etablissements()
            ->with(['ville', 'categorie'])
            ->latest()
            ->get();

        return view('proprietaire.dashboard', compact('etablissements'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $villes     = Ville::where('active', true)->get();
        $categories = Categorie::where('active', true)->get();
        $jours      = self::JOURS;

        return view('proprietaire.create', compact('villes', 'categories', 'jours'));
    }

    /**
     * Enregistrer un nouvel établissement
     */
    public function store(Request $request)
    {
        $validated = $this->validerFormulaire($request);

        // Upload photo principale
        if ($request->hasFile('photo_principale')) {
            $validated['photo_principale'] = $request->file('photo_principale')
                ->store('etablissements', 'public');
        }

        // Horaires structurés → JSON
        $validated['horaires'] = $this->buildHoraires($request);

        $validated['user_id'] = auth()->id();
        $validated['statut']  = 'en_attente';

        $etablissement = Etablissement::create($validated);

        $this->storeServices($request, $etablissement);
        $this->storePhotos($request, $etablissement);

        return redirect()->route('proprietaire.dashboard')
            ->with('success', 'Votre établissement a été soumis. Il sera visible après validation.');
    }

    /**
     * Formulaire de modification
     */
    public function edit(Etablissement $etablissement)
    {
        abort_if($etablissement->user_id !== auth()->id(), 403);

        $villes     = Ville::where('active', true)->get();
        $categories = Categorie::where('active', true)->get();
        $jours      = self::JOURS;

        $etablissement->load(['services', 'photos']);

        return view('proprietaire.edit', compact('etablissement', 'villes', 'categories', 'jours'));
    }

    /**
     * Mettre à jour un établissement
     */
    public function update(Request $request, Etablissement $etablissement)
    {
        abort_if($etablissement->user_id !== auth()->id(), 403);

        $validated = $this->validerFormulaire($request);

        // Nouvelle photo principale
        if ($request->hasFile('photo_principale')) {
            if ($etablissement->photo_principale) {
                Storage::disk('public')->delete($etablissement->photo_principale);
            }
            $validated['photo_principale'] = $request->file('photo_principale')
                ->store('etablissements', 'public');
        }

        // Horaires structurés → JSON
        $validated['horaires'] = $this->buildHoraires($request);

        // Repasse en attente après modification
        $validated['statut'] = 'en_attente';

        $etablissement->update($validated);

        $etablissement->services()->delete();
        $this->storeServices($request, $etablissement);
        $this->storePhotos($request, $etablissement);

        return redirect()->route('proprietaire.dashboard')
            ->with('success', 'Modifications enregistrées. Votre fiche sera revalidée sous 24h.');
    }

    /**
     * Supprimer une photo de la galerie
     */
    public function deletePhoto(Photo $photo)
    {
        abort_if($photo->etablissement->user_id !== auth()->id(), 403);

        Storage::disk('public')->delete($photo->url);
        $photo->delete();

        return back()->with('success', 'Photo supprimée.');
    }

    // ══ HELPERS PRIVÉS ════════════════════════════════════════

    /**
     * Règles de validation communes store / update
     */
    private function validerFormulaire(Request $request): array
    {
        return $request->validate([
            // Infos principales
            'nom'             => ['required', 'string', 'min:2', 'max:150'],
            'description'     => ['required', 'string', 'min:30', 'max:2000'],
            'ville_id'        => ['required', 'exists:villes,id'],
            'categorie_id'    => ['required', 'exists:categories,id'],
            'adresse'         => ['required', 'string', 'min:5', 'max:255'],
            'fourchette_prix' => ['nullable', 'string', 'max:100'],

            // Contacts — regex numéro béninois (229) ou international (+XX…)
            'telephone' => [
                'nullable',
                'string',
                'max:25',
                'regex:/^\+?229[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/',
            ],
            'whatsapp' => [
                'nullable',
                'string',
                'max:25',
                'regex:/^\+?[0-9]{1,3}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/',
            ],
            'email'   => ['nullable', 'email:rfc,dns', 'max:150'],
            'site_web' => ['nullable', 'url', 'max:255'],

            // Photos
            'photo_principale' => [
                'nullable',
                'image',
                'mimes:' . self::PHOTO_MIMES,
                'max:' . self::PHOTO_MAX_KB,
                'dimensions:min_width=400,min_height=300',
            ],
            'photos'   => ['nullable', 'array', 'max:' . self::GALERIE_MAX],
            'photos.*' => [
                'image',
                'mimes:' . self::PHOTO_MIMES,
                'max:' . self::PHOTO_MAX_KB,
                'dimensions:min_width=400,min_height=300',
            ],

            // Services
            'services'   => ['nullable', 'array', 'max:8'],
            'services.*' => ['nullable', 'string', 'max:60'],

            // Horaires (validés par buildHoraires, pas besoin de règle stricte ici)
            'horaires'          => ['nullable', 'array'],
            'horaires.*.ouvert' => ['nullable', 'boolean'],
            'horaires.*.debut'  => ['nullable', 'string', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'horaires.*.fin'    => ['nullable', 'string', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
        ], [
            // Messages personnalisés
            'nom.min'                     => 'Le nom doit comporter au moins 2 caractères.',
            'description.min'             => 'La description doit comporter au moins 30 caractères.',
            'description.max'             => 'La description ne peut pas dépasser 2 000 caractères.',
            'telephone.regex'             => 'Le numéro de téléphone n\'est pas valide (ex : +229 97 00 00 00).',
            'whatsapp.regex'              => 'Le numéro WhatsApp n\'est pas valide (ex : +229 97 00 00 00).',
            'photo_principale.max'        => 'La photo principale ne doit pas dépasser 3 Mo.',
            'photo_principale.mimes'      => 'Format accepté : JPG, PNG ou WebP.',
            'photo_principale.dimensions' => 'La photo principale doit faire au moins 400×300 px.',
            'photos.*.max'                => 'Chaque photo de galerie ne doit pas dépasser 3 Mo.',
            'photos.*.mimes'              => 'Formats acceptés : JPG, PNG ou WebP.',
            'photos.*.dimensions'         => 'Chaque photo doit faire au moins 400×300 px.',
            'photos.max'                  => 'Vous ne pouvez pas ajouter plus de 6 photos de galerie.',
            'services.max'                => 'Vous ne pouvez pas ajouter plus de 8 services.',
            'horaires.*.debut.regex'      => 'Format d\'heure invalide (HH:MM attendu).',
            'horaires.*.fin.regex'        => 'Format d\'heure invalide (HH:MM attendu).',
        ]);
    }

    /**
     * Construire le tableau horaires à partir du formulaire structuré
     * Format stocké : ["Lundi" => "08:00 - 18:00", "Mardi" => "Fermé", ...]
     */
    private function buildHoraires(Request $request): ?array
    {
        $horairesInput = $request->input('horaires');
        if (empty($horairesInput)) return null;

        $result = [];
        foreach (self::JOURS as $jour) {
            $data = $horairesInput[$jour] ?? [];
            $ouvert = !empty($data['ouvert']);

            if (!$ouvert) {
                $result[$jour] = 'Fermé';
                continue;
            }

            $debut = $data['debut'] ?? '08:00';
            $fin   = $data['fin']   ?? '18:00';
            $result[$jour] = $debut . ' – ' . $fin;
        }

        return $result;
    }

    /**
     * Enregistrer les services en filtrant les valeurs vides
     */
    private function storeServices(Request $request, Etablissement $etablissement): void
    {
        $services = collect($request->services ?? [])
            ->map(fn($s) => trim($s))
            ->filter(fn($s) => !empty($s))
            ->unique()
            ->values();

        foreach ($services as $libelle) {
            Service::create([
                'etablissement_id' => $etablissement->id,
                'libelle'          => $libelle,
            ]);
        }
    }

    /**
     * Enregistrer les photos supplémentaires
     */
    private function storePhotos(Request $request, Etablissement $etablissement): void
    {
        if (!$request->hasFile('photos')) return;

        $ordre = $etablissement->photos()->max('ordre') ?? 0;

        foreach ($request->file('photos') as $file) {
            $path = $file->store('etablissements/galerie', 'public');
            Photo::create([
                'etablissement_id' => $etablissement->id,
                'url'              => $path,
                'ordre'            => ++$ordre,
            ]);
        }
    }
}
