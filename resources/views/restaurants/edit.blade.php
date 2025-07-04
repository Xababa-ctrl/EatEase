@extends('layout.layout')

@section('title', 'Modifier: ' . $restaurant->name)

@section('content')
    <div class="container mt-4">
        <h1>Modifier le Restaurant : {{ $restaurant->name }}</h1>

        {{-- Mon formulaire pour mettre à jour le restaurant.
             Il envoie les données en POST (mais Laravel le traitera comme un PUT grâce à @method('PUT'))
             à la route 'restaurants.update' avec l'objet $restaurant.
             J'ajoute enctype="multipart/form-data" pour la gestion des fichiers. --}}
        <form action="{{ route('restaurants.update', $restaurant) }}" method="POST" enctype="multipart/form-data">
            @csrf {{-- Protection CSRF, toujours indispensable. --}}
            @method('PUT') {{-- Indique à Laravel que c'est une requête PUT pour la mise à jour. --}}

            {{-- Champ pour le nom du restaurant. --}}
            <div class="mb-3">
                <label for="name" class="form-label">Nom du Restaurant</label>
                {{-- old('name', $restaurant->name) :
                     Si la validation échoue, j'utilise la valeur 'old'. Sinon, j'utilise la valeur actuelle du restaurant. --}}
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $restaurant->name) }}" required>
                @error('name') {{-- Si une erreur de validation concerne 'name', je l'affiche ici. --}}
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Champ pour la description du restaurant. --}}
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $restaurant->description) }}</textarea>
                @error('description') {{-- Si une erreur de validation concerne 'description', je l'affiche ici. --}}
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Champ pour l'adresse du restaurant. --}}
            <div class="mb-3">
                <label for="address" class="form-label">Adresse</label>
                <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $restaurant->address) }}" required>
                @error('address') {{-- Si une erreur de validation concerne 'address', je l'affiche ici. --}}
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="phone_number" class="form-label">Numéro de Téléphone</label>
                <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', $restaurant->phone_number) }}">
                @error('phone_number') {{-- Si une erreur de validation concerne 'phone_number', je l'affiche ici. --}}
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <hr> {{-- Séparateur avant la section galerie --}}

            {{-- Section pour la gestion de la galerie photos --}}
            <div class="mb-3">
                <h5 class="mb-3">Gestion de la Galerie Photos</h5>

                {{-- Affichage des photos existantes avec option de suppression --}}
                @if ($restaurant->galleries->isNotEmpty())
                    <p>Photos actuelles :</p>
                    <div class="row">
                        @foreach ($restaurant->galleries as $galleryImage)
                            <div class="col-md-3 col-sm-4 col-6 mb-3">
                                <div class="card">
                                    <img src="{{ asset('storage/' . $galleryImage->photo_path) }}" class="card-img-top" alt="Photo galerie" style="height: 150px; object-fit: cover;">
                                    <div class="card-body text-center">
                                        <div class="form-check">
                                            {{-- Je donne un nom au tableau de checkboxes 'delete_photos[]'
                                                 et la valeur est l'ID de l'image à supprimer. --}}
                                            <input class="form-check-input" type="checkbox" name="delete_photos[]" value="{{ $galleryImage->id }}" id="delete_photo_{{ $galleryImage->id }}">
                                            <label class="form-check-label" for="delete_photo_{{ $galleryImage->id }}">
                                                Supprimer
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('delete_photos.*') {{-- Pour afficher les erreurs de validation si besoin pour les suppressions --}}
                        <div class="text-danger small mt-1">{{ $errors->first('delete_photos.*') }}</div>
                    @enderror
                    <hr>
                @endif

                {{-- Champ pour ajouter de nouvelles photos --}}
                <div class="mb-3">
                    <label for="photos" class="form-label">Ajouter de nouvelles photos (optionnel)</label>
                    <input type="file" class="form-control @error('photos') is-invalid @enderror @error('photos.*') is-invalid @enderror" name="photos[]" id="photos" multiple accept="image/*">
                    {{-- 'photos[]' permet de recevoir plusieurs fichiers --}}
                    {{-- 'multiple' permet la sélection de plusieurs fichiers dans le navigateur --}}
                    {{-- 'accept="image/*"' filtre les types de fichiers affichés dans la boîte de dialogue --}}
                    @error('photos') {{-- Erreur générale pour le champ photos (ex: pas un tableau) --}}
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @error('photos.*') {{-- Pour afficher les erreurs de validation pour chaque fichier individuel --}}
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                            <hr>

            <div>
                <h4>Horaires d'ouverture</h4>
                <p class="text-muted">Cochez les jours d'ouverture et entrez les heures. Si un jour est coché sans heure d'ouverture, le restaurant sera considéré comme fermé ce jour-là.</p>

                @php
                    $daysOfWeek = [
                        1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi',
                        5 => 'Vendredi', 6 => 'Samedi', 7 => 'Dimanche',
                    ];
                    // Je crée un tableau pour stocker les horaires existants par jour.
                    $existingHours = $restaurant->openingHours->keyBy('day_of_week');
                @endphp

                @foreach ($daysOfWeek as $day => $dayName)
                    <div class="mb-3">
                        <div class="form-check">
                            {{-- Je vérifie si un horaire existe déjà pour ce jour, et je coche la case en conséquence. --}}
                            <input class="form-check-input" type="checkbox" name="opening_hours[{{ $day }}][is_open]" value="1" id="day_{{ $day }}"
                                   @if(isset($existingHours[$day])) checked @endif>
                            <label class="form-check-label" for="day_{{ $day }}">{{ $dayName }}</label>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="open_time_{{ $day }}" class="form-label">Heure d'ouverture</label>
                                {{-- Si un horaire existe, j'affiche sa valeur. Sinon, je laisse le champ vide. --}}
                                <input type="time" class="form-control @error('opening_hours.' . $day . '.open_time') is-invalid @enderror" id="open_time_{{ $day }}" name="opening_hours[{{ $day }}][open_time]"
                                       value="{{ old('opening_hours.' . $day . '.open_time', isset($existingHours[$day]) ? \Carbon\Carbon::parse($existingHours[$day]->open_time)->format('H:i') : '') }}">
                                @error('opening_hours.' . $day . '.open_time')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="close_time_{{ $day }}" class="form-label">Heure de fermeture</label>
                                <input type="time" class="form-control @error('opening_hours.' . $day . '.close_time') is-invalid @enderror" id="close_time_{{ $day }}" name="opening_hours[{{ $day }}][close_time]"
                                       value="{{ old('opening_hours.' . $day . '.close_time', isset($existingHours[$day]) ? \Carbon\Carbon::parse($existingHours[$day]->close_time)->format('H:i') : '') }}">
                                @error('opening_hours.' . $day . '.close_time')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            </div>

            <hr> {{-- Séparateur avant les boutons d'action --}}

            {{-- Bouton pour soumettre les modifications. --}}
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
            {{-- Bouton pour annuler et retourner à la page de détails du restaurant. --}}
            <a href="{{ route('restaurants.show', $restaurant) }}" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
@endsection
