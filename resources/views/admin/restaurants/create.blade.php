@extends('admin.layout')

@section('title', 'Ajouter un restaurant')

@section('content')
    <h2>Ajouter un nouveau restaurant</h2>

    {{-- Formulaire d'ajout d'un restaurant depuis le back-office admin --}}
    <form method="POST" action="{{ route('admin.restaurants.store') }}">
        @csrf {{-- Jeton CSRF pour la sécurité du formulaire --}}

        {{-- Champ pour le nom du restaurant --}}
        <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        {{-- Champ pour l'adresse complète du restaurant --}}
        <div class="mb-3">
            <label class="form-label">Adresse</label>
            <input type="text" name="address" class="form-control" required>
        </div>

        {{-- Champ pour le numéro de téléphone du restaurant --}}
        <div class="mb-3">
            <label class="form-label">Téléphone</label>
            <input type="text" name="phone_number" class="form-control" required>
        </div>

        {{-- Boutons personnalisés EatEase --}}
        <button type="submit" class="btn btn-eatease">Enregistrer</button>
        <a href="{{ route('admin.restaurants.index') }}" class="btn btn-eatease-secondary">Annuler</a>
    </form>
@endsection
