@extends('layouts.app')
@section('title', 'Propriétaires — Administration')

@section('content')
<div class="admin-page">
    <div class="admin-inner">

        <div class="admin-header">
            <h1>⚙️ Administration</h1>
        </div>

        <div class="admin-nav">
            <a href="{{ route('admin.dashboard') }}">🏠 Tableau de bord</a>
            <a href="{{ route('admin.etablissements') }}">🏪 Établissements</a>
            <a href="{{ route('admin.proprietaires') }}" class="active">👥 Propriétaires</a>
        </div>

        <div class="admin-section">
            <h2>👥 Propriétaires ({{ $proprietaires->total() }})</h2>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Établissements</th>
                            <th>Statut</th>
                            <th>Inscription</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proprietaires as $proprio)
                        <tr>
                            <td><strong>{{ $proprio->nom }}</strong></td>
                            <td>{{ $proprio->email }}</td>
                            <td>{{ $proprio->telephone ?? '—' }}</td>
                            <td style="text-align:center">{{ $proprio->etablissements_count }}</td>
                            <td>
                                <span class="badge badge--{{ $proprio->statut }}">
                                    @if($proprio->statut === 'actif') ✅ Actif
                                    @elseif($proprio->statut === 'en_attente') ⏳ En attente
                                    @else 🚫 Suspendu
                                    @endif
                                </span>
                            </td>
                            <td>{{ $proprio->created_at->format('d/m/Y') }}</td>
                            <td class="actions-cell">
                                @if($proprio->statut !== 'actif')
                                <form method="POST" action="{{ route('admin.activer-proprio', $proprio) }}" style="display:inline">
                                    @csrf
                                    <button class="btn-sm btn-success">✅ Activer</button>
                                </form>
                                @endif
                                @if($proprio->statut !== 'suspendu')
                                <form method="POST" action="{{ route('admin.suspendre-proprio', $proprio) }}" style="display:inline">
                                    @csrf
                                    <button class="btn-sm btn-danger">🚫 Suspendre</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:2rem; color:var(--muted)">
                                Aucun propriétaire inscrit pour le moment.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $proprietaires->links() }}
            </div>
        </div>

    </div>
</div>
@endsection
