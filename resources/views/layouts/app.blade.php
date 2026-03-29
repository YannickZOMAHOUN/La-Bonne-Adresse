<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Bonnes Adresses Bénin') — 📍</title>
    <meta name="description" content="@yield('description', 'Trouvez les meilleures adresses au Bénin : restaurants, hôtels, appartements meublés.')"/>

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json"/>
    <meta name="theme-color" content="#1a6b3c"/>

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>

    {{-- CSS global --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"/>

    @stack('styles')
</head>
<body>

{{-- NAVBAR --}}
<nav class="navbar">
    <div class="nav-inner">
        <a href="{{ route('home') }}" class="logo">
            <div class="logo-icon">📍</div>
            <div class="logo-text">Bonnes<br><span>Adresses Bénin</span></div>
        </a>

        <button class="nav-toggle" id="navToggle" aria-label="Menu">☰</button>

        <ul class="nav-links" id="navLinks">
            <li><a href="{{ route('adresses.liste') }}" class="{{ request()->routeIs('adresses.*') ? 'active' : '' }}">Explorer</a></li>
            <li><a href="{{ route('adresses.liste', ['ville' => 'cotonou']) }}">Cotonou</a></li>
            <li><a href="{{ route('adresses.liste', ['ville' => 'bohicon-abomey']) }}">Bohicon</a></li>
            <li><a href="{{ route('adresses.liste', ['ville' => 'parakou']) }}">Parakou</a></li>

            @auth
                @if(auth()->user()->isAdmin())
                    <li><a href="{{ route('admin.dashboard') }}" class="btn-nav btn-admin">⚙️ Admin</a></li>
                @else
                    <li><a href="{{ route('proprietaire.dashboard') }}" class="btn-nav">Mon espace</a></li>
                @endif
                <li>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline">
                        @csrf
                        <button type="submit" class="btn-nav btn-outline">Déconnexion</button>
                    </form>
                </li>
            @else
                <li><a href="{{ route('register') }}" class="btn-nav">Inscrire mon établissement</a></li>
                <li><a href="{{ route('login') }}" class="btn-nav btn-outline">Connexion</a></li>
            @endauth
        </ul>
    </div>
</nav>

{{-- FLASH MESSAGES --}}
@if(session('success'))
    <div class="alert alert-success">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error">❌ {{ session('error') }}</div>
@endif
@if(session('info'))
    <div class="alert alert-info">ℹ️ {{ session('info') }}</div>
@endif

{{-- CONTENU --}}
@yield('content')

{{-- FOOTER --}}
<footer class="footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <div class="footer-logo">Bonnes <span>Adresses Bénin</span></div>
            <p class="footer-desc">La référence des bonnes adresses au Bénin 🇧🇯</p>
        </div>
        <div class="footer-links">
            <div>
                <h4>Explorer</h4>
                <a href="{{ route('adresses.liste', ['categorie' => 'restaurants']) }}">Restaurants</a>
                <a href="{{ route('adresses.liste', ['categorie' => 'hotels']) }}">Hôtels</a>
                <a href="{{ route('adresses.liste', ['categorie' => 'appartements-meubles']) }}">Appartements</a>
            </div>
            <div>
                <h4>Villes</h4>
                <a href="{{ route('adresses.liste', ['ville' => 'cotonou']) }}">Cotonou</a>
                <a href="{{ route('adresses.liste', ['ville' => 'bohicon-abomey']) }}">Bohicon/Abomey</a>
                <a href="{{ route('adresses.liste', ['ville' => 'parakou']) }}">Parakou</a>
            </div>
            <div>
                <h4>Propriétaires</h4>
                <a href="{{ route('register') }}">Inscrire mon établissement</a>
                <a href="{{ route('login') }}">Connexion</a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© {{ date('Y') }} Bonnes Adresses Bénin · Tous droits réservés</p>
    </div>
</footer>

<script>
    // Toggle menu mobile
    document.getElementById('navToggle').addEventListener('click', function () {
        document.getElementById('navLinks').classList.toggle('open');
    });
</script>

@stack('scripts')
</body>
</html>
