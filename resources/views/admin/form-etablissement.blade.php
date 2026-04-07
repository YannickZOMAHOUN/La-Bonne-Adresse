@extends('layouts.app')

@section('title', isset($etablissement)
    ? 'Modifier « ' . $etablissement->nom . ' » — Administration'
    : 'Ajouter un établissement — Administration')

@section('content')
<style>
.admin-form-badge {
    display:flex; align-items:center; gap:0.75rem;
    background:#fffbeb; border:1px solid #fde68a; border-radius:10px;
    padding:0.65rem 1.1rem; font-size:0.82rem; font-weight:600;
    color:#92400e; margin-bottom:1.8rem;
}
.admin-section-highlight { border-color:#fde68a !important; background:#fffdf5 !important; }
.admin-section-highlight h2 { color:#92400e !important; }

.phone-input-group {
    display:flex; align-items:stretch;
    border:1px solid var(--border); border-radius:10px;
    overflow:hidden; background:var(--cream); transition:border-color 0.2s;
}
.phone-input-group:focus-within { border-color:var(--green); background:#fff; }
.phone-prefix {
    padding:0.65rem 0.9rem; background:var(--cream);
    border-right:1px solid var(--border); font-size:0.88rem;
    font-weight:700; color:var(--muted);
    display:flex; align-items:center; white-space:nowrap;
}
.phone-input-group input {
    border:none !important; border-radius:0 !important;
    background:transparent !important; flex:1;
    padding:0.65rem 0.9rem; font-size:0.93rem;
    outline:none; box-shadow:none !important;
}

/* Grille médias (galerie & menu) */
.media-grid {
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(110px, 1fr));
    gap:0.75rem;
    margin-bottom:0.75rem;
}
.media-item {
    position:relative; border-radius:10px; overflow:hidden;
    border:1px solid var(--border); background:var(--cream);
    aspect-ratio:1; display:flex; align-items:center; justify-content:center;
}
.media-item img {
    width:100%; height:100%; object-fit:cover;
}
.media-item--pdf {
    flex-direction:column; gap:0.3rem; padding:0.5rem; text-align:center;
}
.media-item--pdf span { font-size:1.8rem; }
.media-item--pdf p {
    font-size:0.65rem; color:var(--muted); word-break:break-all;
    line-height:1.3; overflow:hidden; display:-webkit-box;
    -webkit-line-clamp:3; -webkit-box-orient:vertical;
}
.media-item a.media-open {
    position:absolute; inset:0; display:flex; align-items:center;
    justify-content:center; background:rgba(0,0,0,0);
    transition:background 0.2s; text-decoration:none;
}
.media-item a.media-open:hover { background:rgba(0,0,0,0.35); }
.media-item a.media-open::after {
    content:'↗'; color:#fff; font-size:1.4rem; font-weight:700;
    opacity:0; transition:opacity 0.2s;
}
.media-item a.media-open:hover::after { opacity:1; }
.media-delete-cb {
    position:absolute; top:5px; right:5px; z-index:2;
    width:18px; height:18px; accent-color:var(--danger); cursor:pointer;
}

/* Horaires */
.horaires-admin-grid {
    display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:0.55rem;
}
.horaire-admin-row {
    display:flex; align-items:center; gap:0.75rem;
    background:var(--cream); border:1px solid var(--border);
    border-radius:10px; padding:0.6rem 0.9rem;
}
.horaire-toggle-label {
    display:flex; align-items:center; gap:0.5rem;
    cursor:pointer; flex-shrink:0; width:72px;
}
.horaire-toggle-label input[type="checkbox"] { width:16px; height:16px; accent-color:var(--green); cursor:pointer; }
.horaire-jour-name { font-size:0.82rem; font-weight:700; color:var(--dark); }
.horaire-times-wrap { display:flex; align-items:center; gap:0.4rem; flex:1; }
.horaire-times-wrap.horaire-disabled { display:none; }
.horaire-time-input {
    border:1px solid var(--border) !important; border-radius:7px !important;
    padding:0.4rem 0.5rem !important; font-size:0.85rem !important;
    background:#fff !important; flex:1; min-width:0;
    font-family:'DM Sans', sans-serif; outline:none; transition:border-color 0.2s;
}
.horaire-time-input:focus { border-color:var(--green) !important; }
.horaire-time-input:disabled { background:var(--cream) !important; color:var(--muted) !important; }
.horaire-sep { font-size:0.85rem; color:var(--muted); flex-shrink:0; }
.horaire-closed-label { font-size:0.78rem; color:var(--muted); font-style:italic; flex:1; }
.horaire-closed-label.hidden { display:none; }

@media (max-width:640px) {
    .horaires-admin-grid { grid-template-columns:1fr; }
    .media-grid { grid-template-columns:repeat(auto-fill, minmax(90px, 1fr)); }
}
</style>

<div class="form-page">
    <div class="form-inner">

        <div class="form-header">
            <a href="{{ route('admin.etablissements') }}" class="btn-back">← Retour</a>
            <h1>{{ isset($etablissement) ? '✏️ Modifier un établissement' : '➕ Ajouter un établissement' }}</h1>
        </div>

        <div class="admin-form-badge">
            <span>⚙️ Espace administration</span>
            @isset($etablissement)
                <span class="badge badge--{{ $etablissement->statut }}">
                    @if($etablissement->statut === 'actif') ✅ Actif
                    @elseif($etablissement->statut === 'en_attente') ⏳ En attente
                    @else 🚫 Suspendu @endif
                </span>
            @endisset
        </div>

        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:1.5rem;border-radius:10px">
                @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
            </div>
        @endif

        <form method="POST"
              action="{{ isset($etablissement) ? route('admin.update-etablissement', $etablissement) : route('admin.store-etablissement') }}"
              enctype="multipart/form-data" id="etablissementForm">
            @csrf
            @isset($etablissement) @method('PUT') @endisset

            {{-- ══ PARAMÈTRES ADMIN ══════════════════════════════════ --}}
            <div class="form-section admin-section-highlight">
                <h2>⚙️ Paramètres administrateur</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="statut">Statut *</label>
                        <select id="statut" name="statut" required>
                            <option value="actif"      {{ old('statut', $etablissement->statut ?? 'en_attente') === 'actif'      ? 'selected' : '' }}>✅ Actif — visible immédiatement</option>
                            <option value="en_attente" {{ old('statut', $etablissement->statut ?? 'en_attente') === 'en_attente' ? 'selected' : '' }}>⏳ En attente</option>
                            <option value="suspendu"   {{ old('statut', $etablissement->statut ?? '') === 'suspendu'             ? 'selected' : '' }}>🚫 Suspendu</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="user_id">Propriétaire <small style="font-weight:400;color:var(--muted)">(optionnel)</small></label>
                        <select id="user_id" name="user_id">
                            <option value="">— Aucun / conserver l'actuel —</option>
                            @foreach($proprietaires as $p)
                                <option value="{{ $p->id }}" {{ old('user_id', $etablissement->user_id ?? '') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nom }} — {{ $p->email }}
                                </option>
                            @endforeach
                        </select>
                        @isset($etablissement)
                            @if($etablissement->user)
                                <p class="form-hint">Actuel : <strong>{{ $etablissement->user->nom }}</strong>. Laissez vide pour conserver.</p>
                            @else
                                <p class="form-hint" style="color:var(--warning)">⚠️ Aucun propriétaire assigné.</p>
                            @endif
                        @endisset
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" id="en_vedette" name="en_vedette" value="1"
                               {{ old('en_vedette', $etablissement->en_vedette ?? false) ? 'checked' : '' }} />
                        <label for="en_vedette" style="cursor:pointer">
                            ⭐ Mettre en vedette
                            <span class="form-hint" style="display:inline;font-weight:400">— apparaît dans "Coups de cœur"</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- ══ INFORMATIONS PRINCIPALES ══════════════════════════ --}}
            <div class="form-section">
                <h2>📋 Informations principales</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="ville_id">Ville *</label>
                        <select id="ville_id" name="ville_id" required>
                            <option value="">Choisir une ville</option>
                            @foreach($villes as $v)
                                <option value="{{ $v->id }}" {{ old('ville_id', $etablissement->ville_id ?? '') == $v->id ? 'selected' : '' }}>{{ $v->emoji }} {{ $v->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="categorie_id">Catégorie *</label>
                        <select id="categorie_id" name="categorie_id" required>
                            <option value="">Choisir une catégorie</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" {{ old('categorie_id', $etablissement->categorie_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->emoji }} {{ $c->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="nom">Nom de l'établissement *</label>
                    <input type="text" id="nom" name="nom" value="{{ old('nom', $etablissement->nom ?? '') }}"
                           placeholder="ex : Restaurant Le Palmier" maxlength="150" required />
                </div>
                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" rows="5" maxlength="2000"
                              placeholder="Décrivez l'établissement, sa spécialité, son ambiance..." required
                    >{{ old('description', $etablissement->description ?? '') }}</textarea>
                    <p class="form-hint">Spécialité, ambiance, clientèle, points forts…</p>
                </div>
                <div class="form-group">
                    <label for="adresse">Adresse complète *</label>
                    <input type="text" id="adresse" name="adresse" value="{{ old('adresse', $etablissement->adresse ?? '') }}"
                           placeholder="ex : Quartier Cadjehoun, près du Carrefour..." maxlength="255" required />
                </div>
                <div class="form-group">
                    <label for="fourchette_prix">Fourchette de prix</label>
                    <input type="text" id="fourchette_prix" name="fourchette_prix"
                           value="{{ old('fourchette_prix', $etablissement->fourchette_prix ?? '') }}"
                           placeholder="ex : 1 500 – 5 000 FCFA" maxlength="100" />
                </div>
            </div>

            {{-- ══ CONTACTS ═══════════════════════════════════════════ --}}
            <div class="form-section">
                <h2>📞 Contacts</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <div class="phone-input-group">
                            <span class="phone-prefix">+229</span>
                            <input type="text" id="telephone" name="telephone"
                                   value="{{ old('telephone', $etablissement->telephone ?? '01') }}"
                                   placeholder="01 00 00 00 00" inputmode="numeric" maxlength="14"
                                   data-benin-phone data-required="false" />
                        </div>
                        <p class="form-hint">Laissez <strong>01</strong> pour ignorer.</p>
                    </div>
                    <div class="form-group">
                        <label for="whatsapp">WhatsApp</label>
                        <div class="phone-input-group">
                            <span class="phone-prefix">+229</span>
                            <input type="text" id="whatsapp" name="whatsapp"
                                   value="{{ old('whatsapp', $etablissement->whatsapp ?? '01') }}"
                                   placeholder="01 00 00 00 00" inputmode="numeric" maxlength="14"
                                   data-benin-phone data-required="false" />
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email"
                               value="{{ old('email', $etablissement->email ?? '') }}"
                               placeholder="contact@exemple.com" maxlength="150" />
                    </div>
                    <div class="form-group">
                        <label for="site_web">Site web</label>
                        <input type="url" id="site_web" name="site_web"
                               value="{{ old('site_web', $etablissement->site_web ?? '') }}"
                               placeholder="https://..." maxlength="255" />
                    </div>
                </div>
            </div>

            {{-- ══ PHOTO PRINCIPALE ═══════════════════════════════════ --}}
            <div class="form-section">
                <h2>🖼️ Photo principale <small style="font-weight:400;font-size:0.85rem;color:var(--muted)">(optionnel)</small></h2>
                @if(isset($etablissement) && $etablissement->photo_principale)
                    <div class="current-photo">
                        <img src="{{ Storage::url($etablissement->photo_principale) }}" alt="Photo actuelle">
                        <div>
                            <p style="font-size:0.82rem;font-weight:600;color:var(--dark);margin-bottom:0.4rem">Photo actuelle</p>
                            <div class="form-check">
                                <input type="checkbox" id="supprimer_photo_principale" name="supprimer_photo_principale" value="1" />
                                <label for="supprimer_photo_principale" style="font-size:0.83rem;color:var(--danger);cursor:pointer">Supprimer cette photo</label>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="upload-zone" id="mainPhotoZone">
                    <input type="file" id="photo_principale" name="photo_principale"
                           accept="image/jpeg,image/png,image/webp" onchange="previewMainPhoto(this)" />
                    <label for="photo_principale" class="upload-label">
                        <span class="upload-icon">🖼️</span>
                        <span class="upload-text">{{ isset($etablissement) && $etablissement->photo_principale ? 'Remplacer la photo principale' : 'Choisir une photo principale' }}</span>
                        <span class="upload-hint">JPG, PNG ou WebP</span>
                    </label>
                    <div id="mainPhotoPreview" class="photo-preview-wrap" style="display:none">
                        <img id="mainPhotoImg" src="" alt="Aperçu" />
                        <button type="button" class="btn-remove-preview" onclick="removeMainPhoto()">✕ Supprimer</button>
                    </div>
                </div>
            </div>

            {{-- ══ GALERIE ══════════════════════════════════════════════ --}}
            <div class="form-section">
                <h2>📷 Galerie de photos <small style="font-weight:400;font-size:0.85rem;color:var(--muted)">(optionnel — sans limite)</small></h2>

                {{-- Photos existantes --}}
                @if(isset($etablissement) && $etablissement->photos->count() > 0)
                    <p class="form-hint" style="margin-bottom:0.6rem">Cochez les photos à supprimer, puis ajoutez-en de nouvelles ci-dessous.</p>
                    <div class="media-grid">
                        @foreach($etablissement->photos as $photo)
                            <div class="media-item">
                                <img src="{{ Storage::url($photo->url) }}" alt="Photo galerie">
                                <a href="{{ Storage::url($photo->url) }}" target="_blank" class="media-open" title="Ouvrir"></a>
                                <input type="checkbox" name="supprimer_photos[]" value="{{ $photo->id }}"
                                       class="media-delete-cb" title="Cocher pour supprimer" />
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="upload-zone">
                    <input type="file" id="photos" name="photos[]" multiple
                           accept="image/jpeg,image/png,image/webp" onchange="previewGalerie(this)" />
                    <label for="photos" class="upload-label">
                        <span class="upload-icon">📷</span>
                        <span class="upload-text">Ajouter des photos</span>
                        <span class="upload-hint">JPG, PNG ou WebP · Ctrl ou ⌘ pour sélection multiple</span>
                    </label>
                </div>
                <div id="galeriePreview" class="media-grid" style="margin-top:0.75rem"></div>
            </div>

            {{-- ══ MENU ═════════════════════════════════════════════════ --}}
            <div class="form-section">
                <h2>🍽️ Menu <small style="font-weight:400;font-size:0.85rem;color:var(--muted)">(optionnel — photos et/ou PDFs)</small></h2>
                <p class="form-hint" style="margin-bottom:1rem">
                    Ajoutez autant de pages de menu que nécessaire. Chaque fichier sera cliquable sur la fiche.
                    Les PDFs s'ouvrent directement dans le navigateur.
                </p>

                {{-- Menus existants --}}
                @if(isset($etablissement) && $etablissement->menus->count() > 0)
                    <p class="form-hint" style="margin-bottom:0.6rem">Cochez les fichiers à supprimer.</p>
                    <div class="media-grid">
                        @foreach($etablissement->menus as $menu)
                            <div class="media-item {{ $menu->is_pdf ? 'media-item--pdf' : '' }}">
                                @if($menu->is_pdf)
                                    <span>📄</span>
                                    <p>{{ basename($menu->url) }}</p>
                                @else
                                    <img src="{{ Storage::url($menu->url) }}" alt="Menu">
                                @endif
                                <a href="{{ Storage::url($menu->url) }}" target="_blank" class="media-open" title="Ouvrir"></a>
                                <input type="checkbox" name="supprimer_menus[]" value="{{ $menu->id }}"
                                       class="media-delete-cb" title="Cocher pour supprimer" />
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="upload-zone">
                    <input type="file" id="menus" name="menus[]" multiple
                           accept="image/jpeg,image/png,image/webp,application/pdf"
                           onchange="previewMenus(this)" />
                    <label for="menus" class="upload-label">
                        <span class="upload-icon">🍽️</span>
                        <span class="upload-text">Ajouter des pages de menu</span>
                        <span class="upload-hint">JPG, PNG, WebP ou PDF · Ctrl ou ⌘ pour sélection multiple</span>
                    </label>
                </div>
                <div id="menusPreview" class="media-grid" style="margin-top:0.75rem"></div>
            </div>

            {{-- ══ SERVICES ══════════════════════════════════════════════ --}}
            <div class="form-section">
                <h2>✅ Services proposés <small style="font-weight:400;font-size:0.85rem;color:var(--muted)">(max. 8)</small></h2>
                <p class="form-hint" style="margin-bottom:1rem">Ex : WiFi, Climatisation, Parking, Petit-déjeuner…</p>
                <div class="services-inputs" id="servicesInputs">
                    @if(isset($etablissement) && $etablissement->services->count() > 0)
                        @foreach($etablissement->services as $service)
                            <input type="text" name="services[]"
                                   value="{{ old('services.' . $loop->index, $service->libelle) }}"
                                   placeholder="Service" maxlength="60" />
                        @endforeach
                    @else
                        @php $servicesOld = old('services', []); @endphp
                        @for($i = 0; $i < max(3, count($servicesOld)); $i++)
                            <input type="text" name="services[]" value="{{ $servicesOld[$i] ?? '' }}"
                                   placeholder="Service {{ $i + 1 }}" maxlength="60" />
                        @endfor
                    @endif
                </div>
                <button type="button" class="btn-add-service" onclick="addService()">➕ Ajouter un service</button>
            </div>

            {{-- ══ HORAIRES ══════════════════════════════════════════════ --}}
            <div class="form-section">
                <h2>🕐 Horaires d'ouverture <small style="font-weight:400;font-size:0.85rem;color:var(--muted)">(optionnel)</small></h2>
                <div class="horaires-admin-grid">
                    @foreach($jours as $jour)
                        @php
                            $horaires = $etablissement->horaires ?? [];
                            $horaire  = old("horaires.{$jour}", $horaires[$jour] ?? null);
                            $ouvert   = !is_null($horaire) && $horaire !== 'Fermé';
                            $debut    = $ouvert ? (explode(' – ', $horaire)[0] ?? '08:00') : '08:00';
                            $fin      = $ouvert ? (explode(' – ', $horaire)[1] ?? '18:00') : '18:00';
                        @endphp
                        <div class="horaire-admin-row">
                            <label class="horaire-toggle-label">
                                <input type="checkbox" name="horaires[{{ $jour }}][ouvert]" value="1"
                                       {{ $ouvert ? 'checked' : '' }} onchange="toggleHoraire(this)" />
                                <span class="horaire-jour-name">{{ substr($jour, 0, 3) }}</span>
                            </label>
                            <div class="horaire-times-wrap {{ $ouvert ? '' : 'horaire-disabled' }}">
                                <input type="time" name="horaires[{{ $jour }}][debut]" value="{{ $debut }}"
                                       {{ !$ouvert ? 'disabled' : '' }} class="horaire-time-input" />
                                <span class="horaire-sep">–</span>
                                <input type="time" name="horaires[{{ $jour }}][fin]" value="{{ $fin }}"
                                       {{ !$ouvert ? 'disabled' : '' }} class="horaire-time-input" />
                            </div>
                            <span class="horaire-closed-label {{ $ouvert ? 'hidden' : '' }}">Fermé</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.etablissements') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-submit">
                    {{ isset($etablissement) ? '💾 Enregistrer les modifications' : '🏪 Créer l\'établissement' }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ── Téléphone béninois ────────────────────────────────────────────────────
(function () {
    function onlyDigits(v) { return (v || '').replace(/\D+/g, ''); }
    function toLocalDigits(v) {
        let d = onlyDigits(v);
        if (d.startsWith('229')) d = d.slice(3);
        if (!d.length) return '01';
        if (d.startsWith('01')) return d.slice(0, 10);
        return ('01' + d.slice(0, 8)).slice(0, 10);
    }
    function formatPhone(v) { return toLocalDigits(v).match(/.{1,2}/g)?.join(' ') ?? '01'; }
    document.querySelectorAll('[data-benin-phone]').forEach(input => {
        input.value = formatPhone(input.value || '01');
        input.addEventListener('input', () => input.value = formatPhone(input.value));
        input.addEventListener('paste', e => { e.preventDefault(); input.value = formatPhone((e.clipboardData || window.clipboardData).getData('text')); });
    });
    document.getElementById('etablissementForm').addEventListener('submit', () => {
        document.querySelectorAll('[data-benin-phone]').forEach(input => {
            const d = toLocalDigits(input.value);
            input.value = (input.dataset.required !== 'true' && d === '01') ? '' : formatPhone(d);
        });
    });
})();

// ── Horaires ──────────────────────────────────────────────────────────────
function toggleHoraire(cb) {
    const row   = cb.closest('.horaire-admin-row');
    const times = row.querySelector('.horaire-times-wrap');
    const closed = row.querySelector('.horaire-closed-label');
    times.querySelectorAll('input').forEach(i => i.disabled = !cb.checked);
    times.classList.toggle('horaire-disabled', !cb.checked);
    closed.classList.toggle('hidden', cb.checked);
}

// ── Services ──────────────────────────────────────────────────────────────
function addService() {
    const c = document.getElementById('servicesInputs');
    if (c.querySelectorAll('input').length >= 8) { alert('Maximum 8 services.'); return; }
    const i = document.createElement('input');
    i.type = 'text'; i.name = 'services[]';
    i.placeholder = 'Service ' + (c.querySelectorAll('input').length + 1); i.maxLength = 60;
    c.appendChild(i); i.focus();
}

// ── Photo principale ──────────────────────────────────────────────────────
function previewMainPhoto(input) {
    if (!input.files?.[0]) return;
    const r = new FileReader();
    r.onload = e => {
        document.getElementById('mainPhotoImg').src = e.target.result;
        document.getElementById('mainPhotoPreview').style.display = 'flex';
        document.querySelector('#mainPhotoZone .upload-label').style.display = 'none';
    };
    r.readAsDataURL(input.files[0]);
}
function removeMainPhoto() {
    document.getElementById('photo_principale').value = '';
    document.getElementById('mainPhotoPreview').style.display = 'none';
    document.querySelector('#mainPhotoZone .upload-label').style.display = 'flex';
}

// ── Prévisualisation générique (galerie + menu) ───────────────────────────
function buildMediaPreviews(files, container) {
    Array.from(files).forEach(file => {
        const isPdf = file.type === 'application/pdf';
        const item  = document.createElement('div');
        item.className = 'media-item' + (isPdf ? ' media-item--pdf' : '');

        if (isPdf) {
            item.innerHTML = `<span>📄</span><p>${file.name}</p>`;
        } else {
            const r = new FileReader();
            r.onload = e => {
                const img = document.createElement('img');
                img.src = e.target.result; img.alt = file.name;
                item.appendChild(img);
            };
            r.readAsDataURL(file);
        }
        container.appendChild(item);
    });
}

function previewGalerie(input) {
    const c = document.getElementById('galeriePreview');
    c.innerHTML = '';
    if (!input.files?.length) return;
    buildMediaPreviews(input.files, c);
}

function previewMenus(input) {
    const c = document.getElementById('menusPreview');
    c.innerHTML = '';
    if (!input.files?.length) return;
    buildMediaPreviews(input.files, c);
}
</script>

@endsection
