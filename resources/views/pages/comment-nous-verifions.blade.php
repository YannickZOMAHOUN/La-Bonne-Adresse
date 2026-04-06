@extends('layouts.app')

@section('title', 'Comment nous vérifions nos adresses — Bonnes Adresses Bénin')
@section('description', 'Découvrez comment Bonnes Adresses Bénin vérifie les établissements, actualise les informations pratiques et améliore la confiance des utilisateurs.')
@section('canonical', route('verification.methodologie'))
@section('og_title', 'Comment nous vérifions nos adresses — Bonnes Adresses Bénin')
@section('og_description', 'Notre méthode de vérification : contrôle des contacts, de la localisation, des horaires et de l’expérience sur place.')

@section('content')
<section class="section" style="background:linear-gradient(180deg,#0f1720 0%, #173425 100%); color:#fff; padding-top:7rem; padding-bottom:4rem;">
    <div class="section-inner" style="max-width:900px;">
        <div class="section-tag" style="color:#f0d78c; border-color:rgba(240,215,140,.35);">✅ Transparence & confiance</div>
        <h1 class="section-title" style="color:#fff; margin-top:1rem;">Comment nous <em>vérifions</em> les bonnes adresses</h1>
        <p class="section-desc" style="color:rgba(255,255,255,.78); max-width:760px;">
            Notre objectif est simple : publier des fiches utiles, crédibles et faciles à contacter. Quand une fiche affiche une date de vérification,
            cela signifie que les informations clés ont été contrôlées manuellement par notre équipe ou reconfirmées avec l’établissement.
        </p>
    </div>
</section>

<section class="section" style="background:var(--cream)">
    <div class="section-inner" style="max-width:980px;">
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:1rem; margin-bottom:2rem;">
            <div style="background:#fff; border:1px solid var(--border); border-radius:16px; padding:1.2rem;">
                <strong style="display:block; margin-bottom:.45rem;">1. Contact confirmé</strong>
                <span style="color:var(--muted); line-height:1.6;">Nous vérifions au minimum un canal joignable : téléphone, WhatsApp, email ou site web.</span>
            </div>
            <div style="background:#fff; border:1px solid var(--border); border-radius:16px; padding:1.2rem;">
                <strong style="display:block; margin-bottom:.45rem;">2. Localisation contrôlée</strong>
                <span style="color:var(--muted); line-height:1.6;">Nous vérifions l’adresse, le quartier et, quand disponible, le lien Google Maps.</span>
            </div>
            <div style="background:#fff; border:1px solid var(--border); border-radius:16px; padding:1.2rem;">
                <strong style="display:block; margin-bottom:.45rem;">3. Infos pratiques utiles</strong>
                <span style="color:var(--muted); line-height:1.6;">Horaires, fourchette de prix et moyens de paiement sont ajoutés pour faciliter la décision.</span>
            </div>
            <div style="background:#fff; border:1px solid var(--border); border-radius:16px; padding:1.2rem;">
                <strong style="display:block; margin-bottom:.45rem;">4. Mise à jour régulière</strong>
                <span style="color:var(--muted); line-height:1.6;">Les fiches peuvent être révisées au fil du temps. La date visible aide à juger la fraîcheur des informations.</span>
            </div>
        </div>

        <div style="background:#fff; border:1px solid var(--border); border-radius:18px; padding:1.5rem; margin-bottom:1.5rem;">
            <h2 style="margin-bottom:1rem;">Ce que signifie la mention <em>“Vérifié le…”</em></h2>
            <ul style="margin:0; padding-left:1.2rem; color:var(--muted); line-height:1.8;">
                <li>Les coordonnées affichées ont été confirmées.</li>
                <li>L’adresse ou le quartier ont été revus.</li>
                <li>Les informations clés visibles sur la fiche ont été contrôlées.</li>
                <li>La fiche a été jugée suffisamment fiable pour être mise en avant auprès des visiteurs.</li>
            </ul>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:1rem;">
            <div style="background:#fff; border:1px solid var(--border); border-radius:18px; padding:1.5rem;">
                <h3 style="margin-bottom:.8rem;">Pour les visiteurs</h3>
                <p style="color:var(--muted); line-height:1.7; margin:0;">Vous gagnez du temps avec des fiches plus fiables, plus complètes et plus actionnables : appeler, écrire sur WhatsApp ou ouvrir l’itinéraire en un clic.</p>
            </div>
            <div style="background:#fff; border:1px solid var(--border); border-radius:18px; padding:1.5rem;">
                <h3 style="margin-bottom:.8rem;">Pour les propriétaires</h3>
                <p style="color:var(--muted); line-height:1.7; margin:0;">Une fiche complète et vérifiée inspire davantage confiance, améliore la conversion et génère plus de prises de contact qualifiées.</p>
            </div>
        </div>

        <div style="background:#0f1720; color:#fff; border-radius:18px; padding:1.6rem; margin-top:2rem; display:flex; flex-wrap:wrap; gap:1rem; align-items:center; justify-content:space-between;">
            <div>
                <h2 style="margin:0 0 .4rem; color:#fff;">Vous gérez un établissement ?</h2>
                <p style="margin:0; color:rgba(255,255,255,.72);">Ajoutez une fiche plus complète pour augmenter vos appels et messages WhatsApp.</p>
            </div>
            <div style="display:flex; gap:.8rem; flex-wrap:wrap;">
                <a href="{{ route('register') }}" class="btn-primary">Inscrire mon établissement</a>
                <a href="{{ route('adresses.liste') }}" class="btn-secondary" style="background:#fff;">Explorer les adresses</a>
            </div>
        </div>
    </div>
</section>
@endsection
