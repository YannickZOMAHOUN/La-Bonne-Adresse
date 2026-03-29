@extends('layouts.app')
@section('title', 'Inscrire mon établissement — Bonnes Adresses Bénin')

@section('content')
<div class="auth-page">
    <div class="auth-card auth-card--wide">
        <div class="auth-header">
            <div class="auth-icon">🏪</div>
            <h1>Inscrire mon établissement</h1>
            <p>Créez votre compte propriétaire. Activation sous 24h.</p>
        </div>

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="auth-form">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label for="nom">Nom complet *</label>
                    <input type="text" id="nom" name="nom"
                           value="{{ old('nom') }}"
                           placeholder="Jean Dupont" required/>
                </div>
                <div class="form-group">
                    <label for="telephone">Téléphone / WhatsApp *</label>
                    <input type="text" id="telephone" name="telephone"
                           value="{{ old('telephone') }}"
                           placeholder="+229 97 00 00 00" required/>
                </div>
            </div>
            <div class="form-group">
                <label for="email">Adresse email *</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       placeholder="votre@email.com" required/>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Mot de passe *</label>
                    <input type="password" id="password" name="password"
                           placeholder="Minimum 8 caractères" required/>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirmer le mot de passe *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           placeholder="Répétez le mot de passe" required/>
                </div>
            </div>

            <div class="auth-notice">
                ℹ️ Votre compte sera activé manuellement par l'administrateur dans un délai de 24h après vérification.
            </div>

            <button type="submit" class="btn-submit">Soumettre mon inscription →</button>
        </form>

        <div class="auth-footer">
            Déjà inscrit ? <a href="{{ route('login') }}">Se connecter</a>
        </div>
    </div>
</div>
@endsection
