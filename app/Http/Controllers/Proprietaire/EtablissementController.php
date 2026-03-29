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

class EtablissementController extends Controller
{
    /**
     * Dashboard propriétaire
     */
    public function dashboard()
    {
        $etablissements = auth()->user()->etablissements()
            ->with(['ville', 'categorie'])
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

        return view('proprietaire.create', compact('villes', 'categories'));
    }

    /**
     * Enregistrer un nouvel établissement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'              => 'required|string|max:150',
            'description'      => 'required|string|min:30',
            'ville_id'         => 'required|exists:villes,id',
            'categorie_id'     => 'required|exists:categories,id',
            'adresse'          => 'required|string|max:255',
            'telephone'        => 'nullable|string|max:20',
            'whatsapp'         => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:150',
            'fourchette_prix'  => 'nullable|string|max:100',
            'photo_principale' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'photos'           => 'nullable|array|max:6',
            'photos.*'         => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'services'         => 'nullable|array',
            'services.*'       => 'nullable|string|max:50',
        ]);

        // Upload photo principale
        if ($request->hasFile('photo_principale')) {
            $validated['photo_principale'] = $request->file('photo_principale')
                ->store('etablissements', 'public');
        }

        $validated['user_id'] = auth()->id();
        $validated['statut']  = 'en_attente';

        $etablissement = Etablissement::create($validated);

        // Enregistrer les services (filtrer les vides)
        $this->storeServices($request, $etablissement);

        // Enregistrer les photos supplémentaires
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

        $etablissement->load(['services', 'photos']);

        return view('proprietaire.edit', compact('etablissement', 'villes', 'categories'));
    }

    /**
     * Mettre à jour un établissement
     */
    public function update(Request $request, Etablissement $etablissement)
    {
        abort_if($etablissement->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'nom'              => 'required|string|max:150',
            'description'      => 'required|string|min:30',
            'ville_id'         => 'required|exists:villes,id',
            'categorie_id'     => 'required|exists:categories,id',
            'adresse'          => 'required|string|max:255',
            'telephone'        => 'nullable|string|max:20',
            'whatsapp'         => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:150',
            'fourchette_prix'  => 'nullable|string|max:100',
            'photo_principale' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'photos'           => 'nullable|array|max:6',
            'photos.*'         => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'services'         => 'nullable|array',
            'services.*'       => 'nullable|string|max:50',
        ]);

        // Nouvelle photo principale
        if ($request->hasFile('photo_principale')) {
            if ($etablissement->photo_principale) {
                Storage::disk('public')->delete($etablissement->photo_principale);
            }
            $validated['photo_principale'] = $request->file('photo_principale')
                ->store('etablissements', 'public');
        }

        // Repasse en attente après modification
        $validated['statut'] = 'en_attente';

        $etablissement->update($validated);

        // Mettre à jour les services
        $etablissement->services()->delete();
        $this->storeServices($request, $etablissement);

        // Ajouter les nouvelles photos
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
