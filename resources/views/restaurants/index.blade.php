@extends('layout.layout')

@section('title', 'Nos Restaurants')

@section('content')
    <div class="container mt-4">
        <h1>Liste des Restaurants</h1>

        {{-- J'affiche un message de succès s'il y en a un en session (par exemple, après avoir ajouté, modifié ou supprimé un restaurant). --}}
        @if (session('success')) 
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close btn-eatease" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Si l'utilisateur est connecté ET qu'il est restaurateur, j'affiche le bouton pour ajouter un restaurant.
             Auth::user()->isRestaurateur() vient de ma méthode personnalisée dans le modèle User. --}}
        @auth 
            @if(Auth::user()->isRestaurateur())
                <div class="mb-3">
                    <a href="{{ route('restaurants.create') }}" class="btn btn-eatease">Ajouter un Restaurant</a>
                </div>
            @endif
        @endauth

        {{-- Je vérifie si la collection de restaurants est vide. --}}
        @if($restaurants->isEmpty()) 
            <p>Aucun restaurant trouvé.</p>
        @else
            <div class="list-group">
                @foreach ($restaurants as $restaurant)
                    <div class="list-group-item list-group-item-action flex-column align-items-start mb-2 shadow-sm">
                        <div class="d-flex w-100 justify-content-between">
                            <div class="d-flex align-items-center">
                                {{-- J'affiche la première image de la galerie si elle existe --}}
                                @if($restaurant->galleries->isNotEmpty())
                                    <img src="{{ asset('storage/' . $restaurant->galleries->first()->photo_path) }}" alt="{{ $restaurant->name }}" class="img-thumbnail me-3" style="max-width: 100px; max-height: 100px;">
                                @else
                                    <img src="{{ asset('images/default-restaurant.png') }}" alt="{{ $restaurant->name }}" class="img-thumbnail me-3" style="max-width: 100px; max-height: 100px;">
                                @endif
                                <div>
                                    <h5 class="mb-1">{{ $restaurant->name }}</h5>
                                    {{-- J'affiche la date de création du restaurant, formatée. --}}
                                    <small>Ajouté le: {{ $restaurant->created_at->format('d/m/Y') }}</small>
                                </div>
                            </div>
                        </div>
                        
                        {{-- J'affiche une version limitée de la description (150 caractères) pour ne pas surcharger la liste.
                             Str::limit() est un helper Laravel pratique. --}}
                        <p class="mb-1">{{ Str::limit($restaurant->description, 150) }}</p> 
                        <small>{{ $restaurant->address }}</small>
                        
                        <div class="mt-2">
                            {{-- Bouton Voir avec un style outline vert --}}
                            <a href="{{ route('restaurants.show', $restaurant) }}" class="btn btn-sm btn-eatease-view me-1">Voir</a>

                            {{-- J'utilise la directive @can pour vérifier si l'utilisateur actuel a le droit de modifier ce restaurant.
                                 Cela fait appel à la méthode 'update' de ma RestaurantPolicy. --}}
                            @can('update', $restaurant)
                                {{-- Bouton Modifier en vert (couleur secondaire) --}}
                                <a href="{{ route('restaurants.edit', $restaurant) }}" class="btn btn-sm btn-eatease-secondary me-1">Modifier</a>
                            @endcan

                            {{-- Idem pour la suppression, cela fait appel à la méthode 'delete' de ma RestaurantPolicy. --}}
                            @can('delete', $restaurant)
                                {{-- Formulaire pour supprimer le restaurant.
                                     onsubmit demande une confirmation JavaScript. --}}
                                <form action="{{ route('restaurants.destroy', $restaurant) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce restaurant ?');">
                                    @csrf {{-- Protection CSRF. --}}
                                    @method('DELETE') {{-- Indique à Laravel que c'est une requête DELETE. --}}
                                    {{-- Bouton Supprimer en style outline orange --}}
                                    <button type="submit" class="btn btn-sm btn-eatease-outline">Supprimer</button>
                                </form>
                            @endcan
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
