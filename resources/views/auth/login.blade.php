@extends('layout.layout')

@section('title', 'Connexion')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Connexion</div>
                <div class="card-body">
                    {{-- Si Laravel renvoie une erreur globale (par exemple, 'auth.failed' qui signifie que les identifiants sont incorrects),
                         je l'affiche ici. L'erreur est souvent associée au champ 'email' par défaut. --}}
                     @error('email')
                        <div class="alert alert-danger" role="alert">
                            {{ $message }}
                        </div>
                    @enderror

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse Email</label>
                            {{-- J'utilise old('email') pour que l'utilisateur n'ait pas à retaper son email si la validation échoue ailleurs. --}}
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                             {{-- Si une erreur spécifique au champ 'email' arrive (par exemple, format invalide), elle s'affiche ici.
                                  Le @error('email') plus haut gère déjà les erreurs générales d'authentification.
                                  Il n'y a généralement pas besoin d'un @error('email') séparé ici si le premier est présent,
                                  sauf si on veut un message différent ou un emplacement différent pour les erreurs de format vs. les erreurs d'auth.
                                  Pour l'instant, je le laisse, mais c'est bon à savoir. --}}
                        </div>

                        {{-- Mot de passe --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                             @error('password') {{-- Normalement, on n'affiche pas d'erreur spécifique pour le mot de passe pour des raisons de sécurité (on dit juste "identifiants incorrects"). Laravel peut le faire si la validation échoue avant la tentative d'authentification. --}}
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Se souvenir de moi --}}
                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Se souvenir de moi
                            </label>
                        </div>

                        <div class="d-grid mb-3">
                             <button type="submit" class="btn btn-eatease">
                                Connexion
                            </button>
                        </div>

                        {{-- J'ai commenté cette section pour le moment, mais c'est ici que je mettrais le lien "Mot de passe oublié ?" si j'implémente cette fonctionnalité. --}}
                        {{-- @if (Route::has('password.request'))
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                Mot de passe oublié ?
                            </a>
                        @endif --}}
                    </form>
                </div>
            </div>
             <div class="text-center mt-3">
                <p>Pas encore de compte ? <a href="{{ route('register') }}">Inscrivez-vous</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
