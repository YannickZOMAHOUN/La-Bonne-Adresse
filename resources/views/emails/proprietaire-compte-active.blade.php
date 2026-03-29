@extends('emails.layout', [
    'headerIcon'  => '✅',
    'headerTitle' => 'Votre compte<br><em>est activé !</em>',
])

@section('content')

<p class="greeting">
    Bonjour <strong>{{ $user->nom }}</strong>,
</p>

<p class="message">
    Excellente nouvelle ! Votre compte <strong>Bonnes Adresses Bénin</strong> a été
    validé et activé par notre équipe. Vous pouvez dès maintenant vous connecter
    et ajouter votre établissement.
</p>

<div class="success-box">
    🎉 Bienvenue sur Bonnes Adresses Bénin ! Votre compte propriétaire est
    maintenant <strong>actif</strong>.
</div>

<div class="info-box">
    <div class="info-box-title">🚀 Prochaines étapes</div>
    <div class="info-row">
        <span class="info-label">Étape 1</span>
        <span class="info-value">Connectez-vous avec votre email et mot de passe</span>
    </div>
    <div class="info-row">
        <span class="info-label">Étape 2</span>
        <span class="info-value">Créez la fiche de votre établissement</span>
    </div>
    <div class="info-row">
        <span class="info-label">Étape 3</span>
        <span class="info-value">Ajoutez vos photos et services</span>
    </div>
    <div class="info-row">
        <span class="info-label">Étape 4</span>
        <span class="info-value">Soyez visible par vos futurs clients !</span>
    </div>
</div>

<div class="cta-wrap">
    <a href="{{ $loginUrl }}" class="cta-btn">
        🔐 Se connecter maintenant
    </a>
</div>

@endsection
