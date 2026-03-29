<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Redirection selon le rôle
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            // Propriétaire : vérifier le statut
            if (!$user->isActif()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Votre compte est en attente de validation par l\'administrateur.',
                ]);
            }

            return redirect()->route('proprietaire.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email ou mot de passe incorrect.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nom'       => 'required|string|max:100',
            'email'     => 'required|email|unique:users',
            'telephone' => 'required|string|max:20',
            'password'  => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'nom'       => $validated['nom'],
            'email'     => $validated['email'],
            'telephone' => $validated['telephone'],
            'password'  => Hash::make($validated['password']),
            'role'      => 'proprietaire',
            'statut'    => 'en_attente',
        ]);

        // ── Mail à l'admin : nouvelle inscription à valider ───────
        try {
            Mail::send(
                'emails.admin-nouvelle-inscription',
                [
                    'user'     => $user,
                    'adminUrl' => route('admin.proprietaires'),
                ],
                fn($m) => $m
                    ->to(config('mail.from.address'))
                    ->subject('🔔 Nouvelle inscription à valider — Bonnes Adresses Bénin')
            );
        } catch (\Exception $e) {
            // Ne pas bloquer l'inscription si le mail échoue
            \Log::error('Mail admin inscription échoué : ' . $e->getMessage());
        }

        // ── Mail au propriétaire : confirmation de réception ──────
        try {
            Mail::send(
                'emails.proprietaire-inscription-recue',
                ['user' => $user],
                fn($m) => $m
                    ->to($user->email)
                    ->subject('📬 Inscription reçue — Bonnes Adresses Bénin')
            );
        } catch (\Exception $e) {
            \Log::error('Mail proprio inscription échoué : ' . $e->getMessage());
        }

        return redirect()->route('login')
            ->with('success', 'Inscription réussie ! Vous recevrez un mail de confirmation sous 24h.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
