<?php

namespace App\Http\Controllers\Proprietaire;

use App\Http\Controllers\Controller;
use App\Models\Ville;
use App\Models\Categorie;
use App\Models\Etablissement;
use App\Models\Service;
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
            'nom'            => 'required|string|max:150',
            'description'    => 'required|string|min:30',
            'ville_id'       => 'required|exists:villes,id',
            'categorie_id'   => 'required|exists:categories,id',
            'adresse'        => 'required|string|max:255',
            'telephone'      => 'nullable|string|max:20',
            'whatsapp'       => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:150',
            'fourchette_prix'=> 'nullable|string|max:100',
            'photo_principale' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'services'       => 'nullable|array',
            'services.*'     => 'string|max:50',
        ]);

        // Upload photo principale
        if ($request->hasFile('photo_principale')) {
            $validated['photo_principale'] = $request->file('photo_principale')
                ->store('etablissements', 'public');
        }

        $validated['user_id'] = auth()->id();
        $validated['statut']  = 'en_attente'; // toujours en attente à la création

        $etablissement = Etablissement::create($validated);

        // Enregistrer les services
        if ($request->filled('services')) {
            foreach ($request->services as $service) {
                if (trim($service)) {
                    Service::create([
                        'etablissement_id' => $etablissement->id,
                        'libelle'          => trim($service),
                    ]);
                }
            }
        }

        return redirect()->route('proprietaire.dashboard')
            ->with('success', 'Votre établissement a été soumis. Il sera visible après validation.');
    }

    /**
     * Formulaire de modification
     */
    public function edit(Etablissement $etablissement)
    {
        // Vérifier que c'est bien son établissement
        abort_if($etablissement->user_id !== auth()->id(), 403);

        $villes     = Ville::where('active', true)->get();
        $categories = Categorie::where('active', true)->get();

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
            'services'         => 'nullable|array',
            'services.*'       => 'string|max:50',
        ]);

        // Nouvelle photo
        if ($request->hasFile('photo_principale')) {
            if ($etablissement->photo_principale) {
                Storage::disk('public')->delete($etablissement->photo_principale);
            }
            $validated['photo_principale'] = $request->file('photo_principale')
                ->store('etablissements', 'public');
        }

        // Repasse en attente si modification majeure
        $validated['statut'] = 'en_attente';

        $etablissement->update($validated);

        // Mettre à jour les services
        $etablissement->services()->delete();
        if ($request->filled('services')) {
            foreach ($request->services as $service) {
                if (trim($service)) {
                    Service::create([
                        'etablissement_id' => $etablissement->id,
                        'libelle'          => trim($service),
                    ]);
                }
            }
        }

        return redirect()->route('proprietaire.dashboard')
            ->with('success', 'Modifications enregistrées. Votre fiche sera revalidée.');
    }
}
