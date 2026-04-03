@extends('layouts.app')

@section('title', 'Inscrire mon établissement — Bonnes Adresses Bénin')

@section('content')
<div class="auth-page">
    <div class="auth-card auth-card--wide">
        <div class="auth-header">
            <div class="auth-icon">🏪</div>
            <h1>Inscrire mon établissement</h1>
            <p>Créez votre compte propriétaire. Activation sous 24h.</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="auth-form" id="registerForm">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label for="nom">Nom complet *</label>
                    <input
                        type="text"
                        id="nom"
                        name="nom"
                        value="{{ old('nom') }}"
                        placeholder="Jean Dupont"
                        maxlength="100"
                        autocomplete="name"
                        required
                    />
                </div>

                <div class="form-group">
                    <label for="telephone">Téléphone / WhatsApp *</label>

                    <div class="phone-input-group">
                        <span class="phone-prefix">+229</span>
                        <input
                            type="text"
                            id="telephone"
                            name="telephone"
                            value="{{ old('telephone', '01') }}"
                            placeholder="01 00 00 00 00"
                            inputmode="numeric"
                            autocomplete="tel"
                            maxlength="14"
                            data-benin-phone
                            data-required="true"
                            required
                        />
                    </div>

                    <small class="field-help">
                        Le préfixe béninois <strong>+229</strong> est ajouté automatiquement. Complétez simplement les 8 derniers chiffres.
                    </small>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Adresse email *</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="votre@email.com"
                    maxlength="150"
                    autocomplete="email"
                    required
                />
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Mot de passe *</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Minimum 8 caractères"
                        minlength="8"
                        autocomplete="new-password"
                        required
                    />
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirmer le mot de passe *</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Répétez le mot de passe"
                        minlength="8"
                        autocomplete="new-password"
                        required
                    />
                </div>
            </div>

            <div class="auth-notice">
                ℹ️ Votre compte sera activé manuellement par l'administrateur dans un délai de 24h après vérification.
            </div>

            <button type="submit" class="btn-submit">
                Soumettre mon inscription →
            </button>
        </form>

        <div class="auth-footer">
            Déjà inscrit ? <a href="{{ route('login') }}">Se connecter</a>
        </div>
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

    const form = document.getElementById('registerForm');
    if (form) {
        form.addEventListener('submit', () => {
            phoneInputs.forEach((input) => {
                input.value = formatPhone(input.value);
            });
        });
    }
});
</script>
@endpush
