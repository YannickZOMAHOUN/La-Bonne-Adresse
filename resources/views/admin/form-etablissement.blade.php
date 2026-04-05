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
                            <small style="font-weight:400; color:#888">(requis si l'établissement doit avoir un propriétaire)</small>
                        </label>
                        <select id="user_id" name="user_id" required>
                            <option value="">— Sélectionner un propriétaire —</option>
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
                            Le <strong>+229</strong> est ajouté automatiquement.
                            Si vous laissez simplement <strong>01</strong>, le champ sera ignoré.
                        </small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $etablissement->email ?? '') }}"
                        placeholder="ex : contact@exemple.com"
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
                        placeholder="ex : https://www.monsite.com"
                        maxlength="255"
                    />
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 PHOTOS
            ══════════════════════════════════════════════════════════ --}}
            <div class="form-section">
                <h2>📸 Photos</h2>

                <div class="form-group">
                    <label for="photo_principale">Photo principale</label>
                    @if(isset($etablissement) && $etablissement->photo_principale)
                        <div class="current-photo">
                            <img src="{{ Storage::url($etablissement->photo_principale) }}" alt="Photo principale actuelle">
                            <label class="checkbox-label">
                                <input type="checkbox" name="supprimer_photo_principale" value="1" />
                                <span>Supprimer la photo principale</span>
                            </label>
                        </div>
                    @endif
                    <input
                        type="file"
                        id="photo_principale"
                        name="photo_principale"
                        accept="image/jpeg,image/png,image/webp"
                    />
                    <small class="field-help">
                        Format : JPG, PNG ou WebP. Taille max : 3 Mo. Dimensions min : 400x300 px.
                    </small>
                </div>

                <div class="form-group">
                    <label>Photos de la galerie <small>(max 6)</small></label>
                    <div class="gallery-photos-preview">
                        @if(isset($etablissement) && $etablissement->photos->count() > 0)
                            @foreach($etablissement->photos as $photo)
                                <div class="gallery-photo-item">
                                    <img src="{{ Storage::url($photo->url) }}" alt="Photo de galerie">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="supprimer_photos[]" value="{{ $photo->id }}" />
                                        <span>Supprimer</span>
                                    </label>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <input
                        type="file"
                        name="photos[]"
                        accept="image/jpeg,image/png,image/webp"
                        multiple
                    />
                    <small class="field-help">
                        Format : JPG, PNG ou WebP. Taille max : 3 Mo par photo. Dimensions min : 400x300 px.
                    </small>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 SERVICES
            ══════════════════════════════════════════════════════════ --}}
            <div class="form-section">
                <h2>✨ Services <small>(max 8)</small></h2>
                <div id="services-list">
                    @if(isset($etablissement) && $etablissement->services->count() > 0)
                        @foreach($etablissement->services as $service)
                            <div class="form-group form-group--service">
                                <input type="text" name="services[]" value="{{ $service->libelle }}" maxlength="60" placeholder="Nom du service" />
                                <button type="button" class="btn-remove-service">×</button>
                            </div>
                        @endforeach
                    @else
                        <div class="form-group form-group--service">
                            <input type="text" name="services[]" maxlength="60" placeholder="Nom du service" />
                            <button type="button" class="btn-remove-service">×</button>
                        </div>
                    @endif
                </div>
                <button type="button" id="add-service" class="btn-add-service">＋ Ajouter un service</button>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 HORAIRES
            ══════════════════════════════════════════════════════════ --}}
            <div class="form-section">
                <h2>⏰ Horaires d'ouverture</h2>
                <div class="horaires-grid">
                    @foreach($jours as $jour)
                        @php
                            $horaire = old('horaires.' . $jour, $etablissement->horaires[$jour] ?? null);
                            $ouvert = $horaire !== 'Fermé';
                            $debut = $ouvert ? (explode(' – ', $horaire)[0] ?? '09:00') : '09:00';
                            $fin = $ouvert ? (explode(' – ', $horaire)[1] ?? '23:00') : '23:00';
                        @endphp
                        <div class="horaire-item">
                            <label class="checkbox-label">
                                <input type="checkbox" name="horaires[{{ $jour }}][ouvert]" value="1" {{ $ouvert ? 'checked' : '' }} data-jour="{{ $jour }}" />
                                <span>{{ $jour }}</span>
                            </label>
                            <input type="time" name="horaires[{{ $jour }}][debut]" value="{{ $debut }}" {{ !$ouvert ? 'disabled' : '' }} data-jour="{{ $jour }}" />
                            <span>–</span>
                            <input type="time" name="horaires[{{ $jour }}][fin]" value="{{ $fin }}" {{ !$ouvert ? 'disabled' : '' }} data-jour="{{ $jour }}" />
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 BOUTONS DE SOUMISSION
            ══════════════════════════════════════════════════════════ --}}
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    {{ isset($etablissement) ? 'Enregistrer les modifications' : 'Créer l\'établissement' }}
                </button>
            </div>
        </form>

    </div>
</div>

{{-- ── STYLES ─────────────────────────────────────────────────── --}}
<style>
    .form-page {
        max-width: 900px;
        margin: 2rem auto;
        padding: 1.5rem;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }
    .form-header {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #eee;
    }
    .form-header .btn-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #333;
        font-size: 1.2rem;
        text-decoration: none;
        transition: background 0.2s;
    }
    .form-header .btn-back:hover { background: #e2e8f0; }
    .form-header > div {
        display: flex;
        flex-direction: column;
    }
    .admin-badge {
        background: #e0f2fe;
        color: #0284c7;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        align-self: flex-start;
        margin-bottom: 0.5rem;
    }
    .form-header h1 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
    }

    .alert {
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        font-weight: 500;
        line-height: 1.5;
    }
    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    .form-section {
        background: #f9fafb;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #f3f4f6;
    }
    .form-section h2 {
        font-size: 1.3rem;
        font-weight: 600;
        color: #333;
        margin-top: 0;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #eee;
    }
    .form-section--admin {
        background: #fffbeb;
        border-color: #fef3c7;
    }
    .form-section--admin h2 {
        color: #b45309;
        border-color: #fde68a;
    }

    .form-row {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .form-row > .form-group {
        flex: 1;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-group:last-child {
        margin-bottom: 0;
    }
    .form-group label {
        display: block;
        font-size: 0.95rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 0.6rem;
    }
    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="url"],
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 1rem;
        color: #1f2937;
        background: #fff;
        box-sizing: border-box;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    .form-group textarea {
        resize: vertical;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        cursor: pointer;
        font-weight: 500;
        color: #333;
    }
    .checkbox-label input[type="checkbox"] {
        margin-right: 0.6rem;
        width: 18px;
        height: 18px;
        accent-color: #10b981;
    }
    .field-help {
        font-size: 0.85rem;
        color: #6b7280;
        margin-top: 0.4rem;
        display: block;
    }

    /* Phone input group */
    .phone-input-group {
        display: flex;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        overflow: hidden;
        background: #fff;
    }
    .phone-input-group .phone-prefix {
        padding: 0.75rem 0.8rem;
        background: #e5e7eb;
        color: #4b5563;
        font-size: 1rem;
        border-right: 1px solid #d1d5db;
        display: flex;
        align-items: center;
    }
    .phone-input-group input {
        border: none;
        flex-grow: 1;
        padding: 0.75rem 1rem;
    }
    .phone-input-group input:focus {
        box-shadow: none;
    }

    /* Photos */
    .current-photo {
        margin-bottom: 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 1rem;
        background: #f9fafb;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    .current-photo img {
        max-width: 200px;
        height: auto;
        border-radius: 6px;
        border: 1px solid #d1d5db;
    }
    .gallery-photos-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .gallery-photo-item {
        position: relative;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.5rem;
        background: #f9fafb;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    .gallery-photo-item img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #d1d5db;
    }

    /* Services */
    #services-list {
        margin-bottom: 1rem;
    }
    .form-group--service {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }
    .form-group--service input {
        flex-grow: 1;
    }
    .btn-remove-service {
        background: #ef4444;
        color: #fff;
        border: none;
        border-radius: 6px;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-remove-service:hover { background: #dc2626; }
    .btn-add-service {
        background: #22c55e;
        color: #fff;
        padding: 0.6rem 1.2rem;
        border: none;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-add-service:hover { background: #16a34a; }

    /* Horaires */
    .horaires-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1rem;
    }
    .horaire-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.75rem 1rem;
    }
    .horaire-item label {
        margin-bottom: 0;
        flex-shrink: 0;
        min-width: 80px;
    }
    .horaire-item input[type="time"] {
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        flex-grow: 1;
    }
    .horaire-item input[type="time"]:disabled {
        background: #f3f4f6;
        color: #9ca3af;
    }
    .horaire-item span {
        color: #6b7280;
    }

    /* Form Actions */
    .form-actions {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #eee;
        text-align: right;
    }
    .btn-submit {
        background: #10b981;
        color: #fff;
        padding: 0.8rem 2rem;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-submit:hover { background: #059669; }

    @media (max-width: 768px) {
        .form-row {
            flex-direction: column;
            gap: 0;
        }
        .form-section h2 {
            font-size: 1.1rem;
        }
        .form-header h1 {
            font-size: 1.5rem;
        }
    }
</style>

{{-- ── SCRIPTS ─────────────────────────────────────────────────── --}}
<script>
    // Gérer l'ajout/suppression de services
    document.getElementById('add-service').addEventListener('click', function() {
        const servicesList = document.getElementById('services-list');
        const newServiceGroup = document.createElement('div');
        newServiceGroup.classList.add('form-group', 'form-group--service');
        newServiceGroup.innerHTML = `
            <input type="text" name="services[]" maxlength="60" placeholder="Nom du service" />
            <button type="button" class="btn-remove-service">×</button>
        `;
        servicesList.appendChild(newServiceGroup);
        newServiceGroup.querySelector('.btn-remove-service').addEventListener('click', function() {
            newServiceGroup.remove();
        });
    });

    document.querySelectorAll('.btn-remove-service').forEach(button => {
        button.addEventListener('click', function() {
            button.closest('.form-group--service').remove();
        });
    });

    // Gérer l'activation/désactivation des champs d'horaires
    document.querySelectorAll('input[type="checkbox"][data-jour]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const jour = this.dataset.jour;
            const debutInput = document.querySelector(`input[name="horaires[${jour}][debut]"]`);
            const finInput = document.querySelector(`input[name="horaires[${jour}][fin]"]`);
            if (this.checked) {
                debutInput.removeAttribute('disabled');
                finInput.removeAttribute('disabled');
            } else {
                debutInput.setAttribute('disabled', 'disabled');
                finInput.setAttribute('disabled', 'disabled');
            }
        });
    });

    // Gérer le formatage des numéros de téléphone béninois
    document.querySelectorAll('input[data-benin-phone]').forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Supprimer tout ce qui n'est pas un chiffre
            if (value.startsWith('229')) {
                value = value.substring(3); // Supprimer le préfixe pays si présent
            }
            if (value.startsWith('01')) {
                value = value.substring(2); // Supprimer le préfixe local si présent
            }

            let formattedValue = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 2 === 0) {
                    formattedValue += ' ';
                }
                formattedValue += value[i];
            }
            e.target.value = formattedValue.trim();
        });

        // Initialiser le format si une valeur existe
        if (input.value) {
            let value = input.value.replace(/\D/g, '');
            if (value.startsWith('229')) {
                value = value.substring(3);
            }
            if (value.startsWith('01')) {
                value = value.substring(2);
            }
            let formattedValue = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 2 === 0) {
                    formattedValue += ' ';
                }
                formattedValue += value[i];
            }
            input.value = formattedValue.trim();
        }
    });
</script>

@endsection@extends('layouts.app')

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
                            <small style="font-weight:400; color:#888">(requis si l'établissement doit avoir un propriétaire)</small>
                        </label>
                        <select id="user_id" name="user_id" required>
                            <option value="">— Sélectionner un propriétaire —</option>
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
                            Le <strong>+229</strong> est ajouté automatiquement.
                            Si vous laissez simplement <strong>01</strong>, le champ sera ignoré.
                        </small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $etablissement->email ?? '') }}"
                        placeholder="ex : contact@exemple.com"
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
                        placeholder="ex : https://www.monsite.com"
                        maxlength="255"
                    />
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 PHOTOS
            ══════════════════════════════════════════════════════════ --}}
            <div class="form-section">
                <h2>📸 Photos</h2>

                <div class="form-group">
                    <label for="photo_principale">Photo principale</label>
                    @if(isset($etablissement) && $etablissement->photo_principale)
                        <div class="current-photo">
                            <img src="{{ Storage::url($etablissement->photo_principale) }}" alt="Photo principale actuelle">
                            <label class="checkbox-label">
                                <input type="checkbox" name="supprimer_photo_principale" value="1" />
                                <span>Supprimer la photo principale</span>
                            </label>
                        </div>
                    @endif
                    <input
                        type="file"
                        id="photo_principale"
                        name="photo_principale"
                        accept="image/jpeg,image/png,image/webp"
                    />
                    <small class="field-help">
                        Format : JPG, PNG ou WebP. Taille max : 3 Mo. Dimensions min : 400x300 px.
                    </small>
                </div>

                <div class="form-group">
                    <label>Photos de la galerie <small>(max 6)</small></label>
                    <div class="gallery-photos-preview">
                        @if(isset($etablissement) && $etablissement->photos->count() > 0)
                            @foreach($etablissement->photos as $photo)
                                <div class="gallery-photo-item">
                                    <img src="{{ Storage::url($photo->url) }}" alt="Photo de galerie">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="supprimer_photos[]" value="{{ $photo->id }}" />
                                        <span>Supprimer</span>
                                    </label>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <input
                        type="file"
                        name="photos[]"
                        accept="image/jpeg,image/png,image/webp"
                        multiple
                    />
                    <small class="field-help">
                        Format : JPG, PNG ou WebP. Taille max : 3 Mo par photo. Dimensions min : 400x300 px.
                    </small>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 SERVICES
            ══════════════════════════════════════════════════════════ --}}
            <div class="form-section">
                <h2>✨ Services <small>(max 8)</small></h2>
                <div id="services-list">
                    @if(isset($etablissement) && $etablissement->services->count() > 0)
                        @foreach($etablissement->services as $service)
                            <div class="form-group form-group--service">
                                <input type="text" name="services[]" value="{{ $service->libelle }}" maxlength="60" placeholder="Nom du service" />
                                <button type="button" class="btn-remove-service">×</button>
                            </div>
                        @endforeach
                    @else
                        <div class="form-group form-group--service">
                            <input type="text" name="services[]" maxlength="60" placeholder="Nom du service" />
                            <button type="button" class="btn-remove-service">×</button>
                        </div>
                    @endif
                </div>
                <button type="button" id="add-service" class="btn-add-service">＋ Ajouter un service</button>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 HORAIRES
            ══════════════════════════════════════════════════════════ --}}
            <div class="form-section">
                <h2>⏰ Horaires d'ouverture</h2>
                <div class="horaires-grid">
                    @foreach($jours as $jour)
                        @php
                            $horaire = old('horaires.' . $jour, $etablissement->horaires[$jour] ?? null);
                            $ouvert = $horaire !== 'Fermé';
                            $debut = $ouvert ? (explode(' – ', $horaire)[0] ?? '09:00') : '09:00';
                            $fin = $ouvert ? (explode(' – ', $horaire)[1] ?? '23:00') : '23:00';
                        @endphp
                        <div class="horaire-item">
                            <label class="checkbox-label">
                                <input type="checkbox" name="horaires[{{ $jour }}][ouvert]" value="1" {{ $ouvert ? 'checked' : '' }} data-jour="{{ $jour }}" />
                                <span>{{ $jour }}</span>
                            </label>
                            <input type="time" name="horaires[{{ $jour }}][debut]" value="{{ $debut }}" {{ !$ouvert ? 'disabled' : '' }} data-jour="{{ $jour }}" />
                            <span>–</span>
                            <input type="time" name="horaires[{{ $jour }}][fin]" value="{{ $fin }}" {{ !$ouvert ? 'disabled' : '' }} data-jour="{{ $jour }}" />
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 BOUTONS DE SOUMISSION
            ══════════════════════════════════════════════════════════ --}}
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    {{ isset($etablissement) ? 'Enregistrer les modifications' : 'Créer l\'établissement' }}
                </button>
            </div>
        </form>

    </div>
</div>

{{-- ── STYLES ─────────────────────────────────────────────────── --}}
<style>
    .form-page {
        max-width: 900px;
        margin: 2rem auto;
        padding: 1.5rem;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }
    .form-header {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #eee;
    }
    .form-header .btn-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #333;
        font-size: 1.2rem;
        text-decoration: none;
        transition: background 0.2s;
    }
    .form-header .btn-back:hover { background: #e2e8f0; }
    .form-header > div {
        display: flex;
        flex-direction: column;
    }
    .admin-badge {
        background: #e0f2fe;
        color: #0284c7;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        align-self: flex-start;
        margin-bottom: 0.5rem;
    }
    .form-header h1 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
    }

    .alert {
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        font-weight: 500;
        line-height: 1.5;
    }
    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    .form-section {
        background: #f9fafb;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #f3f4f6;
    }
    .form-section h2 {
        font-size: 1.3rem;
        font-weight: 600;
        color: #333;
        margin-top: 0;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #eee;
    }
    .form-section--admin {
        background: #fffbeb;
        border-color: #fef3c7;
    }
    .form-section--admin h2 {
        color: #b45309;
        border-color: #fde68a;
    }

    .form-row {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .form-row > .form-group {
        flex: 1;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-group:last-child {
        margin-bottom: 0;
    }
    .form-group label {
        display: block;
        font-size: 0.95rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 0.6rem;
    }
    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="url"],
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 1rem;
        color: #1f2937;
        background: #fff;
        box-sizing: border-box;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    .form-group textarea {
        resize: vertical;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        cursor: pointer;
        font-weight: 500;
        color: #333;
    }
    .checkbox-label input[type="checkbox"] {
        margin-right: 0.6rem;
        width: 18px;
        height: 18px;
        accent-color: #10b981;
    }
    .field-help {
        font-size: 0.85rem;
        color: #6b7280;
        margin-top: 0.4rem;
        display: block;
    }

    /* Phone input group */
    .phone-input-group {
        display: flex;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        overflow: hidden;
        background: #fff;
    }
    .phone-input-group .phone-prefix {
        padding: 0.75rem 0.8rem;
        background: #e5e7eb;
        color: #4b5563;
        font-size: 1rem;
        border-right: 1px solid #d1d5db;
        display: flex;
        align-items: center;
    }
    .phone-input-group input {
        border: none;
        flex-grow: 1;
        padding: 0.75rem 1rem;
    }
    .phone-input-group input:focus {
        box-shadow: none;
    }

    /* Photos */
    .current-photo {
        margin-bottom: 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 1rem;
        background: #f9fafb;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    .current-photo img {
        max-width: 200px;
        height: auto;
        border-radius: 6px;
        border: 1px solid #d1d5db;
    }
    .gallery-photos-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .gallery-photo-item {
        position: relative;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.5rem;
        background: #f9fafb;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    .gallery-photo-item img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #d1d5db;
    }

    /* Services */
    #services-list {
        margin-bottom: 1rem;
    }
    .form-group--service {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }
    .form-group--service input {
        flex-grow: 1;
    }
    .btn-remove-service {
        background: #ef4444;
        color: #fff;
        border: none;
        border-radius: 6px;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-remove-service:hover { background: #dc2626; }
    .btn-add-service {
        background: #22c55e;
        color: #fff;
        padding: 0.6rem 1.2rem;
        border: none;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-add-service:hover { background: #16a34a; }

    /* Horaires */
    .horaires-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1rem;
    }
    .horaire-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.75rem 1rem;
    }
    .horaire-item label {
        margin-bottom: 0;
        flex-shrink: 0;
        min-width: 80px;
    }
    .horaire-item input[type="time"] {
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        flex-grow: 1;
    }
    .horaire-item input[type="time"]:disabled {
        background: #f3f4f6;
        color: #9ca3af;
    }
    .horaire-item span {
        color: #6b7280;
    }

    /* Form Actions */
    .form-actions {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #eee;
        text-align: right;
    }
    .btn-submit {
        background: #10b981;
        color: #fff;
        padding: 0.8rem 2rem;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-submit:hover { background: #059669; }

    @media (max-width: 768px) {
        .form-row {
            flex-direction: column;
            gap: 0;
        }
        .form-section h2 {
            font-size: 1.1rem;
        }
        .form-header h1 {
            font-size: 1.5rem;
        }
    }
</style>

{{-- ── SCRIPTS ─────────────────────────────────────────────────── --}}
<script>
    // Gérer l'ajout/suppression de services
    document.getElementById('add-service').addEventListener('click', function() {
        const servicesList = document.getElementById('services-list');
        const newServiceGroup = document.createElement('div');
        newServiceGroup.classList.add('form-group', 'form-group--service');
        newServiceGroup.innerHTML = `
            <input type="text" name="services[]" maxlength="60" placeholder="Nom du service" />
            <button type="button" class="btn-remove-service">×</button>
        `;
        servicesList.appendChild(newServiceGroup);
        newServiceGroup.querySelector('.btn-remove-service').addEventListener('click', function() {
            newServiceGroup.remove();
        });
    });

    document.querySelectorAll('.btn-remove-service').forEach(button => {
        button.addEventListener('click', function() {
            button.closest('.form-group--service').remove();
        });
    });

    // Gérer l'activation/désactivation des champs d'horaires
    document.querySelectorAll('input[type="checkbox"][data-jour]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const jour = this.dataset.jour;
            const debutInput = document.querySelector(`input[name="horaires[${jour}][debut]"]`);
            const finInput = document.querySelector(`input[name="horaires[${jour}][fin]"]`);
            if (this.checked) {
                debutInput.removeAttribute('disabled');
                finInput.removeAttribute('disabled');
            } else {
                debutInput.setAttribute('disabled', 'disabled');
                finInput.setAttribute('disabled', 'disabled');
            }
        });
    });

    // Gérer le formatage des numéros de téléphone béninois
    document.querySelectorAll('input[data-benin-phone]').forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Supprimer tout ce qui n'est pas un chiffre
            if (value.startsWith('229')) {
                value = value.substring(3); // Supprimer le préfixe pays si présent
            }
            if (value.startsWith('01')) {
                value = value.substring(2); // Supprimer le préfixe local si présent
            }

            let formattedValue = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 2 === 0) {
                    formattedValue += ' ';
                }
                formattedValue += value[i];
            }
            e.target.value = formattedValue.trim();
        });

        // Initialiser le format si une valeur existe
        if (input.value) {
            let value = input.value.replace(/\D/g, '');
            if (value.startsWith('229')) {
                value = value.substring(3);
            }
            if (value.startsWith('01')) {
                value = value.substring(2);
            }
            let formattedValue = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 2 === 0) {
                    formattedValue += ' ';
                }
                formattedValue += value[i];
            }
            input.value = formattedValue.trim();
        }
    });
</script>

@endsection
