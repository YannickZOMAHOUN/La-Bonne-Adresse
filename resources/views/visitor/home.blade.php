@extends('layouts.app')

@section('title', 'Bonnes Adresses Bénin — Restaurants, Hôtels, Appartements')

@section('content')

{{-- ══ HERO ══════════════════════════════════════════════ --}}
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

        {{-- Formulaire de recherche --}}
        <form class="search-box" action="{{ route('adresses.liste') }}" method="GET">
            <div class="search-field">
                <label>📍 Ville</label>
                <select name="ville">
                    <option value="">Toutes les villes</option>
                    @foreach($villes as $ville)
                        <option value="{{ $ville->slug }}">{{ $ville->emoji }} {{ $ville->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="search-field">
                <label>🗂️ Catégorie</label>
                <select name="categorie">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}">{{ $cat->emoji }} {{ $cat->nom }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-search">🔍 Rechercher</button>
        </form>

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
                <div class="stat-num">100%</div>
                <div class="stat-label">Vérifiées</div>
            </div>
        </div>
    </div>
</section>

{{-- ══ VILLES ═════════════════════════════════════════════ --}}
<section class="section">
    <div class="section-inner">
        <div class="section-tag">📍 Explorer par ville</div>
        <h2 class="section-title">Choisissez votre <em>destination</em></h2>
        <p class="section-desc">Trois grandes villes du Bénin, des adresses soigneusement sélectionnées.</p>

        <div class="villes-grid">
            @foreach($villes as $ville)
            <a href="{{ route('adresses.liste', ['ville' => $ville->slug]) }}" class="ville-card ville-{{ $ville->slug }}">
                <div class="ville-card-bg">{{ $ville->emoji }}</div>
                <div class="ville-card-content">
                    <div class="ville-name">{{ $ville->nom }}</div>
                    <div class="ville-count">
                        {{ $ville->etablissementsActifs()->count() }} adresse(s)
                    </div>
                </div>
                <div class="ville-arrow">→</div>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ══ CATEGORIES ═════════════════════════════════════════ --}}
<section class="cats-section">
    <div class="section-inner">
        <div class="section-tag" style="color: var(--gold2)">🗂️ Par catégorie</div>
        <h2 class="section-title" style="color:#fff">Que cherchez-<em>vous</em> ?</h2>
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

{{-- ══ EN VEDETTE ══════════════════════════════════════════ --}}
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

        <div style="text-align:center; margin-top:2.5rem">
            <a href="{{ route('adresses.liste') }}" class="btn-primary">
                Voir toutes les adresses →
            </a>
        </div>
    </div>
</section>
@endif

{{-- ══ CTA PROPRIÉTAIRE ════════════════════════════════════ --}}
<section class="cta-section">
    <div class="cta-pattern"></div>
    <div class="cta-inner">
        <h2 class="cta-title">Vous avez un établissement ?<br><em>Rejoignez-nous !</em></h2>
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
