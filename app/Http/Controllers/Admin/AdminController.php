<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\EtablissementValide;
use App\Mail\ProprietaireCompteActive;
use App\Models\Etablissement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Dashboard admin avec statistiques
     */
    public function dashboard()
    {
        $stats = [
            'total_etablissements'  => Etablissement::count(),
            'en_attente'            => Etablissement::where('statut', 'en_attente')->count(),
            'actifs'                => Etablissement::where('statut', 'actif')->count(),
            'proprietaires'         => User::where('role', 'proprietaire')->count(),
            'proprietaires_actifs'  => User::where('role', 'proprietaire')->where('statut', 'actif')->count(),

            // ══ VISITEURS ══════════════════════════════════════════
            'visiteurs_aujourd_hui' => DB::table('page_views')
                ->whereDate('created_at', today())
                ->distinct('ip')
                ->count('ip'),

            'visiteurs_ce_mois'     => DB::table('page_views')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->distinct('ip')
                ->count('ip'),

            'page_views_total'      => DB::table('page_views')->count(),
        ];

        $enAttente = Etablissement::where('statut', 'en_attente')
            ->with(['ville', 'categorie', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'enAttente'));
    }

    /**
     * Liste de tous les établissements
     */
    public function etablissements()
    {
        $etablissements = Etablissement::with(['ville', 'categorie', 'user'])
            ->latest()
            ->paginate(20);

        return view('admin.etablissements', compact('etablissements'));
    }

    /**
     * Prévisualisation d'une fiche avant validation
     */
    public function preview(Etablissement $etablissement)
    {
        $etablissement->load(['ville', 'categorie', 'services', 'photos', 'user']);

        $suggestions = Etablissement::actif()
            ->where('ville_id', $etablissement->ville_id)
            ->where('categorie_id', $etablissement->categorie_id)
            ->where('id', '!=', $etablissement->id)
            ->take(3)
            ->get();

        return view('admin.preview', compact('etablissement', 'suggestions'));
    }

    /**
     * Valider un établissement → visible publiquement
     */
    public function valider(Etablissement $etablissement)
    {
        $etablissement->update(['statut' => 'actif']);
        $etablissement->load(['user', 'ville', 'categorie']);

        try {
            Mail::to($etablissement->user->email)
                ->queue(new EtablissementValide($etablissement));
        } catch (\Exception $e) {
            Log::error("Erreur envoi mail validation établissement : " . $e->getMessage());
        }

        return back()->with('success', "« {$etablissement->nom} » est maintenant visible. Le mail de notification a été envoyé.");
    }

    /**
     * Suspendre un établissement
     */
    public function suspendre(Etablissement $etablissement)
    {
        $etablissement->update(['statut' => 'suspendu']);

        return back()->with('info', "« {$etablissement->nom} » a été suspendu.");
    }

    /**
     * Supprimer un établissement (interdit s'il est en vedette)
     */
    public function supprimerEtablissement(Etablissement $etablissement)
    {
        if ($etablissement->en_vedette) {
            return back()->with('error', "Impossible de supprimer « {$etablissement->nom} » : il est en vedette. Retirez-le d'abord.");
        }

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

    /**
     * Mettre en vedette / retirer de la vedette
     */
    public function toggleVedette(Etablissement $etablissement)
    {
        $etablissement->update(['en_vedette' => !$etablissement->en_vedette]);
        $msg = $etablissement->en_vedette ? 'mis en vedette' : 'retiré de la vedette';

        return back()->with('success', "« {$etablissement->nom} » a été {$msg}.");
    }

    /**
     * Liste des propriétaires
     */
    public function proprietaires()
    {
        $proprietaires = User::where('role', 'proprietaire')
            ->withCount('etablissements')
            ->latest()
            ->paginate(20);

        return view('admin.proprietaires', compact('proprietaires'));
    }

    /**
     * Activer un compte propriétaire
     */
    public function activerProprietaire(User $user)
    {
        $user->update(['statut' => 'actif']);

        try {
            Mail::to($user->email)
                ->queue(new ProprietaireCompteActive($user));
        } catch (\Exception $e) {
            Log::error("Erreur envoi mail activation propriétaire : " . $e->getMessage());
        }

        return back()->with('success', "Le compte de {$user->nom} a été activé. Le mail d'accueil a été envoyé.");
    }

    /**
     * Suspendre un propriétaire
     */
    public function suspendrePropietaire(User $user)
    {
        $user->update(['statut' => 'suspendu']);

        return back()->with('info', "Le compte de {$user->nom} a été suspendu.");
    }

    /**
     * Supprimer un propriétaire et tous ses établissements
     */
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
}
