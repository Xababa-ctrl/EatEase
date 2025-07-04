@extends('layout.layout')

@section('title', 'Modifier la Réservation #' . $reservation->id)

@section('content')
    <div class="container mt-4">
        <h1>Modifier la Réservation #{{ $reservation->id }}</h1>

        {{-- Mon formulaire pour mettre à jour la réservation.
             Il envoie les données en POST (mais Laravel le traitera comme un PUT grâce à @method('PUT'))
             à la route 'reservations.update' avec l'ID de la réservation. --}}
        <form action="{{ route('reservations.update', $reservation) }}" method="POST">
            @csrf {{-- Protection CSRF, toujours. --}}
            @method('PUT') {{-- Indique à Laravel que c'est une requête PUT pour la mise à jour. --}}

            {{-- Sélection du restaurant --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="restaurant_id" class="form-label">Restaurant</label>
                    <select class="form-select @error('restaurant_id') is-invalid @enderror" id="restaurant_id" name="restaurant_id" required>
                        <option value="" disabled>-- Sélectionnez --</option>
                        @foreach ($restaurants as $id => $name)
                            <option value="{{ $id }}" {{ old('restaurant_id', $reservation->restaurant_id) == $id ? 'selected' : '' }}>
                                {{-- old('restaurant_id', $reservation->restaurant_id) :
                                     Si la validation échoue, j'utilise la valeur 'old'. Sinon, j'utilise la valeur actuelle de la réservation. --}}
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('restaurant_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Nombre de personnes --}}
                <div class="col-md-6 mb-3">
                    <label for="number_of_guests" class="form-label">Nombre de Personnes</label>
                    <input type="number" class="form-control @error('number_of_guests') is-invalid @enderror" id="number_of_guests" name="number_of_guests" value="{{ old('number_of_guests', $reservation->number_of_guests) }}" min="1" required>
                    {{-- Idem pour old() et la valeur actuelle. min="1" pour au moins une personne. --}}
                    @error('number_of_guests')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                {{-- Date de la réservation --}}
                <div class="col-md-6 mb-3">
                    <label for="reservation_date" class="form-label">Date de Réservation</label>
                    <input type="date" class="form-control @error('reservation_date') is-invalid @enderror" id="reservation_date" name="reservation_date" value="{{ old('reservation_date', $reservation->reservation_date->format('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                    {{-- Je formate la date de la réservation en 'Y-m-d' pour le champ input type="date".
                         min="{{ date('Y-m-d') }}" empêche de sélectionner une date passée. --}}
                    @error('reservation_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Heure de la réservation --}}
                <div class="col-md-6 mb-3">
                    <label for="reservation_time" class="form-label">Heure de Réservation</label>
                    <input type="time" class="form-control @error('reservation_time') is-invalid @enderror" id="reservation_time" name="reservation_time" value="{{ old('reservation_time', \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i')) }}" required>
                    {{-- Je formate l'heure de la réservation en 'H:i' pour le champ input type="time".
                         J'utilise Carbon::parse() car $reservation->reservation_time est un objet DateTime complet. --}}
                    @error('reservation_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr> {{-- Séparateur visuel --}}
            <h5>Informations Client</h5>

            <div class="mb-3">
                <label for="customer_name" class="form-label">Nom Complet</label>
                <input type="text" class="form-control @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name" value="{{ old('customer_name', $reservation->customer_name) }}" required>
                @error('customer_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="customer_email" class="form-label">Email</label>
                    <input type="email" class="form-control @error('customer_email') is-invalid @enderror" id="customer_email" name="customer_email" value="{{ old('customer_email', $reservation->customer_email) }}" required>
                    @error('customer_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="customer_phone" class="form-label">Téléphone (Optionnel)</label>
                    <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror" id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $reservation->customer_phone) }}">
                    @error('customer_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

             {{-- Champ pour modifier le statut (si admin) --}}
             {{-- J'ai laissé cette section commentée. Si j'ai besoin de permettre la modification du statut
                  (par exemple, pour un admin), je peux la décommenter et implémenter la logique de Policy/Gate. --}}
             {{--
             @can('update-status', $reservation) // Exemple de Policy/Gate
             <div class="mb-3">
                 <label for="status" class="form-label">Statut</label>
                 <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                     <option value="pending" {{ old('status', $reservation->status) == 'pending' ? 'selected' : '' }}>En attente</option>
                     <option value="confirmed" {{ old('status', $reservation->status) == 'confirmed' ? 'selected' : '' }}>Confirmée</option>
                     <option value="cancelled" {{ old('status', $reservation->status) == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                 </select>
                 @error('status')
                     <div class="invalid-feedback">{{ $message }}</div>
                 @enderror
             </div>
             @endcan
             --}}

            <button type="submit" class="btn btn-primary">Mettre à jour la Réservation</button> {{-- Bouton pour soumettre les modifications. --}}
            {{-- Lien pour annuler et retourner à la page de détails de la réservation. --}}
            <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-secondary">Annuler</a>

        </form>
    </div>
@endsection
