@extends('layout.layout')

@section('title', 'Inscription')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Inscription</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        {{-- Nom --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom</label>
                            {{-- J'utilise old('name') pour que l'utilisateur n'ait pas à retaper son nom si la validation échoue ailleurs. --}}
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus> {{-- autofocus met le curseur ici au chargement de la page --}}
                            @error('name') {{-- Si une erreur de validation spécifique au champ 'name' arrive, elle s'affiche ici. --}}
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse Email</label>
                            {{-- J'utilise old('email') pour que l'utilisateur n'ait pas à retaper son email. --}}
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                            @error('email') {{-- Si une erreur de validation spécifique au champ 'email' arrive (par exemple, format invalide ou déjà utilisé), elle s'affiche ici. --}}
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Mot de passe --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            {{-- autocomplete="new-password" est une indication pour les navigateurs que c'est un champ pour un nouveau mot de passe. --}}
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                            @error('password') {{-- Si une erreur de validation spécifique au champ 'password' arrive (par exemple, trop court), elle s'affiche ici. --}}
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Confirmation Mot de passe --}}
                        <div class="mb-3">
                            <label for="password-confirm" class="form-label">Confirmer le mot de passe</label>
                            {{-- Ce champ n'a pas besoin de la directive @error car Laravel gère la confirmation via la règle 'confirmed' sur le champ 'password'. --}}
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                        </div>

                        {{-- Choix du rôle --}}
                        <div class="mb-3">
                            <label for="role" class="form-label">Je suis un...</label>
                            {{-- J'utilise old('role') pour pré-sélectionner le rôle si la validation échoue. --}}
                            <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Client</option>
                                <option value="restaurateur" {{ old('role') == 'restaurateur' ? 'selected' : '' }}>Restaurateur</option>
                            </select>
                            @error('role') {{-- Si une erreur de validation spécifique au champ 'role' arrive, elle s'affiche ici. --}}
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-eatease">
                                S'inscrire
                            </button>
                        </div>
                    </form>
                </div>
            </div>
             <div class="text-center mt-3">
                <p>Déjà un compte ? <a href="{{ route('login') }}">Connectez-vous ici</a></p> {{-- Lien vers la page de connexion --}}
            </div>
        </div>
    </div>
</div>
@endsection
