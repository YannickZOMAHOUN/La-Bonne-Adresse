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
    private const LOCAL_PREFIX  = '01';

    private const PHOTO_MIMES  = 'jpg,jpeg,png,webp';
    private const MENU_MIMES   = 'jpg,jpeg,png,webp,pdf';
    private const GALERIE_MAX  = 6;

    private const JOURS = [
        'Lundi', 'Mardi', 'Mercredi', 'Jeudi',
        'Vendredi', 'Samedi', 'Dimanche',
    ];

    public function dashboard()
    {
        $etablissements = auth()->user()
            ->etablissements()
            ->with(['ville', 'categorie'])
            ->latest()
            ->get();

        return view('proprietaire.dashboard', compact('etablissements'));
    }

    public function create()
    {
        $villes     = Ville::where('active', true)->orderBy('nom')->get();
        $categories = Categorie::where('active', true)->orderBy('nom')->get();
        $jours      = self::JOURS;

        return view('proprietaire.create', compact('villes', 'categories', 'jours'));
    }

    public function store(Request $request)
    {
        $validated = $this->validerFormulaire($request);

        if ($request->hasFile('photo_principale')) {
            $validated['photo_principale'] = $request->file('photo_principale')
                ->store('etablissements', 'public');
        }

        if ($request->hasFile('menu')) {
            $validated['menu'] = $request->file('menu')
                ->store('etablissements/menus', 'public');
        }

        $validated['horaires'] = $this->buildHoraires($request);
        $validated['user_id']  = auth()->id();
        $validated['statut']   = 'en_attente';

        $etablissement = Etablissement::create($validated);

        $this->storeServices($request, $etablissement);
        $this->storePhotos($request, $etablissement);

        return redirect()
            ->route('proprietaire.dashboard')
            ->with('success', 'Votre établissement a été soumis et sera visible après validation.');
    }

    public function edit(Etablissement $etablissement)
    {
        abort_if($etablissement->user_id !== auth()->id(), 403);

        $villes     = Ville::where('active', true)->orderBy('nom')->get();
        $categories = Categorie::where('active', true)->orderBy('nom')->get();
        $jours      = self::JOURS;

        $etablissement->load(['services', 'photos']);

        return view('proprietaire.edit', compact('etablissement', 'villes', 'categories', 'jours'));
    }

    public function update(Request $request, Etablissement $etablissement)
    {
        abort_if($etablissement->user_id !== auth()->id(), 403);

        $validated = $this->validerFormulaire($request);

        // Photo principale
        if ($request->hasFile('photo_principale')) {
            if ($etablissement->photo_principale) {
                Storage::disk('public')->delete($etablissement->photo_principale);
            }
            $validated['photo_principale'] = $request->file('photo_principale')
                ->store('etablissements', 'public');
        }

        // Menu
        if ($request->boolean('supprimer_menu') && $etablissement->menu) {
            Storage::disk('public')->delete($etablissement->menu);
            $validated['menu'] = null;
        }

        if ($request->hasFile('menu')) {
            if ($etablissement->menu) {
                Storage::disk('public')->delete($etablissement->menu);
            }
            $validated['menu'] = $request->file('menu')
                ->store('etablissements/menus', 'public');
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

    public function destroy(Etablissement $etablissement)
    {
        abort_if($etablissement->user_id !== auth()->id(), 403);

        $etablissement->loadMissing('photos');

        if ($etablissement->photo_principale) {
            Storage::disk('public')->delete($etablissement->photo_principale);
        }
        if ($etablissement->menu) {
            Storage::disk('public')->delete($etablissement->menu);
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

    public function deletePhoto(Photo $photo)
    {
        $photo->loadMissing('etablissement');
        abort_if($photo->etablissement->user_id !== auth()->id(), 403);

        Storage::disk('public')->delete($photo->url);
        $photo->delete();

        return back()->with('success', 'Photo supprimée.');
    }

    // ── Validation ───────────────────────────────────────────────────────

    private function validerFormulaire(Request $request): array
    {
        $validated = $request->validate([
            'nom'             => ['required', 'string', 'min:2', 'max:150'],
            'description'     => ['required', 'string','max:2000'],
            'ville_id'        => ['required', 'exists:villes,id'],
            'categorie_id'    => ['required', 'exists:categories,id'],
            'adresse'         => ['required', 'string', 'min:5', 'max:255'],
            'fourchette_prix' => ['nullable', 'string', 'max:100'],

            'telephone' => $this->beninPhoneRules(false, 'Le numéro de téléphone'),
            'whatsapp'  => $this->beninPhoneRules(false, 'Le numéro WhatsApp'),

            'email'    => ['nullable', 'email', 'max:150'],
            'site_web' => ['nullable', 'url', 'max:255'],

            // Photo principale — aucune contrainte de taille ni de dimensions
            'photo_principale' => ['nullable', 'image', 'mimes:' . self::PHOTO_MIMES],

            // Galerie — aucune contrainte de taille ni de dimensions
            'photos'   => ['nullable', 'array', 'max:' . self::GALERIE_MAX],
            'photos.*' => ['image', 'mimes:' . self::PHOTO_MIMES],

            // Menu — image ou PDF, aucune contrainte de taille
            'menu' => ['nullable', 'file', 'mimes:' . self::MENU_MIMES],

            'services'   => ['nullable', 'array', 'max:8'],
            'services.*' => ['nullable', 'string', 'max:60'],

            'horaires'          => ['nullable', 'array'],
            'horaires.*.ouvert' => ['nullable', 'boolean'],
            'horaires.*.debut'  => ['nullable', 'string', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'horaires.*.fin'    => ['nullable', 'string', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
        ], [
            'nom.min'            => 'Le nom doit comporter au moins 2 caractères.',
            'description.max'    => 'La description ne peut pas dépasser 2 000 caractères.',
            'email.email'        => "L'adresse email n'est pas valide.",
            'site_web.url'       => 'Le site web doit être une URL valide.',
            'photo_principale.mimes' => 'Format accepté : JPG, PNG ou WebP.',
            'photos.*.mimes'     => 'Formats acceptés : JPG, PNG ou WebP.',
            'menu.mimes'         => 'Le menu doit être une image (JPG, PNG, WebP) ou un PDF.',
            'services.max'       => 'Maximum 8 services.',
        ]);

        $this->validateHorairesConsistency($request);

        $validated['nom']             = trim($validated['nom']);
        $validated['description']     = trim($validated['description']);
        $validated['adresse']         = trim($validated['adresse']);
        $validated['fourchette_prix'] = filled($validated['fourchette_prix'] ?? null)
            ? trim($validated['fourchette_prix']) : null;

        $validated['telephone'] = $this->normalizeBeninPhone($validated['telephone'] ?? null);
        $validated['whatsapp']  = $this->normalizeBeninPhone($validated['whatsapp'] ?? null);
        $validated['email']     = filled($validated['email'] ?? null)
            ? strtolower(trim($validated['email'])) : null;
        $validated['site_web']  = filled($validated['site_web'] ?? null)
            ? trim($validated['site_web']) : null;

        return $validated;
    }

    private function validateHorairesConsistency(Request $request): void
    {
        foreach (self::JOURS as $jour) {
            $data   = $request->input("horaires.{$jour}", []);
            $ouvert = !empty($data['ouvert']);
            $debut  = $data['debut'] ?? null;
            $fin    = $data['fin']   ?? null;

            if (!$ouvert) continue;

            if (empty($debut) || empty($fin)) {
                throw ValidationException::withMessages([
                    "horaires.{$jour}.debut" => "Pour {$jour}, renseignez l'heure d'ouverture et de fermeture.",
                ]);
            }
            if ($fin <= $debut) {
                throw ValidationException::withMessages([
                    "horaires.{$jour}.fin" => "Pour {$jour}, la fermeture doit être après l'ouverture.",
                ]);
            }
        }
    }

    private function buildHoraires(Request $request): ?array
    {
        $input = $request->input('horaires');
        if (empty($input)) return null;

        $result = [];
        foreach (self::JOURS as $jour) {
            $data   = $input[$jour] ?? [];
            $ouvert = !empty($data['ouvert']);

            if (!$ouvert) { $result[$jour] = 'Fermé'; continue; }

            $debut = $data['debut'] ?? '08:00';
            $fin   = $data['fin']   ?? '18:00';
            $result[$jour] = "{$debut} – {$fin}";
        }

        return $result;
    }

    private function storeServices(Request $request, Etablissement $etablissement): void
    {
        collect($request->input('services', []))
            ->map(fn ($s) => trim((string) $s))
            ->filter()->unique()->take(8)->values()
            ->each(fn ($libelle) => Service::create([
                'etablissement_id' => $etablissement->id,
                'libelle'          => $libelle,
            ]));
    }

    private function storePhotos(Request $request, Etablissement $etablissement): void
    {
        if (!$request->hasFile('photos')) return;

        $existingCount  = $etablissement->photos()->count();
        $remainingSlots = max(0, self::GALERIE_MAX - $existingCount);
        if ($remainingSlots === 0) return;

        $ordre  = $etablissement->photos()->max('ordre') ?? 0;
        $photos = array_slice($request->file('photos', []), 0, $remainingSlots);

        foreach ($photos as $file) {
            Photo::create([
                'etablissement_id' => $etablissement->id,
                'url'              => $file->store('etablissements/galerie', 'public'),
                'ordre'            => ++$ordre,
            ]);
        }
    }

    // ── Téléphone béninois ────────────────────────────────────────────────

    private function beninPhoneRules(bool $required = false, string $label = 'Le numéro'): array
    {
        return [
            $required ? 'required' : 'nullable', 'string', 'max:20',
            function (string $attribute, mixed $value, Closure $fail) use ($required, $label) {
                $val = trim((string) ($value ?? ''));
                if ($val === '') { if ($required) $fail("{$label} est obligatoire."); return; }
                if (!$required && $this->isEffectivelyEmptyBeninPhone($val)) return;
                if (!$this->isValidBeninPhone($val))
                    $fail("{$label} doit être au format béninois : 01 00 00 00 00.");
            },
        ];
    }

    private function isEffectivelyEmptyBeninPhone(?string $value): bool
    {
        if ($value === null) return true;
        $digits = preg_replace('/\D+/', '', $value);
        return $digits === '' || $digits === self::LOCAL_PREFIX;
    }

    private function isValidBeninPhone(?string $value): bool
    {
        if ($value === null) return false;
        $digits = preg_replace('/\D+/', '', $value);
        if (!$digits) return false;
        if (str_starts_with($digits, self::COUNTRY_CODE)) $digits = substr($digits, 3);
        if (strlen($digits) === 8) $digits = self::LOCAL_PREFIX . $digits;
        return strlen($digits) === 10 && str_starts_with($digits, self::LOCAL_PREFIX);
    }

    private function normalizeBeninPhone(?string $value): ?string
    {
        if ($value === null) return null;
        $digits = preg_replace('/\D+/', '', $value);
        if (!$digits || $digits === self::LOCAL_PREFIX) return null;
        if (str_starts_with($digits, self::COUNTRY_CODE)) $digits = substr($digits, 3);
        if (strlen($digits) === 8) $digits = self::LOCAL_PREFIX . $digits;
        if (strlen($digits) !== 10 || !str_starts_with($digits, self::LOCAL_PREFIX)) return null;
        return sprintf('+229 %s %s %s %s %s',
            substr($digits, 0, 2), substr($digits, 2, 2),
            substr($digits, 4, 2), substr($digits, 6, 2),
            substr($digits, 8, 2));
    }
}
