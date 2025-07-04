@extends('layout.layout')

@section('title', $restaurant->name . ' - Détails et Réservation')

@php
    // Vérifie si l'utilisateur connecté est le propriétaire du restaurant
    $isOwner = false;
    if (Auth::check() && Auth::user()->isRestaurateur()) {
        $isOwner = Auth::id() === $restaurant->user_id;
    }

    // Liste des jours de la semaine pour l'affichage des horaires
    $daysOfWeek = [
        1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi',
        5 => 'Vendredi', 6 => 'Samedi', 7 => 'Dimanche',
    ];
@endphp

@section('content')
<div class="row">
    {{-- Informations du restaurant --}}
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
                                                {{ \Carbon\Carbon::parse($hours->open_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($hours->close_time)->format('H:i') }}
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

    {{-- Colonne droite : Formulaire ou Espace Propriétaire --}}
    <div class="col-lg-5">
        @if ($isOwner)
            {{-- Si le restaurateur est connecté, pas de formulaire --}}
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
        @elseif (Auth::check())
            {{-- Formulaire de réservation si l'utilisateur est connecté en tant que client --}}
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

                        {{-- Bouton --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-eatease btn-lg">Envoyer ma demande</button>
                        </div>
                        <p class="text-muted small mt-2 text-center">Votre réservation sera en attente de confirmation par le restaurateur.</p>
                    </form>
                </div>
            </div>
        @else
            {{-- Si l'utilisateur n'est pas connecté, message d'invitation --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <p class="text-muted">Pour réserver une table, vous devez <a href="{{ route('login') }}">vous connecter</a> ou <a href="{{ route('register') }}">créer un compte</a>.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
