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

                                {{-- Activer --}}
                                @if($proprio->statut !== 'actif')
                                <button class="btn-sm btn-success"
                                        onclick="ouvrirModal('modal-activer-{{ $proprio->id }}')">
                                    ✅ Activer
                                </button>
                                @endif

                                {{-- Suspendre --}}
                                @if($proprio->statut !== 'suspendu')
                                <button class="btn-sm btn-warning"
                                        onclick="ouvrirModal('modal-suspendre-{{ $proprio->id }}')">
                                    ⏸ Suspendre
                                </button>
                                @endif

                                {{-- Supprimer --}}
                                <button class="btn-sm btn-danger"
                                        onclick="ouvrirModal('modal-suppr-proprio-{{ $proprio->id }}')">
                                    🗑 Supprimer
                                </button>
                            </td>
                        </tr>

                        {{-- ── MODALS pour ce propriétaire ─────────────────── --}}

                        {{-- Modal : Activer --}}
                        @if($proprio->statut !== 'actif')
                        <div id="modal-activer-{{ $proprio->id }}" class="modal-overlay" style="display:none">
                            <div class="modal-box">
                                <div class="modal-icon">✅</div>
                                <h3>Activer ce compte ?</h3>
                                <p>
                                    <strong>{{ $proprio->nom }}</strong> pourra se connecter
                                    et créer ses fiches établissement. Un mail de confirmation
                                    lui sera envoyé automatiquement.
                                </p>
                                <div class="modal-actions">
                                    <button class="btn-modal-cancel" onclick="fermerModal('modal-activer-{{ $proprio->id }}')">
                                        Annuler
                                    </button>
                                    <form method="POST" action="{{ route('admin.activer-proprio', $proprio) }}">
                                        @csrf
                                        <button type="submit" class="btn-modal-confirm btn-modal-success">
                                            ✅ Oui, activer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Modal : Suspendre --}}
                        @if($proprio->statut !== 'suspendu')
                        <div id="modal-suspendre-{{ $proprio->id }}" class="modal-overlay" style="display:none">
                            <div class="modal-box">
                                <div class="modal-icon">⏸</div>
                                <h3>Suspendre ce compte ?</h3>
                                <p>
                                    <strong>{{ $proprio->nom }}</strong> ne pourra plus se connecter
                                    tant que son compte sera suspendu. Ses établissements resteront
                                    dans la base de données.
                                </p>
                                <div class="modal-actions">
                                    <button class="btn-modal-cancel" onclick="fermerModal('modal-suspendre-{{ $proprio->id }}')">
                                        Annuler
                                    </button>
                                    <form method="POST" action="{{ route('admin.suspendre-proprio', $proprio) }}">
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
                        <div id="modal-suppr-proprio-{{ $proprio->id }}" class="modal-overlay" style="display:none">
                            <div class="modal-box">
                                <div class="modal-icon">🗑</div>
                                <h3>Supprimer ce compte ?</h3>
                                <p>
                                    Le compte de <strong>{{ $proprio->nom }}</strong> et ses
                                    <strong>{{ $proprio->etablissements_count }} établissement(s)</strong>
                                    seront supprimés définitivement avec toutes leurs photos.
                                    Cette action est <strong>irréversible</strong>.
                                </p>
                                <div class="modal-actions">
                                    <button class="btn-modal-cancel" onclick="fermerModal('modal-suppr-proprio-{{ $proprio->id }}')">
                                        Annuler
                                    </button>
                                    <form method="POST" action="{{ route('admin.supprimer-proprio', $proprio) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-modal-confirm btn-modal-danger">
                                            🗑 Oui, supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

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

{{-- Styles et scripts modals (identiques à la page établissements) --}}
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
.modal-overlay[style*="display:none"] { display: none !important; }
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
.modal-icon { font-size: 2.5rem; margin-bottom: 0.75rem; }
.modal-box h3 { font-size: 1.2rem; margin-bottom: 0.75rem; color: var(--dark, #1a1a1a); }
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
.btn-modal-success { background: #16a34a; }
.btn-modal-success:hover { background: #15803d; }
.btn-modal-warning { background: #d97706; }
.btn-modal-warning:hover { background: #b45309; }
.btn-modal-danger  { background: #dc2626; }
.btn-modal-danger:hover  { background: #b91c1c; }
.btn-warning { background: #d97706; color: #fff; }
.alert { padding: 0.85rem 1.2rem; border-radius: 8px; margin-bottom: 1rem; font-weight: 500; }
.alert-success { background: #dcfce7; color: #166534; }
.alert-error   { background: #fee2e2; color: #991b1b; }
.alert-info    { background: #dbeafe; color: #1e40af; }
</style>

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
