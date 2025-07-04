@extends('layout.layout')

@section('title', 'Effectuer une Réservation')

@section('content')
    <div class="container mt-4">
        <h1>Réserver une Table</h1>

        {{-- Mon formulaire pour créer une nouvelle réservation.
             Il envoie les données en POST à la route 'reservations.store'. --}}
        <form action="{{ route('reservations.store') }}" method="POST">
            @csrf {{-- Protection CSRF indispensable pour tous les formulaires POST. --}}

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="restaurant_id" class="form-label">Choisir un Restaurant</label>
                    {{-- Menu déroulant pour sélectionner un restaurant.
                         La classe 'is-invalid' s'ajoute si le champ 'restaurant_id' a une erreur de validation. --}}
                    <select class="form-select @error('restaurant_id') is-invalid @enderror" id="restaurant_id" name="restaurant_id" required>
                        <option value="" selected disabled>-- Sélectionnez --</option>
                        @foreach ($restaurants as $id => $name)
                            {{-- Je pré-sélectionne l'option si elle correspond à une ancienne valeur (old) ou à un paramètre 'restaurant_id' dans l'URL. --}}
                            <option value="{{ $id }}" {{ old('restaurant_id', request('restaurant_id')) == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('restaurant_id') {{-- Si une erreur de validation concerne 'restaurant_id', je l'affiche ici. --}}
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="number_of_guests" class="form-label">Nombre de Personnes</label>
                    {{-- old('number_of_guests') : si la validation échoue, la valeur entrée est conservée. min="1" : au moins une personne. --}}
                    <input type="number" class="form-control @error('number_of_guests') is-invalid @enderror" id="number_of_guests" name="number_of_guests" value="{{ old('number_of_guests') }}" min="1" required>
                    @error('number_of_guests')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="reservation_date" class="form-label">Date de Réservation</label>
                    {{-- min="{{ date('Y-m-d') }}" : empêche de sélectionner une date passée. --}}
                    <input type="date" class="form-control @error('reservation_date') is-invalid @enderror" id="reservation_date" name="reservation_date" value="{{ old('reservation_date') }}" min="{{ date('Y-m-d') }}" required>
                    @error('reservation_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="reservation_time" class="form-label">Heure de Réservation</label>
                    {{-- Champ pour l'heure de la réservation. --}}
                    <input type="time" class="form-control @error('reservation_time') is-invalid @enderror" id="reservation_time" name="reservation_time" value="{{ old('reservation_time') }}" required>
                    @error('reservation_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr>
            <h5>Vos Informations</h5>

            <div class="mb-3">
                <label for="customer_name" class="form-label">Votre Nom Complet</label>
                {{-- Je pré-remplis le nom avec celui de l'utilisateur connecté s'il existe (auth()->user()->name ?? '').
                     Le '?? ""' évite une erreur si l'utilisateur n'est pas connecté. --}}
                <input type="text" class="form-control @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name" value="{{ old('customer_name', auth()->user()->name ?? '') }}" required>
                @error('customer_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="customer_email" class="form-label">Votre Email</label>
                    {{-- Idem pour l'email, pré-remplissage si l'utilisateur est connecté. --}}
                    <input type="email" class="form-control @error('customer_email') is-invalid @enderror" id="customer_email" name="customer_email" value="{{ old('customer_email', auth()->user()->email ?? '') }}" required>
                    @error('customer_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="customer_phone" class="form-label">Votre Téléphone (Optionnel)</label>
                    {{-- Le numéro de téléphone est optionnel. --}}
                    <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}">
                    @error('customer_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-success">Confirmer la Réservation</button>
            {{-- url()->previous() : lien pour retourner à la page précédente. Pratique ! --}}
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Annuler</a> 

        </form>
    </div>
@endsection
