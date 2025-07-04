@extends('admin.layout')

@section('title', 'Ajouter un administrateur')

@section('content')
    <h2 class="mb-4">Ajouter un administrateur</h2>

    {{-- Formulaire d’ajout d’un nouvel administrateur --}}
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        {{-- Nom de l’administrateur --}}
        <div class="mb-3">
            <label>Nom</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        {{-- Adresse email --}}
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        {{-- Mot de passe --}}
        <div class="mb-3">
            <label>Mot de passe</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        {{-- Confirmation du mot de passe --}}
        <div class="mb-3">
            <label>Confirmation du mot de passe</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        {{-- Boutons EatEase --}}
        <button type="submit" class="btn btn-success">Créer</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
@endsection
