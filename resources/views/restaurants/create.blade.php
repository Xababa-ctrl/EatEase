@extends('layout.layout')

@section('title', 'Ajouter un Restaurant')

@section('content')
    <div class="container mt-4">
        <h1>Ajouter un Nouveau Restaurant</h1>

        {{-- Mon formulaire pour créer un nouveau restaurant.
             Il envoie les données en POST à la route 'restaurants.store'. --}}
        <form action="{{ route('restaurants.store') }}" method="POST" enctype="multipart/form-data">
            @csrf {{-- Protection CSRF, indispensable pour les formulaires POST. --}}

            {{-- Champ pour le nom du restaurant. --}}
            <div class="mb-3">
                <label for="name" class="form-label">Nom du Restaurant</label>
                {{-- old('name') : si la validation échoue, la valeur entrée est conservée.
                     @error('name') is-invalid @enderror : ajoute la classe Bootstrap pour le style d'erreur. --}}
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name') {{-- Si une erreur de validation concerne 'name', je l'affiche ici. --}}
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Champ pour la description du restaurant. --}}
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description') {{-- Si une erreur de validation concerne 'description', je l'affiche ici. --}}
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Champ pour l'adresse du restaurant. --}}
            <div class="mb-3">
                <label for="address" class="form-label">Adresse</label>
                <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address') }}" required>
                @error('address') {{-- Si une erreur de validation concerne 'address', je l'affiche ici. --}}
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="phone_number" class="form-label">Numéro de Téléphone</label>
                <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number') }}">
                @error('phone_number') {{-- Si une erreur de validation concerne 'phone_number', je l'affiche ici. --}}
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

             {{-- Nouveau champ pour les photos de la galerie --}}
    <div>
        <label for="photos">Photos de la galerie (optionnel)</label>
            <input type="file" name="photos[]" id="photos" multiple accept="image/*">
        {{-- 'photos[]' permet de recevoir plusieurs fichiers --}}
        {{-- 'multiple' permet la sélection de plusieurs fichiers dans le navigateur --}}
        {{-- 'accept="image/*"' filtre les types de fichiers affichés dans la boîte de dialogue --}}
        @error('photos')
            <span>{{ $message }}</span>
        @enderror
        @error('photos.*') {{-- Pour afficher les erreurs de validation pour chaque fichier --}}
            <span>{{ $message }}</span>
        @enderror

            </div>

    <hr class="my-4">

    <div>
        <h4>Horaires d'ouverture</h4>
        <p class="text-muted">Si un jour est coché sans heure d'ouverture, le restaurant sera considéré comme fermé ce jour-là.</p>

        @php
            $daysOfWeek = [
                1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi',
                5 => 'Vendredi', 6 => 'Samedi', 7 => 'Dimanche',
            ];
        @endphp

        @foreach ($daysOfWeek as $day => $dayName)
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="opening_hours[{{ $day }}][is_open]" value="1" id="day_{{ $day }}">
                <label class="form-check-label" for="day_{{ $day }}">{{ $dayName }}</label>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <label for="open_time_{{ $day }}" class="form-label">Heure d'ouverture</label>
                    <input type="time" class="form-control" id="open_time_{{ $day }}" name="opening_hours[{{ $day }}][open_time]" value="{{ old('opening_hours.' . $day . '.open_time') }}">
                    @error('opening_hours.' . $day . '.open_time')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="close_time_{{ $day }}" class="form-label">Heure de fermeture</label>
                    <input type="time" class="form-control" id="close_time_{{ $day }}" name="opening_hours[{{ $day }}][close_time]" value="{{ old('opening_hours.' . $day . '.close_time') }}">
                    @error('opening_hours.' . $day . '.close_time')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            @error('opening_hours.' . $day)
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
        @endforeach
    </div>

            {{-- Bouton pour soumettre le formulaire. --}}
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            {{-- Bouton pour annuler et retourner à la liste des restaurants. --}}
            <a href="{{ route('restaurants.index') }}" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
@endsection
