@extends('layouts.app')

{{-- ══ TITRE DYNAMIQUE selon les filtres actifs ════════════ --}}
@php
    $villeActive     = $villes->firstWhere('slug', request('ville'));
    $categorieActive = $categories->firstWhere('slug', request('categorie'));
    $recherche       = request('q');

    // Titre & description adaptés aux filtres
    if ($villeActive && $categorieActive) {
        $ogTitle = ($categorieActive->emoji ?? '🗂️') . ' ' . $categorieActive->nom
                 . ' à ' . $villeActive->nom . ' — Bonnes Adresses Bénin';
        $ogDesc  = 'Trouvez les meilleurs ' . strtolower($categorieActive->nom)
                 . ' à ' . $villeActive->nom
                 . '. Contacts directs, adresses vérifiées sur Bonnes Adresses Bénin.';
    } elseif ($villeActive) {
        $ogTitle = '📍 Adresses à ' . $villeActive->nom . ' — Bonnes Adresses Bénin';
        $ogDesc  = 'Restaurants, hôtels, appartements et plus à ' . $villeActive->nom
                 . '. Découvrez les meilleures adresses vérifiées avec contacts directs.';
    } elseif ($categorieActive) {
        $ogTitle = ($categorieActive->emoji ?? '🗂️') . ' ' . $categorieActive->nom
                 . ' au Bénin — Bonnes Adresses Bénin';
        $ogDesc  = 'Trouvez les meilleurs ' . strtolower($categorieActive->nom)
                 . ' au Bénin à Cotonou, Bohicon et Parakou. Contacts directs vérifiés.';
    } elseif ($recherche) {
        $ogTitle = 'Résultats pour "' . $recherche . '" — Bonnes Adresses Bénin';
        $ogDesc  = $etablissements->total() . ' adresse(s) trouvée(s) pour "' . $recherche
                 . '" sur Bonnes Adresses Bénin.';
    } else {
        $ogTitle = 'Explorer les adresses au Bénin — Restaurants, Hôtels, Appartements';
        $ogDesc  = 'Parcourez toutes les meilleures adresses au Bénin : restaurants, hôtels, appartements meublés à Cotonou, Bohicon et Parakou.';
    }

    $pageTitle = $villeActive || $categorieActive || $recherche
        ? $ogTitle
        : 'Explorer les adresses — Bonnes Adresses Bénin';
@endphp

@section('title', $pageTitle)

{{-- ══ OPEN GRAPH — Page liste ══════════════════════════════ --}}
@section('og_type',        'website')
@section('og_title',       $ogTitle)
@section('og_description', $ogDesc)
@section('og_image',       asset('images/og-default.jpg'))
@section('og_image_alt',   'Explorer les adresses — Bonnes Adresses Bénin')
@section('canonical',      route('adresses.liste', array_filter([
    'ville'     => request('ville'),
    'categorie' => request('categorie'),
])))

@section('content')

<div class="liste-page">

    {{-- ══ FILTRES ══════════════════════════════════════════ --}}
    <div class="filtres-bar">
        <div class="filtres-inner">
            <form class="filtres-form" action="{{ route('adresses.liste') }}" method="GET">
                <div class="filtre-group">
                    <label>🔍 Recherche</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Nom, description..."/>
                </div>
                <div class="filtre-group">
                    <label>📍 Ville</label>
                    <select name="ville">
                        <option value="">Toutes les villes</option>
                        @foreach($villes as $ville)
                            <option value="{{ $ville->slug }}" {{ request('ville') == $ville->slug ? 'selected' : '' }}>
                                {{ $ville->emoji }} {{ $ville->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filtre-group">
                    <label>🗂️ Catégorie</label>
                    <select name="categorie">
                        <option value="">Toutes</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}" {{ request('categorie') == $cat->slug ? 'selected' : '' }}>
                                {{ $cat->emoji }} {{ $cat->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-filtre">Filtrer</button>
                @if(request()->hasAny(['q','ville','categorie']))
                    <a href="{{ route('adresses.liste') }}" class="btn-reset">✕ Réinitialiser</a>
                @endif
            </form>
        </div>
    </div>

    {{-- ══ RÉSULTATS ════════════════════════════════════════ --}}
    <div class="section">
        <div class="section-inner">

            <div class="results-header">
                <p class="results-count">
                    <strong>{{ $etablissements->total() }}</strong> adresse(s) trouvée(s)
                    @if($villeActive) dans <strong>{{ $villeActive->nom }}</strong>@endif
                    @if($categorieActive) — <strong>{{ $categorieActive->nom }}</strong>@endif
                    @if($recherche) pour <strong>"{{ $recherche }}"</strong>@endif
                </p>
            </div>

            @if($etablissements->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">🔍</div>
                    <h3>Aucune adresse trouvée</h3>
                    <p>Essayez de modifier vos critères de recherche.</p>
                    <a href="{{ route('adresses.liste') }}" class="btn-primary">Voir toutes les adresses</a>
                </div>
            @else
                <div class="cards-grid">
                    @foreach($etablissements as $etab)
                        @include('visitor.partials.card', ['etab' => $etab])
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="pagination-wrapper">
                    {{ $etablissements->links() }}
                </div>
            @endif

        </div>
    </div>
</div>

@endsection
