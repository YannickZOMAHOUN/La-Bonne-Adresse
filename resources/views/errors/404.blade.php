@extends('layouts.app')

@section('title', 'Page introuvable — Bonnes Adresses Bénin')
@section('description', 'Cette adresse n\'existe pas ou a été déplacée.')

@section('content')
<div style="
    min-height: 70vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 3rem 1.5rem;
">
    <div style="font-size: 5rem; margin-bottom: 1.5rem; line-height: 1;">📍</div>

    <h1 style="
        font-family: 'Playfair Display', serif;
        font-size: clamp(1.8rem, 5vw, 3rem);
        color: var(--dark);
        margin-bottom: 0.8rem;
    ">
        Adresse introuvable
    </h1>

    <p style="
        color: var(--muted);
        font-size: 1.05rem;
        max-width: 420px;
        line-height: 1.7;
        margin-bottom: 2.5rem;
    ">
        Cette page n'existe pas ou a été déplacée.<br>
        Explorez nos adresses vérifiées au Bénin.
    </p>

    <div style="display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center;">
        <a href="{{ route('home') }}" class="btn-primary">
            🏠 Retour à l'accueil
        </a>
        <a href="{{ route('adresses.liste') }}" style="
            display: inline-block;
            padding: 0.75rem 1.8rem;
            border: 2px solid var(--green);
            color: var(--green);
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        " onmouseover="this.style.background='var(--green)';this.style.color='#fff'"
           onmouseout="this.style.background='transparent';this.style.color='var(--green)'">
            🔍 Explorer les adresses
        </a>
    </div>

    {{-- Suggestions rapides --}}
    <div style="margin-top: 4rem; width: 100%; max-width: 600px;">
        <p style="color: var(--muted); font-size: 0.88rem; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.08em;">
            Explorer par ville
        </p>
        <div style="display: flex; gap: 0.6rem; flex-wrap: wrap; justify-content: center;">
            @foreach($villesNav as $ville)
                <a href="{{ route('adresses.liste', ['ville' => $ville->slug]) }}"
                   style="
                       padding: 0.5rem 1.2rem;
                       background: var(--cream);
                       border: 1px solid var(--border);
                       border-radius: 50px;
                       font-size: 0.9rem;
                       color: var(--dark);
                       text-decoration: none;
                       transition: all 0.2s;
                   "
                   onmouseover="this.style.background='var(--green)';this.style.color='#fff';this.style.borderColor='var(--green)'"
                   onmouseout="this.style.background='var(--cream)';this.style.color='var(--dark)';this.style.borderColor='var(--border)'">
                    {{ $ville->emoji }} {{ $ville->nom }}
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
