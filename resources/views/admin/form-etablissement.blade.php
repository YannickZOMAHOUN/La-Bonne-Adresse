@extends('layouts.app')

@section('title', isset($etablissement)
    ? 'Modifier « ' . $etablissement->nom . ' » — Administration'
    : 'Ajouter un établissement — Administration')

@section('content')
<div class="form-page">
    <div class="form-inner">

        {{-- ── EN-TÊTE ──────────────────────────────────────────────────── --}}
        <div class="form-header">
            <a href="{{ route('admin.etablissements') }}" class="btn-back">← Retour</a>
            <div>
                <span class="admin-badge">⚙️ Administration</span>
                <h1>{{ isset($etablissement) ? '✏️ Modifier un établissement' : '➕ Ajouter un établissement' }}</h1>
            </div>
        </div>

        {{-- ── ERREURS ──────────────────────────────────────────────────── --}}
        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        {{-- ── FORMULAIRE ──────────────────────────────────────────────── --}}
        <form
            method="POST"
            action="{{ isset($etablissement)
                ? route('admin.update-etablissement', $etablissement)
                : route('admin.store-etablissement') }}"
            enctype="multipart/form-data"
            class="etab-form"
            id="etablissementForm"
        >
            @csrf
            @isset($etablissement)
                @method('PUT')
            @endisset

            {{-- ══════════════════════════════════════════════════════════
                 BLOC ADMIN — statut, propriétaire, vedette
            ══════════════════════════════════════════════════════════ --}}
            <div class="form-section form-section--admin">
                <h2>⚙️ Paramètres administrateur</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="statut">Statut de publication *</label>
                        <select id="statut" name="statut" required>
                            <option value="actif"
                                {{ old('statut', $etablissement->statut ?? 'en_attente') === 'actif' ? 'selected' : '' }}>
                                ✅ Actif — visible immédiatement
                            </option>
                            <option value="en_attente"
                                {{ old('statut', $etablissement->statut ?? 'en_attente') === 'en_attente' ? 'selected' : '' }}>
                                ⏳ En attente de validation
                            </option>
                            <option value="suspendu"
                                {{ old('statut', $etablissement->statut ?? '') === 'suspendu' ? 'selected' : '' }}>
                                🚫 Suspendu
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="user_id">
                            Propriétaire
                            <small style="font-weight:400; color:#888">(optionnel)</small>
                        </label>
                        <select id="user_id" name="user_id">
                            <option value="">— Aucun pour l'instant —</option>
                            @foreach($proprietaires as $proprio)
                                <option value="{{ $proprio->id }}"
                                    {{ old('user_id', $etablissement->user_id ?? '') == $proprio->id ? 'selected' : '' }}>
                                    {{ $proprio->nom }} — {{ $proprio->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input
                            type="checkbox"
                            name="en_vedette"
                            value="1"
                            {{ old('en_vedette', $etablissement->en_vedette ?? false) ? 'checked' : '' }}
                        />
                        <span>⭐ Mettre en vedette</span>
                    </label>
                    <small class="field-help" style="margin-left:1.6rem">
                        Apparaît dans les sections "Coups de cœur" et "À la une".
                    </small>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 INFORMATIONS PRINCIPALES
            ══════════════════════════════════════════════════════════ --}}
            <div class="form-section">
                <h2>📋 Informations principales</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="ville_id">Ville *</label>
                        <select id="ville_id" name="ville_id" required>
                            <option value="">Choisir une ville</option>
                            @foreach($villes as $ville)
                                <option value="{{ $ville->id }}"
                                    {{ old('ville_id', $etablissement->ville_id ?? '') == $ville->id ? 'selected' : '' }}>
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
                                <option value="{{ $cat->id }}"
                                    {{ old('categorie_id', $etablissement->categorie_id ?? '') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->emoji }} {{ $cat->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nom">Nom de l'établissement *</label>
                    <input
                        type="text"
                        id="nom"
                        name="nom"
                        value="{{ old('nom', $etablissement->nom ?? '') }}"
                        placeholder="ex : Restaurant Le Palmier"
                        maxlength="150"
                        required
                    />
                </div>

                <div class="form-group">
                    <label for="description">
                        Description * <small>(minimum 30 caractères)</small>
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        maxlength="2000"
                        placeholder="Décrivez l'établissement, sa spécialité, son ambiance..."
                        required
                    >{{ old('description', $etablissement->description ?? '') }}</textarea>
                    <small class="field-help">Soyez clair et précis : spécialité, ambiance, clientèle, points forts…</small>
                </div>

                <div class="form-group">
                    <label for="adresse">Adresse complète *</label>
                    <input
                        type="text"
                        id="adresse"
                        name="adresse"
                        value="{{ old('adresse', $etablissement->adresse ?? '') }}"
                        placeholder="ex : Quartier Cadjehoun, près du Carrefour..."
                        maxlength="255"
                        required
                    />
                </div>

                <div class="form-group">
                    <label for="fourchette_prix">Fourchette de prix</label>
                    <input
                        type="text"
                        id="fourchette_prix"
                        name="fourchette_prix"
                        value="{{ old('fourchette_prix', $etablissement->fourchette_prix ?? '') }}"
                        placeholder="ex : 1 500 – 5 000 FCFA"
                        maxlength="100"
                    />
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 CONTACTS
            ══════════════════════════════════════════════════════════ --}}
            <div class="form-section">
                <h2>📞 Contacts</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <div class="phone-input-group">
                            <span class="phone-prefix">+229</span>
                            <input
                                type="text"
                                id="telephone"
                                name="telephone"
                                value="{{ old('telephone', $etablissement->telephone ?? '01') }}"
                                placeholder="01 00 00 00 00"
                                inputmode="numeric"
                                maxlength="14"
                                data-benin-phone
                                data-required="false"
                            />
                        </div>
                        <small class="field-help">
                            Le <strong>+229</strong> est ajouté automatiquement.
                            Si vous laissez simplement <strong>01</strong>, le champ sera ignoré.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="whatsapp">WhatsApp</label>
                        <div class="phone-input-group">
                            <span class="phone-prefix">+229</span>
                            <input
                                type="text"
                                id="whatsapp"
                                name="whatsapp"
                                value="{{ old('whatsapp', $etablissement->whatsapp ?? '01') }}"
                                placeholder="01 00 00 00 00"
                                inputmode="numeric"
                                maxlength="14"
                                data-benin-phone
                                data-required="false"
                            />
                        </div>
                        <small class="field-help">
                            Même principe : complétez seulement les 8 derniers chiffres.
                        </small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $etablissement->email ?? '') }}"
                            placeholder="contact@votre-etablissement.bj"
                            maxlength="150"
                        />
                    </div>

                    <div class="form-group">
                        <label for="site_web">Site web</label>
                        <input
                            type="url"
                            id="site_web"
                            name="site_web"
                            value="{{ old('site_web', $etablissement->site_web ?? '') }}"
                            placeholder="https://..."
                            maxlength="255"
                        />
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 HORAIRES
            ══════════════════════════════════════════════════════════ --}}
            @include('proprietaire.partials.horaires', [
                'horaires' => $etablissement->horaires ?? null
            ])

            {{-- ══════════════════════════════════════════════════════════
                 SERVICES
            ══════════════════════════════════════════════════════════ --}}
            <div class="form-section">
                <h2>✅ Services proposés</h2>
                <p class="form-hint">
                    Ajoutez jusqu'à 8 services. Ex : WiFi, Climatisation, Parking, Petit-déjeuner…
                </p>

                <div class="services-inputs" id="servicesInputs">
                    @php
                        $servicesExistants = isset($etablissement)
                            ? $etablissement->services->pluck('libelle')->toArray()
                            : [];
                        $servicesOld = old('services', $servicesExistants);
                    @endphp

                    @for($i = 0; $i < max(3, count($servicesOld)); $i++)
                        <input
                            type="text"
                            name="services[]"
                            value="{{ $servicesOld[$i] ?? '' }}"
                            placeholder="Service {{ $i + 1 }}"
                            maxlength="60"
                        />
                    @endfor
                </div>

                <button type="button" class="btn-add-service" onclick="addService()">
                    ➕ Ajouter un service
                </button>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 PHOTO PRINCIPALE
            ══════════════════════════════════════════════════════════ --}}
            <div class="form-section">
                <h2>📸 Photo principale</h2>
                <p class="form-hint">
                    Cette photo sera affichée sur la carte de l'établissement dans les résultats.
                </p>

                {{-- Photo actuelle (mode édition uniquement) --}}
                @isset($etablissement)
                    @if($etablissement->photo_principale)
                        <div class="current-photo-wrap">
                            <p class="current-photo-label">📌 Photo actuelle</p>
                            <img
                                src="{{ asset('storage/' . $etablissement->photo_principale) }}"
                                alt="Photo principale actuelle"
                                class="current-photo-thumb"
                            />
                            <label class="checkbox-label" style="margin-top: 0.75rem">
                                <input type="checkbox" name="supprimer_photo_principale" value="1" />
                                <span>🗑 Supprimer cette photo</span>
                            </label>
                        </div>
                    @endif
                @endisset

                <div class="upload-zone" id="mainPhotoZone">
                    <input
                        type="file"
                        id="photo_principale"
                        name="photo_principale"
                        accept="image/jpeg,image/png,image/webp"
                        onchange="previewMainPhoto(this)"
                    />
                    <label for="photo_principale" class="upload-label">
                        <span class="upload-icon">🖼️</span>
                        <span class="upload-text">
                            @isset($etablissement)
                                @if($etablissement->photo_principale)
                                    Cliquez pour remplacer la photo
                                @else
                                    Cliquez pour choisir une photo
                                @endif
                            @else
                                Cliquez pour choisir une photo
                            @endisset
                        </span>
                        <span class="upload-hint">JPG, PNG ou WebP — Max 3 Mo — Min 400×300 px</span>
                    </label>

                    <div id="mainPhotoPreview" class="photo-preview-wrap" style="display:none">
                        <img id="mainPhotoImg" src="" alt="Aperçu" />
                        <button type="button" class="btn-remove-preview" onclick="removeMainPhoto()">
                            ✕ Supprimer
                        </button>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 GALERIE
            ══════════════════════════════════════════════════════════ --}}
            <div class="form-section">
                <h2>
                    🖼️ Galerie de photos
                    <small style="font-weight:400; color:var(--muted)">(optionnel)</small>
                </h2>

                {{-- Photos existantes (mode édition) --}}
                @isset($etablissement)
                    @if($etablissement->photos->count())
                        <p class="form-hint">📌 Photos actuelles — cochez pour en supprimer :</p>
                        <div class="galerie-existing-grid">
                            @foreach($etablissement->photos->sortBy('ordre') as $photo)
                                <div class="galerie-existing-item">
                                    <img
                                        src="{{ asset('storage/' . $photo->url) }}"
                                        alt="Photo galerie {{ $loop->iteration }}"
                                    />
                                    <label class="galerie-delete-label">
                                        <input
                                            type="checkbox"
                                            name="supprimer_photos[]"
                                            value="{{ $photo->id }}"
                                        />
                                        🗑 Supprimer
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @php $slotsRestants = 6 - $etablissement->photos->count(); @endphp

                    @if($slotsRestants > 0)
                        <p class="form-hint" style="margin-top:1rem">
                            ➕ Ajouter de nouvelles photos
                            ({{ $slotsRestants }} emplacement{{ $slotsRestants > 1 ? 's' : '' }} disponible{{ $slotsRestants > 1 ? 's' : '' }}) :
                        </p>
                    @endif
                @endisset

                @php
                    $slotsMax = isset($etablissement) ? (6 - $etablissement->photos->count()) : 6;
                @endphp

                @if($slotsMax > 0)
                    <p class="form-hint">
                        Maintenez <kbd>Ctrl</kbd> pour sélectionner plusieurs fichiers à la fois.
                    </p>

                    <div class="upload-zone">
                        <input
                            type="file"
                            id="photos"
                            name="photos[]"
                            multiple
                            accept="image/jpeg,image/png,image/webp"
                            onchange="previewGalerie(this, {{ $slotsMax }})"
                        />
                        <label for="photos" class="upload-label">
                            <span class="upload-icon">📷</span>
                            <span class="upload-text">Cliquez pour choisir vos photos</span>
                            <span class="upload-hint">
                                Maximum {{ $slotsMax }} photo{{ $slotsMax > 1 ? 's' : '' }}
                                · JPG, PNG ou WebP · Max 3 Mo chacune
                            </span>
                        </label>
                    </div>

                    <div id="galeriePreview" class="galerie-preview-grid"></div>
                @else
                    <p class="form-hint" style="color:#d97706">
                        ⚠️ La galerie est complète (6/6). Supprimez des photos existantes pour en ajouter de nouvelles.
                    </p>
                @endif
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 ACTIONS
            ══════════════════════════════════════════════════════════ --}}
            <div class="form-actions">
                <a href="{{ route('admin.etablissements') }}" class="btn-secondary">
                    Annuler
                </a>

                @isset($etablissement)
                    <button type="submit" class="btn-submit btn-submit--edit">
                        💾 Enregistrer les modifications
                    </button>
                @else
                    <button type="submit" class="btn-submit">
                        🏪 Créer l'établissement
                    </button>
                @endisset
            </div>

        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
/* ── Badge admin ── */
.admin-badge {
    display: inline-block;
    background: #16a34a;
    color: #fff;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    padding: 0.2rem 0.65rem;
    border-radius: 20px;
    margin-bottom: 0.4rem;
}

/* ── Section admin ── */
.form-section--admin {
    background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
    border: 1.5px solid #bbf7d0;
    position: relative;
}
.form-section--admin::before {
    content: 'ADMIN ONLY';
    position: absolute;
    top: -0.65rem;
    right: 1.2rem;
    background: #16a34a;
    color: #fff;
    font-size: 0.65rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    padding: 0.15rem 0.65rem;
    border-radius: 20px;
}

/* ── Phone input ── */
.phone-input-group {
    display: flex;
    align-items: center;
    border: 1px solid #d9dde5;
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
}
.phone-input-group:focus-within {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.10);
}
.phone-prefix {
    background: #f6f8fb;
    color: #111827;
    font-weight: 700;
    padding: 0.95rem 1rem;
    border-right: 1px solid #e5e7eb;
    white-space: nowrap;
}
.phone-input-group input {
    border: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    flex: 1;
}

/* ── Checkbox label ── */
.checkbox-label {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    font-weight: 500;
    color: #374151;
}
.checkbox-label input[type="checkbox"] {
    accent-color: #16a34a;
    width: 16px;
    height: 16px;
    flex-shrink: 0;
}

/* ── Photo actuelle ── */
.current-photo-wrap {
    margin-bottom: 1rem;
    padding: 1rem;
    background: #f9fafb;
    border: 1px dashed #d1d5db;
    border-radius: 10px;
}
.current-photo-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 0.6rem;
}
.current-photo-thumb {
    display: block;
    width: 200px;
    height: 130px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

/* ── Galerie existante ── */
.galerie-existing-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 0.75rem;
    margin-bottom: 1rem;
}
.galerie-existing-item {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}
.galerie-existing-item img {
    width: 100%;
    height: 90px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}
.galerie-delete-label {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.78rem;
    color: #dc2626;
    font-weight: 500;
    cursor: pointer;
}
.galerie-delete-label input[type="checkbox"] {
    accent-color: #dc2626;
}

/* ── Bouton modifier ── */
.btn-submit--edit {
    background: #2563eb;
}
.btn-submit--edit:hover {
    background: #1d4ed8;
}

/* ── Field help ── */
.field-help {
    display: block;
    margin-top: 0.45rem;
    color: #6b7280;
    line-height: 1.45;
    font-size: 0.85rem;
}
</style>
@endpush

@push('scripts')
<script>
// ── Format téléphone Bénin ──────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const phoneInputs = document.querySelectorAll('[data-benin-phone]');

    function onlyDigits(value) {
        return (value || '').replace(/\D+/g, '');
    }

    function toLocalDigits(value) {
        let digits = onlyDigits(value);
        if (digits.startsWith('229')) digits = digits.slice(3);
        if (!digits.length) return '01';
        if (digits.startsWith('01')) return digits.slice(0, 10);
        return ('01' + digits.slice(0, 8)).slice(0, 10);
    }

    function formatPhone(value) {
        const digits = toLocalDigits(value);
        return digits.match(/.{1,2}/g)?.join(' ') ?? '01';
    }

    phoneInputs.forEach((input) => {
        input.value = formatPhone(input.value || '01');

        input.addEventListener('focus', () => {
            if (!onlyDigits(input.value)) input.value = '01';
        });

        input.addEventListener('input', () => {
            input.value = formatPhone(input.value);
        });

        input.addEventListener('paste', (event) => {
            event.preventDefault();
            const pasted = (event.clipboardData || window.clipboardData).getData('text');
            input.value = formatPhone(pasted);
        });
    });

    const form = document.getElementById('etablissementForm');
    if (form) {
        form.addEventListener('submit', () => {
            phoneInputs.forEach((input) => {
                const digits    = toLocalDigits(input.value);
                const isRequired = input.dataset.required === 'true';
                if (!isRequired && digits === '01') {
                    input.value = '';
                    return;
                }
                input.value = formatPhone(digits);
            });
        });
    }
});

// ── Services dynamiques ────────────────────────────────────────────────────
function addService() {
    const container = document.getElementById('servicesInputs');
    const count = container.querySelectorAll('input').length;

    if (count >= 8) {
        alert('Maximum 8 services autorisés.');
        return;
    }

    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'services[]';
    input.placeholder = 'Service ' + (count + 1);
    input.maxLength = 60;
    container.appendChild(input);
    input.focus();
}

// ── Aperçu photo principale ────────────────────────────────────────────────
function previewMainPhoto(input) {
    if (!input.files || !input.files[0]) return;

    const reader = new FileReader();
    reader.onload = function (e) {
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

// ── Aperçu galerie ─────────────────────────────────────────────────────────
function previewGalerie(input, max) {
    const container = document.getElementById('galeriePreview');
    container.innerHTML = '';

    if (!input.files || input.files.length === 0) return;

    const files = Array.from(input.files).slice(0, max);

    files.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function (e) {
            const div = document.createElement('div');
            div.className = 'galerie-thumb-preview';
            div.innerHTML = `
                <img src="${e.target.result}" alt="Photo ${index + 1}">
                <span class="galerie-thumb-num">${index + 1}</span>
            `;
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    });

    if (input.files.length > max) {
        const note = document.createElement('p');
        note.className = 'form-hint';
        note.style.marginTop = '0.5rem';
        note.textContent = `⚠️ Vous avez sélectionné ${input.files.length} photos. Le maximum autorisé est ${max}.`;
        container.appendChild(note);
    }
}
</script>
@endpush
