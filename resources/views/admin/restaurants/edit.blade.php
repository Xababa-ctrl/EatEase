@extends('admin.layout')

@section('title', 'Modifier un restaurant')

@section('content')
    <h2>Modifier le restaurant</h2>

    {{-- Formulaire de mise à jour d'un restaurant existant --}}
    <form method="POST" action="{{ route('admin.restaurants.update', $restaurant->id) }}">
        @csrf {{-- Jeton CSRF pour la sécurité --}}
        @method('PUT') {{-- Méthode HTTP PUT pour la mise à jour --}}

        {{-- Champ prérempli : Nom du restaurant --}}
        <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" name="name" class="form-control"
                   value="{{ old('name', $restaurant->name) }}" required>
        </div>

        {{-- Champ prérempli : Adresse du restaurant --}}
        <div class="mb-3">
            <label class="form-label">Adresse</label>
            <input type="text" name="address" class="form-control"
                   value="{{ old('address', $restaurant->address) }}" required>
        </div>

        {{-- Champ prérempli : Téléphone du restaurant --}}
        <div class="mb-3">
            <label class="form-label">Téléphone</label>
            <input type="text" name="phone_number" class="form-control"
                   value="{{ old('phone_number', $restaurant->phone_number) }}" required>
        </div>

        {{-- Boutons personnalisés EatEase --}}
        <button type="submit" class="btn btn-eatease">Mettre à jour</button>
        <a href="{{ route('admin.restaurants.index') }}" class="btn btn-eatease-secondary">Annuler</a>
    </form>
@endsection
