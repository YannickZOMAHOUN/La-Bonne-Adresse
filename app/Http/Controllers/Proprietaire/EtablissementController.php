<?php

namespace App\Http\Controllers\Proprietaire;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Etablissement;
use App\Models\Photo;
use App\Models\Service;
use App\Models\Ville;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class EtablissementController extends Controller
{
    private const COUNTRY_CODE = '229';
    private const LOCAL_PREFIX = '01';

    private const PHOTO_MIMES  = 'jpg,jpeg,png,webp';
    private const PHOTO_MAX_KB = 3072; // 3 Mo
    private const GALERIE_MAX  = 6;

    private const JOURS = [
        'Lundi',
        'Mardi',
        'Mercredi',
        'Jeudi',
        'Vendredi',
        'Samedi',
        'Dimanche',
    ];

    /**
     * Dashboard propriétaire
     */
    public function dashboard()
    {
        $etablissements = auth()->user()
            ->etablissements()
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
        $villes = Ville::where('active', true)
            ->orderBy('nom')
            ->get();

        $categories = Categorie::where('active', true)
            ->orderBy('nom')
            ->get();

        $jours = self::JOURS;

        return view('proprietaire.create', compact('villes', 'categories', 'jours'));
    }

    /**
     * Enregistrer un nouvel établissement
     */
    public function store(Request $request)
    {
        $validated = $this->validerFormulaire($request);

        if ($request->hasFile('photo_principale')) {
            $validated['photo_principale'] = $request->file('photo_principale')
                ->store('etablissements', 'public');
        }

        $validated['horaires'] = $this->buildHoraires($request);
        $validated['user_id']  = auth()->id();
        $validated['statut']   = 'en_attente';

        $etablissement = Etablissement::create($validated);

        $this->storeServices($request, $etablissement);
        $this->storePhotos($request, $etablissement);

        return redirect()
            ->route('proprietaire.dashboard')
            ->with('success', 'Votre établissement a été soumis et sera visible après validation par notre équipe.');
    }

    /**
     * Formulaire d’édition
     */
    public function edit(Etablissement $etablissement)
    {
        abort_if($etablissement->user_id !== auth()->id(), 403);

        $villes = Ville::where('active', true)
            ->orderBy('nom')
            ->get();

        $categories = Categorie::where('active', true)
            ->orderBy('nom')
            ->get();

        $jours = self::JOURS;

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

        if ($request->hasFile('photo_principale')) {
            if ($etablissement->photo_principale) {
                Storage::disk('public')->delete($etablissement->photo_principale);
            }

            $validated['photo_principale'] = $request->file('photo_principale')
                ->store('etablissements', 'public');
        }

        $validated['horaires'] = $this->buildHoraires($request);

        $etablissement->update($validated);

        $etablissement->services()->delete();
        $this->storeServices($request, $etablissement);
        $this->storePhotos($request, $etablissement);

        return redirect()
            ->route('proprietaire.dashboard')
            ->with('success', 'Modifications enregistrées avec succès.');
    }

    /**
     * Supprimer un établissement
     */
    public function destroy(Etablissement $etablissement)
    {
        abort_if($etablissement->user_id !== auth()->id(), 403);

        $etablissement->loadMissing('photos');

        if ($etablissement->photo_principale) {
            Storage::disk('public')->delete($etablissement->photo_principale);
        }

        foreach ($etablissement->photos as $photo) {
            Storage::disk('public')->delete($photo->url);
        }

        $nom = $etablissement->nom;
        $etablissement->delete();

        return redirect()
            ->route('proprietaire.dashboard')
            ->with('success', "« {$nom} » a été supprimé.");
    }

    /**
     * Supprimer une photo de galerie
     */
    public function deletePhoto(Photo $photo)
    {
        $photo->loadMissing('etablissement');

        abort_if($photo->etablissement->user_id !== auth()->id(), 403);

        Storage::disk('public')->delete($photo->url);
        $photo->delete();

        return back()->with('success', 'Photo supprimée.');
    }

    /**
     * Validation principale du formulaire
     */
    private function validerFormulaire(Request $request): array
    {
        $validated = $request->validate([
            'nom'             => ['required', 'string', 'min:2', 'max:150'],
            'description'     => ['required', 'string', 'min:30', 'max:2000'],
            'ville_id'        => ['required', 'exists:villes,id'],
            'categorie_id'    => ['required', 'exists:categories,id'],
            'adresse'         => ['required', 'string', 'min:5', 'max:255'],
            'fourchette_prix' => ['nullable', 'string', 'max:100'],

            'telephone' => $this->beninPhoneRules(false, 'Le numéro de téléphone'),
            'whatsapp'  => $this->beninPhoneRules(false, 'Le numéro WhatsApp'),

            'email'    => ['nullable', 'email', 'max:150'],
            'site_web' => ['nullable', 'url', 'max:255'],

            'photo_principale' => [
                'nullable',
                'image',
                'mimes:' . self::PHOTO_MIMES,
                'max:' . self::PHOTO_MAX_KB,
                'dimensions:min_width=400,min_height=300',
            ],

            'photos' => ['nullable', 'array', 'max:' . self::GALERIE_MAX],

            'photos.*' => [
                'image',
                'mimes:' . self::PHOTO_MIMES,
                'max:' . self::PHOTO_MAX_KB,
                'dimensions:min_width=400,min_height=300',
            ],

            'services'   => ['nullable', 'array', 'max:8'],
            'services.*' => ['nullable', 'string', 'max:60'],

            'horaires'          => ['nullable', 'array'],
            'horaires.*.ouvert' => ['nullable', 'boolean'],
            'horaires.*.debut'  => ['nullable', 'string', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'horaires.*.fin'    => ['nullable', 'string', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
        ], [
            'nom.min'                     => 'Le nom doit comporter au moins 2 caractères.',
            'description.min'             => 'La description doit comporter au moins 30 caractères.',
            'description.max'             => 'La description ne peut pas dépasser 2 000 caractères.',
            'email.email'                 => 'L’adresse email n’est pas valide.',
            'site_web.url'                => 'Le site web doit être une URL valide (ex : https://monsite.com).',
            'photo_principale.max'        => 'La photo principale ne doit pas dépasser 3 Mo.',
            'photo_principale.mimes'      => 'Format accepté : JPG, PNG ou WebP.',
            'photo_principale.dimensions' => 'La photo principale doit faire au moins 400×300 px.',
            'photos.max'                  => 'Maximum 6 photos de galerie.',
            'photos.*.max'                => 'Chaque photo ne doit pas dépasser 3 Mo.',
            'photos.*.mimes'              => 'Formats acceptés : JPG, PNG ou WebP.',
            'photos.*.dimensions'         => 'Chaque photo doit faire au moins 400×300 px.',
            'services.max'                => 'Maximum 8 services.',
        ]);

        $this->validateHorairesConsistency($request);

        $validated['nom']             = trim($validated['nom']);
        $validated['description']     = trim($validated['description']);
        $validated['adresse']         = trim($validated['adresse']);
        $validated['fourchette_prix'] = filled($validated['fourchette_prix'] ?? null)
            ? trim($validated['fourchette_prix'])
            : null;

        $validated['telephone'] = $this->normalizeBeninPhone($validated['telephone'] ?? null);
        $validated['whatsapp']  = $this->normalizeBeninPhone($validated['whatsapp'] ?? null);
        $validated['email']     = filled($validated['email'] ?? null)
            ? strtolower(trim($validated['email']))
            : null;
        $validated['site_web']  = filled($validated['site_web'] ?? null)
            ? trim($validated['site_web'])
            : null;

        return $validated;
    }

    /**
     * Vérifie la cohérence des horaires
     */
    private function validateHorairesConsistency(Request $request): void
    {
        $horaires = $request->input('horaires', []);

        foreach (self::JOURS as $jour) {
            $data   = $horaires[$jour] ?? [];
            $ouvert = !empty($data['ouvert']);
            $debut  = $data['debut'] ?? null;
            $fin    = $data['fin'] ?? null;

            if (!$ouvert) {
                continue;
            }

            if (empty($debut) || empty($fin)) {
                throw ValidationException::withMessages([
                    "horaires.{$jour}.debut" => "Pour {$jour}, veuillez renseigner l'heure d'ouverture et de fermeture.",
                ]);
            }

            if ($fin <= $debut) {
                throw ValidationException::withMessages([
                    "horaires.{$jour}.fin" => "Pour {$jour}, l'heure de fermeture doit être après l'heure d'ouverture.",
                ]);
            }
        }
    }

    /**
     * Construit la structure des horaires
     */
    private function buildHoraires(Request $request): ?array
    {
        $horairesInput = $request->input('horaires');

        if (empty($horairesInput)) {
            return null;
        }

        $result = [];

        foreach (self::JOURS as $jour) {
            $data   = $horairesInput[$jour] ?? [];
            $ouvert = !empty($data['ouvert']);

            if (!$ouvert) {
                $result[$jour] = 'Fermé';
                continue;
            }

            $debut = $data['debut'] ?? '08:00';
            $fin   = $data['fin'] ?? '18:00';

            $result[$jour] = "{$debut} – {$fin}";
        }

        return $result;
    }

    /**
     * Enregistre les services
     */
    private function storeServices(Request $request, Etablissement $etablissement): void
    {
        $services = collect($request->input('services', []))
            ->map(fn ($service) => trim((string) $service))
            ->filter()
            ->unique()
            ->take(8)
            ->values();

        foreach ($services as $libelle) {
            Service::create([
                'etablissement_id' => $etablissement->id,
                'libelle'          => $libelle,
            ]);
        }
    }

    /**
     * Enregistre les photos de galerie
     */
    private function storePhotos(Request $request, Etablissement $etablissement): void
    {
        if (!$request->hasFile('photos')) {
            return;
        }

        $existingCount = $etablissement->photos()->count();
        $remainingSlots = max(0, self::GALERIE_MAX - $existingCount);

        if ($remainingSlots === 0) {
            return;
        }

        $ordre = $etablissement->photos()->max('ordre') ?? 0;
        $photos = array_slice($request->file('photos', []), 0, $remainingSlots);

        foreach ($photos as $file) {
            $path = $file->store('etablissements/galerie', 'public');

            Photo::create([
                'etablissement_id' => $etablissement->id,
                'url'              => $path,
                'ordre'            => ++$ordre,
            ]);
        }
    }

    /**
     * Règles de validation d’un numéro béninois
     * Accepte :
     * - 01 00 00 00 00
     * - +229 01 00 00 00 00
     * - 2290100000000
     * - 0100000000
     *
     * Si champ optionnel et valeur = "01" uniquement, on le considère vide.
     */
    private function beninPhoneRules(bool $required = false, string $label = 'Le numéro'): array
    {
        return [
            $required ? 'required' : 'nullable',
            'string',
            'max:20',
            function (string $attribute, mixed $value, Closure $fail) use ($required, $label) {
                $stringValue = trim((string) ($value ?? ''));

                if ($stringValue === '') {
                    if ($required) {
                        $fail("{$label} est obligatoire.");
                    }
                    return;
                }

                if (!$required && $this->isEffectivelyEmptyBeninPhone($stringValue)) {
                    return;
                }

                if (!$this->isValidBeninPhone($stringValue)) {
                    $fail("{$label} doit être au format béninois : 01 00 00 00 00.");
                }
            },
        ];
    }

    /**
     * Vérifie si la valeur correspond à un champ laissé au préfixe par défaut
     */
    private function isEffectivelyEmptyBeninPhone(?string $value): bool
    {
        if ($value === null) {
            return true;
        }

        $digits = preg_replace('/\D+/', '', $value);

        return $digits === '' || $digits === self::LOCAL_PREFIX;
    }

    /**
     * Vérifie si le numéro est valide
     */
    private function isValidBeninPhone(?string $value): bool
    {
        if ($value === null) {
            return false;
        }

        $digits = preg_replace('/\D+/', '', $value);

        if (!$digits) {
            return false;
        }

        if (str_starts_with($digits, self::COUNTRY_CODE)) {
            $digits = substr($digits, strlen(self::COUNTRY_CODE));
        }

        if (strlen($digits) === 8) {
            $digits = self::LOCAL_PREFIX . $digits;
        }

        return strlen($digits) === 10 && str_starts_with($digits, self::LOCAL_PREFIX);
    }

    /**
     * Normalise vers : +229 01 00 00 00 00
     */
    private function normalizeBeninPhone(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $value);

        if (!$digits || $digits === self::LOCAL_PREFIX) {
            return null;
        }

        if (str_starts_with($digits, self::COUNTRY_CODE)) {
            $digits = substr($digits, strlen(self::COUNTRY_CODE));
        }

        if (strlen($digits) === 8) {
            $digits = self::LOCAL_PREFIX . $digits;
        }

        if (strlen($digits) !== 10 || !str_starts_with($digits, self::LOCAL_PREFIX)) {
            return null;
        }

        return sprintf(
            '+229 %s %s %s %s %s',
            substr($digits, 0, 2),
            substr($digits, 2, 2),
            substr($digits, 4, 2),
            substr($digits, 6, 2),
            substr($digits, 8, 2)
        );
    }
}
