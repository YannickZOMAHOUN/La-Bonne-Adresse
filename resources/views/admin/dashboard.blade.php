@extends('layouts.app')
@section('title', 'Administration — Bonnes Adresses Bénin')

@section('content')
<div class="admin-page">
    <div class="admin-inner">

        <div class="admin-header">
            <h1>⚙️ Administration</h1>
            <p>Gérez les établissements et les propriétaires.</p>
        </div>

        {{-- Statistiques --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-num">{{ $stats['total_etablissements'] }}</div>
                <div class="stat-card-label">Établissements total</div>
            </div>
            <div class="stat-card stat-card--warning">
                <div class="stat-card-num">{{ $stats['en_attente'] }}</div>
                <div class="stat-card-label">En attente de validation</div>
            </div>
            <div class="stat-card stat-card--success">
                <div class="stat-card-num">{{ $stats['actifs'] }}</div>
                <div class="stat-card-label">Établissements actifs</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-num">{{ $stats['proprietaires_actifs'] }} / {{ $stats['proprietaires'] }}</div>
                <div class="stat-card-label">Propriétaires actifs</div>
            </div>
        </div>

        {{-- Navigation admin --}}
        <div class="admin-nav">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                🏠 Tableau de bord
            </a>
            <a href="{{ route('admin.etablissements') }}" class="{{ request()->routeIs('admin.etablissements') ? 'active' : '' }}">
                🏪 Établissements
            </a>
            <a href="{{ route('admin.proprietaires') }}" class="{{ request()->routeIs('admin.proprietaires') ? 'active' : '' }}">
                👥 Propriétaires
            </a>
        </div>

        {{-- En attente de validation --}}
        @if($enAttente->isNotEmpty())
        <div class="admin-section">
            <h2>⏳ En attente de validation ({{ $enAttente->count() }})</h2>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Établissement</th>
                            <th>Propriétaire</th>
                            <th>Ville</th>
                            <th>Catégorie</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($enAttente as $etab)
                        <tr>
                            <td>
                                <strong>{{ $etab->nom }}</strong><br>
                                <small>{{ Str::limit($etab->adresse, 40) }}</small>
                            </td>
                            <td>
                                {{ $etab->user->nom }}<br>
                                <small>{{ $etab->user->telephone }}</small>
                            </td>
                            <td>{{ $etab->ville->emoji }} {{ $etab->ville->nom }}</td>
                            <td>{{ $etab->categorie->emoji }} {{ $etab->categorie->nom }}</td>
                            <td>{{ $etab->created_at->format('d/m/Y') }}</td>
                            <td class="actions-cell">
                                <form method="POST" action="{{ route('admin.valider', $etab) }}" style="display:inline">
                                    @csrf
                                    <button class="btn-sm btn-success" onclick="return confirm('Valider cet établissement ?')">
                                        ✅ Valider
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.suspendre', $etab) }}" style="display:inline">
                                    @csrf
                                    <button class="btn-sm btn-danger" onclick="return confirm('Refuser cet établissement ?')">
                                        ❌ Refuser
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">✅</div>
                <h3>Aucun établissement en attente</h3>
                <p>Tout est à jour !</p>
            </div>
        @endif

    </div>
</div>
@endsection
