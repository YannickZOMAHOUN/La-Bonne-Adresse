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
                <button class="btn-add" onclick="ouvrirModal('modal-creer-etab')">
                    ＋ Ajouter un établissement
                </button>
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
                                        disabled title="Retirez de la vedette d'abord"
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
                                    <div class="form-group">
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

{{-- ══════════════════════════════════════════════════════════════
     MODAL : CRÉER UN ÉTABLISSEMENT
══════════════════════════════════════════════════════════════ --}}
<div id="modal-creer-etab" class="modal-overlay" style="display:none">
    <div class="modal-box modal-box--large">
        <div class="modal-icon">🏪</div>
        <h3>Ajouter un établissement</h3>

        <form method="POST" action="{{ route('admin.store-etablissement') }}" class="form-grid">
            @csrf

            <div class="form-row">
                <div class="form-group form-group--full">
                    <label class="form-label">Nom de l'établissement <span class="required">*</span></label>
                    <input type="text" name="nom" class="form-input" required placeholder="Ex: Restaurant Le Festin">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Ville <span class="required">*</span></label>
                    <select name="ville_id" class="form-select" required>
                        <option value="">— Choisir —</option>
                        @foreach($villes as $ville)
                            <option value="{{ $ville->id }}">{{ $ville->emoji }} {{ $ville->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Catégorie <span class="required">*</span></label>
                    <select name="categorie_id" class="form-select" required>
                        <option value="">— Choisir —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->emoji }} {{ $cat->nom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group form-group--full">
                    <label class="form-label">Adresse <span class="required">*</span></label>
                    <input type="text" name="adresse" class="form-input" required placeholder="Ex: Quartier Zongo, Cotonou">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group form-group--full">
                    <label class="form-label">Description <span class="required">*</span></label>
                    <textarea name="description" class="form-textarea" required rows="3"
                              placeholder="Décrivez l'établissement…"></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="telephone" class="form-input" placeholder="+229 97 00 00 00">
                </div>
                <div class="form-group">
                    <label class="form-label">WhatsApp</label>
                    <input type="text" name="whatsapp" class="form-input" placeholder="+229 97 00 00 00">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" placeholder="contact@example.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Site web</label>
                    <input type="url" name="site_web" class="form-input" placeholder="https://...">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Fourchette de prix</label>
                    <input type="text" name="fourchette_prix" class="form-input" placeholder="Ex: 2 000 – 10 000 FCFA">
                </div>
                <div class="form-group">
                    <label class="form-label">Statut initial</label>
                    <select name="statut" class="form-select">
                        <option value="actif">✅ Actif (publié immédiatement)</option>
                        <option value="en_attente">⏳ En attente de validation</option>
                        <option value="suspendu">🚫 Suspendu</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group form-group--full">
                    <label class="form-label">
                        Propriétaire
                        <small style="font-weight:normal; color:#888">(optionnel — peut être attribué plus tard)</small>
                    </label>
                    <select name="user_id" class="form-select">
                        <option value="">— Aucun pour l'instant —</option>
                        @foreach($proprietaires as $proprio)
                            <option value="{{ $proprio->id }}">
                                {{ $proprio->nom }} — {{ $proprio->email }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="modal-actions" style="margin-top:0.5rem">
                <button type="button" class="btn-modal-cancel"
                        onclick="fermerModal('modal-creer-etab')">
                    Annuler
                </button>
                <button type="submit" class="btn-modal-confirm btn-modal-success">
                    🏪 Créer l'établissement
                </button>
            </div>
        </form>
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
.modal-box--large {
    max-width: 680px;
    text-align: left;
}
.modal-box--large h3 { text-align: center; }
.modal-box--large .modal-icon { text-align: center; }
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
.btn-modal-success  { background: #16a34a; }
.btn-modal-success:hover  { background: #15803d; }
.btn-modal-warning  { background: #d97706; }
.btn-modal-warning:hover  { background: #b45309; }
.btn-modal-danger   { background: #dc2626; }
.btn-modal-danger:hover   { background: #b91c1c; }

/* ── Formulaire dans modal ── */
.form-grid { width: 100%; }
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}
.form-group { display: flex; flex-direction: column; gap: 0.3rem; }
.form-group--full { grid-column: 1 / -1; }
.form-label { font-size: 0.85rem; font-weight: 600; color: #374151; }
.required { color: #dc2626; }
.form-input,
.form-select,
.form-textarea {
    padding: 0.55rem 0.8rem;
    border: 1.5px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.9rem;
    color: #1f2937;
    background: #fff;
    transition: border-color 0.15s;
    width: 100%;
    box-sizing: border-box;
}
.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22,163,74,0.12);
}
.form-textarea { resize: vertical; min-height: 80px; }

/* ── Alertes ── */
.alert { padding: 0.85rem 1.2rem; border-radius: 8px; margin-bottom: 1rem; font-weight: 500; }
.alert-success { background: #dcfce7; color: #166534; }
.alert-error   { background: #fee2e2; color: #991b1b; }
.alert-info    { background: #dbeafe; color: #1e40af; }

@media (max-width: 600px) {
    .form-row { grid-template-columns: 1fr; }
    .form-group--full { grid-column: 1; }
}
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
