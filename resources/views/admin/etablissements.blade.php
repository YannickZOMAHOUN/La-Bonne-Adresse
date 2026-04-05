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
            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.75rem; margin-bottom:1rem">
                <h2 style="margin:0">🏪 Tous les établissements ({{ $etablissements->total() }})</h2>

                {{-- Lien pleine page au lieu d'une modal --}}
                <a href="{{ route('admin.create-etablissement') }}" class="btn-add">
                    ＋ Ajouter un établissement
                </a>
            </div>

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

                                {{-- Prévisualiser --}}
                                <a href="{{ route('admin.preview', $etab) }}"
                                   class="btn-icon btn-icon--view" title="Prévisualiser">
                                    👁️
                                </a>

                                {{-- Modifier (pleine page) --}}
                                <a href="{{ route('admin.edit-etablissement', $etab) }}"
                                   class="btn-icon btn-icon--edit" title="Modifier">
                                    ✏️
                                </a>

                                {{-- Attribuer propriétaire --}}
                                <button class="btn-icon btn-icon--assign"
                                        title="Attribuer un propriétaire"
                                        onclick="ouvrirModal('modal-attrib-{{ $etab->id }}')">
                                    👤
                                </button>

                                {{-- Valider --}}
                                @if($etab->statut !== 'actif')
                                <button class="btn-icon btn-icon--success"
                                        title="Valider"
                                        onclick="ouvrirModal('modal-valider-{{ $etab->id }}')">
                                    ✅
                                </button>
                                @endif

                                {{-- Suspendre --}}
                                @if($etab->statut !== 'suspendu')
                                <button class="btn-icon btn-icon--warning"
                                        title="Suspendre"
                                        onclick="ouvrirModal('modal-suspendre-{{ $etab->id }}')">
                                    ⏸
                                </button>
                                @endif

                                {{-- Vedette --}}
                                <form method="POST" action="{{ route('admin.vedette', $etab) }}" style="display:inline">
                                    @csrf
                                    <button class="btn-icon btn-icon--star"
                                            title="{{ $etab->en_vedette ? 'Retirer vedette' : 'Mettre en vedette' }}">
                                        {{ $etab->en_vedette ? '⭐' : '☆' }}
                                    </button>
                                </form>

                                {{-- Supprimer --}}
                                @if(!$etab->en_vedette)
                                <button class="btn-icon btn-icon--danger"
                                        title="Supprimer"
                                        onclick="ouvrirModal('modal-suppr-etab-{{ $etab->id }}')">
                                    🗑
                                </button>
                                @else
                                <button class="btn-icon btn-icon--danger"
                                        disabled
                                        title="Retirez de la vedette d'abord"
                                        style="opacity:0.35; cursor:not-allowed">
                                    🗑
                                </button>
                                @endif

                            </td>
                        </tr>

                        {{-- ── MODALS pour cet établissement ──────────────── --}}

                        {{-- Modal : Attribuer propriétaire --}}
                        <div id="modal-attrib-{{ $etab->id }}" class="modal-overlay" style="display:none">
                            <div class="modal-box">
                                <div class="modal-icon">👤</div>
                                <h3>Attribuer un propriétaire</h3>
                                <p>
                                    Choisissez le propriétaire pour <strong>« {{ $etab->nom }} »</strong>.<br>
                                    Propriétaire actuel : <strong>{{ $etab->user->nom }}</strong>
                                </p>
                                <form method="POST" action="{{ route('admin.attribuer-proprietaire', $etab) }}">
                                    @csrf
                                    <div class="form-group" style="margin-bottom:1rem; text-align:left">
                                        <select name="user_id" class="form-select" required>
                                            <option value="">— Sélectionner un propriétaire —</option>
                                            @foreach($proprietaires as $proprio)
                                                <option value="{{ $proprio->id }}"
                                                    {{ $etab->user_id == $proprio->id ? 'selected' : '' }}>
                                                    {{ $proprio->nom }} ({{ $proprio->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="modal-actions">
                                        <button type="button" class="btn-modal-cancel"
                                                onclick="fermerModal('modal-attrib-{{ $etab->id }}')">
                                            Annuler
                                        </button>
                                        <button type="submit" class="btn-modal-confirm btn-modal-success">
                                            👤 Attribuer
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

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
                                    <button class="btn-modal-cancel"
                                            onclick="fermerModal('modal-valider-{{ $etab->id }}')">
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
                                    <button class="btn-modal-cancel"
                                            onclick="fermerModal('modal-suspendre-{{ $etab->id }}')">
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
                                    <button class="btn-modal-cancel"
                                            onclick="fermerModal('modal-suppr-etab-{{ $etab->id }}')">
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

{{-- ── STYLES ─────────────────────────────────────────────────── --}}
<style>
/* ── Bouton ajout ── */
.btn-add {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.55rem 1.2rem;
    background: #16a34a;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.15s;
}
.btn-add:hover { background: #15803d; }

/* ── Boutons picto ── */
.btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-size: 1rem;
    background: transparent;
    transition: background 0.15s, transform 0.1s;
    text-decoration: none;
}
.btn-icon:hover { transform: scale(1.15); }
.btn-icon--view    { background: #eff6ff; }
.btn-icon--view:hover { background: #dbeafe; }
.btn-icon--edit    { background: #f0f9ff; }
.btn-icon--edit:hover { background: #e0f2fe; }
.btn-icon--assign  { background: #f5f3ff; }
.btn-icon--assign:hover { background: #ede9fe; }
.btn-icon--success { background: #f0fdf4; }
.btn-icon--success:hover { background: #dcfce7; }
.btn-icon--warning { background: #fffbeb; }
.btn-icon--warning:hover { background: #fef3c7; }
.btn-icon--star    { background: #fefce8; }
.btn-icon--star:hover { background: #fef9c3; }
.btn-icon--danger  { background: #fff1f2; }
.btn-icon--danger:hover { background: #fee2e2; }

/* ── Actions cell ── */
.actions-cell { display: flex; flex-wrap: wrap; gap: 4px; align-items: center; }

/* ── Modals ── */
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
    max-height: 90vh;
    overflow-y: auto;
}
@keyframes modalIn {
    from { transform: scale(0.92); opacity: 0; }
    to   { transform: scale(1);    opacity: 1; }
}
.modal-icon { font-size: 2.5rem; margin-bottom: 0.75rem; }
.modal-box h3 { font-size: 1.2rem; margin-bottom: 0.75rem; color: var(--dark, #1a1a1a); }
.modal-box p  { color: #555; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.5rem; }
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

/* ── Form dans modal attrib ── */
.form-select {
    width: 100%;
    padding: 0.55rem 0.8rem;
    border: 1.5px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.9rem;
    color: #1f2937;
    background: #fff;
    box-sizing: border-box;
}
.form-select:focus {
    outline: none;
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22,163,74,0.12);
}

/* ── Alertes ── */
.alert { padding: 0.85rem 1.2rem; border-radius: 8px; margin-bottom: 1rem; font-weight: 500; }
.alert-success { background: #dcfce7; color: #166534; }
.alert-error   { background: #fee2e2; color: #991b1b; }
.alert-info    { background: #dbeafe; color: #1e40af; }
</style>

{{-- ── SCRIPTS ─────────────────────────────────────────────────── --}}
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
        document.querySelectorAll('.modal-overlay').forEach(m => {
            m.style.display = 'none';
        });
        document.body.style.overflow = '';
    }
});
</script>

@endsection
