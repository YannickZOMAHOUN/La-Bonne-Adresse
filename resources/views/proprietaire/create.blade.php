@extends('layouts.app')

@section('title', 'Ajouter un établissement — Bonnes Adresses Bénin')

@section('content')
<div class="form-page">
    <div class="form-inner">

        <div class="form-header">
            <a href="{{ route('proprietaire.dashboard') }}" class="btn-back">← Retour</a>
            <h1>➕ Ajouter un établissement</h1>
        </div>

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('proprietaire.store') }}"
            enctype="multipart/form-data"
            class="etab-form"
            id="etablissementForm"
        >
            @csrf

            {{-- INFORMATIONS PRINCIPALES --}}
            <div class="form-section">
                <h2>📋 Informations principales</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="ville_id">Ville *</label>
                        <select id="ville_id" name="ville_id" required>
                            <option value="">Choisir une ville</option>
                            @foreach($villes as $ville)
                                <option value="{{ $ville->id }}" {{ old('ville_id') == $ville->id ? 'selected' : '' }}>
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
                                <option value="{{ $cat->id }}" {{ old('categorie_id') == $cat->id ? 'selected' : '' }}>
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
                        value="{{ old('nom') }}"
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
                        placeholder="Décrivez votre établissement, sa spécialité, son ambiance..."
                        required
                    >{{ old('description') }}</textarea>
                    <small class="field-help">Soyez clair et précis : spécialité, ambiance, clientèle, points forts…</small>
                </div>

                <div class="form-group">
                    <label for="adresse">Adresse complète *</label>
                    <input
                        type="text"
                        id="adresse"
                        name="adresse"
                        value="{{ old('adresse') }}"
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
                        value="{{ old('fourchette_prix') }}"
                        placeholder="ex : 1 500 – 5 000 FCFA"
                        maxlength="100"
                    />
                </div>
            </div>

            {{-- CONTACTS --}}
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
                                value="{{ old('telephone', '01') }}"
                                placeholder="01 00 00 00 00"
                                inputmode="numeric"
                                maxlength="14"
                                data-benin-phone
                                data-required="false"
                            />
                        </div>

                        <small class="field-help">
                            Le <strong>+229</strong> est ajouté automatiquement. Si vous laissez simplement <strong>01</strong>, le champ sera ignoré.
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
                                value="{{ old('whatsapp', '01') }}"
                                placeholder="01 00 00 00 00"
                                inputmode="numeric"
                                maxlength="14"
                                data-benin-phone
                                data-required="false"
                            />
                        </div>

                        <small class="field-help">
                            Même principe : vous complétez seulement les 8 derniers chiffres.
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
                            value="{{ old('email') }}"
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
                            value="{{ old('site_web') }}"
                            placeholder="https://..."
                            maxlength="255"
                        />
                    </div>
                </div>
            </div>

            {{-- HORAIRES --}}
            @include('proprietaire.partials.horaires', ['horaires' => null])

            {{-- PHOTO PRINCIPALE --}}
            <div class="form-section">
                <h2>📸 Photo principale</h2>
                <p class="form-hint">
                    Cette photo sera affichée sur la carte de votre établissement dans les résultats.
                </p>

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
                        <span class="upload-text">Cliquez pour choisir une photo</span>
                        <span class="upload-hint">JPG, PNG ou WebP — Max 3 Mo</span>
                    </label>

                    <div id="mainPhotoPreview" class="photo-preview-wrap" style="display:none">
                        <img id="mainPhotoImg" src="" alt="Aperçu" />
                        <button type="button" class="btn-remove-preview" onclick="removeMainPhoto()">
                            ✕ Supprimer
                        </button>
                    </div>
                </div>
            </div>

            {{-- GALERIE --}}
            <div class="form-section">
                <h2>
                    🖼️ Galerie de photos
                    <small style="font-weight:400; color:var(--muted)">(optionnel)</small>
                </h2>

                <p class="form-hint">
                    Ajoutez jusqu'à 6 photos supplémentaires.
                    Maintenez <kbd>Ctrl</kbd> pour en sélectionner plusieurs à la fois.
                </p>

                <div class="upload-zone">
                    <input
                        type="file"
                        id="photos"
                        name="photos[]"
                        multiple
                        accept="image/jpeg,image/png,image/webp"
                        onchange="previewGalerie(this)"
                    />

                    <label for="photos" class="upload-label">
                        <span class="upload-icon">📷</span>
                        <span class="upload-text">Cliquez pour choisir vos photos</span>
                        <span class="upload-hint">Maximum 6 photos · JPG, PNG ou WebP · Max 3 Mo chacune</span>
                    </label>
                </div>

                <div id="galeriePreview" class="galerie-preview-grid"></div>
            </div>

            {{-- MENU --}}
            <div class="form-section">
                <h2>🍽️ Menu <small style="font-weight:400; color:var(--muted)">(optionnel)</small></h2>
                <p class="form-hint">
                    Partagez une photo ou un PDF de votre menu.
                    Les visiteurs pourront le consulter directement sur votre fiche.
                </p>

                <div class="upload-zone" id="menuZone">
                    <input type="file" id="menu" name="menu"
                           accept="image/jpeg,image/png,image/webp,application/pdf"
                           onchange="previewMenu(this)" />
                    <label for="menu" class="upload-label">
                        <span class="upload-icon">🍽️</span>
                        <span class="upload-text">Ajouter votre menu</span>
                        <span class="upload-hint">JPG, PNG, WebP ou PDF</span>
                    </label>
                    <div id="menuPreview" class="photo-preview-wrap" style="display:none">
                        <img id="menuImg" src="" alt="Aperçu menu" />
                        <div>
                            <p id="menuFileName" style="font-size:0.83rem;font-weight:600;color:var(--dark)"></p>
                            <button type="button" class="btn-remove-preview" onclick="removeMenu()">✕ Supprimer</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SERVICES --}}
            <div class="form-section">
                <h2>✅ Services proposés</h2>
                <p class="form-hint">
                    Ajoutez jusqu'à 8 services. Ex : WiFi, Climatisation, Parking, Petit-déjeuner…
                </p>

                <div class="services-inputs" id="servicesInputs">
                    @php $servicesOld = old('services', []); @endphp
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

            {{-- ACTIONS --}}
            <div class="form-actions">
                <a href="{{ route('proprietaire.dashboard') }}" class="btn-secondary">
                    Annuler
                </a>

                <button type="submit" class="btn-submit">
                    📤 Soumettre pour validation
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
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

    .field-help {
        display: block;
        margin-top: 0.45rem;
        color: #6b7280;
        line-height: 1.45;
    }
</style>
@endpush

@push('scripts')
<script>
// Format téléphone Bénin
document.addEventListener('DOMContentLoaded', function () {
    const phoneInputs = document.querySelectorAll('[data-benin-phone]');

    function onlyDigits(value) {
        return (value || '').replace(/\D+/g, '');
    }

    function toLocalDigits(value) {
        let digits = onlyDigits(value);

        if (digits.startsWith('229')) {
            digits = digits.slice(3);
        }

        if (!digits.length) {
            return '01';
        }

        if (digits.startsWith('01')) {
            return digits.slice(0, 10);
        }

        return ('01' + digits.slice(0, 8)).slice(0, 10);
    }

    function formatPhone(value) {
        const digits = toLocalDigits(value);
        return digits.match(/.{1,2}/g)?.join(' ') ?? '01';
    }

    phoneInputs.forEach((input) => {
        input.value = formatPhone(input.value || '01');

        input.addEventListener('focus', () => {
            if (!onlyDigits(input.value)) {
                input.value = '01';
            }
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
                const digits = toLocalDigits(input.value);
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

// Service dynamique
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

// Aperçu photo principale
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

// Aperçu galerie
function previewGalerie(input) {
    const container = document.getElementById('galeriePreview');
    container.innerHTML = '';

    if (!input.files || input.files.length === 0) return;

    const max = 6;
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

// Aperçu menu
function previewMenu(input) {
    if (!input.files || !input.files[0]) return;
    const file  = input.files[0];
    const isPdf = file.type === 'application/pdf';
    const preview = document.getElementById('menuPreview');
    const img     = document.getElementById('menuImg');
    const name    = document.getElementById('menuFileName');

    document.querySelector('#menuZone .upload-label').style.display = 'none';
    name.textContent = isPdf ? ('📄 ' + file.name) : file.name;

    if (isPdf) {
        img.style.display = 'none';
    } else {
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; img.style.display = 'block'; };
        reader.readAsDataURL(file);
    }

    preview.style.display = 'flex';
}

function removeMenu() {
    document.getElementById('menu').value = '';
    document.getElementById('menuPreview').style.display = 'none';
    document.getElementById('menuImg').style.display = 'none';
    document.querySelector('#menuZone .upload-label').style.display = 'flex';
}
</script>
@endpush
