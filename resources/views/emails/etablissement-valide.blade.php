@extends('emails.layout', [
    'headerIcon'  => '🏪',
    'headerTitle' => 'Votre fiche<br><em>est en ligne !</em>',
])

@section('content')

<p class="greeting">
    Bonjour <strong>{{ $etablissement->user->nom }}</strong>,
</p>

<p class="message">
    Votre établissement a été examiné et validé par notre équipe.
    Il est désormais <strong>visible par tous les visiteurs</strong> de Bonnes Adresses Bénin !
</p>

<div class="success-box">
    ✅ <strong>« {{ $etablissement->nom }} »</strong> est maintenant en ligne et accessible
    à tous les visiteurs de Cotonou, Bohicon et Parakou.
</div>

<div class="info-box">
    <div class="info-box-title">📋 Détails de votre fiche</div>
    <div class="info-row">
        <span class="info-label">Établissement</span>
        <span class="info-value">{{ $etablissement->nom }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Catégorie</span>
        <span class="info-value">{{ $etablissement->categorie->emoji }} {{ $etablissement->categorie->nom }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Ville</span>
        <span class="info-value">{{ $etablissement->ville->emoji }} {{ $etablissement->ville->nom }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Adresse</span>
        <span class="info-value">{{ $etablissement->adresse }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Statut</span>
        <span class="info-value" style="color:#16a34a">✅ Actif et visible</span>
    </div>
</div>

<div class="cta-wrap">
    <a href="{{ $ficheUrl }}" class="cta-btn">
        👁 Voir ma fiche en ligne
    </a>
</div>

<p class="message" style="font-size:0.88rem; color:#6b7280">
    Vous pouvez à tout moment modifier votre fiche depuis votre espace propriétaire
    pour mettre à jour vos informations, ajouter des photos ou modifier vos services.
</p>

@endsection
