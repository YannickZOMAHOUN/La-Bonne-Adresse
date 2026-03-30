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

        <form method="POST"
              action="{{ route('proprietaire.store') }}"
              enctype="multipart/form-data"
              class="etab-form">
            @csrf

            {{-- ── INFORMATIONS PRINCIPALES ────────────────── --}}
            <div class="form-section">
                <h2>📋 Informations principales</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="ville_id">Ville *</label>
                        <select id="ville_id" name="ville_id" required>
                            <option value="">Choisir une ville</option>
                            @foreach($villes as $ville)
                                <option value="{{ $ville->id }}"
                                    {{ old('ville_id') == $ville->id ? 'selected' : '' }}>
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
                                    {{ old('categorie_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->emoji }} {{ $cat->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nom">Nom de l'établissement *</label>
                    <input type="text" id="nom" name="nom"
                           value="{{ old('nom') }}"
                           placeholder="ex: Restaurant Le Palmier" required/>
                </div>

                <div class="form-group">
                    <label for="description">
                        Description * <small>(minimum 30 caractères)</small>
                    </label>
                    <textarea id="description" name="description" rows="5"
                              placeholder="Décrivez votre établissement, sa spécialité, son ambiance..."
                              required>{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="adresse">Adresse complète *</label>
                    <input type="text" id="adresse" name="adresse"
                           value="{{ old('adresse') }}"
                           placeholder="ex: Quartier Cadjehoun, près du Carrefour..." required/>
                </div>

                <div class="form-group">
                    <label for="fourchette_prix">Fourchette de prix</label>
                    <input type="text" id="fourchette_prix" name="fourchette_prix"
                           value="{{ old('fourchette_prix') }}"
                           placeholder="ex: 1 500 – 5 000 FCFA"/>
                </div>
            </div>

            {{-- ── CONTACTS ─────────────────────────────────── --}}
            <div class="form-section">
                <h2>📞 Contacts</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="text" id="telephone" name="telephone"
                               value="{{ old('telephone') }}"
                               placeholder="+229 97 00 00 00"/>
                    </div>
                    <div class="form-group">
                        <label for="whatsapp">WhatsApp</label>
                        <input type="text" id="whatsapp" name="whatsapp"
                               value="{{ old('whatsapp') }}"
                               placeholder="+229 97 00 00 00"/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email"
                               value="{{ old('email') }}"
                               placeholder="contact@votre-etablissement.bj"/>
                    </div>
                    <div class="form-group">
                        <label for="site_web">Site web</label>
                        <input type="url" id="site_web" name="site_web"
                               value="{{ old('site_web') }}"
                               placeholder="https://..."/>
                    </div>
                </div>
            </div>


            {{-- ── HORAIRES ─────────────────────────────────── --}}
            @include('proprietaire.partials.horaires', ['horaires' => null])

            {{-- ── SERVICES ─────────────────────────────────── --}}
            <div class="form-section">
                <h2>✅ Services proposés</h2>
                <p class="form-hint">
                    Ajoutez jusqu'à 8 services. Ex : WiFi, Climatisation, Parking, Petit-déjeuner…
                </p>
                <div class="services-inputs" id="servicesInputs">
                    @php $servicesOld = old('services', []); @endphp
                    @for($i = 0; $i < max(3, count($servicesOld)); $i++)
                        <input type="text" name="services[]"
                               value="{{ $servicesOld[$i] ?? '' }}"
                               placeholder="Service {{ $i + 1 }}"/>
                    @endfor
                </div>
                <button type="button" class="btn-add-service" onclick="addService()">
                    ➕ Ajouter un service
                </button>
            </div>

            {{-- ── PHOTO PRINCIPALE ────────────────────────── --}}
            <div class="form-section">
                <h2>📸 Photo principale</h2>
                <p class="form-hint">
                    Cette photo sera affichée sur la carte de votre établissement dans les résultats.
                </p>
                <div class="upload-zone" id="mainPhotoZone">
                    <input type="file" id="photo_principale" name="photo_principale"
                           accept="image/jpeg,image/png,image/webp"
                           onchange="previewMainPhoto(this)"/>
                    <label for="photo_principale" class="upload-label">
                        <span class="upload-icon">🖼️</span>
                        <span class="upload-text">Cliquez pour choisir une photo</span>
                        <span class="upload-hint">JPG, PNG ou WebP — Max 2 Mo</span>
                    </label>
                    <div id="mainPhotoPreview" class="photo-preview-wrap" style="display:none">
                        <img id="mainPhotoImg" src="" alt="Aperçu"/>
                        <button type="button" class="btn-remove-preview"
                                onclick="removeMainPhoto()">✕ Supprimer</button>
                    </div>
                </div>
            </div>

            {{-- ── GALERIE DE PHOTOS ───────────────────────── --}}
            <div class="form-section">
                <h2>🖼️ Galerie de photos <small style="font-weight:400; color:var(--muted)">(optionnel)</small></h2>
                <p class="form-hint">
                    Ajoutez jusqu'à 6 photos supplémentaires.
                    Maintenez <kbd>Ctrl</kbd> pour en sélectionner plusieurs à la fois.
                </p>

                <div class="upload-zone">
                    <input type="file" id="photos" name="photos[]"
                           multiple accept="image/jpeg,image/png,image/webp"
                           onchange="previewGalerie(this)"/>
                    <label for="photos" class="upload-label">
                        <span class="upload-icon">📷</span>
                        <span class="upload-text">Cliquez pour choisir vos photos</span>
                        <span class="upload-hint">Maximum 6 photos · JPG, PNG ou WebP · Max 2 Mo chacune</span>
                    </label>
                </div>

                <div id="galeriePreview" class="galerie-preview-grid"></div>
            </div>

            {{-- ── ACTIONS ──────────────────────────────────── --}}
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

@push('scripts')
<script>
// ── Service dynamique ──────────────────────────────────
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
    container.appendChild(input);
    input.focus();
}

// ── Aperçu photo principale ───────────────────────────
function previewMainPhoto(input) {
    if (!input.files || !input.files[0]) return;

    const reader = new FileReader();
    reader.onload = function(e) {
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

// ── Aperçu galerie ────────────────────────────────────
function previewGalerie(input) {
    const container = document.getElementById('galeriePreview');
    container.innerHTML = '';

    if (!input.files || input.files.length === 0) return;

    const max = 6;
    const files = Array.from(input.files).slice(0, max);

    files.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'galerie-thumb-preview';
            div.innerHTML = `
                <img src="${e.target.result}" alt="Photo ${index + 1}"/>
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
        note.textContent = `⚠️ Seules les ${max} premières photos seront enregistrées.`;
        container.appendChild(note);
    }
}
</script>
@endpush

@endsection
