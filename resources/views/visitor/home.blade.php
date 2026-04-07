@extends('layouts.app')

@section('title', 'Bonnes Adresses Bénin — Restaurants, Hôtels, Appartements')
@section('og_title', 'Bonnes Adresses Bénin — Restaurants, Hôtels, Appartements')
@section('og_description', 'Trouvez les meilleures adresses au Bénin : restaurants, hôtels, appartements meublés à Cotonou, Bohicon et Parakou. Contacts directs, vérifiés.')
@section('og_image', asset('images/og-default.jpg'))
@section('canonical', route('home'))

@push('styles')
<style>
/* =====================================================
   HOME - STYLES SPÉCIFIQUES À LA PAGE D'ACCUEIL
   ===================================================== */

/* Hero Section */
.hero {
    position: relative;
    overflow: hidden;
    min-height: 90vh;
    display: flex;
    flex-direction: column;
    background: var(--dark);
}

.hero-stats {
    display: flex;
    gap: 2.5rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 2.5rem;
}

.stat {
    text-align: center;
}

.stat-num {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--gold2);
    font-family: 'Playfair Display', serif;
}

.stat-label {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.5);
    text-transform: uppercase;
    letter-spacing: 0.06em;
}

/* Villes Grid */
.villes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.ville-card {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    height: 240px;
    text-decoration: none;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: block;
}

.ville-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}

.ville-card-bg {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 5rem;
    transition: transform 0.5s ease;
}

.ville-card:hover .ville-card-bg {
    transform: scale(1.05);
}

.ville-cotonou .ville-card-bg { background: linear-gradient(135deg, #0f3d22, #1a6b3c); }
.ville-bohicon-abomey .ville-card-bg { background: linear-gradient(135deg, #2a1a06, #8b5c1a); }
.ville-parakou .ville-card-bg { background: linear-gradient(135deg, #0a1a2e, #1a3d6b); }

.ville-card-content {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 60%);
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 1.5rem;
}

.ville-name {
    font-family: 'Playfair Display', serif;
    font-size: 1.5rem;
    color: #fff;
    font-weight: 700;
}

.ville-count {
    font-size: 0.85rem;
    color: rgba(255,255,255,0.6);
    margin-top: 0.3rem;
}

.ville-arrow {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 32px;
    height: 32px;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(4px);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    transition: all 0.2s ease;
    font-size: 0.9rem;
}

.ville-card:hover .ville-arrow {
    background: var(--gold);
    transform: translateX(3px);
}

/* Catégories Section */
.cats-section {
    background: var(--dark);
    padding: 5rem 1.5rem;
}

.cats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.cat-card {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 20px;
    padding: 2rem 1.3rem;
    text-align: center;
    text-decoration: none;
    transition: all 0.3s ease;
    display: block;
}

.cat-card:hover {
    background: rgba(26,107,60,0.15);
    border-color: rgba(37,149,79,0.5);
    transform: translateY(-4px);
}

.cat-icon {
    font-size: 2.8rem;
    margin-bottom: 1rem;
    display: block;
}

.cat-name {
    font-family: 'Playfair Display', serif;
    font-size: 1.1rem;
    color: #fff;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.cat-desc {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.45);
    line-height: 1.5;
}

/* Cards Grid */
.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.8rem;
    margin-top: 1.5rem;
}

/* Section commune */
.section {
    padding: 5rem 1.5rem;
}

.section-inner {
    max-width: 1200px;
    margin: 0 auto;
}

/* CTA Section */
.cta-section {
    background: linear-gradient(135deg, var(--green) 0%, #0d4a28 100%);
    padding: 5rem 1.5rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.cta-pattern {
    position: absolute;
    inset: 0;
    opacity: 0.05;
    background-image: repeating-linear-gradient(45deg, #fff 0, #fff 1px, transparent 0, transparent 30px);
}

.cta-inner {
    max-width: 600px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.cta-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(1.8rem, 4vw, 2.8rem);
    color: #fff;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.cta-title em {
    color: var(--gold2);
    font-style: italic;
}

.cta-desc {
    color: rgba(255,255,255,0.8);
    font-size: 1rem;
    line-height: 1.7;
    margin-bottom: 2rem;
}

.btn-cta {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    background: linear-gradient(135deg, var(--gold), var(--gold2));
    color: var(--dark);
    font-weight: 700;
    padding: 0.9rem 2.2rem;
    border-radius: 60px;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 1rem;
}

.btn-cta:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(200,146,42,0.4);
}

/* =====================================================
   RESPONSIVE HOME
   ===================================================== */
@media (max-width: 768px) {
    .hero {
        min-height: auto;
    }

    .hero-stats {
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .stat-num {
        font-size: 1.5rem;
    }

    .stat-label {
        font-size: 0.7rem;
    }

    .villes-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .ville-card {
        height: 180px;
    }

    .ville-name {
        font-size: 1.3rem;
    }

    .cats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .cat-card {
        padding: 1.3rem 0.9rem;
    }

    .cat-icon {
        font-size: 2rem;
    }

    .cat-name {
        font-size: 0.9rem;
    }

    .cat-desc {
        display: none;
    }

    .cards-grid {
        grid-template-columns: 1fr;
        gap: 1.2rem;
    }

    .section {
        padding: 3rem 1rem;
    }

    .cats-section {
        padding: 3rem 1rem;
    }

    .cta-section {
        padding: 3rem 1rem;
    }

    .btn-cta {
        width: 100%;
        justify-content: center;
        padding: 0.8rem 1.5rem;
        font-size: 0.95rem;
    }
}

@media (max-width: 480px) {
    .hero-stats {
        gap: 1rem;
    }

    .stat-num {
        font-size: 1.3rem;
    }

    .ville-card {
        height: 160px;
    }

    .cats-grid {
        gap: 0.8rem;
    }

    .cat-card {
        padding: 1rem 0.7rem;
    }

    .cat-icon {
        font-size: 1.6rem;
        margin-bottom: 0.5rem;
    }
}
</style>
@endpush

@section('content')

{{-- HERO SECTION --}}
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-pattern"></div>

    <div class="hero-content">
        <div class="hero-badge">
            <span>🇧🇯</span> Cotonou · Bohicon · Parakou
        </div>

        <h1 class="hero-title">
            Découvrez les<br><em>meilleures adresses</em><br>du Bénin
        </h1>
        <p class="hero-sub">
            Restaurants, hôtels, appartements meublés —
            trouvez les bonnes adresses vérifiées avec contacts directs.
        </p>

        {{-- Formulaire de recherche optimisé --}}
        <form class="search-box" action="{{ route('adresses.liste') }}" method="GET">
            <div class="search-field">
                <label>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    Ville
                </label>
                <div class="sf-select-wrap">
                    <select name="ville">
                        <option value="">Toutes les villes</option>
                        @foreach($villes as $ville)
                            <option value="{{ $ville->slug }}">{{ $ville->emoji }} {{ $ville->nom }}</option>
                        @endforeach
                    </select>
                    <span class="sf-arrow">▼</span>
                </div>
            </div>

            <div class="search-field">
                <label>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <line x1="3" y1="9" x2="21" y2="9"/>
                        <line x1="3" y1="15" x2="21" y2="15"/>
                        <line x1="9" y1="21" x2="9" y2="9"/>
                    </svg>
                    Catégorie
                </label>
                <div class="sf-select-wrap">
                    <select name="categorie">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}">{{ $cat->emoji }} {{ $cat->nom }}</option>
                        @endforeach
                    </select>
                    <span class="sf-arrow">▼</span>
                </div>
            </div>

            <button type="submit" class="btn-search">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <span>Rechercher</span>
            </button>
        </form>

        {{-- Stats --}}
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

{{-- VILLES SECTION --}}
<section class="section" style="background: var(--cream)">
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

{{-- CATÉGORIES SECTION --}}
<section class="cats-section">
    <div class="section-inner">
        <div class="section-tag" style="color: var(--gold2)">🗂️ Par catégorie</div>
        <h2 class="section-title" style="color: #fff">Que cherchez-<em>vous</em>&nbsp;?</h2>
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

{{-- EN VEDETTE SECTION --}}
@if($enVedette->isNotEmpty())
<section class="section" style="background: #f4f0e8">
    <div class="section-inner">
        <div class="section-tag">✨ Adresses populaires</div>
        <h2 class="section-title">Les coups de <em>cœur</em></h2>
        <p class="section-desc">Une sélection d'établissements recommandés par notre communauté.</p>

        <div class="cards-grid">
            @foreach($enVedette as $etab)
                @include('visitor.partials.card', ['etab' => $etab])
            @endforeach
        </div>

        <div style="text-align: center; margin-top: 2.5rem">
            <a href="{{ route('adresses.liste') }}" class="btn-primary">
                Voir toutes les adresses →
            </a>
        </div>
    </div>
</section>
@endif

{{-- CTA PROPRIÉTAIRE --}}
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
