@extends('layouts.app')

@section('title', isset($etablissement)
    ? 'Modifier « ' . $etablissement->nom . ' » — Administration'
    : 'Ajouter un établissement — Administration')

@section('content')

{{-- ── STYLES SPÉCIFIQUES À CETTE VUE ──────────────────────────────── --}}
<style>
.admin-form-badge {
    display: flex; align-items: center; gap: 0.75rem;
    background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px;
    padding: 0.65rem 1.1rem; font-size: 0.82rem; font-weight: 600;
    color: #92400e; margin-bottom: 1.8rem;
}
.admin-section-highlight { border-color: #fde68a !important; background: #fffdf5 !important; }
.admin-section-highlight h2 { color: #92400e !important; }

.phone-input-group {
    display: flex; align-items: stretch;
    border: 1px solid var(--border); border-radius: 10px;
    overflow: hidden; background: var(--cream); transition: border-color 0.2s;
}
.phone-input-group:focus-within { border-color: var(--green); background: #fff; }
.phone-prefix {
    padding: 0.65rem 0.9rem; background: var(--cream);
    border-right: 1px solid var(--border); font-size: 0.88rem;
    font-weight: 700; color: var(--muted);
    display: flex; align-items: center; white-space: nowrap;
}
.phone-input-group input {
    border: none !important; border-radius: 0 !important;
    background: transparent !important; flex: 1;
    padding: 0.65rem 0.9rem; font-size: 0.93rem;
    outline: none; box-shadow: none !important;
}

.horaires-admin-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 0.55rem;
}
.horaire-admin-row {
    display: flex; align-items: center; gap: 0.75rem;
    background: var(--cream); border: 1px solid var(--border);
    border-radius: 10px; padding: 0.6rem 0.9rem;
}
.horaire-toggle-label {
    display: flex; align-items: center; gap: 0.5rem;
    cursor: pointer; flex-shrink: 0; width: 72px;
}
.horaire-toggle-label input[type="checkbox"] {
    width: 16px; height: 16px; accent-color: var(--green); cursor: pointer;
}
.horaire-jour-name { font-size: 0.82rem; font-weight: 700; color: var(--dark); }
.horaire-times-wrap { display: flex; align-items: center; gap: 0.4rem; flex: 1; }
.horaire-times-wrap.horaire-disabled { display: none; }
.horaire-time-input {
    border: 1px solid var(--border) !important; border-radius: 7px !important;
    padding: 0.4rem 0.5rem !important; font-size: 0.85rem !important;
    background: #fff !important; flex: 1; min-width: 0;
    font-family: 'DM Sans', sans-serif; outline: none; transition: border-color 0.2s;
}
.horaire-time-input:focus { border-color: var(--green) !important; }
.horaire-time-input:disabled { background: var(--cream) !important; color: var(--muted) !important; }
.horaire-sep { font-size: 0.85rem; color: var(--muted); flex-shrink: 0; }
.horaire-closed-label { font-size: 0.78rem; color: var(--muted); font-style: italic; flex: 1; }
.horaire-closed-label.hidden { display: none; }

@media (max-width: 640px) {
    .horaires-admin-grid { grid-template-columns: 1fr; }
}
</style>

<div class="form-page">
    <div class="form-inner">

        {{-- ── EN-TÊTE ──────────────────────────────────────────────────── --}}
        <div class="form-header">
            <a href="{{ route('admin.etablissements') }}" class="btn-back">← Retour</a>
            <h1>{{ isset($etablissement) ? '✏️ Modifier un établissement' : '➕ Ajouter un établissement' }}</h1>
        </div>

        {{-- ── BADGE ADMIN ──────────────────────────────────────────────── --}}
        <div class="admin-form-badge">
            <span>⚙️ Espace administration</span>
            @isset($etablissement)
                <span class="badge badge--{{ $etablissement->statut }}">
                    @if($etablissement->statut === 'actif') ✅ Actif
                    @elseif($etablissement->statut === 'en_attente') ⏳ En attente
                    @else 🚫 Suspendu
                    @endif
                </span>
            @endisset
        </div>

        {{-- ── ERREURS ──────────────────────────────────────────────────── --}}
        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:1.5rem; border-radius:10px">
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form
            method="POST"
            action="{{ isset($etablissement)
                ? route('admin.update-etablissement', $etablissement)
                : route('admin.store-etablissement') }}"
            enctype="multipart/form-data"
            id="etablissementForm"
        >
            @csrf
            @isset($etablissement) @method('PUT') @endisset

            {{-- ══ PARAMÈTRES ADMIN ═════════════════════════════════════ --}}
            <div class="form-section admin-section-highlight">
                <h2>⚙️ Paramètres administrateur</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="statut">Statut de publication *</label>
                        <select id="statut" name="statut" required>
                            <option value="actif"      {{ old('statut', $etablissement->statut ?? 'en_attente') === 'actif'      ? 'selected' : '' }}>✅ Actif — visible immédiatement</option>
                            <option value="en_attente" {{ old('statut', $etablissement->statut ?? 'en_attente') === 'en_attente' ? 'selected' : '' }}>⏳ En attente de validation</option>
                            <option value="suspendu"   {{ old('statut', $etablissement->statut ?? '') === 'suspendu'             ? 'selected' : '' }}>🚫 Suspendu</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="user_id">Propriétaire <small style="font-weight:400; color:var(--muted)">(optionnel)</small></label>
                        <select id="user_id" name="user_id">
                            <option value="">— Aucun / conserver l'actuel —</option>
                            @foreach($proprietaires as $proprio)
                                <option value="{{ $proprio->id }}"
                                    {{ old('user_id', $etablissement->user_id ?? '') == $proprio->id ? 'selected' : '' }}>
                                    {{ $proprio->nom }} — {{ $proprio->email }}
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
                            <span class="form-hint" style="display:inline; font-weight:400">— apparaît dans les sections "Coups de cœur"</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- ══ INFORMATIONS PRINCIPALES ═════════════════════════════ --}}
            <div class="form-section">
                <h2>📋 Informations principales</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="ville_id">Ville *</label>
                        <select id="ville_id" name="ville_id" required>
                            <option value="">Choisir une ville</option>
                            @foreach($villes as $ville)
                                <option value="{{ $ville->id }}" {{ old('ville_id', $etablissement->ville_id ?? '') == $ville->id ? 'selected' : '' }}>
                                    {{ $ville->emoji }} {{ $ville->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="categorie_id">Catégorie *</label>
                        <select id="categorie_id" name="categorie_id" required>
                            <option value="">Choisir une catégorie</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('categorie_id', $etablissement->categorie_id ?? '') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->emoji }} {{ $cat->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nom">Nom de l'établissement *</label>
                    <input type="text" id="nom" name="nom"
                           value="{{ old('nom', $etablissement->nom ?? '') }}"
                           placeholder="ex : Restaurant Le Palmier" maxlength="150" required />
                </div>

                <div class="form-group">
                    <label for="description">Description * <small style="font-weight:400; color:var(--muted)">(min. 30 caractères)</small></label>
                    <textarea id="description" name="description" rows="5" maxlength="2000"
                              placeholder="Décrivez l'établissement, sa spécialité, son ambiance..." required
                    >{{ old('description', $etablissement->description ?? '') }}</textarea>
                    <p class="form-hint">Soyez précis : spécialité, ambiance, clientèle, points forts…</p>
                </div>

                <div class="form-group">
                    <label for="adresse">Adresse complète *</label>
                    <input type="text" id="adresse" name="adresse"
                           value="{{ old('adresse', $etablissement->adresse ?? '') }}"
                           placeholder="ex : Quartier Cadjehoun, près du Carrefour..." maxlength="255" required />
                </div>

                <div class="form-group">
                    <label for="fourchette_prix">Fourchette de prix</label>
                    <input type="text" id="fourchette_prix" name="fourchette_prix"
                           value="{{ old('fourchette_prix', $etablissement->fourchette_prix ?? '') }}"
                           placeholder="ex : 1 500 – 5 000 FCFA" maxlength="100" />
                </div>
            </div>

            {{-- ══ CONTACTS ═════════════════════════════════════════════ --}}
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
                        <p class="form-hint">Laissez <strong>01</strong> pour ignorer ce champ.</p>
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

            {{-- ══ PHOTOS ════════════════════════════════════════════════ --}}
            <div class="form-section">
                <h2>📸 Photos</h2>

                <div class="form-group">
                    <label>Photo principale</label>
                    @if(isset($etablissement) && $etablissement->photo_principale)
                        <div class="current-photo">
                            <img src="{{ Storage::url($etablissement->photo_principale) }}" alt="Photo actuelle">
                            <div>
                                <p style="font-size:0.82rem; font-weight:600; color:var(--dark); margin-bottom:0.4rem">Photo actuelle</p>
                                <div class="form-check">
                                    <input type="checkbox" id="supprimer_photo_principale" name="supprimer_photo_principale" value="1" />
                                    <label for="supprimer_photo_principale" style="font-size:0.83rem; color:var(--danger); cursor:pointer">Supprimer cette photo</label>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="upload-zone" id="mainPhotoZone">
                        <input type="file" id="photo_principale" name="photo_principale"
                               accept="image/jpeg,image/png,image/webp"
                               onchange="previewMainPhoto(this)" />
                        <label for="photo_principale" class="upload-label">
                            <span class="upload-icon">🖼️</span>
                            <span class="upload-text">{{ isset($etablissement) && $etablissement->photo_principale ? 'Remplacer la photo principale' : 'Choisir une photo principale' }}</span>
                            <span class="upload-hint">JPG, PNG, WebP — min. 400×300 px — max. 3 Mo</span>
                        </label>
                        <div id="mainPhotoPreview" class="photo-preview-wrap" style="display:none">
                            <img id="mainPhotoImg" src="" alt="Aperçu" />
                            <button type="button" class="btn-remove-preview" onclick="removeMainPhoto()">✕ Supprimer</button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Galerie</label>
                    @if(isset($etablissement) && $etablissement->photos->count() > 0)
                        <div class="galerie-existante" style="margin-bottom:0.5rem">
                            @foreach($etablissement->photos as $photo)
                                <div class="galerie-thumb" style="position:relative">
                                    <img src="{{ Storage::url($photo->url) }}" alt="Photo galerie">
                                    <label style="position:absolute;inset:0;border-radius:8px;cursor:pointer;display:flex;align-items:flex-start;justify-content:flex-end;padding:4px" title="Cocher pour supprimer">
                                        <input type="checkbox" name="supprimer_photos[]" value="{{ $photo->id }}"
                                               style="width:16px;height:16px;accent-color:var(--danger)" />
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <p class="form-hint" style="margin-bottom:0.8rem">Cochez les photos à supprimer.</p>
                    @endif
                    <div class="upload-zone">
                        <input type="file" id="photos" name="photos[]" multiple
                               accept="image/jpeg,image/png,image/webp"
                               onchange="previewGalerie(this)" />
                        <label for="photos" class="upload-label">
                            <span class="upload-icon">📷</span>
                            <span class="upload-text">Choisir des photos de galerie</span>
                            <span class="upload-hint">Maintenir Ctrl pour sélection multiple · max. 3 Mo chacune</span>
                        </label>
                    </div>
                    <div id="galeriePreview" class="galerie-preview-grid"></div>
                </div>
            </div>

            {{-- ══ SERVICES ══════════════════════════════════════════════ --}}
            <div class="form-section">
                <h2>✅ Services proposés <small style="font-weight:400; font-size:0.85rem; color:var(--muted)">(max. 8)</small></h2>
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
                            <input type="text" name="services[]"
                                   value="{{ $servicesOld[$i] ?? '' }}"
                                   placeholder="Service {{ $i + 1 }}" maxlength="60" />
                        @endfor
                    @endif
                </div>
                <button type="button" class="btn-add-service" onclick="addService()">➕ Ajouter un service</button>
            </div>

            {{-- ══ HORAIRES ══════════════════════════════════════════════ --}}
            <div class="form-section">
                <h2>🕐 Horaires d'ouverture <small style="font-weight:400; font-size:0.85rem; color:var(--muted)">(optionnel)</small></h2>

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

            {{-- ══ ACTIONS ════════════════════════════════════════════════ --}}
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
(function () {
    function onlyDigits(v) { return (v || '').replace(/\D+/g, ''); }
    function toLocalDigits(v) {
        let d = onlyDigits(v);
        if (d.startsWith('229')) d = d.slice(3);
        if (!d.length) return '01';
        if (d.startsWith('01')) return d.slice(0, 10);
        return ('01' + d.slice(0, 8)).slice(0, 10);
    }
    function formatPhone(v) {
        const d = toLocalDigits(v);
        return d.match(/.{1,2}/g)?.join(' ') ?? '01';
    }
    document.querySelectorAll('[data-benin-phone]').forEach(input => {
        input.value = formatPhone(input.value || '01');
        input.addEventListener('input', () => { input.value = formatPhone(input.value); });
        input.addEventListener('paste', e => {
            e.preventDefault();
            input.value = formatPhone((e.clipboardData || window.clipboardData).getData('text'));
        });
    });
    document.getElementById('etablissementForm').addEventListener('submit', () => {
        document.querySelectorAll('[data-benin-phone]').forEach(input => {
            const d = toLocalDigits(input.value);
            input.value = (input.dataset.required !== 'true' && d === '01') ? '' : formatPhone(d);
        });
    });
})();

function toggleHoraire(checkbox) {
    const row    = checkbox.closest('.horaire-admin-row');
    const times  = row.querySelector('.horaire-times-wrap');
    const closed = row.querySelector('.horaire-closed-label');
    times.querySelectorAll('input').forEach(i => i.disabled = !checkbox.checked);
    times.classList.toggle('horaire-disabled', !checkbox.checked);
    closed.classList.toggle('hidden', checkbox.checked);
}

function addService() {
    const container = document.getElementById('servicesInputs');
    const count = container.querySelectorAll('input').length;
    if (count >= 8) { alert('Maximum 8 services autorisés.'); return; }
    const input = document.createElement('input');
    input.type = 'text'; input.name = 'services[]';
    input.placeholder = 'Service ' + (count + 1); input.maxLength = 60;
    container.appendChild(input);
    input.focus();
}

function previewMainPhoto(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('mainPhotoImg').src = e.target.result;
        document.getElementById('mainPhotoPreview').style.display = 'flex';
        document.querySelector('#mainPhotoZone .upload-label').style.display = 'none';
    };
    reader.readAsDataURL(input.files[0]);
}
function removeMainPhoto() {
    document.getElementById('photo_principale').value = '';
    document.getElementById('mainPhotoPreview').style.display = 'none';
    document.querySelector('#mainPhotoZone .upload-label').style.display = 'flex';
}

function previewGalerie(input) {
    const container = document.getElementById('galeriePreview');
    container.innerHTML = '';
    if (!input.files || !input.files.length) return;
    Array.from(input.files).slice(0, 6).forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'galerie-thumb-preview';
            div.innerHTML = `<img src="${e.target.result}" alt="Photo ${i+1}"><span class="galerie-thumb-num">${i+1}</span>`;
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
    if (input.files.length > 6) {
        const p = document.createElement('p');
        p.className = 'form-hint'; p.style.marginTop = '0.5rem';
        p.textContent = '⚠️ Seules les 6 premières photos seront enregistrées.';
        container.appendChild(p);
    }
}
</script>

@endsection
