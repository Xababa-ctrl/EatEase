@extends('layout.layout')

@section('title', 'Mes Réservations')

@section('content')
    <div class="container mt-4">
        <h1>Mes Réservations</h1>

        {{-- J'affiche un message de succès s'il y en a un en session (par exemple, après avoir annulé une réservation). --}}
        @if (session('success')) 
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Je vérifie si la collection de réservations est vide. --}}
        @if($reservations->isEmpty()) 
            <p>Vous n'avez aucune réservation pour le moment.</p>
            {{-- Si c'est vide, j'affiche un message et un bouton pour créer une nouvelle réservation.
                 La route 'reservations.create' doit exister et pointer vers le formulaire de création. --}}
            <a href="{{ route('reservations.create') }}" class="btn btn-primary">Faire une réservation</a>
        @else
            <div class="list-group">
                @foreach ($reservations as $reservation)
                    <div class="list-group-item list-group-item-action flex-column align-items-start mb-2 shadow-sm">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">
                                {{-- J'affiche le nom du restaurant. Le '??' est une sécurité si jamais $reservation->restaurant est null. --}}
                                Réservation chez : {{ $reservation->restaurant->name ?? 'Restaurant inconnu' }}
                            </h5>
                            {{-- J'affiche le statut avec un badge Bootstrap dont la couleur change selon le statut.
                                 ucfirst() met la première lettre en majuscule pour un affichage plus propre. --}}
                            <small>Statut: <span class="badge bg-{{ $reservation->status == 'confirmed' ? 'success' : ($reservation->status == 'cancelled' ? 'danger' : 'warning') }}">{{ ucfirst($reservation->status) }}</span></small>
                        </div>
                        <p class="mb-1">
                            {{-- Je formate la date et l'heure pour un affichage lisible. --}}
                            Le {{ $reservation->reservation_date->format('d/m/Y') }}
                            à {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }}
                            pour {{ $reservation->number_of_guests }} personne(s).
                        </p>
                        <small>Réservé par : {{ $reservation->customer_name }} ({{ $reservation->customer_email }})</small>
                        <div class="mt-2">
                            {{-- Bouton pour voir les détails de la réservation. --}}
                            <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-sm btn-info me-1">Détails</a>
                            {{-- J'affiche les boutons Modifier et Annuler seulement si la réservation n'est pas déjà annulée. --}}
                            @if($reservation->status != 'cancelled')
                                {{-- Bouton pour modifier la réservation. --}}
                                <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-sm btn-warning me-1">Modifier</a>
                                {{-- Formulaire pour annuler (supprimer) la réservation.
                                     onsubmit demande une confirmation JavaScript avant d'envoyer le formulaire. --}}
                                <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?');">
                                    @csrf {{-- Protection CSRF. --}}
                                    @method('DELETE') {{-- Indique à Laravel que c'est une requête DELETE. --}}
                                    <button type="submit" class="btn btn-sm btn-danger">Annuler</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
