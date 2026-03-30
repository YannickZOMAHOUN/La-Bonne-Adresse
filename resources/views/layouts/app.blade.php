<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    {{-- ══ SEO DE BASE ══════════════════════════════════════ --}}
    <title>@yield('title', 'Bonnes Adresses Bénin') — 📍</title>
    <meta name="description" content="@yield('description', 'Trouvez les meilleures adresses au Bénin : restaurants, hôtels, appartements meublés.')"/>
    <meta name="robots" content="index, follow"/>
    <link rel="canonical" href="@yield('canonical', url()->current())"/>

    {{-- ══ OPEN GRAPH (Facebook, WhatsApp, LinkedIn…) ══════ --}}
    <meta property="og:type"        content="@yield('og_type', 'website')"/>
    <meta property="og:site_name"   content="Bonnes Adresses Bénin"/>
    <meta property="og:locale"      content="fr_BJ"/>
    <meta property="og:url"         content="@yield('canonical', url()->current())"/>
    <meta property="og:title" content="@yield('og_title', View::yieldContent('title', 'Bonnes Adresses Bénin'))"/>
    <meta property="og:description" content="@yield('og_description', View::yieldContent('description', 'Trouvez les meilleures adresses au Bénin : restaurants, hôtels, appartements meublés.'))"/>
    <meta property="og:image"       content="@yield('og_image', asset('images/og-default.jpg'))"/>
    <meta property="og:image:width"  content="1200"/>
    <meta property="og:image:height" content="630"/>
    <meta property="og:image:alt"   content="@yield('og_image_alt', 'Bonnes Adresses Bénin')"/>

    {{-- ══ TWITTER CARD (aussi utilisé par WhatsApp parfois) --}}
    <meta name="twitter:card"        content="summary_large_image"/>
    <meta name="twitter:title" content="@yield('og_title', View::yieldContent('title', 'Bonnes Adresses Bénin'))"/>
    <meta name="twitter:description" content="@yield('og_description', View::yieldContent('description', 'Trouvez les meilleures adresses au Bénin.'))"/>
    <meta name="twitter:image"       content="@yield('og_image', asset('images/og-default.jpg'))"/>

    {{-- ══ PWA ══════════════════════════════════════════════ --}}
    <link rel="manifest" href="/manifest.json"/>
    <meta name="theme-color" content="#1a6b3c"/>
    <meta name="mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-title" content="BonnesAdresses"/>
    <link rel="apple-touch-icon" href="/images/icon-192.png"/>

    {{-- ══ FONTS ════════════════════════════════════════════ --}}
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>

    {{-- ══ CSS ═════════════════════════════════════════════ --}}
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
            <li><a href="{{ route('adresses.liste') }}" class="{{ request()->routeIs('adresses.*') && !request('ville') ? 'active' : '' }}">Explorer</a></li>
            @foreach($villesNav as $villeNav)
                <li>
                    <a href="{{ route('adresses.liste', ['ville' => $villeNav->slug]) }}"
                       class="{{ request('ville') === $villeNav->slug ? 'active' : '' }}">
                        {{ $villeNav->nom }}
                    </a>
                </li>
            @endforeach

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
                @foreach($villesNav as $villeNav)
                    <a href="{{ route('adresses.liste', ['ville' => $villeNav->slug]) }}">{{ $villeNav->nom }}</a>
                @endforeach
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

    // Enregistrement Service Worker (PWA)
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .catch(err => console.warn('SW :', err));
        });
    }
</script>

@stack('scripts')
</body>
</html>
