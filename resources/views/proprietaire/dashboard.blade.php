@extends('layouts.app')
@section('title', 'Mon espace — Bonnes Adresses Bénin')

@section('content')
<div class="dashboard-page">
    <div class="dashboard-inner">

        {{-- En-tête --}}
        <div class="dashboard-header">
            <div>
                <h1>Bonjour, {{ auth()->user()->nom }} 👋</h1>
                <p>Gérez vos établissements depuis votre espace personnel.</p>
            </div>
            <a href="{{ route('proprietaire.create') }}" class="btn-primary">
                ➕ Ajouter un établissement
            </a>
        </div>

        {{-- Mes établissements --}}
        @if($etablissements->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">🏪</div>
                <h3>Vous n'avez pas encore d'établissement</h3>
                <p>Commencez par ajouter votre premier établissement.</p>
                <a href="{{ route('proprietaire.create') }}" class="btn-primary">
                    ➕ Ajouter maintenant
                </a>
            </div>
        @else
            <div class="etab-table-wrapper">
                <table class="etab-table">
                    <thead>
                        <tr>
                            <th>Établissement</th>
                            <th>Ville</th>
                            <th>Catégorie</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($etablissements as $etab)
                        <tr>
                            <td>
                                <div class="etab-name">{{ $etab->nom }}</div>
                                <div class="etab-adresse">{{ Str::limit($etab->adresse, 50) }}</div>
                            </td>
                            <td>{{ $etab->ville->emoji }} {{ $etab->ville->nom }}</td>
                            <td>{{ $etab->categorie->emoji }} {{ $etab->categorie->nom }}</td>
                            <td>
                                <span class="badge badge--{{ $etab->statut }}">
                                    @if($etab->statut === 'actif') ✅ Actif
                                    @elseif($etab->statut === 'en_attente') ⏳ En attente
                                    @else 🚫 Suspendu
                                    @endif
                                </span>
                            </td>
                            <td>
                                @if($etab->statut === 'actif')
                                    <a href="{{ route('adresses.show', $etab->slug) }}" class="btn-sm btn-view" target="_blank">
                                        👁 Voir
                                    </a>
                                @endif
                                <a href="{{ route('proprietaire.edit', $etab) }}" class="btn-sm btn-edit">
                                    ✏️ Modifier
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>
</div>
@endsection
