@extends('layout.layout')

@section('title', 'Détails de la Réservation #' . $reservation->id)

@section('content')
    <div class="container mt-4">
        {{-- J'affiche un message de succès s'il y en a un en session (par exemple, après une mise à jour). --}}
        @if (session('success')) 
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- J'utilise une carte Bootstrap pour bien présenter les détails. --}}
        <div class="card shadow-sm">
            <div class="card-header">
                <h1>Détails de la Réservation #{{ $reservation->id }}</h1>
            </div>
            <div class="card-body">
                {{-- J'affiche le nom du restaurant. Le '??' est une sécurité si $reservation->restaurant est null. --}}
                <p><strong>Restaurant :</strong> {{ $reservation->restaurant->name ?? 'Restaurant inconnu' }}</p>
                {{-- Je formate la date pour qu'elle soit plus lisible (jour de la semaine, jour, mois en toutes lettres, année). --}}
                <p><strong>Date :</strong> {{ $reservation->reservation_date->format('l d F Y') }}</p> 
                {{-- Je formate l'heure en HH:MM. --}}
                <p><strong>Heure :</strong> {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }}</p>
                <p><strong>Nombre de personnes :</strong> {{ $reservation->number_of_guests }}</p>
                <hr>
                <p><strong>Nom du client :</strong> {{ $reservation->customer_name }}</p>
                <p><strong>Email du client :</strong> {{ $reservation->customer_email }}</p>
                {{-- Si le téléphone n'est pas spécifié, j'affiche 'Non spécifié'. --}}
                <p><strong>Téléphone du client :</strong> {{ $reservation->customer_phone ?? 'Non spécifié' }}</p>
                <hr>
                {{-- J'affiche le statut avec un badge Bootstrap dont la couleur change selon le statut.
                     ucfirst() met la première lettre en majuscule. --}}
                <p><strong>Statut :</strong> <span class="badge bg-{{ $reservation->status == 'confirmed' ? 'success' : ($reservation->status == 'cancelled' ? 'danger' : 'warning') }}">{{ ucfirst($reservation->status) }}</span></p>
                {{-- J'affiche la date et l'heure de création de la réservation. --}}
                <p><strong>Réservé le :</strong> {{ $reservation->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="card-footer">
                 {{-- J'affiche les boutons Modifier et Annuler seulement si la réservation n'est pas déjà annulée. --}}
                 @if($reservation->status != 'cancelled' && $reservation->status != 'rejected') {{-- Ajout de 'rejected' pour plus de cohérence --}}
                    {{-- Bouton pour modifier la réservation. --}}
                    <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-warning me-2">Modifier</a>
                    {{-- Formulaire pour annuler (supprimer) la réservation.
                         onsubmit demande une confirmation JavaScript. --}}
                    <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?');">
                        @csrf {{-- Protection CSRF. --}}
                        @method('DELETE') {{-- Indique à Laravel que c'est une requête DELETE. --}}
                        <button type="submit" class="btn btn-danger me-2">Annuler la réservation</button>
                    </form>
                @endif
                {{-- Bouton pour retourner à la liste des réservations. --}}
                <a href="{{ route('reservations.index') }}" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </div>
    </div>
@endsection
