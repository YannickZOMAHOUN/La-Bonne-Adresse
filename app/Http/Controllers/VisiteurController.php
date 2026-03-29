<?php

namespace App\Http\Controllers;

use App\Models\Ville;
use App\Models\Categorie;
use App\Models\Etablissement;
use Illuminate\Http\Request;

class VisiteurController extends Controller
{
    /**
     * Page d'accueil
     */
    public function index()
    {
        $villes      = Ville::where('active', true)->get();
        $categories  = Categorie::where('active', true)->get();
        $enVedette   = Etablissement::enVedette()
                            ->with(['ville', 'categorie', 'services'])
                            ->latest()
                            ->take(6)
                            ->get();

        return view('visitor.home', compact('villes', 'categories', 'enVedette'));
    }

    /**
     * Page de résultats (recherche + liste)
     */
    public function liste(Request $request)
    {
        $villes     = Ville::where('active', true)->get();
        $categories = Categorie::where('active', true)->get();

        $query = Etablissement::actif()->with(['ville', 'categorie', 'services']);

        // Filtres
        if ($request->filled('ville')) {
            $query->whereHas('ville', fn($q) => $q->where('slug', $request->ville));
        }
        if ($request->filled('categorie')) {
            $query->whereHas('categorie', fn($q) => $q->where('slug', $request->categorie));
        }
        if ($request->filled('q')) {
            $query->where('nom', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%');
        }

        $etablissements = $query->latest()->paginate(12)->withQueryString();

        return view('visitor.liste', compact('etablissements', 'villes', 'categories'));
    }

    /**
     * Fiche détaillée d'un établissement
     */
    public function show(string $slug)
    {
        $etablissement = Etablissement::actif()
            ->with(['ville', 'categorie', 'services', 'photos'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Suggestions : même ville et même catégorie
        $suggestions = Etablissement::actif()
            ->where('ville_id', $etablissement->ville_id)
            ->where('categorie_id', $etablissement->categorie_id)
            ->where('id', '!=', $etablissement->id)
            ->take(3)
            ->get();

        return view('visitor.show', compact('etablissement', 'suggestions'));
    }
}
