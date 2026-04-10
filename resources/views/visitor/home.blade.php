@extends('layouts.app')

@section('title', 'Bonnes Adresses Bénin — Restaurants, Hôtels, Appartements')

@section('og_type',        'website')
@section('og_title',       'Bonnes Adresses Bénin — Restaurants, Hôtels, Appartements')
@section('og_description', 'Trouvez les meilleures adresses au Bénin : restaurants, hôtels, appartements meublés à Cotonou, Bohicon et Parakou. Contacts directs, vérifiés.')
@section('og_image',       asset('images/og-default.jpg'))
@section('og_image_alt',   'Bonnes Adresses Bénin — Votre guide des meilleures adresses')
@section('canonical',      route('home'))

@push('styles')
<style>
/* ═══════════════════════════════════════════════
   HOME — styles spécifiques uniquement
   (les règles globales .search-box etc. sont
   dans app.css — aucun doublon ici)
   ═══════════════════════════════════════════════ */

/* ── HERO wrapper ────────────────────────────── */
.hero {
    position: relative;
    overflow: hidden;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background: var(--dark);
}

/* ── Stats hero ──────────────────────────────── */
.hero-stats {
    display: flex;
    gap: 2.5rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 2.5rem;
}
.stat { text-align: center; }

/* ── Grille villes ───────────────────────────── */
.villes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 1.2rem;
    margin-top: 2rem;
}
.ville-card {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    height: 220px;
    text-decoration: none;
    transition: transform .3s, box-shadow .3s;
}
.ville-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 18px 40px rgba(0,0,0,.18);
}
.ville-cotonou        .ville-card-bg { background: linear-gradient(135deg,#0f3d22,#1a6b3c); }
.ville-bohicon-abomey .ville-card-bg { background: linear-gradient(135deg,#2a1a06,#8b5c1a); }
.ville-parakou        .ville-card-bg { background: linear-gradient(135deg,#0a1a2e,#1a3d6b); }
.ville-card-bg {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 4.5rem;
}
.ville-card-content {
    position: absolute; inset: 0;
    background: linear-gradient(to top,rgba(0,0,0,.7) 0%,transparent 55%);
    display: flex; flex-direction: column; justify-content: flex-end;
    padding: 1.3rem;
}
.ville-name  { font-family:'Playfair Display',serif; font-size:1.4rem; color:#fff; font-weight:700; }
.ville-count { font-size:.8rem; color:rgba(255,255,255,.55); margin-top:.2rem; }
.ville-arrow {
    position: absolute; top:1rem; right:1rem;
    width:30px; height:30px;
    background: rgba(255,255,255,.15); border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    color:#fff; transition:background .2s;
}
.ville-card:hover .ville-arrow { background: var(--gold); }

/* ── Catégories ──────────────────────────────── */
.cats-section { background:var(--dark); padding:4.5rem 1.5rem; }
.cats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px,1fr));
    gap: 1.1rem;
    margin-top: 2rem;
}
.cat-card {
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 14px;
    padding: 1.8rem 1.3rem;
    text-align: center;
    text-decoration: none;
    transition: background .3s, border-color .3s, transform .3s;
    display: block;
}
.cat-card:hover {
    background: rgba(26,107,60,.2);
    border-color: rgba(37,149,79,.4);
    transform: translateY(-3px);
}
.cat-icon  { font-size:2.3rem; margin-bottom:.8rem; display:block; }
.cat-name  { font-family:'Playfair Display',serif; font-size:1.05rem; color:#fff; margin-bottom:.3rem; }
.cat-desc  { font-size:.8rem; color:rgba(255,255,255,.4); line-height:1.5; }

/* ── Cards établissements ────────────────────── */
.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill,minmax(290px,1fr));
    gap: 1.4rem;
    margin-top: 1.5rem;
}

/* ── Section commune ─────────────────────────── */
.section { padding: 4.5rem 1.5rem; }
.section-inner { max-width:1100px; margin:0 auto; }

/* ── CTA ─────────────────────────────────────── */
.cta-inner { max-width:580px; margin:0 auto; position:relative; z-index:1; }

/* ══ RESPONSIVE HOME ════════════════════════════ */
@media (max-width: 768px) {
    .hero-stats    { gap:1.2rem; margin-top:1.8rem; }
    .stat-num      { font-size:1.4rem; }

    .villes-grid   { grid-template-columns:1fr; gap:1rem; }
    .ville-card    { height:180px; }

    .cats-grid     { grid-template-columns:repeat(2,1fr); gap:.8rem; }
    .cat-card      { padding:1.2rem .9rem; }
    .cat-icon      { font-size:1.8rem; }
    .cat-name      { font-size:.9rem; }
    .cat-desc      { display:none; }

    .cards-grid    { grid-template-columns:1fr; gap:1rem; }

    .section       { padding:3rem 1.2rem; }
    .cats-section  { padding:3rem 1.2rem; }

    .cta-section   { padding:3.5rem 1.2rem; }
    .btn-cta       { width:100%; justify-content:center; font-size:.92rem; }
}

@media (max-width: 480px) {
    .cats-grid     { grid-template-columns:1fr 1fr; }
    .villes-grid   { grid-template-columns:1fr; }
    .hero-stats    { gap:1rem; }
    .ville-card    { height:160px; }
}
/* Fix search-box desktop alignment */
@media (min-width: 641px) {
    .search-box {
        flex-wrap: nowrap;
        max-width: 720px;
    }

    .search-field {
        flex: 1 1 0;
        min-width: 0;
    }

    .btn-search {
        flex-shrink: 0;
        white-space: nowrap;
    }
}
</style>
@endpush

@section('content')

{{-- ══ HERO ══════════════════════════════════════════════════════ --}}
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-pattern"></div>

    <div class="hero-content">
        <div class="hero-badge">🇧🇯 Cotonou · Bohicon · Parakou</div>

        <h1 class="hero-title">
            Découvrez les<br><em>meilleures adresses</em><br>du Bénin
        </h1>
        <p class="hero-sub">
            Restaurants, hôtels, appartements meublés —
            trouvez les bonnes adresses vérifiées avec contacts directs.
        </p>

        {{-- ── Formulaire de recherche ── --}}
        <form class="search-box" action="{{ route('adresses.liste') }}" method="GET">

            {{-- Ville --}}
            <div class="search-field">
                <label for="sf-ville">
                    <span class="sf-icon" aria-hidden="true">📍</span> Ville
                </label>
                <div class="sf-select-wrap">
                    <select id="sf-ville" name="ville">
                        <option value="">Toutes les villes</option>
                        @foreach($villes as $ville)
                            <option value="{{ $ville->slug }}">{{ $ville->emoji }} {{ $ville->nom }}</option>
                        @endforeach
                    </select>
                    <span class="sf-arrow" aria-hidden="true">
                        <svg viewBox="0 0 12 8" width="12" height="8" fill="none">
                            <path d="M1 1l5 5 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </div>
            </div>

            {{-- Catégorie --}}
            <div class="search-field">
                <label for="sf-categorie">
                    <span class="sf-icon" aria-hidden="true">🗂️</span> Catégorie
                </label>
                <div class="sf-select-wrap">
                    <select id="sf-categorie" name="categorie">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}">{{ $cat->emoji }} {{ $cat->nom }}</option>
                        @endforeach
                    </select>
                    <span class="sf-arrow" aria-hidden="true">
                        <svg viewBox="0 0 12 8" width="12" height="8" fill="none">
                            <path d="M1 1l5 5 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </div>
            </div>

            {{-- Bouton --}}
            <button type="submit" class="btn-search">
                <svg class="btn-search-icon" viewBox="0 0 20 20" width="18" height="18" fill="none" aria-hidden="true">
                    <circle cx="8.5" cy="8.5" r="5.5" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M13 13l4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                <span>Rechercher</span>
            </button>
        </form>

        {{-- ── Stats ── --}}
        <div class="hero-stats">
            <div class="stat">
                <div class="stat-num">{{ $villes->count() }}</div>
                <div class="stat-label">Villes</div>
            </div>
            <div class="stat">
                <div class="stat-num">{{ $categories->count() }}</div>
                <div class="stat-label">Catégories</div>
            </div>
            <div class="stat">
                <div class="stat-num">100&nbsp;%</div>
                <div class="stat-label">Vérifiées</div>
            </div>
        </div>
    </div>
</section>

{{-- ══ VILLES ══════════════════════════════════════════════════════ --}}
<section class="section" style="background:var(--cream)">
    <div class="section-inner">
        <div class="section-tag">📍 Explorer par ville</div>
        <h2 class="section-title">Choisissez votre <em>destination</em></h2>
        <p class="section-desc">Trois grandes villes du Bénin, des adresses soigneusement sélectionnées.</p>

        <div class="villes-grid">
            @foreach($villes as $ville)
            <a href="{{ route('adresses.liste', ['ville' => $ville->slug]) }}"
               class="ville-card ville-{{ $ville->slug }}">
                <div class="ville-card-bg">{{ $ville->emoji }}</div>
                <div class="ville-card-content">
                    <div class="ville-name">{{ $ville->nom }}</div>
                    <div class="ville-count">{{ $ville->etablissementsActifs()->count() }} adresse(s)</div>
                </div>
                <div class="ville-arrow">→</div>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ══ CATÉGORIES ══════════════════════════════════════════════════ --}}
<section class="cats-section">
    <div class="section-inner">
        <div class="section-tag" style="color:var(--gold2)">🗂️ Par catégorie</div>
        <h2 class="section-title" style="color:#fff">Que cherchez-<em>vous</em>&nbsp;?</h2>
        <div class="cats-grid">
            @foreach($categories as $cat)
            <a href="{{ route('adresses.liste', ['categorie' => $cat->slug]) }}" class="cat-card">
                <span class="cat-icon">{{ $cat->emoji }}</span>
                <div class="cat-name">{{ $cat->nom }}</div>
                <div class="cat-desc">{{ $cat->description }}</div>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ══ EN VEDETTE ══════════════════════════════════════════════════ --}}
@if($enVedette->isNotEmpty())
<section class="section" style="background:#f4f0e8">
    <div class="section-inner">
        <div class="section-tag">✨ Adresses populaires</div>
        <h2 class="section-title">Les coups de <em>cœur</em></h2>
        <p class="section-desc">Une sélection d'établissements recommandés.</p>

        <div class="cards-grid">
            @foreach($enVedette as $etab)
                @include('visitor.partials.card', ['etab' => $etab])
            @endforeach
        </div>

        <div style="text-align:center;margin-top:2.5rem">
            <a href="{{ route('adresses.liste') }}" class="btn-primary">
                Voir toutes les adresses →
            </a>
        </div>
    </div>
</section>
@endif

{{-- ══ CTA PROPRIÉTAIRE ════════════════════════════════════════════ --}}
<section class="cta-section">
    <div class="cta-pattern"></div>
    <div class="cta-inner">
        <h2 class="cta-title">Vous avez un établissement&nbsp;?<br><em>Rejoignez-nous&nbsp;!</em></h2>
        <p class="cta-desc">
            Inscrivez votre restaurant, hôtel ou appartement et soyez visible
            auprès de milliers de visiteurs au Bénin.
        </p>
        <a href="{{ route('register') }}" class="btn-cta">
            ✨ Inscrire mon établissement gratuitement
        </a>
    </div>
</section>

@endsection
