@extends('layout.layout')

@section('title', $restaurant->name . ' - Détails et Réservation')

@php
    // Vérifie si l'utilisateur connecté est le propriétaire du restaurant
    $isOwner = false;
    if (Auth::check() && Auth::user()->isRestaurateur()) {
        $isOwner = Auth::id() === $restaurant->user_id;
    }

    // Jours de la semaine pour l'affichage des horaires
    $daysOfWeek = [
        1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi',
        5 => 'Vendredi', 6 => 'Samedi', 7 => 'Dimanche',
    ];
@endphp

@section('content')
<div class="row">
    {{-- Colonne gauche : Informations du restaurant --}}
    <div class="col-lg-7 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="card-title">{{ $restaurant->name }}</h1>
                <p class="card-text text-muted">{{ $restaurant->address }}</p>
                @if($restaurant->phone_number)
                    <p class="card-text"><strong>Téléphone :</strong> {{ $restaurant->phone_number }}</p>
                @endif

                <hr>
                <h5 class="card-subtitle mb-2">Description</h5>
                <p class="card-text">{{ $restaurant->description ?: 'Aucune description fournie.' }}</p>

                <hr>
                <h5 class="card-subtitle mb-3">Horaires d'Ouverture</h5>
                @if($restaurant->openingHours->isNotEmpty())
                    <ul class="list-group list-group-flush">
                        @foreach($daysOfWeek as $dayNumber => $dayName)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $dayName }}</span>
                                <div>
                                    @php
                                        $hoursForDay = $restaurant->openingHours->where('day_of_week', $dayNumber);
                                    @endphp
                                    @if($hoursForDay->isNotEmpty())
                                        @foreach($hoursForDay as $hours)
                                            <span class="badge bg-success me-1">
                                                {{ \Carbon\Carbon::parse($hours->open_time)->format('H:i') }}
                                                -
                                                {{ \Carbon\Carbon::parse($hours->close_time)->format('H:i') }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="badge bg-secondary">Fermé</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">Les horaires d'ouverture ne sont pas disponibles.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Colonne droite : Formulaire de réservation ou espace propriétaire --}}
    <div class="col-lg-5">
        @if ($isOwner)
            {{-- Espace restaurateur (pas de réservation) --}}
            <div class="card shadow-sm">
                <div class="card-header custom-header text-white">
                    <h4 class="mb-0">Votre Espace</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">Ceci est votre établissement. Vous ne pouvez pas y réserver de table.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('restaurants.edit', $restaurant) }}" class="btn btn-eatease">Gérer le restaurant</a>
                        <a href="{{ route('restaurateur.reservations') }}" class="btn btn-eatease-view">Voir les réservations</a>
                    </div>
                </div>
            </div>
        @elseif(Auth::check())
            {{-- Formulaire de réservation pour les clients connectés --}}
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Réserver une table</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('reservations.store', $restaurant) }}">
                        @csrf

                        {{-- Date --}}
                        <div class="mb-3">
                            <label for="reservation_date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" id="reservation_date" name="reservation_date"
                                   class="form-control @error('reservation_date') is-invalid @enderror"
                                   value="{{ old('reservation_date') }}"
                                   min="{{ date('Y-m-d') }}" required>
                            @error('reservation_date')
                                <div class="invalid-feedback">{{ $errors->first('reservation_date') }}</div>
                            @enderror
                        </div>

                        {{-- Heure --}}
                        <div class="mb-3">
                            <label for="reservation_time" class="form-label">Heure <span class="text-danger">*</span></label>
                            <input type="time" id="reservation_time" name="reservation_time"
                                   class="form-control @error('reservation_time') is-invalid @enderror"
                                   value="{{ old('reservation_time') }}"
                                   step="900" required>
                            @error('reservation_time')
                                <div class="invalid-feedback">{{ $errors->first('reservation_time') }}</div>
                            @enderror
                        </div>

                        {{-- Nombre de personnes --}}
                        <div class="mb-3">
                            <label for="party_size" class="form-label">Nombre de personnes <span class="text-danger">*</span></label>
                            <input type="number" id="party_size" name="party_size"
                                   class="form-control @error('party_size') is-invalid @enderror"
                                   value="{{ old('party_size', 2) }}" min="1" required>
                            @error('party_size')
                                <div class="invalid-feedback">{{ $errors->first('party_size') }}</div>
                            @enderror
                        </div>

                        {{-- Notes --}}
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (allergies, occasion spéciale...)</label>
                            <textarea id="notes" name="notes" rows="3"
                                      class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $errors->first('notes') }}</div>
                            @enderror
                        </div>

                        {{-- Bouton d’envoi --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-eatease btn-lg">Envoyer ma demande</button>
                        </div>
                        <p class="text-muted small mt-2 text-center">Votre réservation sera en attente de confirmation par le restaurateur.</p>
                    </form>
                </div>
            </div>
        @else
            {{-- Invitation à se connecter pour les invités --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <p class="text-muted">Pour réserver une table, vous devez <a href="{{ route('login') }}">vous connecter</a> ou <a href="{{ route('register') }}">créer un compte</a>.</p>
                </div>
            </div>
        @endif
    </div> {{-- fin col-lg-5 --}}
</div> {{-- fin row --}}

{{-- Galerie photos, placée directement après les horaires et avant les avis --}}
@if($restaurant->galleries->isNotEmpty())
    <div class="mt-4">
        <h2>Galerie Photos</h2>
        <div class="d-flex flex-wrap">
            @foreach($restaurant->galleries as $galleryImage)
                <img src="{{ asset('storage/' . $galleryImage->photo_path) }}"
                     alt="Photo du restaurant {{ $restaurant->name }}"
                     class="img-thumbnail me-2 mb-2 gallery-image"
                     style="max-width:200px; height:auto;">
            @endforeach
        </div>
    </div>
@endif

<hr class="my-4">

{{-- Section Avis des clients --}}
<div class="mt-4">
    <h3>Avis des clients</h3>

    @php
        $avgRating = $restaurant->reviews->avg('rating');
    @endphp
    @if($avgRating)
        <p class="mb-3"><strong>Note moyenne :</strong> {{ number_format($avgRating,1) }}/5 (sur {{ $restaurant->reviews->count() }} avis)</p>
    @endif

    @if($restaurant->reviews->isNotEmpty())
        @foreach($restaurant->reviews as $review)
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">
                        Note : {{ $review->rating }}/5
                        @if($review->user)
                            <small class="text-muted">- Par {{ $review->user->name }}</small>
                        @endif
                    </h5>
                    <p class="card-text">{{ $review->comment }}</p>
                    <p class="card-text"><small class="text-muted">Posté le {{ $review->created_at->format('d/m/Y H:i') }}</small></p>
                </div>
            </div>
        @endforeach
    @else
        <p>Ce restaurant n'a pas encore reçu d'avis.</p>
    @endif

    @auth
        @if(Auth::user()->isCustomer())
            <hr class="my-4">
            <h4>Laissez votre avis</h4>
            <form action="{{ route('reviews.store', $restaurant) }}" method="POST">
                @csrf
                {{-- Note --}}
                <div class="mb-3">
                    <label for="rating" class="form-label">Votre note (sur 5) <span class="text-danger">*</span></label>
                    <select id="rating" name="rating" class="form-select @error('rating') is-invalid @enderror" required>
                        <option value="" disabled selected>Choisissez une note</option>
                        @for($i=5; $i>=1; $i--)
                            <option value="{{ $i }}" {{ old('rating')==$i?'selected':'' }}>
                                {{ $i }} - {{ ['Mauvais','Pas terrible','Moyen','Très bien','Excellent'][$i-1] }}
                            </option>
                        @endfor
                    </select>
                    @error('rating')<div class="invalid-feedback">{{ $errors->first('rating') }}</div>@enderror
                </div>
                {{-- Commentaire --}}
                <div class="mb-3">
                    <label for="comment" class="form-label">Commentaire (optionnel)</label>
                    <textarea id="comment" name="comment" rows="3" class="form-control @error('comment') is-invalid @enderror">{{ old('comment') }}</textarea>
                    @error('comment')<div class="invalid-feedback">{{ $errors->first('comment') }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-primary">Envoyer mon avis</button>
            </form>
        @endif
    @endauth
</div>
@endsection
