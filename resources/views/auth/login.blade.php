@extends('layouts.app')
@section('title', 'Connexion — Bonnes Adresses Bénin')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon">🔐</div>
            <h1>Connexion</h1>
            <p>Accédez à votre espace propriétaire</p>
        </div>

        @if($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="auth-form">
            @csrf
            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       placeholder="votre@email.com" required autofocus/>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password"
                       placeholder="••••••••" required/>
            </div>
            <div class="form-check">
                <input type="checkbox" id="remember" name="remember"/>
                <label for="remember">Se souvenir de moi</label>
            </div>
            <button type="submit" class="btn-submit">Se connecter →</button>
        </form>

        <div class="auth-footer">
            Pas encore inscrit ?
            <a href="{{ route('register') }}">Inscrire mon établissement</a>
        </div>
    </div>
</div>
@endsection
