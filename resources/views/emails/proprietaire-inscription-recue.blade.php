@extends('emails.layout', [
    'headerIcon'  => '📬',
    'headerTitle' => 'Inscription<br><em>bien reçue !</em>',
])

@section('content')

<p class="greeting">
    Bonjour <strong>{{ $user->nom }}</strong>,
</p>

<p class="message">
    Nous avons bien reçu votre demande d'inscription sur <strong>Bonnes Adresses Bénin</strong>.
    Votre dossier est en cours d'examen par notre équipe.
</p>

<div class="success-box">
    ✅ Votre inscription a été enregistrée avec succès. Vous recevrez un mail de confirmation
    dès que votre compte sera activé, généralement <strong>sous 24 heures</strong>.
</div>

<div class="info-box">
    <div class="info-box-title">📋 Récapitulatif de votre inscription</div>
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
        <span class="info-label">Statut</span>
        <span class="info-value" style="color:#d97706">⏳ En attente de validation</span>
    </div>
</div>

<p class="message">
    Une fois votre compte activé, vous pourrez vous connecter et créer la fiche
    de votre établissement pour être visible par des milliers de visiteurs au Bénin.
</p>

@endsection
