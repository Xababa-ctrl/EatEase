@extends('layout.layout')

@section('title', 'Gestion des Réservations')

@section('content')
<div class="container mt-4">
    <h1>Gestion des Réservations</h1>
    <p>Consultez et gérez les réservations pour vos restaurants.</p>

    {{-- J'affiche les messages flash (succès, avertissement, erreur) s'il y en a en session.
         Utile pour donner un retour à l'utilisateur après une action (confirmer/rejeter une réservation). --}}
    @if (session('success')) 
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('warning')) 
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
     @if (session('error')) 
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header">
            Liste des Réservations
            {{-- TODO: Si j'ai besoin de filtrer les réservations (par statut, par date, par restaurant),
                 c'est ici que j'ajouterais les contrôles de formulaire pour ces filtres. --}}
        </div>
        <div class="card-body">
            {{-- Je vérifie si la collection de réservations est vide. --}}
            @if($reservations->isEmpty()) 
                <div class="alert alert-info" role="alert">
                    {{-- Message affiché si aucune réservation n'est trouvée. --}}
                    Aucune réservation à afficher pour le moment.
                </div>
            @else
                {{-- Si des réservations existent, je les affiche dans un tableau responsive. --}}
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Restaurant</th>
                                <th>Date</th>
                                <th>Heure</th>
                                {{-- Colonne pour les informations du client. --}}
                                <th>Client</th>
                                <th>Personnes</th>
                                <th>Statut</th>
                                <th>Demandée le</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Je boucle sur chaque réservation pour afficher ses détails. --}}
                            @foreach ($reservations as $reservation)
                                <tr>
                                    <td>{{ $reservation->restaurant->name }}</td>
                                    <td>{{ $reservation->reservation_time->format('d/m/Y') }}</td>
                                    <td>{{ $reservation->reservation_time->format('H:i') }}</td>
                                    <td>
                                        {{-- J'affiche le nom de l'utilisateur s'il est associé à la réservation (utilisateur connecté),
                                             sinon j'affiche le nom du client fourni lors de la réservation.
                                             Le '??' est l'opérateur de coalescence nulle. --}}
                                        {{ $reservation->user->name ?? $reservation->customer_name }}
                                        <br>
                                        <small class="text-muted">{{ $reservation->user->email ?? $reservation->customer_email }}</small>
                                        {{-- J'affiche le téléphone s'il est disponible, soit directement sur la réservation,
                                             soit via l'utilisateur associé (si le modèle User a un champ 'phone'). --}}
                                        @if($reservation->customer_phone || ($reservation->user && $reservation->user->phone)) {{-- TODO: Adapter si le modèle User a un champ 'phone'. --}}
                                        <br><small class="text-muted">Tél: {{ $reservation->user->phone ?? $reservation->customer_phone }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $reservation->party_size }}</td>
                                    <td>
                                        {{-- J'utilise un bloc @php pour définir dynamiquement la classe du badge Bootstrap
                                             en fonction du statut de la réservation. C'est plus propre que de multiples @if. --}}
                                    
                                        {{-- J'affiche le statut avec le badge et je formate le texte (majuscule, remplacement des '_'). --}}
                                        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</span>
                                    </td>
                                    <td>{{ $reservation->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        {{-- J'affiche les boutons d'action (Confirmer/Rejeter) seulement si la réservation est en statut 'pending'. --}}
                                        @if ($reservation->status === 'pending')
                                            {{-- Formulaire pour confirmer la réservation.
                                                 Utilise la méthode PATCH et une confirmation JavaScript. --}}
                                            <form action="{{ route('restaurateur.reservations.confirm', $reservation) }}" method="POST" class="d-inline me-1" onsubmit="return confirm('Confirmer cette réservation ?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success" title="Confirmer">
                                                    {{-- J'utilise des icônes Font Awesome pour les boutons. --}}
                                                    <i class="fas fa-check"></i> Valider
                                                </button>
                                            </form>

                                            {{-- Formulaire pour rejeter la réservation.
                                                 Utilise la méthode PATCH et une confirmation JavaScript. --}}
                                            <form action="{{ route('restaurateur.reservations.reject', $reservation) }}" method="POST" class="d-inline" onsubmit="return confirm('Rejeter cette réservation ?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Rejeter">
                                                    <i class="fas fa-times"></i> Rejeter
                                                </button>
                                            </form>
                                        @else
                                            {{-- Si le statut n'est plus 'pending', je n'affiche pas d'action. --}}
                                            <span class="text-muted">-</span>
                                        @endif
                                        {{-- TODO: Si j'ai besoin d'un lien vers une page de détails spécifique pour le restaurateur,
                                             je le décommenterai et l'adapterai. --}}
                                        {{-- <a href="#" class="btn btn-sm btn-outline-info ms-1" title="Voir détails"><i class="fas fa-eye"></i></a> --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- J'affiche les liens de pagination si la collection $reservations est paginée. --}}
                <div class="mt-3">
                    {{ $reservations->links() }}
                </div>

            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
{{-- J'inclus la feuille de style de Font Awesome depuis un CDN pour pouvoir utiliser les icônes
     (comme fas fa-check et fas fa-times) dans mes boutons d'action. --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
