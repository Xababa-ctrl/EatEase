@extends('layout.layout')

@section('title', 'Mon Tableau de Bord Restaurateur')

@section('content')
<div class="container mt-4">
    <h1>Tableau de Bord Restaurateur</h1>

    {{-- Petit message de bienvenue personnalisé avec le nom de l'utilisateur connecté. --}}
    <p>Bienvenue, {{ Auth::user()->name }} !</p>

    {{-- Section avec les actions rapides sous forme de cartes. --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Gérer mes Réservations</h5>
                    <p class="card-text">Voir, confirmer ou rejeter les demandes de réservation.</p>
                    {{-- Lien vers la page de gestion des réservations du restaurateur. --}}
                    <a href="{{ route('restaurateur.reservations') }}" class="btn btn-eatease-view">Voir les Réservations</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Gérer mes Restaurants</h5>
                    <p class="card-text">Ajouter un nouveau restaurant ou modifier les informations existantes.</p>
                    {{-- Lien pour créer un nouveau restaurant. --}}
                    <a href="{{ route('restaurants.create') }}" class="btn btn-eatease me-2">Ajouter un Restaurant</a>
                    {{-- Lien vers la liste des restaurants spécifiques à ce restaurateur.
                         La route 'restaurateur.restaurants.list' doit être définie pour afficher cela. --}}
                    <a href="{{ route('restaurateur.restaurants.list') }}" class="btn btn-eatease-secondary">Voir mes Restaurants</a>
                </div>
            </div>
        </div>
    </div>

    <hr> {{-- Séparateur visuel. --}}

    <h2>Mes Restaurants Enregistrés</h2>

    {{-- Je vérifie si le restaurateur a déjà enregistré des restaurants. --}}
    @if($restaurants->isEmpty()) 
        <div class="alert alert-info" role="alert">
            {{-- Message affiché si aucun restaurant n'est trouvé, avec un lien pour en ajouter un. --}}
            Vous n'avez pas encore enregistré de restaurant. 
            <a href="{{ route('restaurants.create') }}" class="btn btn-sm btn-eatease ms-2">Ajouter un Restaurant</a>
        </div>
    @else
        {{-- Si des restaurants existent, je les affiche dans une liste. --}}
        <div class="list-group">
            @foreach ($restaurants as $restaurant)
                <div class="list-group-item list-group-item-action flex-column align-items-start mb-2">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">{{ $restaurant->name }}</h5>
                        {{-- J'affiche la date de création du restaurant, formatée. --}}
                        <small>Ajouté le: {{ $restaurant->created_at->format('d/m/Y') }}</small>
                    </div>
                    {{-- J'affiche une version limitée de la description (100 caractères). --}}
                    <p class="mb-1">{{ Str::limit($restaurant->description, 100) }}</p>
                    <small>{{ $restaurant->address }}</small>
                    <div class="mt-2">
                        {{-- Boutons d'action pour chaque restaurant.
                             Les autorisations (Policies) sont vérifiées sur les pages cibles (show, edit)
                             ou directement ici avec @can pour la suppression. --}}

                        {{-- Bouton Voir personnalisé --}}
                        <a href="{{ route('restaurants.show', $restaurant) }}" class="btn btn-sm btn-eatease-view me-1">Voir</a>

                        {{-- Bouton Modifier personnalisé --}}
                        <a href="{{ route('restaurants.edit', $restaurant) }}" class="btn btn-sm btn-eatease-secondary me-1">Modifier</a>

                        {{-- Suppression conditionnelle selon les permissions --}}
                        @can('delete', $restaurant)
                            <form action="{{ route('restaurants.destroy', $restaurant) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce restaurant ?');">
                                @csrf {{-- Protection CSRF. --}}
                                @method('DELETE') {{-- Indique à Laravel que c'est une requête DELETE. --}}
                                {{-- Bouton Supprimer en style outline orange --}}
                                <button type="submit" class="btn btn-sm btn-eatease-outline">Supprimer</button>
                            </form>
                        @endcan

                        {{-- TODO: Si j'implémente une page pour gérer les horaires d'un restaurant spécifique,
                             je décommenterai et adapterai ce lien. --}}
                        {{-- <a href="#" class="btn btn-sm btn-outline-secondary">Gérer Horaires</a> --}}
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
