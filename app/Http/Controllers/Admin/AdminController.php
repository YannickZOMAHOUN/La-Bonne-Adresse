<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Etablissement;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * Dashboard admin avec statistiques
     */
    public function dashboard()
    {
        $stats = [
            'total_etablissements' => Etablissement::count(),
            'en_attente'           => Etablissement::where('statut', 'en_attente')->count(),
            'actifs'               => Etablissement::where('statut', 'actif')->count(),
            'proprietaires'        => User::where('role', 'proprietaire')->count(),
            'proprietaires_actifs' => User::where('role', 'proprietaire')->where('statut', 'actif')->count(),
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

        // Chargement des relations nécessaires pour l'email
        $etablissement->load(['user', 'ville', 'categorie']);

        // On utilise queue() au lieu de send() pour éviter le timeout de 30s
        try {
            Mail::queue(
                'emails.etablissement-valide',
                [
                    'etablissement' => $etablissement,
                    'ficheUrl'      => route('adresses.show', $etablissement->slug),
                ],
                function ($m) use ($etablissement) {
                    $m->to($etablissement->user->email)
                      ->subject("✅ Votre fiche « {$etablissement->nom} » est en ligne !");
                }
            );
        } catch (\Exception $e) {
            Log::error("Erreur mise en file d'attente mail validation : " . $e->getMessage());
        }

        return back()->with('success', "« {$etablissement->nom} » est maintenant visible. Le mail de notification a été mis en file d'attente.");
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

        // On utilise queue() pour une réponse instantanée de l'interface admin
        try {
            Mail::queue(
                'emails.proprietaire-compte-active',
                [
                    'user'     => $user,
                    'loginUrl' => route('login'),
                ],
                function ($m) use ($user) {
                    $m->to($user->email)
                      ->subject('✅ Votre compte est activé — Bonnes Adresses Bénin');
                }
            );
        } catch (\Exception $e) {
            Log::error("Erreur mise en file d'attente mail activation propriétaire : " . $e->getMessage());
        }

        return back()->with('success', "Le compte de {$user->nom} a été activé. Le mail d'accueil est en cours d'envoi.");
    }

    /**
     * Suspendre un propriétaire
     */
    public function suspendrePropietaire(User $user)
    {
        $user->update(['statut' => 'suspendu']);

        return back()->with('info', "Le compte de {$user->nom} a été suspendu.");
    }
}
