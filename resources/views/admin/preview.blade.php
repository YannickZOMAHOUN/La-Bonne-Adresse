@extends('layouts.app')

@section('title', '[Prévisualisation] ' . $etablissement->nom . ' — Admin')

@push('styles')
<style>
/* ══ BARRE ADMIN FLOTTANTE ═══════════════════════════════ */
.preview-bar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 99999;
    background: #1a1a2e;
    border-bottom: 3px solid #f59e0b;
    padding: 0.75rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.75rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.4);
}
.preview-bar-left {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}
.preview-badge {
    background: #f59e0b;
    color: #1a1a2e;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.3rem 0.8rem;
    border-radius: 50px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}
.preview-info {
    color: rgba(255,255,255,0.85);
    font-size: 0.9rem;
}
.preview-info strong {
    color: #fff;
}
.preview-info small {
    color: rgba(255,255,255,0.5);
    margin-left: 0.4rem;
}
.preview-bar-right {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    flex-wrap: wrap;
}
.preview-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1.2rem;
    border-radius: 8px;
    font-size: 0.88rem;
    font-weight: 600;
    cursor: pointer;
    border: none;
    text-decoration: none;
    transition: all 0.2s;
    white-space: nowrap;
}
.preview-btn--back {
    background: rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.8);
    border: 1px solid rgba(255,255,255,0.15);
}
.preview-btn--back:hover {
    background: rgba(255,255,255,0.2);
    color: #fff;
}
.preview-btn--valider {
    background: #16a34a;
    color: #fff;
}
.preview-btn--valider:hover { background: #15803d; }
.preview-btn--suspendre {
    background: #dc2626;
    color: #fff;
}
.preview-btn--suspendre:hover { background: #b91c1c; }
.preview-btn--vedette {
    background: #d97706;
    color: #fff;
}
.preview-btn--vedette:hover { background: #b45309; }

/* Décale le contenu sous la barre */
body { padding-top: 60px !important; }
.navbar { top: 57px !important; position: sticky !important; }

/* Bandeau statut en haut de la fiche */
.preview-statut-banner {
    background: #fef3c7;
    border-bottom: 2px solid #f59e0b;
    padding: 0.6rem 1.5rem;
    font-size: 0.88rem;
    color: #92400e;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.preview-statut-banner.actif     { background: #dcfce7; border-color: #16a34a; color: #14532d; }
.preview-statut-banner.suspendu  { background: #fee2e2; border-color: #dc2626; color: #7f1d1d; }

@media (max-width: 600px) {
    .preview-bar { padding: 0.6rem 1rem; }
    .preview-info small { display: none; }
    .preview-btn { padding: 0.45rem 0.9rem; font-size: 0.82rem; }
}
</style>
@endpush

@section('content')

{{-- ══ BARRE ADMIN FLOTTANTE ════════════════════════════════ --}}
<div class="preview-bar">
    <div class="preview-bar-left">
        <span class="preview-badge">👁️ Prévisualisation</span>
        <div class="preview-info">
            <strong>{{ $etablissement->nom }}</strong>
            <small>par {{ $etablissement->user->nom }} · {{ $etablissement->user->email }}</small>
        </div>
    </div>

    <div class="preview-bar-right">
        {{-- Retour --}}
        <a href="{{ url()->previous() }}" class="preview-btn preview-btn--back">
            ← Retour
        </a>

        {{-- Valider --}}
        @if($etablissement->statut !== 'actif')
        <form method="POST" action="{{ route('admin.valider', $etablissement) }}" style="display:inline">
            @csrf
            <button type="submit" class="preview-btn preview-btn--valider"
                    onclick="return confirm('Valider et publier « {{ $etablissement->nom }} » ?')">
                ✅ Valider & publier
            </button>
        </form>
        @endif

        {{-- Suspendre --}}
        @if($etablissement->statut !== 'suspendu')
        <form method="POST" action="{{ route('admin.suspendre', $etablissement) }}" style="display:inline">
            @csrf
            <button type="submit" class="preview-btn preview-btn--suspendre"
                    onclick="return confirm('Refuser/suspendre « {{ $etablissement->nom }} » ?')">
                🚫 Refuser
            </button>
        </form>
        @endif

        {{-- Vedette --}}
        <form method="POST" action="{{ route('admin.vedette', $etablissement) }}" style="display:inline">
            @csrf
            <button type="submit" class="preview-btn preview-btn--vedette">
                {{ $etablissement->en_vedette ? '⭐ Retirer vedette' : '☆ Mettre en vedette' }}
            </button>
        </form>
    </div>
</div>

{{-- ══ BANDEAU STATUT ACTUEL ════════════════════════════════ --}}
<div class="preview-statut-banner {{ $etablissement->statut }}">
    @if($etablissement->statut === 'en_attente')
        ⏳ Cette fiche est <strong>en attente de validation</strong> — non visible par les visiteurs
    @elseif($etablissement->statut === 'actif')
        ✅ Cette fiche est <strong>déjà publiée</strong> et visible publiquement
    @else
        🚫 Cette fiche est <strong>suspendue</strong> — non visible par les visiteurs
    @endif
    &nbsp;·&nbsp; Soumise le {{ $etablissement->created_at->format('d/m/Y à H:i') }}
    &nbsp;·&nbsp; Propriétaire : {{ $etablissement->user->nom }} ({{ $etablissement->user->telephone ?? 'pas de téléphone' }})
</div>

{{-- ══ FICHE COMPLÈTE (identique à visitor/show) ════════════ --}}
<div class="fiche-page">

    {{-- EN-TÊTE --}}
    <div class="fiche-hero{{ $etablissement->photo_principale ? ' fiche-hero--clickable' : '' }}"
         style="@if($etablissement->photo_principale) background-image: url('{{ $etablissement->photo_url }}') @endif"
         @if($etablissement->photo_principale)
             id="heroImage"
             data-url="{{ $etablissement->photo_url }}"
             data-legend="{{ $etablissement->nom }}"
         @endif>
        <div class="fiche-hero-overlay"></div>
        <div class="fiche-hero-content">
            <div class="fiche-breadcrumb">
                <span>Accueil</span> /
                <span>{{ $etablissement->ville->nom }}</span> /
                <span>{{ $etablissement->categorie->nom }}</span>
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

    {{-- CORPS --}}
    <div class="fiche-body">
        <div class="fiche-inner">

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

                {{-- Galerie --}}
                @if($etablissement->photos->isNotEmpty())
                <div class="fiche-section">
                    <h2>Photos</h2>
                    <div class="galerie-grid" id="galerieGrid">
                        @foreach($etablissement->photos as $photo)
                            <div class="galerie-item"
                                 data-url="{{ asset('storage/' . $photo->url) }}"
                                 data-legend="{{ $photo->legende ?? $etablissement->nom }}"
                                 data-index="{{ $loop->index }}"
                                 role="button" tabindex="0">
                                <img src="{{ asset('storage/' . $photo->url) }}"
                                     alt="{{ $photo->legende ?? $etablissement->nom }}"
                                     loading="lazy"/>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Horaires --}}
                @if($etablissement->horaires)
                <div class="fiche-section">
                    <h2>Horaires</h2>
                    @foreach($etablissement->horaires as $jour => $heure)
                        <div class="horaire-row">
                            <span>{{ $jour }}</span>
                            <span>{{ $heure }}</span>
                        </div>
                    @endforeach
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

            {{-- Sidebar contact --}}
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

                    <div class="fiche-meta">
                        <p>📍 {{ $etablissement->ville->nom }}</p>
                        <p>🗂️ {{ $etablissement->categorie->nom }}</p>
                        <p style="color:var(--muted);font-size:0.82rem;margin-top:0.5rem">
                            👤 {{ $etablissement->user->nom }}<br>
                            📧 {{ $etablissement->user->email }}
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Suggestions --}}
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
    const photos = [];
    const hero = document.getElementById('heroImage');
    if (hero) photos.push({ url: hero.dataset.url, legend: hero.dataset.legend });
    document.querySelectorAll('#galerieGrid .galerie-item').forEach(el => {
        photos.push({ url: el.dataset.url, legend: el.dataset.legend });
    });
    if (photos.length === 0) return;

    // Lightbox simple (réutilise les styles de show.blade.php)
    const overlay  = document.createElement('div');
    overlay.id     = 'lightbox';
    overlay.className = 'lightbox-overlay';
    overlay.innerHTML = `
        <button class="lightbox-close" id="lightboxClose">✕</button>
        <button class="lightbox-btn lightbox-btn--prev" id="lightboxPrev">‹</button>
        <div class="lightbox-inner"><img class="lightbox-img" id="lightboxImg" src="" alt=""/></div>
        <button class="lightbox-btn lightbox-btn--next" id="lightboxNext">›</button>
        <div class="lightbox-counter" id="lightboxCounter"></div>
    `;
    document.body.appendChild(overlay);

    let current = 0;

    function show(i) {
        current = (i + photos.length) % photos.length;
        document.getElementById('lightboxImg').src = photos[current].url;
        document.getElementById('lightboxCounter').textContent = photos.length > 1 ? `${current + 1} / ${photos.length}` : '';
        document.getElementById('lightboxPrev').style.display = photos.length > 1 ? 'flex' : 'none';
        document.getElementById('lightboxNext').style.display = photos.length > 1 ? 'flex' : 'none';
    }

    function open(i) { show(i); overlay.classList.add('open'); document.body.style.overflow = 'hidden'; }
    function close()  { overlay.classList.remove('open'); document.body.style.overflow = ''; }

    if (hero) { hero.addEventListener('click', () => open(0)); }
    document.querySelectorAll('#galerieGrid .galerie-item').forEach((el, i) => {
        el.addEventListener('click', () => open(hero ? i + 1 : i));
    });

    document.getElementById('lightboxClose').addEventListener('click', close);
    document.getElementById('lightboxPrev').addEventListener('click', () => show(current - 1));
    document.getElementById('lightboxNext').addEventListener('click', () => show(current + 1));
    overlay.addEventListener('click', e => { if (e.target === overlay) close(); });
    document.addEventListener('keydown', e => {
        if (!overlay.classList.contains('open')) return;
        if (e.key === 'Escape') close();
        if (e.key === 'ArrowLeft') show(current - 1);
        if (e.key === 'ArrowRight') show(current + 1);
    });

    // Styles lightbox inline si pas déjà chargés
    if (!document.getElementById('lightbox-styles')) {
        const s = document.createElement('style');
        s.id = 'lightbox-styles';
        s.textContent = `
            .lightbox-overlay{display:none;position:fixed;inset:0;z-index:99998;background:rgba(0,0,0,0.92);align-items:center;justify-content:center;padding:1rem}
            .lightbox-overlay.open{display:flex}
            .lightbox-img{max-width:92vw;max-height:85vh;border-radius:12px;box-shadow:0 20px 60px rgba(0,0,0,0.5);object-fit:contain}
            .lightbox-close{position:fixed;top:70px;right:1rem;width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.2);color:#fff;font-size:1.3rem;cursor:pointer;display:flex;align-items:center;justify-content:center}
            .lightbox-btn{position:fixed;top:50%;transform:translateY(-50%);width:48px;height:48px;border-radius:50%;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);color:#fff;font-size:1.3rem;cursor:pointer;display:flex;align-items:center;justify-content:center}
            .lightbox-btn--prev{left:1rem}.lightbox-btn--next{right:1rem}
            .lightbox-counter{position:fixed;bottom:1.2rem;left:50%;transform:translateX(-50%);color:rgba(255,255,255,0.7);font-size:0.85rem}
            .galerie-item{cursor:pointer;overflow:hidden;border-radius:10px}
            .galerie-item img{width:100%;height:140px;object-fit:cover;transition:transform 0.3s}
            .galerie-item:hover img{transform:scale(1.05)}
        `;
        document.head.appendChild(s);
    }
})();
</script>
@endpush
