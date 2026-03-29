@extends('layouts.app')
@section('title', 'Établissements — Administration')

@section('content')
<div class="admin-page">
    <div class="admin-inner">

        <div class="admin-header">
            <h1>⚙️ Administration</h1>
        </div>

        {{-- Navigation admin --}}
        <div class="admin-nav">
            <a href="{{ route('admin.dashboard') }}">🏠 Tableau de bord</a>
            <a href="{{ route('admin.etablissements') }}" class="active">🏪 Établissements</a>
            <a href="{{ route('admin.proprietaires') }}">👥 Propriétaires</a>
        </div>

        <div class="admin-section">
            <h2>🏪 Tous les établissements ({{ $etablissements->total() }})</h2>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Établissement</th>
                            <th>Propriétaire</th>
                            <th>Ville</th>
                            <th>Catégorie</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($etablissements as $etab)
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
                            <td>
                                <span class="badge badge--{{ $etab->statut }}">
                                    @if($etab->statut === 'actif') ✅ Actif
                                    @elseif($etab->statut === 'en_attente') ⏳ En attente
                                    @else 🚫 Suspendu
                                    @endif
                                </span>
                            </td>
                            <td>{{ $etab->created_at->format('d/m/Y') }}</td>
                            <td class="actions-cell">
                                @if($etab->statut !== 'actif')
                                <form method="POST" action="{{ route('admin.valider', $etab) }}" style="display:inline">
                                    @csrf
                                    <button class="btn-sm btn-success">✅ Valider</button>
                                </form>
                                @endif
                                @if($etab->statut !== 'suspendu')
                                <form method="POST" action="{{ route('admin.suspendre', $etab) }}" style="display:inline">
                                    @csrf
                                    <button class="btn-sm btn-danger">🚫 Suspendre</button>
                                </form>
                                @endif
                                <form method="POST" action="{{ route('admin.vedette', $etab) }}" style="display:inline">
                                    @csrf
                                    <button class="btn-sm btn-view">
                                        {{ $etab->en_vedette ? '⭐ Retirer vedette' : '☆ Mettre en vedette' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:2rem; color:var(--muted)">
                                Aucun établissement pour le moment.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $etablissements->links() }}
            </div>
        </div>

    </div>
</div>
@endsection
