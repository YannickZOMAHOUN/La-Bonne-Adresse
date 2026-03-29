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
     * Valider un établissement → visible publiquement
     */
    public function valider(Etablissement $etablissement)
    {
        $etablissement->update(['statut' => 'actif']);

        // ── Mail au propriétaire : fiche en ligne ─────────────────
        try {
            Mail::send(
                'emails.etablissement-valide',
                [
                    'etablissement' => $etablissement->load(['ville', 'categorie', 'user']),
                    'ficheUrl'      => route('adresses.show', $etablissement->slug),
                ],
                fn($m) => $m
                    ->to($etablissement->user->email)
                    ->subject("✅ Votre fiche « {$etablissement->nom} » est en ligne !")
            );
        } catch (\Exception $e) {
            \Log::error('Mail validation établissement échoué : ' . $e->getMessage());
        }

        return back()->with('success', "« {$etablissement->nom} » est maintenant visible. Un mail a été envoyé au propriétaire.");
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

        // ── Mail au propriétaire : compte activé ──────────────────
        try {
            Mail::send(
                'emails.proprietaire-compte-active',
                [
                    'user'     => $user,
                    'loginUrl' => route('login'),
                ],
                fn($m) => $m
                    ->to($user->email)
                    ->subject('✅ Votre compte est activé — Bonnes Adresses Bénin')
            );
        } catch (\Exception $e) {
            \Log::error('Mail activation propriétaire échoué : ' . $e->getMessage());
        }

        return back()->with('success', "Le compte de {$user->nom} a été activé. Un mail lui a été envoyé.");
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
