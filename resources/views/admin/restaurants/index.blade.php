@extends('admin.layout')

@section('title', 'Restaurants')

@section('content')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Liste des restaurants</h2>

        {{-- Bouton Ajouter avec couleur personnalisée --}}
        <a href="{{ route('admin.restaurants.create') }}" class="btn btn-eatease">Ajouter un restaurant</a>
    </div>

    @if($restaurants->isEmpty())
        <div class="alert alert-info">Aucun restaurant enregistré.</div>
    @else
        <table class="table table-bordered table-hover bg-white">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Adresse</th>
                    <th>Téléphone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($restaurants as $restaurant)
                    <tr>
                        <td>{{ $restaurant->id }}</td>
                        <td>{{ $restaurant->name }}</td>
                        <td>{{ $restaurant->address }}</td>
                        <td>{{ $restaurant->phone_number }}</td>
                        <td>

                            {{-- Bouton Éditer personnalisé --}}
                            <a href="{{ route('admin.restaurants.edit', $restaurant->id) }}"
                                class="btn btn-sm btn-eatease-secondary">Éditer</a>

                            {{-- Bouton Supprimer en style outline + confirmation --}}
                            <form action="{{ route('admin.restaurants.destroy', $restaurant->id) }}" method="POST"
                                style="display:inline-block;" onsubmit="return confirm('Confirmer la suppression ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-eatease-outline">Supprimer</button>
                            </form>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

@endsection
