@extends('layouts.app')
@section('title', isset($etablissement) ? 'Modifier ma fiche' : 'Nouvelle fiche — Bonnes Adresses Bénin')

@section('content')
<div class="form-page">
    <div class="form-inner">

        <div class="form-header">
            <a href="{{ route('proprietaire.dashboard') }}" class="btn-back">← Retour</a>
            <h1>{{ isset($etablissement) ? '✏️ Modifier ma fiche' : '➕ Ajouter un établissement' }}</h1>
        </div>

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST"
              action="{{ isset($etablissement) ? route('proprietaire.update', $etablissement) : route('proprietaire.store') }}"
              enctype="multipart/form-data"
              class="etab-form">
            @csrf
            @if(isset($etablissement)) @method('PUT') @endif

            {{-- Informations principales --}}
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
                    <input type="text" id="nom" name="nom"
                           value="{{ old('nom', $etablissement->nom ?? '') }}"
                           placeholder="ex: Restaurant Le Palmier" required/>
                </div>

                <div class="form-group">
                    <label for="description">Description * <small>(minimum 30 caractères)</small></label>
                    <textarea id="description" name="description" rows="5"
                              placeholder="Décrivez votre établissement, sa spécialité, son ambiance..."
                              required>{{ old('description', $etablissement->description ?? '') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="adresse">Adresse complète *</label>
                    <input type="text" id="adresse" name="adresse"
                           value="{{ old('adresse', $etablissement->adresse ?? '') }}"
                           placeholder="ex: Quartier Cadjehoun, près du Carrefour..." required/>
                </div>

                <div class="form-group">
                    <label for="fourchette_prix">Fourchette de prix</label>
                    <input type="text" id="fourchette_prix" name="fourchette_prix"
                           value="{{ old('fourchette_prix', $etablissement->fourchette_prix ?? '') }}"
                           placeholder="ex: 1500 - 5000 FCFA"/>
                </div>
            </div>

            {{-- Contact --}}
            <div class="form-section">
                <h2>📞 Contacts</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="text" id="telephone" name="telephone"
                               value="{{ old('telephone', $etablissement->telephone ?? '') }}"
                               placeholder="+229 97 00 00 00"/>
                    </div>
                    <div class="form-group">
                        <label for="whatsapp">WhatsApp</label>
                        <input type="text" id="whatsapp" name="whatsapp"
                               value="{{ old('whatsapp', $etablissement->whatsapp ?? '') }}"
                               placeholder="+229 97 00 00 00"/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email"
                               value="{{ old('email', $etablissement->email ?? '') }}"
                               placeholder="contact@votre-etablissement.bj"/>
                    </div>
                    <div class="form-group">
                        <label for="site_web">Site web</label>
                        <input type="url" id="site_web" name="site_web"
                               value="{{ old('site_web', $etablissement->site_web ?? '') }}"
                               placeholder="https://..."/>
                    </div>
                </div>
            </div>

            {{-- Services --}}
            <div class="form-section">
                <h2>✅ Services proposés</h2>
                <p class="form-hint">Ajoutez jusqu'à 8 services. Ex: WiFi, Climatisation, Parking, Petit-déjeuner...</p>
                <div class="services-inputs" id="servicesInputs">
                    @php
                        $servicesExistants = isset($etablissement) ? $etablissement->services->pluck('libelle')->toArray() : [];
                        $servicesOld = old('services', $servicesExistants);
                        $count = max(3, count($servicesOld));
                    @endphp
                    @for($i = 0; $i < $count; $i++)
                        <input type="text" name="services[]"
                               value="{{ $servicesOld[$i] ?? '' }}"
                               placeholder="Service {{ $i + 1 }}"/>
                    @endfor
                </div>
                <button type="button" class="btn-add-service" onclick="addService()">
                    ➕ Ajouter un service
                </button>
            </div>

            {{-- Photo principale --}}
            <div class="form-section">
                <h2>📸 Photo principale</h2>
                @if(isset($etablissement) && $etablissement->photo_principale)
                    <div class="current-photo">
                        <img src="{{ $etablissement->photo_url }}" alt="Photo actuelle"/>
                        <p>Photo actuelle — uploader une nouvelle remplacera celle-ci.</p>
                    </div>
                @endif
                <input type="file" id="photo_principale" name="photo_principale"
                       accept="image/jpeg,image/png,image/webp"/>
                <p class="form-hint">Format JPG, PNG ou WebP. Max 2 Mo.</p>
            </div>

            <div class="form-actions">
                <a href="{{ route('proprietaire.dashboard') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-submit">
                    {{ isset($etablissement) ? '💾 Enregistrer les modifications' : '📤 Soumettre pour validation' }}
                </button>
            </div>

        </form>
    </div>
</div>

@push('scripts')
<script>
function addService() {
    const container = document.getElementById('servicesInputs');
    const count = container.querySelectorAll('input').length;
    if (count >= 8) return alert('Maximum 8 services.');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'services[]';
    input.placeholder = 'Service ' + (count + 1);
    container.appendChild(input);
}
</script>
@endpush

@endsection
