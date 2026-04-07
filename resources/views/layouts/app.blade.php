<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Bonnes Adresses Bénin')</title>
    <meta name="description" content="@yield('description', 'Trouvez les meilleures adresses au Bénin : restaurants, hôtels, appartements meublés. Contacts vérifiés et avis authentiques.')">

    <!-- Open Graph -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('og_title', 'Bonnes Adresses Bénin')">
    <meta property="og:description" content="@yield('og_description', 'Trouvez les meilleures adresses au Bénin')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">
    <meta property="og:url" content="@yield('canonical', url()->current())">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title')">
    <meta name="twitter:description" content="@yield('og_description')">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-inner">
            <a href="{{ route('home') }}" class="logo">
                <div class="logo-icon">⭐</div>
                <div class="logo-text">Bonnes Adresses<br><span>Bénin</span></div>
            </a>

            <button class="nav-toggle" aria-label="Menu">
                ☰
            </button>

            <ul class="nav-links">
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Accueil</a></li>
                <li><a href="{{ route('adresses.liste') }}" class="{{ request()->routeIs('adresses.liste') ? 'active' : '' }}">Explorer</a></li>

                @auth
                    @if(auth()->user()->isProprietaire())
                        <li><a href="{{ route('proprietaire.dashboard') }}">Mon Dashboard</a></li>
                    @endif
                    @if(auth()->user()->isAdmin())
                        <li><a href="{{ route('admin.dashboard') }}" class="btn-nav btn-admin">Admin</a></li>
                    @endif
                    <li>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-nav btn-outline" style="background: none; border: none; cursor: pointer;">Déconnexion</button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}" class="btn-nav btn-outline">Connexion</a></li>
                    <li><a href="{{ route('register') }}" class="btn-nav">Inscription Pro</a></li>
                @endauth
            </ul>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success" style="max-width: 1200px; margin: 1rem auto 0;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error" style="max-width: 1200px; margin: 1rem auto 0;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-inner">
            <div class="footer-brand">
                <div class="footer-logo">Bonnes Adresses<br><span>Bénin</span></div>
                <p class="footer-desc">
                    La référence des bonnes adresses au Bénin.<br>
                    Restaurants, hôtels, appartements meublés vérifiés.
                </p>
            </div>

            <div class="footer-links">
                <div>
                    <h4>Explorer</h4>
                    <a href="{{ route('adresses.liste') }}">Toutes les adresses</a>
                    <a href="{{ route('adresses.liste', ['ville' => 'cotonou']) }}">Cotonou</a>
                    <a href="{{ route('adresses.liste', ['ville' => 'bohicon-abomey']) }}">Bohicon/Abomey</a>
                    <a href="{{ route('adresses.liste', ['ville' => 'parakou']) }}">Parakou</a>
                </div>

                <div>
                    <h4>Professionnels</h4>
                    @guest
                        <a href="{{ route('register') }}">Inscrire mon établissement</a>
                        <a href="{{ route('login') }}">Espace propriétaire</a>
                    @else
                        <a href="{{ route('proprietaire.dashboard') }}">Mon tableau de bord</a>
                        <a href="{{ route('proprietaire.etablissements.create') }}">Ajouter une adresse</a>
                    @endguest
                </div>

                <div>
                    <h4>Légal</h4>
                    <a href="{{ route('page.mentions') }}">Mentions légales</a>
                    <a href="{{ route('page.confidentialite') }}">Confidentialité</a>
                    <a href="{{ route('page.contact') }}">Contact</a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Bonnes Adresses Bénin. Tous droits réservés.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Mobile menu toggle
        document.querySelector('.nav-toggle')?.addEventListener('click', function() {
            document.querySelector('.nav-links')?.classList.toggle('open');
        });

        // Close mobile menu on link click
        document.querySelectorAll('.nav-links a')?.forEach(link => {
            link.addEventListener('click', () => {
                document.querySelector('.nav-links')?.classList.remove('open');
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
