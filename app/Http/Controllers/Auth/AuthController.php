<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        User::create([
            'nom'       => $validated['nom'],
            'email'     => $validated['email'],
            'telephone' => $validated['telephone'],
            'password'  => Hash::make($validated['password']),
            'role'      => 'proprietaire',
            'statut'    => 'en_attente', // Validation manuelle par l'admin
        ]);

        return redirect()->route('login')
            ->with('success', 'Inscription réussie ! Votre compte sera activé sous 24h après vérification.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
