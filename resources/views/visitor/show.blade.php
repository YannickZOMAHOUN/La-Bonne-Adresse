@extends('layouts.app')

@section('title', $etablissement->nom . ' — Bonnes Adresses Bénin')
@section('description', Str::limit($etablissement->description, 150))

@section('content')

<div class="fiche-page">

    {{-- ══ EN-TÊTE FICHE ══════════════════════════════════ --}}
    <div class="fiche-hero" style="@if($etablissement->photo_principale) background-image: url('{{ $etablissement->photo_url }}') @endif">
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
                    <h2>Photos</h2>
                    <div class="galerie-grid">
                        @foreach($etablissement->photos as $photo)
                            <div class="galerie-item">
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
