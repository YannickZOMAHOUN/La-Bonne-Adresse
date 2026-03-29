@extends('layouts.app')

@section('title', 'Explorer les adresses — Bonnes Adresses Bénin')

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
                    @if(request('ville')) dans <strong>{{ request('ville') }}</strong>@endif
                    @if(request('categorie')) — <strong>{{ request('categorie') }}</strong>@endif
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
