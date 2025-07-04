@extends('layout.layout')

@section('title', 'Accueil')

@section('content')

    {{-- Section Hero : Titre, intro et barre de recherche avec fond personnalisé --}}
    <section class="hero-section text-center text-white">
        {{-- Titre principal et petit texte d'introduction --}}
        <h1 class="display-4 mb-4 main-welcome-h1">Bienvenue sur notre site de réservation !</h1>
        <p class="lead mb-4">
            Réservez facilement une table dans votre restaurant préféré.
        </p>

        {{-- Barre de recherche --}}
        <div class="row justify-content-center mb-2">
            <div class="col-md-6">
                {{-- Mon champ de saisie pour la recherche. URL de recherche récupérée pour le JS --}}
                <input type="text" id="restaurant-search" class="form-control form-control-lg mb-2"
                    placeholder="Tapez le nom d'un restaurant..." data-search-url="{{ route('restaurants.search') }}"
                    autocomplete="off">
            </div>
        </div>

        {{-- Conteneur des résultats de recherche --}}
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div id="search-results" class="list-group mt-3">
                    {{-- Résultats injectés ici par JS --}}
                </div>
            </div>
        </div>
    

    <br>
    <br>

    {{-- Section Carrousel --}}
    <div class="row justify-content-center mb-5">
        <div class="col-md-8">
            <div id="restaurantCarousel" class="carousel slide carousel-fade shadow-lg rounded" data-bs-ride="carousel"
                data-url="{{ route('restaurants.carouselData') }}">
                <div class="carousel-indicators">
                    {{-- Les indicateurs seront ajoutés par JavaScript --}}
                </div>
                <div class="carousel-inner bg-light rounded">
                    {{-- Les slides du carrousel seront injectés ici par JavaScript --}}
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#restaurantCarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Précédent</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#restaurantCarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Suivant</span>
                </button>
            </div>
        </div>
    </div>
    </section>

    <hr class="section-divider">

    {{-- Section À propos --}}
    <section class="py-5 bg-light text-center">
        <div class="container">
            <h2 class="mb-4">À propos de EatEase</h2>
            <p class="lead mb-3">
                Bienvenue sur <strong>EatEase</strong>, votre compagnon idéal pour réserver une table en toute simplicité.
            </p>
            <p class="mb-3">
                Notre mission est de connecter les amoureux de la bonne cuisine avec les meilleurs établissements autour
                d’eux. Grâce à notre plateforme intuitive, trouvez un restaurant, consultez les avis, explorez les galeries photo,
                et effectuez votre réservation en quelques clics.
            </p>
            <p class="mb-0">
                Côté restaurateurs, EatEase offre une interface de gestion fluide pour visualiser les réservations, gérer
                les horaires d’ouverture, et mettre à jour les informations de leur établissement.
            </p>
        </div>
    </section>

    <hr class="section-divider">

    {{-- Section Témoignages --}}
    <section class="py-5 text-center bg-light">
        <div class="container">
            <h2 class="mb-5">Ils nous font confiance</h2>
            <div class="row justify-content-center">
                {{-- Témoignage Camille --}}
                <div class="col-md-5 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="row g-0 align-items-center">
                            <div class="col-4">
                                <img src="{{ asset('images/camilla.png') }}" alt="Camille" class="testimonial-img camille">
                            </div>
                            <div class="col-8">
                                <div class="card-body text-start">
                                    <p class="card-text">"Grâce à EatEase, j’ai découvert des adresses incroyables sans
                                        perdre de temps à téléphoner !"</p>
                                    <p class="card-text"><small class="text-muted">— Camille, Paris</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Témoignage Antoine --}}
                <div class="col-md-5 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="row g-0 align-items-center">
                            <div class="col-4">
                                <img src="{{ asset('images/antoine.png') }}" alt="Antoine" class="testimonial-img">
                            </div>
                            <div class="col-8">
                                <div class="card-body text-start">
                                    <p class="card-text">"J’utilise la plateforme pour gérer les réservations de mon
                                        bistrot. Simple, rapide, efficace."</p>
                                    <p class="card-text"><small class="text-muted">— Antoine, restaurateur</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <hr class="section-divider">

@endsection

@push('scripts')
    {{-- Script JS spécifique à la page d’accueil --}}
    @vite('resources/js/welcome.js')
@endpush
