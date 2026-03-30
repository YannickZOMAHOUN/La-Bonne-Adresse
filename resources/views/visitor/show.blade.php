@extends('layouts.app')

@section('title', $etablissement->nom . ' — Bonnes Adresses Bénin')
@section('description', Str::limit($etablissement->description, 150))

{{-- ══ OPEN GRAPH — Fiche établissement ════════════════════ --}}
@section('og_type', 'business.business')
@section('og_title', $etablissement->nom . ' · ' . $etablissement->categorie->nom . ' à ' . $etablissement->ville->nom)
@section('og_description',
    ($etablissement->categorie->emoji ?? '📍') . ' ' .
    $etablissement->categorie->nom . ' à ' . $etablissement->ville->nom .
    ($etablissement->fourchette_prix ? ' · ' . $etablissement->fourchette_prix : '') .
    ' — ' . Str::limit($etablissement->description, 120)
)
@section('og_image', $etablissement->photo_principale
    ? asset('storage/' . $etablissement->photo_principale)
    : asset('images/og-default.jpg')
)
@section('og_image_alt', $etablissement->nom . ' — ' . $etablissement->ville->nom)
@section('canonical', route('adresses.show', $etablissement->slug))

@push('styles')
<style>
/* ══ LIGHTBOX ════════════════════════════════════════════ */
.lightbox-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(0, 0, 0, 0.92);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    align-items: center;
    justify-content: center;
    padding: 1rem;
    touch-action: none;
}
.lightbox-overlay.open {
    display: flex;
}
.lightbox-inner {
    position: relative;
    max-width: 92vw;
    max-height: 88vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
.lightbox-img {
    max-width: 92vw;
    max-height: 85vh;
    width: auto;
    height: auto;
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    object-fit: contain;
    display: block;
    transition: opacity 0.2s ease;
}
.lightbox-close {
    position: fixed;
    top: 1rem;
    right: 1rem;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.2);
    color: #fff;
    font-size: 1.3rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
    z-index: 10000;
}
.lightbox-close:hover { background: rgba(255,255,255,0.3); }
.lightbox-btn {
    position: fixed;
    top: 50%;
    transform: translateY(-50%);
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.25);
    color: #fff;
    font-size: 1.3rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
    z-index: 10000;
}
.lightbox-btn:hover { background: rgba(255,255,255,0.3); }
.lightbox-btn--prev { left: 1rem; }
.lightbox-btn--next { right: 1rem; }
.lightbox-counter {
    position: fixed;
    bottom: 1.2rem;
    left: 50%;
    transform: translateX(-50%);
    color: rgba(255,255,255,0.7);
    font-size: 0.85rem;
    letter-spacing: 0.04em;
}
.lightbox-legend {
    position: fixed;
    bottom: 3rem;
    left: 50%;
    transform: translateX(-50%);
    color: rgba(255,255,255,0.85);
    font-size: 0.9rem;
    text-align: center;
    max-width: 80vw;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* ══ GALERIE CLIQUABLE ══════════════════════════════════ */
.galerie-item {
    cursor: pointer;
    position: relative;
    overflow: hidden;
    border-radius: 10px;
}
.galerie-item img {
    width: 100%;
    height: 140px;
    object-fit: cover;
    transition: transform 0.3s ease, filter 0.3s ease;
    display: block;
}
.galerie-item:hover img {
    transform: scale(1.05);
    filter: brightness(0.85);
}
.galerie-item::after {
    content: '🔍';
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    opacity: 0;
    transition: opacity 0.2s;
    background: rgba(0,0,0,0.2);
}
.galerie-item:hover::after {
    opacity: 1;
}

/* ══ HERO PHOTO PRINCIPALE CLIQUABLE ════════════════════ */
.fiche-hero--clickable {
    cursor: zoom-in;
}

/* ══ RESPONSIVE AMÉLIORÉ ════════════════════════════════ */
@media (max-width: 900px) {
    .fiche-inner {
        grid-template-columns: 1fr !important;
    }
    .fiche-sidebar {
        order: -1;
    }
}
@media (max-width: 600px) {
    .fiche-hero {
        height: 260px;
    }
    .fiche-hero-content {
        bottom: 1.2rem;
        left: 1rem;
        right: 1rem;
    }
    .fiche-title {
        font-size: 1.5rem !important;
    }
    .fiche-body {
        padding: 1.5rem 1rem;
    }
    .galerie-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 0.5rem;
    }
    .galerie-item img {
        height: 120px;
    }
    .lightbox-btn--prev { left: 0.4rem; }
    .lightbox-btn--next { right: 0.4rem; }
    .contact-card {
        position: static !important;
    }
}
@media (max-width: 380px) {
    .fiche-badges {
        flex-wrap: wrap;
        gap: 0.4rem;
    }
    .galerie-grid {
        grid-template-columns: 1fr 1fr !important;
    }
}
</style>
@endpush

@section('content')

{{-- ══ LIGHTBOX ════════════════════════════════════════════ --}}
<div class="lightbox-overlay" id="lightbox" role="dialog" aria-modal="true" aria-label="Visionneuse de photos">
    <button class="lightbox-close" id="lightboxClose" aria-label="Fermer">✕</button>
    <button class="lightbox-btn lightbox-btn--prev" id="lightboxPrev" aria-label="Photo précédente">‹</button>
    <div class="lightbox-inner">
        <img class="lightbox-img" id="lightboxImg" src="" alt=""/>
    </div>
    <button class="lightbox-btn lightbox-btn--next" id="lightboxNext" aria-label="Photo suivante">›</button>
    <div class="lightbox-legend" id="lightboxLegend"></div>
    <div class="lightbox-counter" id="lightboxCounter"></div>
</div>

<div class="fiche-page">

    {{-- ══ EN-TÊTE FICHE ══════════════════════════════════ --}}
    <div class="fiche-hero{{ $etablissement->photo_principale ? ' fiche-hero--clickable' : '' }}"
         style="@if($etablissement->photo_principale) background-image: url('{{ $etablissement->photo_url }}') @endif"
         @if($etablissement->photo_principale)
             id="heroImage"
             data-url="{{ $etablissement->photo_url }}"
             data-legend="{{ $etablissement->nom }}"
             role="button"
             tabindex="0"
             aria-label="Voir la photo principale en grand"
         @endif>
        <div class="fiche-hero-overlay"></div>
        <div class="fiche-hero-content">
            <div class="fiche-breadcrumb">
                <a href="{{ route('home') }}">Accueil</a> /
                <a href="{{ route('adresses.liste', ['ville' => $etablissement->ville->slug]) }}">{{ $etablissement->ville->nom }}</a> /
                <a href="{{ route('adresses.liste', ['categorie' => $etablissement->categorie->slug]) }}">{{ $etablissement->categorie->nom }}</a>
            </div>
            <div class="fiche-badges">
                <span class="fiche-badge">{{ $etablissement->categorie->emoji }} {{ $etablissement->categorie->nom }}</span>
                <span class="fiche-badge">📍 {{ $etablissement->ville->nom }}</span>
                @if($etablissement->en_vedette)
                    <span class="fiche-badge fiche-badge--gold">⭐ Recommandé</span>
                @endif
            </div>
            <h1 class="fiche-title">{{ $etablissement->nom }}</h1>
            @if($etablissement->fourchette_prix)
                <div class="fiche-prix">💰 {{ $etablissement->fourchette_prix }}</div>
            @endif
        </div>
    </div>

    {{-- ══ CORPS DE LA FICHE ════════════════════════════ --}}
    <div class="fiche-body">
        <div class="fiche-inner">

            {{-- Colonne principale --}}
            <div class="fiche-main">

                {{-- Description --}}
                <div class="fiche-section">
                    <h2>À propos</h2>
                    <p class="fiche-description">{{ $etablissement->description }}</p>
                </div>

                {{-- Services --}}
                @if($etablissement->services->isNotEmpty())
                <div class="fiche-section">
                    <h2>Services proposés</h2>
                    <div class="services-grid">
                        @foreach($etablissement->services as $service)
                            <div class="service-item">
                                <span>{{ $service->emoji }}</span>
                                {{ $service->libelle }}
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Galerie photos --}}
                @if($etablissement->photos->isNotEmpty())
                <div class="fiche-section">
                    <h2>Photos <span style="font-size:0.8rem;font-weight:400;color:var(--muted)">(appuyez pour agrandir)</span></h2>
                    <div class="galerie-grid" id="galerieGrid">
                        @foreach($etablissement->photos as $photo)
                            <div class="galerie-item"
                                 data-url="{{ asset('storage/' . $photo->url) }}"
                                 data-legend="{{ $photo->legende ?? $etablissement->nom }}"
                                 data-index="{{ $loop->index }}"
                                 role="button"
                                 tabindex="0"
                                 aria-label="Voir la photo {{ $loop->iteration }}">
                                <img src="{{ asset('storage/' . $photo->url) }}"
                                     alt="{{ $photo->legende ?? $etablissement->nom }}"
                                     loading="lazy"/>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Localisation --}}
                @if($etablissement->latitude && $etablissement->longitude)
                <div class="fiche-section">
                    <h2>Localisation</h2>
                    <div class="map-container">
                        <iframe
                            src="https://maps.google.com/maps?q={{ $etablissement->latitude }},{{ $etablissement->longitude }}&z=15&output=embed"
                            width="100%" height="300" style="border:0; border-radius:12px"
                            allowfullscreen loading="lazy">
                        </iframe>
                    </div>
                    <p class="fiche-adresse">📍 {{ $etablissement->adresse }}</p>
                </div>
                @else
                <div class="fiche-section">
                    <h2>Adresse</h2>
                    <p class="fiche-adresse">📍 {{ $etablissement->adresse }}</p>
                </div>
                @endif

            </div>

            {{-- Colonne contact (sidebar) --}}
            <div class="fiche-sidebar">
                <div class="contact-card">
                    <h3>Contacter</h3>

                    @if($etablissement->whatsapp)
                        <a href="{{ $etablissement->whatsapp_link }}" target="_blank" class="btn-contact btn-whatsapp">
                            📱 Contacter sur WhatsApp
                        </a>
                    @endif

                    @if($etablissement->telephone)
                        <a href="tel:{{ $etablissement->telephone }}" class="btn-contact btn-tel">
                            📞 {{ $etablissement->telephone }}
                        </a>
                    @endif

                    @if($etablissement->email)
                        <a href="mailto:{{ $etablissement->email }}" class="btn-contact btn-email">
                            ✉️ {{ $etablissement->email }}
                        </a>
                    @endif

                    @if($etablissement->site_web)
                        <a href="{{ $etablissement->site_web }}" target="_blank" class="btn-contact btn-web">
                            🌐 Site web
                        </a>
                    @endif

                    @if($etablissement->horaires)
                        <div class="horaires">
                            <h4>⏰ Horaires</h4>
                            @foreach($etablissement->horaires as $jour => $heure)
                                <div class="horaire-row">
                                    <span>{{ $jour }}</span>
                                    <span>{{ $heure }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="fiche-meta">
                        <p>📍 {{ $etablissement->ville->nom }}</p>
                        <p>🗂️ {{ $etablissement->categorie->nom }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ══ SUGGESTIONS ════════════════════════════════════ --}}
    @if($suggestions->isNotEmpty())
    <section class="section suggestions-section">
        <div class="section-inner">
            <h2 class="section-title">Autres adresses à <em>{{ $etablissement->ville->nom }}</em></h2>
            <div class="cards-grid">
                @foreach($suggestions as $etab)
                    @include('visitor.partials.card', ['etab' => $etab])
                @endforeach
            </div>
        </div>
    </section>
    @endif

</div>
@endsection

@push('scripts')
<script>
(function () {
    // ── Collecte toutes les images (photo principale + galerie) ──
    const photos = [];

    // Photo héro (principale)
    const hero = document.getElementById('heroImage');
    if (hero) {
        photos.push({ url: hero.dataset.url, legend: hero.dataset.legend });
    }

    // Photos de galerie
    document.querySelectorAll('#galerieGrid .galerie-item').forEach(el => {
        photos.push({ url: el.dataset.url, legend: el.dataset.legend });
    });

    if (photos.length === 0) return;

    // ── Éléments lightbox ──
    const overlay   = document.getElementById('lightbox');
    const img       = document.getElementById('lightboxImg');
    const legend    = document.getElementById('lightboxLegend');
    const counter   = document.getElementById('lightboxCounter');
    const btnClose  = document.getElementById('lightboxClose');
    const btnPrev   = document.getElementById('lightboxPrev');
    const btnNext   = document.getElementById('lightboxNext');

    let current = 0;

    function show(index) {
        current = (index + photos.length) % photos.length;
        img.src = photos[current].url;
        img.alt = photos[current].legend;
        legend.textContent = photos[current].legend || '';
        counter.textContent = photos.length > 1 ? `${current + 1} / ${photos.length}` : '';
        // Affiche/cache les boutons nav si 1 seule photo
        btnPrev.style.display = photos.length > 1 ? 'flex' : 'none';
        btnNext.style.display = photos.length > 1 ? 'flex' : 'none';
    }

    function open(index) {
        show(index);
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
        btnClose.focus();
    }

    function close() {
        overlay.classList.remove('open');
        document.body.style.overflow = '';
        img.src = '';
    }

    // ── Clics ouverture ──
    if (hero) {
        hero.addEventListener('click', () => open(0));
        hero.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') open(0); });
    }

    document.querySelectorAll('#galerieGrid .galerie-item').forEach((el, i) => {
        // Si photo principale existe, les galeries commencent à l'index 1
        const offset = hero ? 1 : 0;
        el.addEventListener('click', () => open(i + offset));
        el.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') open(i + offset); });
    });

    // ── Navigation ──
    btnClose.addEventListener('click', close);
    btnPrev.addEventListener('click',  () => show(current - 1));
    btnNext.addEventListener('click',  () => show(current + 1));

    // Clic en dehors de l'image
    overlay.addEventListener('click', e => { if (e.target === overlay) close(); });

    // Clavier
    document.addEventListener('keydown', e => {
        if (!overlay.classList.contains('open')) return;
        if (e.key === 'Escape')     close();
        if (e.key === 'ArrowLeft')  show(current - 1);
        if (e.key === 'ArrowRight') show(current + 1);
    });

    // ── Swipe tactile (mobile) ──
    let touchStartX = null;
    overlay.addEventListener('touchstart', e => {
        touchStartX = e.touches[0].clientX;
    }, { passive: true });
    overlay.addEventListener('touchend', e => {
        if (touchStartX === null) return;
        const diff = touchStartX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) {
            diff > 0 ? show(current + 1) : show(current - 1);
        }
        touchStartX = null;
    }, { passive: true });

    // ── PWA : enregistrement du Service Worker ──
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .catch(err => console.warn('SW non enregistré :', err));
        });
    }
})();
</script>
@endpush
