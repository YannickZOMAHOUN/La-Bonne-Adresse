{{--
    Composant horaires structuré — jour par jour
    Usage dans create.blade.php et edit.blade.php :

    @include('proprietaire.partials.horaires', [
        'horaires' => $etablissement->horaires ?? null   // null en création
    ])
--}}

@php
    $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];

    // Parsing des horaires existants : "08:00 – 18:00" → ['debut' => '08:00', 'fin' => '18:00']
    $horairesParsed = [];
    if (!empty($horaires)) {
        foreach ($horaires as $jour => $valeur) {
            if ($valeur === 'Fermé' || empty($valeur)) {
                $horairesParsed[$jour] = ['ouvert' => false, 'debut' => '08:00', 'fin' => '18:00'];
            } else {
                // Format attendu : "08:00 – 18:00" ou "08:00 - 18:00"
                $parts = preg_split('/\s*[–\-]\s*/', $valeur);
                $horairesParsed[$jour] = [
                    'ouvert' => true,
                    'debut'  => trim($parts[0] ?? '08:00'),
                    'fin'    => trim($parts[1] ?? '18:00'),
                ];
            }
        }
    }

    // Valeurs old() en priorité (après erreur de validation)
    $horaireOld = old('horaires', []);
@endphp

<div class="form-section" id="horaireSection">
    <h2>⏰ Horaires d'ouverture <small style="font-weight:400;color:var(--muted)">(optionnel)</small></h2>
    <p class="form-hint">Cochez les jours d'ouverture et renseignez les horaires.</p>

    <div class="horaires-grid">
        @foreach($jours as $jour)
            @php
                // Priorité : old() > données existantes > défaut ouvert en semaine
                $oldJour   = $horaireOld[$jour] ?? null;
                $existing  = $horairesParsed[$jour] ?? null;
                $isWeekend = in_array($jour, ['Samedi', 'Dimanche']);

                $isOuvert = $oldJour
                    ? !empty($oldJour['ouvert'])
                    : ($existing ? $existing['ouvert'] : !$isWeekend);

                $debut = $oldJour['debut'] ?? ($existing['debut'] ?? '08:00');
                $fin   = $oldJour['fin']   ?? ($existing['fin']   ?? '18:00');

                $inputId = 'horaire_' . strtolower($jour);
            @endphp

            <div class="horaire-row-form" id="row_{{ $jour }}">
                {{-- Checkbox ouvert/fermé --}}
                <label class="horaire-toggle">
                    <input
                        type="checkbox"
                        name="horaires[{{ $jour }}][ouvert]"
                        value="1"
                        id="{{ $inputId }}_ouvert"
                        {{ $isOuvert ? 'checked' : '' }}
                        onchange="toggleHoraire('{{ $jour }}', this.checked)"
                    />
                    <span class="horaire-jour">{{ $jour }}</span>
                </label>

                {{-- Plage horaire --}}
                <div class="horaire-plage" id="plage_{{ $jour }}"
                     style="{{ $isOuvert ? '' : 'opacity:0.35; pointer-events:none;' }}">
                    <input
                        type="time"
                        name="horaires[{{ $jour }}][debut]"
                        value="{{ $debut }}"
                        class="horaire-time"
                        aria-label="Heure d'ouverture {{ $jour }}"
                    />
                    <span class="horaire-sep">–</span>
                    <input
                        type="time"
                        name="horaires[{{ $jour }}][fin]"
                        value="{{ $fin }}"
                        class="horaire-time"
                        aria-label="Heure de fermeture {{ $jour }}"
                    />
                </div>

                {{-- Badge Fermé --}}
                <span class="horaire-ferme-label"
                      id="ferme_{{ $jour }}"
                      style="{{ $isOuvert ? 'display:none' : '' }}">
                    Fermé
                </span>
            </div>
        @endforeach
    </div>

    {{-- Raccourcis rapides --}}
    <div class="horaires-shortcuts">
        <span style="font-size:0.82rem;color:var(--muted);margin-right:0.5rem;">Raccourcis :</span>
        <button type="button" class="btn-shortcut"
                onclick="setHorairesPreset('semaine')">
            Lun–Ven 08:00–18:00
        </button>
        <button type="button" class="btn-shortcut"
                onclick="setHorairesPreset('tous')">
            7j/7 08:00–22:00
        </button>
        <button type="button" class="btn-shortcut"
                onclick="setHorairesPreset('reset')">
            Tout effacer
        </button>
    </div>
</div>

@push('styles')
<style>
.horaires-grid {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
    margin-bottom: 1rem;
}
.horaire-row-form {
    display: grid;
    grid-template-columns: 160px 1fr auto;
    align-items: center;
    gap: 1rem;
    padding: 0.6rem 1rem;
    background: var(--cream);
    border: 1px solid var(--border);
    border-radius: 10px;
    transition: background 0.2s;
}
.horaire-row-form:has(input[type="checkbox"]:checked) {
    background: #f0f7f3;
    border-color: #b8ddc8;
}
.horaire-toggle {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    cursor: pointer;
    user-select: none;
}
.horaire-toggle input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: var(--green);
    cursor: pointer;
    flex-shrink: 0;
}
.horaire-jour {
    font-weight: 500;
    font-size: 0.92rem;
    color: var(--dark);
    min-width: 80px;
}
.horaire-plage {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: opacity 0.2s;
}
.horaire-time {
    padding: 0.35rem 0.6rem;
    border: 1px solid var(--border);
    border-radius: 8px;
    font-size: 0.9rem;
    width: 110px;
    background: #fff;
    color: var(--dark);
}
.horaire-sep {
    color: var(--muted);
    font-size: 1rem;
}
.horaire-ferme-label {
    font-size: 0.82rem;
    color: var(--muted);
    font-style: italic;
    white-space: nowrap;
}
.horaires-shortcuts {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.8rem;
}
.btn-shortcut {
    padding: 0.35rem 0.9rem;
    font-size: 0.8rem;
    border: 1px solid var(--border);
    border-radius: 50px;
    background: #fff;
    color: var(--dark);
    cursor: pointer;
    transition: all 0.2s;
}
.btn-shortcut:hover {
    background: var(--green);
    color: #fff;
    border-color: var(--green);
}
@media (max-width: 600px) {
    .horaire-row-form {
        grid-template-columns: 1fr;
        gap: 0.4rem;
    }
    .horaire-plage { flex-wrap: wrap; }
    .horaire-time  { width: 100%; }
}
</style>
@endpush

@push('scripts')
<script>
function toggleHoraire(jour, isOpen) {
    const plage = document.getElementById('plage_' + jour);
    const ferme = document.getElementById('ferme_' + jour);
    plage.style.opacity        = isOpen ? '1' : '0.35';
    plage.style.pointerEvents  = isOpen ? 'auto' : 'none';
    ferme.style.display        = isOpen ? 'none' : '';
}

function setHorairesPreset(preset) {
    const jours = ['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'];
    const semaine = ['Lundi','Mardi','Mercredi','Jeudi','Vendredi'];

    jours.forEach(jour => {
        const checkbox = document.getElementById('horaire_' + jour.toLowerCase() + '_ouvert');
        const plage    = document.getElementById('plage_' + jour);
        const ferme    = document.getElementById('ferme_' + jour);
        const inputs   = plage ? plage.querySelectorAll('input[type="time"]') : [];

        let open = false, debut = '08:00', fin = '18:00';

        if (preset === 'semaine') {
            open  = semaine.includes(jour);
            debut = '08:00'; fin = '18:00';
        } else if (preset === 'tous') {
            open  = true;
            debut = '08:00'; fin = '22:00';
        } else if (preset === 'reset') {
            open  = false;
        }

        if (checkbox) checkbox.checked = open;
        if (plage) {
            plage.style.opacity       = open ? '1' : '0.35';
            plage.style.pointerEvents = open ? 'auto' : 'none';
        }
        if (ferme) ferme.style.display = open ? 'none' : '';
        if (inputs[0]) inputs[0].value = debut;
        if (inputs[1]) inputs[1].value = fin;
    });
}
</script>
@endpush
