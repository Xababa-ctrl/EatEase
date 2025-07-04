@extends('admin.layout')

@section('title', 'Modifier réservation')

@section('content')
<h2>Modifier une réservation</h2>

{{-- Formulaire pour modifier une réservation existante --}}
<form method="POST" action="{{ route('admin.reservations.update', $reservation->id) }}">
    @csrf {{-- Protection CSRF obligatoire pour les formulaires --}}
    @method('PUT') {{-- Méthode HTTP simulée pour la mise à jour --}}

    {{-- Sélection du restaurant concerné par la réservation --}}
    <div class="mb-3">
        <label>Restaurant</label>
        <select name="restaurant_id" class="form-control" required>
            @foreach ($restaurants as $restaurant)
                {{-- Je présélectionne l’option correspondant à la réservation actuelle --}}
                <option value="{{ $restaurant->id }}" @selected($reservation->restaurant_id == $restaurant->id)>
                    {{ $restaurant->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Si le client est un utilisateur inscrit, je n’affiche que son nom (lecture seule) --}}
    @if($reservation->user)
        <div class="mb-3">
            <label>Client inscrit</label>
            <input type="text" class="form-control" value="{{ $reservation->user->name }}" disabled>
            {{-- Champ caché pour garder l’ID de l’utilisateur --}}
            <input type="hidden" name="user_id" value="{{ $reservation->user_id }}">
        </div>
    @else
        {{-- Sinon, je montre les champs du client manuel (nom, email, téléphone) --}}
        <div class="mb-3">
            <label>Nom du client</label>
            <input type="text" name="customer_name" class="form-control"
                   value="{{ old('customer_name', $reservation->customer_name) }}" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="customer_email" class="form-control"
                   value="{{ old('customer_email', $reservation->customer_email) }}" required>
        </div>

        <div class="mb-3">
            <label>Téléphone</label>
            <input type="text" name="customer_phone" class="form-control"
                   value="{{ old('customer_phone', $reservation->customer_phone) }}" required>
        </div>
    @endif

    {{-- Date et heure de la réservation au format datetime-local --}}
    <div class="mb-3">
        <label>Date & heure</label>
        <input type="datetime-local" name="reservation_time" class="form-control"
               value="{{ old('reservation_time', \Carbon\Carbon::parse($reservation->reservation_time)->format('Y-m-d\TH:i')) }}" required>
    </div>

    {{-- Nombre de personnes prévues pour la réservation --}}
    <div class="mb-3">
        <label>Nombre de personnes</label>
        <input type="number" name="party_size" class="form-control"
               value="{{ old('party_size', $reservation->party_size) }}" required>
    </div>

    {{-- Statut actuel de la réservation (à confirmer, confirmée, ou rejetée) --}}
    <div class="mb-3">
        <label>Statut</label>
        <select name="status" class="form-control" required>
            <option value="pending" @selected($reservation->status === 'pending')>En attente</option>
            <option value="confirmed" @selected($reservation->status === 'confirmed')>Confirmée</option>
            <option value="rejected" @selected($reservation->status === 'rejected')>Rejetée</option>
        </select>
    </div>

    {{-- Boutons personnalisés EatEase --}}
    <button type="submit" class="btn btn-eatease">Mettre à jour</button>
    <a href="{{ route('admin.reservations.index') }}" class="btn btn-eatease-secondary">Annuler</a>
</form>
@endsection
