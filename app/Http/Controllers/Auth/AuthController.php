<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\AdminNouvelleInscription;
use App\Mail\ProprietaireInscriptionRecue;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    private const COUNTRY_CODE = '229';
    private const LOCAL_PREFIX = '01';

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials['email'] = strtolower(trim($credentials['email']));

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            if (!$user->isActif()) {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Votre compte est en attente de validation par l\'administrateur.',
                ])->onlyInput('email');
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
            'nom'       => ['required', 'string', 'min:2', 'max:100'],
            'email'     => ['required', 'email', 'max:150', 'unique:users,email'],
            'telephone' => $this->beninPhoneRules(required: true, label: 'Le numéro de téléphone'),
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'nom.required'      => 'Veuillez renseigner votre nom complet.',
            'nom.min'           => 'Le nom doit comporter au moins 2 caractères.',
            'email.required'    => 'Veuillez renseigner votre adresse email.',
            'email.email'       => 'L’adresse email n’est pas valide.',
            'email.unique'      => 'Cette adresse email est déjà utilisée.',
            'password.required' => 'Veuillez renseigner un mot de passe.',
            'password.min'      => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed'=> 'La confirmation du mot de passe ne correspond pas.',
        ]);

        $user = User::create([
            'nom'       => trim($validated['nom']),
            'email'     => strtolower(trim($validated['email'])),
            'telephone' => $this->normalizeBeninPhone($validated['telephone']),
            'password'  => Hash::make($validated['password']),
            'role'      => 'proprietaire',
            'statut'    => 'en_attente',
        ]);

        try {
            Mail::to(config('mail.from.address'))
                ->queue(new AdminNouvelleInscription($user));
        } catch (\Throwable $e) {
            Log::error('Mail admin inscription échoué', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }

        try {
            Mail::to($user->email)
                ->queue(new ProprietaireInscriptionRecue($user));
        } catch (\Throwable $e) {
            Log::error('Mail propriétaire inscription échoué', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
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

    private function beninPhoneRules(bool $required = false, string $label = 'Le numéro'): array
    {
        return [
            $required ? 'required' : 'nullable',
            'string',
            'max:20',
            function (string $attribute, mixed $value, Closure $fail) use ($label) {
                if ($value === null || trim((string) $value) === '') {
                    return;
                }

                if (!$this->isValidBeninPhone($value)) {
                    $fail("{$label} doit être au format béninois : 01 00 00 00 00.");
                }
            },
        ];
    }

    private function isValidBeninPhone(?string $value): bool
    {
        if ($value === null) {
            return false;
        }

        $digits = preg_replace('/\D+/', '', (string) $value);

        if (!$digits) {
            return false;
        }

        if (str_starts_with($digits, self::COUNTRY_CODE)) {
            $digits = substr($digits, 3);
        }

        if (strlen($digits) === 8) {
            $digits = self::LOCAL_PREFIX . $digits;
        }

        return strlen($digits) === 10 && str_starts_with($digits, self::LOCAL_PREFIX);
    }

    private function normalizeBeninPhone(?string $value): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', (string) $value);

        if (!$digits) {
            return null;
        }

        if (str_starts_with($digits, self::COUNTRY_CODE)) {
            $digits = substr($digits, 3);
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
