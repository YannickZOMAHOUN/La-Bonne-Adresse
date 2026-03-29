@extends('emails.layout', [
    'headerIcon'  => '🔔',
    'headerTitle' => 'Nouvelle inscription<br><em>à valider</em>',
])

@section('content')

<p class="greeting">
    Bonjour <strong>Administrateur</strong>,
</p>

<p class="message">
    Un nouveau propriétaire vient de s'inscrire sur <strong>Bonnes Adresses Bénin</strong>
    et attend la validation de son compte.
</p>

<div class="info-box">
    <div class="info-box-title">📋 Informations du propriétaire</div>
    <div class="info-row">
        <span class="info-label">Nom</span>
        <span class="info-value">{{ $user->nom }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Email</span>
        <span class="info-value">{{ $user->email }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Téléphone</span>
        <span class="info-value">{{ $user->telephone ?? 'Non renseigné' }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Inscrit le</span>
        <span class="info-value">{{ $user->created_at->format('d/m/Y à H:i') }}</span>
    </div>
</div>

<div class="alert-box">
    ⏳ Ce compte est actuellement <strong>en attente</strong>. Le propriétaire ne peut pas
    se connecter tant que vous n'avez pas validé son inscription.
</div>

<div class="cta-wrap">
    <a href="{{ $adminUrl }}" class="cta-btn cta-btn-gold">
        ⚙️ Valider l'inscription
    </a>
</div>

@endsection
