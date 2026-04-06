<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\EtablissementValide;
use App\Mail\ProprietaireCompteActive;
use App\Models\Categorie;
use App\Models\Etablissement;
use App\Models\Photo;
use App\Models\Service;
use App\Models\User;
use App\Models\Ville;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    private const COUNTRY_CODE = '229';
    private const LOCAL_PREFIX  = '01';

    private const PHOTO_MIMES  = 'jpg,jpeg,png,webp';
    // Pas de limite de taille ni de nombre de photos

    private const JOURS = [
        'Lundi', 'Mardi', 'Mercredi', 'Jeudi',
        'Vendredi', 'Samedi', 'Dimanche',
    ];

    // ════════════════════════════════════════════════════════════════════════
    //  DASHBOARD
    // ════════════════════════════════════════════════════════════════════════

    public function dashboard()
    {
        $stats = [
            'total_etablissements'  => Etablissement::count(),
            'en_attente'            => Etablissement::where('statut', 'en_attente')->count(),
            'actifs'                => Etablissement::where('statut', 'actif')->count(),
            'proprietaires'         => User::where('role', 'proprietaire')->count(),
            'proprietaires_actifs'  => User::where('role', 'proprietaire')->where('statut', 'actif')->count(),

            'visiteurs_aujourd_hui' => DB::table('page_views')
                ->whereDate('created_at', today())
                ->distinct('ip')->count('ip'),

            'visiteurs_ce_mois' => DB::table('page_views')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->distinct('ip')->count('ip'),

            'page_views_total' => DB::table('page_views')->count(),
        ];

        $enAttente = Etablissement::where('statut', 'en_attente')
            ->with(['ville', 'categorie', 'user'])
            ->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'enAttente'));
    }

    // ════════════════════════════════════════════════════════════════════════
    //  ÉTABLISSEMENTS — liste
    // ════════════════════════════════════════════════════════════════════════

    public function etablissements()
    {
        $etablissements = Etablissement::with(['ville', 'categorie', 'user'])
            ->latest()->paginate(20);

        $villes        = Ville::where('active', true)->orderBy('nom')->get();
        $categories    = Categorie::where('active', true)->orderBy('nom')->get();
        $proprietaires = User::where('role', 'proprietaire')
            ->where('statut', 'actif')->orderBy('nom')->get();

        return view('admin.etablissements', compact(
            'etablissements', 'villes', 'categories', 'proprietaires'
        ));
    }

    // ════════════════════════════════════════════════════════════════════════
    //  ÉTABLISSEMENTS — formulaire pleine page (créer)
    // ════════════════════════════════════════════════════════════════════════

    public function createEtablissement()
    {
        $villes        = Ville::where('active', true)->orderBy('nom')->get();
        $categories    = Categorie::where('active', true)->orderBy('nom')->get();
        $proprietaires = User::where('role', 'proprietaire')
            ->where('statut', 'actif')->orderBy('nom')->get();
        $jours         = self::JOURS;

        return view('admin.form-etablissement', compact(
            'villes', 'categories', 'proprietaires', 'jours'
        ));
    }

    public function storeEtablissement(Request $request)
    {
        $validated = $this->validerFormulaireAdmin($request);

        if ($request->hasFile('photo_principale')) {
            $validated['photo_principale'] = $request->file('photo_principale')
                ->store('etablissements', 'public');
        }

        $validated['horaires'] = $this->buildHoraires($request);

        // Si aucun propriétaire choisi, on rattache l'admin connecté
        if (empty($validated['user_id'])) {
            $validated['user_id'] = auth()->id();
        }

        $etablissement = Etablissement::create($validated);

        $this->storeServices($request, $etablissement);
        $this->storePhotos($request, $etablissement);

        return redirect()
            ->route('admin.etablissements')
            ->with('success', "« {$etablissement->nom} » a été créé avec succès.");
    }

    // ════════════════════════════════════════════════════════════════════════
    //  ÉTABLISSEMENTS — formulaire pleine page (modifier)
    // ════════════════════════════════════════════════════════════════════════

    public function editEtablissement(Etablissement $etablissement)
    {
        $etablissement->load(['services', 'photos']);

        $villes        = Ville::where('active', true)->orderBy('nom')->get();
        $categories    = Categorie::where('active', true)->orderBy('nom')->get();
        $proprietaires = User::where('role', 'proprietaire')
            ->where('statut', 'actif')->orderBy('nom')->get();
        $jours         = self::JOURS;

        return view('admin.form-etablissement', compact(
            'etablissement', 'villes', 'categories', 'proprietaires', 'jours'
        ));
    }

    public function updateEtablissement(Request $request, Etablissement $etablissement)
    {
        $validated = $this->validerFormulaireAdmin($request);

        // ── Photo principale ──────────────────────────────────────────────
        if ($request->boolean('supprimer_photo_principale') && $etablissement->photo_principale) {
            Storage::disk('public')->delete($etablissement->photo_principale);
            $validated['photo_principale'] = null;
        }

        if ($request->hasFile('photo_principale')) {
            if ($etablissement->photo_principale) {
                Storage::disk('public')->delete($etablissement->photo_principale);
            }
            $validated['photo_principale'] = $request->file('photo_principale')
                ->store('etablissements', 'public');
        }

        // ── Horaires ──────────────────────────────────────────────────────
        $validated['horaires'] = $this->buildHoraires($request);

        // ── Suppression sélective de photos de galerie ────────────────────
        if ($request->filled('supprimer_photos')) {
            $photosASupprimer = Photo::whereIn('id', $request->input('supprimer_photos'))
                ->where('etablissement_id', $etablissement->id)
                ->get();

            foreach ($photosASupprimer as $photo) {
                Storage::disk('public')->delete($photo->url);
                $photo->delete();
            }
        }

        // CORRECTIF : Gestion sécurisée du user_id avant la mise à jour
        if (empty($validated['user_id'])) {
            if ($etablissement->user_id) {
                $validated['user_id'] = $etablissement->user_id;
            } else {
                $validated['user_id'] = auth()->id();
            }
        }

        // ── Mise à jour des champs ────────────────────────────────────────
        $etablissement->update($validated);

        // ── Services : recréer proprement ────────────────────────────────
        $etablissement->services()->delete();
        $this->storeServices($request, $etablissement);

        // ── Nouvelles photos de galerie ───────────────────────────────────
        $this->storePhotos($request, $etablissement);

        return redirect()
            ->route('admin.etablissements')
            ->with('success', "« {$etablissement->nom} » a été mis à jour avec succès.");
    }

    // ════════════════════════════════════════════════════════════════════════
    //  ÉTABLISSEMENTS — actions rapides (depuis la liste)
    // ════════════════════════════════════════════════════════════════════════

    public function preview(Etablissement $etablissement)
    {
        $etablissement->load(['ville', 'categorie', 'services', 'photos', 'user']);

        $suggestions = Etablissement::actif()
            ->where('ville_id', $etablissement->ville_id)
            ->where('categorie_id', $etablissement->categorie_id)
            ->where('id', '!=', $etablissement->id)
            ->take(3)->get();

        return view('admin.preview', compact('etablissement', 'suggestions'));
    }

    public function valider(Etablissement $etablissement)
    {
        $etablissement->update(['statut' => 'actif']);
        $etablissement->load(['user', 'ville', 'categorie']);

        try {
            Mail::to($etablissement->user->email)
                ->queue(new EtablissementValide($etablissement));
        } catch (\Exception $e) {
            Log::error("Erreur mail validation établissement : " . $e->getMessage());
        }

        return back()->with('success', "« {$etablissement->nom} » est maintenant visible. Mail envoyé.");
    }

    public function suspendre(Etablissement $etablissement)
    {
        $etablissement->update(['statut' => 'suspendu']);

        return back()->with('info', "« {$etablissement->nom} » a été suspendu.");
    }

    public function supprimerEtablissement(Etablissement $etablissement)
    {
        if ($etablissement->en_vedette) {
            return back()->with('error', "Impossible de supprimer « {$etablissement->nom} » : il est en vedette.");
        }

        $etablissement->loadMissing('photos');

        if ($etablissement->photo_principale) {
            Storage::disk('public')->delete($etablissement->photo_principale);
        }

        foreach ($etablissement->photos as $photo) {
            Storage::disk('public')->delete($photo->url);
        }

        $nom = $etablissement->nom;
        $etablissement->delete();

        return back()->with('success', "« {$nom} » a été supprimé définitivement.");
    }

    public function toggleVedette(Etablissement $etablissement)
    {
        $etablissement->update(['en_vedette' => !$etablissement->en_vedette]);
        $msg = $etablissement->en_vedette ? 'mis en vedette' : 'retiré de la vedette';

        return back()->with('success', "« {$etablissement->nom} » a été {$msg}.");
    }

    public function attribuerProprietaire(Request $request, Etablissement $etablissement)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $ancien = $etablissement->user->nom ?? '—';
        $etablissement->update(['user_id' => $request->user_id]);
        $nouveau = User::find($request->user_id)->nom;

        return back()->with('success', "« {$etablissement->nom} » attribué à {$nouveau} (ancien : {$ancien}).");
    }

    // ════════════════════════════════════════════════════════════════════════
    //  PROPRIÉTAIRES
    // ════════════════════════════════════════════════════════════════════════

    public function proprietaires()
    {
        $proprietaires = User::where('role', 'proprietaire')
            ->withCount('etablissements')
            ->latest()->paginate(20);

        return view('admin.proprietaires', compact('proprietaires'));
    }

    public function activerProprietaire(User $user)
    {
        $user->update(['statut' => 'actif']);

        try {
            Mail::to($user->email)->queue(new ProprietaireCompteActive($user));
        } catch (\Exception $e) {
            Log::error("Erreur mail activation propriétaire : " . $e->getMessage());
        }

        return back()->with('success', "Le compte de {$user->nom} a été activé. Mail d'accueil envoyé.");
    }

    public function suspendrePropietaire(User $user)
    {
        $user->update(['statut' => 'suspendu']);

        return back()->with('info', "Le compte de {$user->nom} a été suspendu.");
    }

    public function supprimerProprietaire(User $user)
    {
        foreach ($user->etablissements()->with('photos')->get() as $etablissement) {
            if ($etablissement->photo_principale) {
                Storage::disk('public')->delete($etablissement->photo_principale);
            }
            foreach ($etablissement->photos as $photo) {
                Storage::disk('public')->delete($photo->url);
            }
        }

        $nom = $user->nom;
        $user->delete();

        return back()->with('success', "Le compte de {$nom} et tous ses établissements ont été supprimés.");
    }

    // ════════════════════════════════════════════════════════════════════════
    //  HELPERS PRIVÉS — validation
    // ════════════════════════════════════════════════════════════════════════

    private function validerFormulaireAdmin(Request $request): array
    {
        $validated = $request->validate([
            'nom'             => ['required', 'string', 'min:2', 'max:150'],
            'description'      => ['required', 'string', 'max:2000'],
            'ville_id'        => ['required', 'exists:villes,id'],
            'categorie_id'    => ['required', 'exists:categories,id'],
            'adresse'         => ['required', 'string', 'min:5', 'max:255'],
            'fourchette_prix' => ['nullable', 'string', 'max:100'],

            'telephone' => $this->beninPhoneRules(false, 'Le numéro de téléphone'),
            'whatsapp'  => $this->beninPhoneRules(false, 'Le numéro WhatsApp'),

            'email'    => ['nullable', 'email', 'max:150'],
            'site_web' => ['nullable', 'url', 'max:255'],

            // Photo principale — sans limite de taille ni de dimensions minimales imposées
            'photo_principale' => [
                'nullable',
                'image',
                'mimes:' . self::PHOTO_MIMES,
            ],

            // Galerie — sans limite de nombre ni de taille
            'photos'   => ['nullable', 'array'],
            'photos.*' => [
                'image',
                'mimes:' . self::PHOTO_MIMES,
            ],

            'services'   => ['nullable', 'array', 'max:8'],
            'services.*' => ['nullable', 'string', 'max:60'],

            'horaires'          => ['nullable', 'array'],
            'horaires.*.ouvert' => ['nullable', 'boolean'],
            'horaires.*.debut'  => ['nullable', 'string', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'horaires.*.fin'    => ['nullable', 'string', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],

            // Champs spécifiques admin
            'statut'     => ['required', 'in:actif,en_attente,suspendu'],
            'user_id'    => ['nullable', 'exists:users,id'],
            'en_vedette' => ['nullable', 'boolean'],
        ], [
            'nom.min'          => 'Le nom doit comporter au moins 2 caractères.',
            'description.min'  => 'La description doit comporter au moins 30 caractères.',
            'description.max'  => 'La description ne peut pas dépasser 2 000 caractères.',
            'email.email'      => "L'adresse email n'est pas valide.",
            'site_web.url'     => 'Le site web doit être une URL valide (ex : https://monsite.com).',
            'photo_principale.mimes' => 'Format accepté : JPG, PNG ou WebP.',
            'photos.*.mimes'         => 'Formats acceptés : JPG, PNG ou WebP.',
            'services.max'     => 'Maximum 8 services.',
            'statut.in'        => "Le statut choisi n'est pas valide.",
        ]);

        $this->validateHorairesConsistency($request);

        // Nettoyage
        $validated['nom']             = trim($validated['nom']);
        $validated['description']     = trim($validated['description']);
        $validated['adresse']         = trim($validated['adresse']);
        $validated['fourchette_prix'] = filled($validated['fourchette_prix'] ?? null)
            ? trim($validated['fourchette_prix']) : null;

        $validated['telephone'] = $this->normalizeBeninPhone($validated['telephone'] ?? null);
        $validated['whatsapp']  = $this->normalizeBeninPhone($validated['whatsapp'] ?? null);

        $validated['email']    = filled($validated['email'] ?? null)
            ? strtolower(trim($validated['email'])) : null;
        $validated['site_web'] = filled($validated['site_web'] ?? null)
            ? trim($validated['site_web']) : null;

        // Checkbox en_vedette : présente = 1, absente = 0
        $validated['en_vedette'] = $request->boolean('en_vedette');

        return $validated;
    }

    private function validateHorairesConsistency(Request $request): void
    {
        $horaires = $request->input('horaires', []);

        foreach (self::JOURS as $jour) {
            $data   = $horaires[$jour] ?? [];
            $ouvert = !empty($data['ouvert']);
            $debut  = $data['debut'] ?? null;
            $fin    = $data['fin']   ?? null;

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
            $fin   = $data['fin']   ?? '18:00';

            $result[$jour] = "{$debut} – {$fin}";
        }

        return $result;
    }

    private function storeServices(Request $request, Etablissement $etablissement): void
    {
        $services = collect($request->input('services', []))
            ->map(fn ($s) => trim((string) $s))
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

    private function storePhotos(Request $request, Etablissement $etablissement): void
    {
        if (!$request->hasFile('photos')) {
            return;
        }

        // Aucune limite de nombre de photos
        $ordre  = $etablissement->photos()->max('ordre') ?? 0;

        foreach ($request->file('photos', []) as $file) {
            $path = $file->store('etablissements/galerie', 'public');

            Photo::create([
                'etablissement_id' => $etablissement->id,
                'url'              => $path,
                'ordre'            => ++$ordre,
            ]);
        }
    }

    // ── Helpers téléphone béninois ────────────────────────────────────────

    private function beninPhoneRules(bool $required = false, string $label = 'Le numéro'): array
    {
        return [
            $required ? 'required' : 'nullable',
            'string',
            'max:20',
            function (string $attribute, mixed $value, Closure $fail) use ($required, $label) {
                $val = trim((string) ($value ?? ''));

                if ($val === '') {
                    if ($required) {
                        $fail("{$label} est obligatoire.");
                    }
                    return;
                }

                if (!$required && $this->isEffectivelyEmptyBeninPhone($val)) {
                    return;
                }

                if (!$this->isValidBeninPhone($val)) {
                    $fail("{$label} doit être au format béninois : 01 00 00 00 00.");
                }
            },
        ];
    }

    private function isEffectivelyEmptyBeninPhone(?string $value): bool
    {
        if ($value === null) {
            return true;
        }

        $digits = preg_replace('/\D+/', '', $value);

        return $digits === '' || $digits === self::LOCAL_PREFIX;
    }

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
