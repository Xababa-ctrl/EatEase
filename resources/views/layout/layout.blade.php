<!DOCTYPE html>
{{-- Je définis la langue de la page en utilisant la configuration de Laravel.
str_replace remplace '_' par '-' (par exemple, 'en_US' devient 'en-US'). --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- J'inclus le token CSRF. C'est une mesure de sécurité importante pour protéger
    contre les attaques de type Cross-Site Request Forgery, surtout pour les formulaires et les requêtes AJAX. --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Le titre de la page. @yield('title') permet aux vues enfants de définir leur propre titre.
    S'il n'est pas défini, 'EatEase - Réservation Facile' sera utilisé par défaut. --}}
    <title>@yield('title', 'EatEase - Réservation Facile')</title>
    {{-- J'importe les icônes Font Awesome depuis un CDN pour les utiliser dans la navigation ou ailleurs. --}}
    {{-- Font Awesome est une bibliothèque d'icônes vectorielles très populaire. --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- J'importe des polices depuis Google Fonts (via Bunny Fonts pour la confidentialité). --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    {{-- J'importe la feuille de style CSS de Bootstrap depuis un CDN pour le design de base. --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    {{-- J'appelle mon fichier CSS principal via Vite. Il doit être dans le head. --}}
    @vite('resources/css/app.css')
    {{-- @stack('styles') est un emplacement où les vues enfants peuvent injecter
    leurs propres feuilles de style CSS spécifiques si nécessaire. --}}
    @stack('styles')

</head>

<body> {{-- Les couleurs de fond et de texte sont maintenant définies dans app.css --}}
    {{-- La div #app est souvent utilisée comme conteneur principal, surtout si on utilise
    des frameworks JavaScript comme Vue.js (même si ce n'est pas le cas ici, c'est une convention). --}}
    <div id="app">
            {{-- Ma barre de navigation principale. Elle est fixe en haut de la page (fixed-top) --}}
        {{-- La couleur de fond est maintenant gérée uniquement par custom.css --}}
        <nav class="navbar navbar-expand-lg navbar-light shadow-sm fixed-top">

            <div class="container">
                {{-- *** LOGO DANS LA NAVBAR (REMIS) *** --}}
                <a class="navbar-brand" href="{{ route('home') }}">
                    {{-- Assurez-vous que le chemin est correct. La taille est maintenant gérée par custom.css --}}
                    <img src="{{ asset('images/Logo.png') }}" alt="EatEase Logo">
                    {{-- Taille pour la navbar --}}
                </a>
                {{-- ************************************ --}}

                {{-- C'est le bouton "burger" qui apparaît sur les petits écrans pour afficher/cacher les liens de
                navigation. --}}
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                {{-- Conteneur pour mes liens de navigation, qui sera "collapsible" sur mobile. --}}
                <div class="collapse navbar-collapse" id="navbarNav">
                    {{-- Liens principaux de navigation, alignés à gauche (me-auto). --}}
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            {{-- J'utilise request()->routeIs('home') pour ajouter la classe 'active' si la route
                            actuelle est 'home'. --}}
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" aria-current="page"
                                href="{{ route('home') }}">Accueil</a>
                        </li>
                        <li class="nav-item">
                            {{-- request()->routeIs('restaurants.index*') inclut aussi les sous-routes de
                            restaurants.index. --}}
                            <a class="nav-link {{ request()->routeIs('restaurants.index*') ? 'active' : '' }}"
                                href="{{ route('restaurants.index') }}">Restaurants</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}"
                                href="{{ route('contact') }}">Contact</a>
                        </li>
                    </ul>

                    {{-- Liens pour l'authentification et le profil utilisateur, alignés à droite (ms-auto). --}}
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        @guest {{-- Si l'utilisateur est un visiteur (non connecté) --}}
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">Connexion</a>
                                </li>
                            @endif
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">Inscription</a>
                                </li>
                            @endif
                        @else {{-- Si l'utilisateur EST connecté --}}
                            <li class="nav-item dropdown">
                                {{-- Menu déroulant pour l'utilisateur connecté, affichant son nom. --}}
                                <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdown" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarUserDropdown">
                                    {{-- Liens spécifiques à l'utilisateur connecté --}}
                                    {{-- Exemples de liens que je pourrais ajouter plus tard :
                                    {{-- <li><a class="dropdown-item" href="#">Mon Profil</a></li> --}}
                                    {{-- <li><a class="dropdown-item" href="#">Mes Réservations</a></li> --}}

                                    {{-- Lien vers le tableau de bord du restaurateur, affiché seulement si l'utilisateur
                                    a le rôle de restaurateur (vérifié avec Auth::user()->isRestaurateur()). --}}
                                    @if(Auth::user()->isRestaurateur())
                                        {{-- <li>
                                            <hr class="dropdown-divider">
                                        </li> --}}
                                        <li><a class="dropdown-item" href="{{ route('restaurateur.dashboard') }}">Espace
                                                Restaurateur</a></li>
                                    @endif

                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        {{-- La déconnexion doit se faire via une requête POST pour des raisons de sécurité
                                        (CSRF).
                                        J'utilise un petit formulaire JavaScript pour ça. --}}
                                        <form method="POST" action="{{ route('logout') }}" id="logout-form-nav">
                                            @csrf
                                            <a class="dropdown-item" href="{{ route('logout') }}"
                                                onclick="event.preventDefault(); document.getElementById('logout-form-nav').submit();">
                                                Déconnexion
                                            </a>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        {{-- Conteneur principal pour le contenu de chaque page.
        py-4 ajoute du padding vertical. --}}
        <main class="py-4">
            <div class="container">
                {{-- Mon logo, affiché plus grand dans la partie principale de la page. --}}
                {{-- *** LOGO PLUS GRAND DANS LE MAIN *** --}}
                <div class="text-center mb-4"> {{-- Pour centrer et ajouter une marge en bas --}}
                    <a href="{{ route('home') }}">
                        {{-- Assurez-vous que le chemin vers votre logo est correct. La taille est maintenant gérée par custom.css --}}
                        <img src="{{ asset('images/Logo.png') }}" alt="EatEase Logo" class="main-logo-img">
                    </a>
                </div>
                {{-- ************************************ --}}


                {{-- Section pour afficher les messages "flash" (messages temporaires stockés en session).
                Utile pour les confirmations d'action, les erreurs, etc. --}}
                @if (session('success')) {{-- Si un message de succès est en session --}}
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- @yield('content') est le placeholder principal où le contenu spécifique
                de chaque vue enfant sera injecté. --}}
                @yield('content')
            </div>
        </main>

        <footer class="footer">
    <div class="footer-content">
        <div class="social-media">
            <h5>Suivez-nous sur les réseaux</h5>
            <div class="icons">
                <a href="https://www.facebook.com/"><i class="fab fa-facebook"></i></a>
                <a href="https://x.com/"><i class="fab fa-x-twitter"></i></a>
                <a href="https://www.instagram.com"><i class="fab fa-instagram"></i></a>
                <a href="https://www.linkedin.com/home"><i class="fab fa-linkedin"></i></a>
            </div>
        </div>
        <div class="copyright">
            &copy; 2025 EatEase. Tous droits réservés.
        </div>
    </div>
</footer>

    </div>{{-- Fin div #app --}}

    {{-- J'importe le bundle JavaScript de Bootstrap (qui inclut Popper.js pour les tooltips, popovers, dropdowns, etc.)
    depuis un CDN. Il est généralement placé à la fin du body pour un meilleur temps de chargement perçu. --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    {{-- @stack('scripts') est un emplacement où les vues enfants peuvent injecter
    leurs propres fichiers JavaScript spécifiques si nécessaire. --}}
    @stack('scripts')
</body>

</html>