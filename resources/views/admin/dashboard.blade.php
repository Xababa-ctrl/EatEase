@extends('admin.layout')

{{-- Titre de la page --}}
@section('title', 'Dashboard Admin')

{{-- Contenu principal --}}
@section('content')
    <h1 class="mb-4">Tableau de bord</h1>
    <p class="mb-5">Bienvenue Xavier 👋 Voici la gestion des données en cours.</p>

    {{-- Cartes de statistiques en grille responsive --}}
    <div class="row g-4">
        
        {{-- Nombre total d’utilisateurs (tous rôles confondus) --}}
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Utilisateurs</h5>
                    <p class="card-text fs-4">{{ $totalUsers }}</p>
                </div>
            </div>
        </div>

        {{-- Nombre total de clients --}}
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Clients</h5>
                    <p class="card-text fs-4">{{ $totalClients }}</p>
                </div>
            </div>
        </div>

        {{-- Nombre total de restaurateurs --}}
        <div class="col-md-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Restaurateurs</h5>
                    <p class="card-text fs-4">{{ $totalRestaurateurs }}</p>
                </div>
            </div>
        </div>

        {{-- Nombre total d’administrateurs --}}
        <div class="col-md-4">
            <div class="card text-white bg-dark">
                <div class="card-body">
                    <h5 class="card-title">Administrateurs</h5>
                    <p class="card-text fs-4">{{ $totalAdmins }}</p>
                </div>
            </div>
        </div>

        {{-- Nombre total de restaurants enregistrés --}}
        <div class="col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Restaurants</h5>
                    <p class="card-text fs-4">{{ $totalRestaurants }}</p>
                </div>
            </div>
        </div>

        {{-- Nombre total de réservations, avec celles du jour en bonus --}}
        <div class="col-md-4">
            <div class="card text-white bg-secondary">
                <div class="card-body">
                    <h5 class="card-title">Réservations</h5>
                    <p class="card-text fs-4">
                        {{ $totalReservations }}<br>
                        <small class="text-light">({{ $reservationsToday }} aujourd'hui)</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
