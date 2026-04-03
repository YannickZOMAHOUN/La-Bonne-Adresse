@extends('layouts.app')
@section('title', 'Établissements — Administration')

@section('content')
<div class="admin-page">
    <div class="admin-inner">

        <div class="admin-header">
            <h1>⚙️ Administration</h1>
        </div>

        <div class="admin-nav">
            <a href="{{ route('admin.dashboard') }}">🏠 Tableau de bord</a>
            <a href="{{ route('admin.etablissements') }}" class="active">🏪 Établissements</a>
            <a href="{{ route('admin.proprietaires') }}">👥 Propriétaires</a>
        </div>

        {{-- Alertes --}}
        @if(session('success'))
            <div class="alert alert-success">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">❌ {{ session('error') }}</div>
        @endif
        @if(session('info'))
            <div class="alert alert-info">ℹ️ {{ session('info') }}</div>
        @endif

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
                                @if($etab->en_vedette)
                                    <span style="color:#d97706; font-size:0.75rem"> ⭐ Vedette</span>
                                @endif
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
                                <a href="{{ route('admin.preview', $etab) }}" class="btn-sm btn-view">
                                    👁️ Prévisualiser
                                </a>

                                {{-- Valider --}}
                                @if($etab->statut !== 'actif')
                                <button class="btn-sm btn-success"
                                        onclick="ouvrirModal('modal-valider-{{ $etab->id }}')">
                                    ✅ Valider
                                </button>
                                @endif

                                {{-- Suspendre --}}
                                @if($etab->statut !== 'suspendu')
                                <button class="btn-sm btn-warning"
                                        onclick="ouvrirModal('modal-suspendre-{{ $etab->id }}')">
                                    ⏸ Suspendre
                                </button>
                                @endif

                                {{-- Vedette --}}
                                <form method="POST" action="{{ route('admin.vedette', $etab) }}" style="display:inline">
                                    @csrf
                                    <button class="btn-sm btn-view">
                                        {{ $etab->en_vedette ? '⭐ Retirer vedette' : '☆ Vedette' }}
                                    </button>
                                </form>

                                {{-- Supprimer (désactivé si en vedette) --}}
                                @if(!$etab->en_vedette)
                                <button class="btn-sm btn-danger"
                                        onclick="ouvrirModal('modal-suppr-etab-{{ $etab->id }}')">
                                    🗑 Supprimer
                                </button>
                                @else
                                <button class="btn-sm btn-danger" disabled title="Retirez de la vedette d'abord" style="opacity:0.4; cursor:not-allowed">
                                    🗑 Supprimer
                                </button>
                                @endif
                            </td>
                        </tr>

                        {{-- ── MODALS pour cet établissement ──────────────── --}}

                        {{-- Modal : Valider --}}
                        @if($etab->statut !== 'actif')
                        <div id="modal-valider-{{ $etab->id }}" class="modal-overlay" style="display:none">
                            <div class="modal-box">
                                <div class="modal-icon">✅</div>
                                <h3>Valider cet établissement ?</h3>
                                <p>
                                    <strong>« {{ $etab->nom }} »</strong> sera immédiatement visible
                                    par tous les visiteurs et le propriétaire recevra un mail de confirmation.
                                </p>
                                <div class="modal-actions">
                                    <button class="btn-modal-cancel" onclick="fermerModal('modal-valider-{{ $etab->id }}')">
                                        Annuler
                                    </button>
                                    <form method="POST" action="{{ route('admin.valider', $etab) }}">
                                        @csrf
                                        <button type="submit" class="btn-modal-confirm btn-modal-success">
                                            ✅ Oui, valider
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Modal : Suspendre --}}
                        @if($etab->statut !== 'suspendu')
                        <div id="modal-suspendre-{{ $etab->id }}" class="modal-overlay" style="display:none">
                            <div class="modal-box">
                                <div class="modal-icon">⏸</div>
                                <h3>Suspendre cet établissement ?</h3>
                                <p>
                                    <strong>« {{ $etab->nom }} »</strong> ne sera plus visible
                                    par les visiteurs tant qu'il restera suspendu.
                                </p>
                                <div class="modal-actions">
                                    <button class="btn-modal-cancel" onclick="fermerModal('modal-suspendre-{{ $etab->id }}')">
                                        Annuler
                                    </button>
                                    <form method="POST" action="{{ route('admin.suspendre', $etab) }}">
                                        @csrf
                                        <button type="submit" class="btn-modal-confirm btn-modal-warning">
                                            ⏸ Oui, suspendre
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Modal : Supprimer --}}
                        @if(!$etab->en_vedette)
                        <div id="modal-suppr-etab-{{ $etab->id }}" class="modal-overlay" style="display:none">
                            <div class="modal-box">
                                <div class="modal-icon">🗑</div>
                                <h3>Supprimer définitivement ?</h3>
                                <p>
                                    <strong>« {{ $etab->nom }} »</strong> sera supprimé avec toutes
                                    ses photos et services. Cette action est <strong>irréversible</strong>.
                                </p>
                                <div class="modal-actions">
                                    <button class="btn-modal-cancel" onclick="fermerModal('modal-suppr-etab-{{ $etab->id }}')">
                                        Annuler
                                    </button>
                                    <form method="POST" action="{{ route('admin.supprimer-etablissement', $etab) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-modal-confirm btn-modal-danger">
                                            🗑 Oui, supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

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

{{-- ── STYLES MODALS ──────────────────────────────────────────── --}}
<style>
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.55);
    z-index: 9999;
    display: flex !important;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    backdrop-filter: blur(3px);
}
.modal-overlay[style*="display:none"] {
    display: none !important;
}
.modal-box {
    background: #fff;
    border-radius: 16px;
    padding: 2rem;
    max-width: 420px;
    width: 100%;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    animation: modalIn 0.2s ease;
}
@keyframes modalIn {
    from { transform: scale(0.92); opacity: 0; }
    to   { transform: scale(1);    opacity: 1; }
}
.modal-icon {
    font-size: 2.5rem;
    margin-bottom: 0.75rem;
}
.modal-box h3 {
    font-size: 1.2rem;
    margin-bottom: 0.75rem;
    color: var(--dark, #1a1a1a);
}
.modal-box p {
    color: #555;
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}
.modal-actions {
    display: flex;
    gap: 0.75rem;
    justify-content: center;
    flex-wrap: wrap;
}
.btn-modal-cancel {
    padding: 0.6rem 1.4rem;
    border: 2px solid #ddd;
    border-radius: 8px;
    background: #fff;
    color: #555;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
}
.btn-modal-cancel:hover { background: #f5f5f5; }
.btn-modal-confirm {
    padding: 0.6rem 1.4rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    color: #fff;
    transition: all 0.15s;
}
.btn-modal-success  { background: #16a34a; }
.btn-modal-success:hover  { background: #15803d; }
.btn-modal-warning  { background: #d97706; }
.btn-modal-warning:hover  { background: #b45309; }
.btn-modal-danger   { background: #dc2626; }
.btn-modal-danger:hover   { background: #b91c1c; }
.alert { padding: 0.85rem 1.2rem; border-radius: 8px; margin-bottom: 1rem; font-weight: 500; }
.alert-success { background: #dcfce7; color: #166534; }
.alert-error   { background: #fee2e2; color: #991b1b; }
.alert-info    { background: #dbeafe; color: #1e40af; }
</style>

{{-- ── SCRIPTS MODALS ─────────────────────────────────────────── --}}
<script>
function ouvrirModal(id) {
    document.getElementById(id).style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function fermerModal(id) {
    document.getElementById(id).style.display = 'none';
    document.body.style.overflow = '';
}
// Fermer en cliquant sur l'overlay
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) fermerModal(this.id);
    });
});
// Fermer avec Échap
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay').forEach(m => {
            m.style.display = 'none';
        });
        document.body.style.overflow = '';
    }
});
</script>

@endsection
