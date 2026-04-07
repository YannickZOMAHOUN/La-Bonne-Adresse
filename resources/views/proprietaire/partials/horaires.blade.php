{{--
    Partial : horaires
    Variables attendues :
      $horaires  — tableau ['Lundi' => '08:00 – 18:00', ...] ou null (édition)
                   null en création
      $jours     — ['Lundi','Mardi',...,'Dimanche']
--}}

@php
    $joursDisponibles = $jours ?? ['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'];

    // Décodage des horaires existants pour pré-remplissage (édition)
    $horairesData = [];
    if (!empty($horaires) && is_array($horaires)) {
        foreach ($horaires as $jour => $valeur) {
            if ($valeur === 'Fermé') {
                $horairesData[$jour] = ['ouvert' => false, 'debut' => '08:00', 'fin' => '18:00'];
            } else {
                // format "HH:MM – HH:MM"
                $parts = preg_split('/\s*[–\-]\s*/', $valeur);
                $horairesData[$jour] = [
                    'ouvert' => true,
                    'debut'  => $parts[0] ?? '08:00',
                    'fin'    => $parts[1] ?? '18:00',
                ];
            }
        }
    }
    foreach ($joursDisponibles as $j) {
        if (!isset($horairesData[$j])) {
            $horairesData[$j] = ['ouvert' => false, 'debut' => '08:00', 'fin' => '18:00'];
        }
    }
@endphp

<div class="form-section" id="horaires-section">
    <h2>⏰ Horaires <small style="font-weight:400; color:var(--muted)">(optionnel)</small></h2>

    {{-- ══ OUTIL : Appliquer à plusieurs jours ══════════════════════ --}}
    <div class="horaires-copier-bloc">
        <p class="horaires-copier-titre">⚡ Application rapide — même horaire sur plusieurs jours</p>
        <div class="horaires-copier-inner">

            {{-- Plage horaire à copier --}}
            <div class="horaires-copier-plage">
                <label>De</label>
                <input type="time" id="copier_debut" value="08:00" />
                <label>à</label>
                <input type="time" id="copier_fin"   value="18:00" />
            </div>

            {{-- Sélection des jours cibles --}}
            <div class="horaires-copier-jours">
                @foreach($joursDisponibles as $j)
                    <label class="jour-check">
                        <input type="checkbox" class="copier-jour-cb" value="{{ $j }}" />
                        {{ substr($j, 0, 3) }}
                    </label>
                @endforeach
            </div>

            {{-- Raccourcis --}}
            <div class="horaires-copier-shortcuts">
                <button type="button" class="shortcut-btn" data-jours="Lundi,Mardi,Mercredi,Jeudi,Vendredi">
                    Lun – Ven
                </button>
                <button type="button" class="shortcut-btn" data-jours="Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi">
                    Lun – Sam
                </button>
                <button type="button" class="shortcut-btn" data-jours="Samedi,Dimanche">
                    Week-end
                </button>
                <button type="button" class="shortcut-btn" data-jours="Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi,Dimanche">
                    Tous
                </button>
                <button type="button" class="shortcut-btn shortcut-btn--clear" data-jours="">
                    Désélectionner
                </button>
            </div>

            <button type="button" class="btn-appliquer" id="btnAppliquerHoraires">
                ✅ Appliquer aux jours sélectionnés
            </button>
        </div>
    </div>

    {{-- ══ LIGNES PAR JOUR ═══════════════════════════════════════════ --}}
    <div class="horaires-grid">
        @foreach($joursDisponibles as $jour)
        @php
            $h      = $horairesData[$jour];
            $ouvert = $h['ouvert'];
            $debut  = $h['debut'];
            $fin    = $h['fin'];
        @endphp
        <div class="horaire-row-form" data-jour="{{ $jour }}">

            {{-- Toggle ouvert --}}
            <label class="horaire-toggle">
                <input
                    type="checkbox"
                    name="horaires[{{ $jour }}][ouvert]"
                    value="1"
                    class="horaire-ouvert-cb"
                    {{ $ouvert ? 'checked' : '' }}
                    onchange="toggleJourHoraire(this)"
                />
                <span class="horaire-jour-label">{{ $jour }}</span>
            </label>

            {{-- Plage horaire --}}
            <div class="horaire-plage {{ !$ouvert ? 'horaire-plage--hidden' : '' }}">
                <input
                    type="time"
                    name="horaires[{{ $jour }}][debut]"
                    class="horaire-debut"
                    value="{{ $debut }}"
                />
                <span class="horaire-sep">–</span>
                <input
                    type="time"
                    name="horaires[{{ $jour }}][fin]"
                    class="horaire-fin"
                    value="{{ $fin }}"
                />
            </div>

            {{-- Label fermé --}}
            <span class="horaire-ferme-label {{ $ouvert ? 'horaire-ferme-label--hidden' : '' }}">
                Fermé
            </span>

        </div>
        @endforeach
    </div>
</div>

@push('styles')
<style>
/* ══ SECTION HORAIRES ════════════════════════════════════════════ */

/* Bloc outil de copie */
.horaires-copier-bloc {
    background: #f0f9ff;
    border: 1.5px solid #bae6fd;
    border-radius: 12px;
    padding: 1.1rem 1.3rem;
    margin-bottom: 1.4rem;
}
.horaires-copier-titre {
    font-weight: 700;
    font-size: 0.9rem;
    color: #0369a1;
    margin: 0 0 0.9rem;
}
.horaires-copier-inner {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

/* Plage horaire */
.horaires-copier-plage {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    flex-wrap: wrap;
}
.horaires-copier-plage label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #374151;
    margin: 0;
}
.horaires-copier-plage input[type="time"] {
    padding: 0.4rem 0.6rem;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    font-size: 0.9rem;
    background: #fff;
}

/* Jours à cocher */
.horaires-copier-jours {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
}
.jour-check {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    background: #fff;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    padding: 0.3rem 0.6rem;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
    user-select: none;
}
.jour-check:has(input:checked) {
    background: #0ea5e9;
    border-color: #0ea5e9;
    color: #fff;
}
.jour-check input { display: none; }

/* Raccourcis */
.horaires-copier-shortcuts {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
}
.shortcut-btn {
    padding: 0.3rem 0.75rem;
    border-radius: 20px;
    border: 1.5px solid #94a3b8;
    background: #fff;
    color: #374151;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
}
.shortcut-btn:hover { background: #f1f5f9; border-color: #64748b; }
.shortcut-btn--clear { border-color: #fca5a5; color: #dc2626; }
.shortcut-btn--clear:hover { background: #fee2e2; }

/* Bouton appliquer */
.btn-appliquer {
    align-self: flex-start;
    padding: 0.5rem 1.2rem;
    background: #0ea5e9;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 0.88rem;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.15s;
}
.btn-appliquer:hover { background: #0284c7; }

/* ══ GRILLE PAR JOUR ════════════════════════════════════════════ */
.horaires-grid {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.horaire-row-form {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.6rem 0.8rem;
    border-radius: 10px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    transition: background 0.15s;
}
.horaire-row-form:hover { background: #f3f4f6; }

.horaire-toggle {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    min-width: 130px;
}
.horaire-toggle input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: #16a34a;
    cursor: pointer;
    flex-shrink: 0;
}
.horaire-jour-label {
    font-weight: 600;
    font-size: 0.9rem;
    color: #1f2937;
}

.horaire-plage {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex: 1;
}
.horaire-plage--hidden { display: none; }
.horaire-plage input[type="time"] {
    padding: 0.35rem 0.55rem;
    border: 1.5px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.88rem;
    background: #fff;
    min-width: 0;
    flex: 1;
    max-width: 130px;
}
.horaire-plage input[type="time"]:focus {
    border-color: #16a34a;
    outline: none;
    box-shadow: 0 0 0 3px rgba(22,163,74,0.1);
}
.horaire-sep {
    color: #9ca3af;
    font-weight: 700;
    flex-shrink: 0;
}

.horaire-ferme-label {
    color: #9ca3af;
    font-size: 0.85rem;
    font-style: italic;
}
.horaire-ferme-label--hidden { display: none; }

@media (max-width: 520px) {
    .horaire-row-form { flex-wrap: wrap; gap: 0.4rem; }
    .horaire-toggle { min-width: 100px; }
}
</style>
@endpush

@push('scripts')
<script>
// ── Toggle ouverture/fermeture d'un jour ──────────────────────────────
function toggleJourHoraire(cb) {
    const row   = cb.closest('.horaire-row-form');
    const plage = row.querySelector('.horaire-plage');
    const ferme = row.querySelector('.horaire-ferme-label');
    if (cb.checked) {
        plage.classList.remove('horaire-plage--hidden');
        ferme.classList.add('horaire-ferme-label--hidden');
    } else {
        plage.classList.add('horaire-plage--hidden');
        ferme.classList.remove('horaire-ferme-label--hidden');
    }
}

// ── Application rapide à plusieurs jours ─────────────────────────────
document.addEventListener('DOMContentLoaded', function () {

    // Raccourcis de sélection
    document.querySelectorAll('.shortcut-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const jours = this.dataset.jours
                ? this.dataset.jours.split(',')
                : [];

            document.querySelectorAll('.copier-jour-cb').forEach(cb => {
                cb.checked = jours.includes(cb.value);
            });
        });
    });

    // Bouton appliquer
    document.getElementById('btnAppliquerHoraires')?.addEventListener('click', function () {
        const debut = document.getElementById('copier_debut').value;
        const fin   = document.getElementById('copier_fin').value;

        if (!debut || !fin) {
            alert('Veuillez renseigner une plage horaire (début et fin).');
            return;
        }
        if (fin <= debut) {
            alert("L'heure de fermeture doit être après l'heure d'ouverture.");
            return;
        }

        const joursSelectionnes = [...document.querySelectorAll('.copier-jour-cb:checked')]
            .map(cb => cb.value);

        if (joursSelectionnes.length === 0) {
            alert('Sélectionnez au moins un jour.');
            return;
        }

        joursSelectionnes.forEach(jour => {
            const row = document.querySelector(`.horaire-row-form[data-jour="${jour}"]`);
            if (!row) return;

            // Cocher "ouvert"
            const cb = row.querySelector('.horaire-ouvert-cb');
            if (!cb.checked) {
                cb.checked = true;
                toggleJourHoraire(cb);
            }

            // Remplir les heures
            row.querySelector('.horaire-debut').value = debut;
            row.querySelector('.horaire-fin').value   = fin;
        });

        // Feedback visuel
        const btn = document.getElementById('btnAppliquerHoraires');
        btn.textContent = '✅ Appliqué !';
        btn.style.background = '#16a34a';
        setTimeout(() => {
            btn.textContent = '✅ Appliquer aux jours sélectionnés';
            btn.style.background = '';
        }, 1800);
    });
});
</script>
@endpush
