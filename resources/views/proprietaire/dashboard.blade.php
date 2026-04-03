@extends('layouts.app')
@section('title', 'Mon espace — Bonnes Adresses Bénin')

@section('content')
<div class="dashboard-page">
    <div class="dashboard-inner">

        {{-- Alertes --}}
        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom:1.5rem">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error" style="margin-bottom:1.5rem">❌ {{ session('error') }}</div>
        @endif

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
                            <td class="actions-cell">
                                @if($etab->statut === 'actif')
                                    <a href="{{ route('adresses.show', $etab->slug) }}"
                                       class="btn-sm btn-view" target="_blank">
                                        👁 Voir
                                    </a>
                                @endif
                                <a href="{{ route('proprietaire.edit', $etab) }}" class="btn-sm btn-edit">
                                    ✏️ Modifier
                                </a>
                                <button class="btn-sm btn-danger"
                                        onclick="ouvrirModal('modal-suppr-{{ $etab->id }}')">
                                    🗑 Supprimer
                                </button>
                            </td>
                        </tr>

                        {{-- Modal suppression --}}
                        <div id="modal-suppr-{{ $etab->id }}" class="modal-overlay" style="display:none">
                            <div class="modal-box">
                                <div class="modal-icon">🗑</div>
                                <h3>Supprimer cette fiche ?</h3>
                                <p>
                                    <strong>« {{ $etab->nom }} »</strong> sera supprimé définitivement
                                    avec toutes ses photos. Cette action est <strong>irréversible</strong>.
                                </p>
                                <div class="modal-actions">
                                    <button class="btn-modal-cancel"
                                            onclick="fermerModal('modal-suppr-{{ $etab->id }}')">
                                        Annuler
                                    </button>
                                    <form method="POST" action="{{ route('proprietaire.destroy', $etab) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-modal-confirm btn-modal-danger">
                                            🗑 Oui, supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>
</div>

{{-- ── STYLES MODALS ─────────────────────────────────────────── --}}
<style>
.modal-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.55);
    z-index: 9999;
    display: flex !important;
    align-items: center; justify-content: center;
    padding: 1rem;
    backdrop-filter: blur(3px);
}
.modal-overlay[style*="display:none"] { display: none !important; }
.modal-box {
    background: #fff; border-radius: 16px; padding: 2rem;
    max-width: 420px; width: 100%; text-align: center;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    animation: modalIn 0.2s ease;
}
@keyframes modalIn {
    from { transform: scale(0.92); opacity: 0; }
    to   { transform: scale(1);    opacity: 1; }
}
.modal-icon { font-size: 2.5rem; margin-bottom: 0.75rem; }
.modal-box h3 { font-size: 1.2rem; margin-bottom: 0.75rem; color: var(--dark); }
.modal-box p { color: #555; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.5rem; }
.modal-actions { display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap; }
.btn-modal-cancel {
    padding: 0.6rem 1.4rem; border: 2px solid #ddd; border-radius: 8px;
    background: #fff; color: #555; font-weight: 600; cursor: pointer; transition: all 0.15s;
}
.btn-modal-cancel:hover { background: #f5f5f5; }
.btn-modal-confirm {
    padding: 0.6rem 1.4rem; border: none; border-radius: 8px;
    font-weight: 600; cursor: pointer; color: #fff; transition: all 0.15s;
}
.btn-modal-danger  { background: #dc2626; }
.btn-modal-danger:hover  { background: #b91c1c; }
</style>

{{-- ── SCRIPTS MODALS ────────────────────────────────────────── --}}
<script>
function ouvrirModal(id) {
    document.getElementById(id).style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function fermerModal(id) {
    document.getElementById(id).style.display = 'none';
    document.body.style.overflow = '';
}
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) fermerModal(this.id);
    });
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay').forEach(m => m.style.display = 'none');
        document.body.style.overflow = '';
    }
});
</script>

@endsection
