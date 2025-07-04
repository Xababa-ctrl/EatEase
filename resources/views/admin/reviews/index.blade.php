@extends('admin.layout')

@section('title', 'Avis clients')

@section('content')
    <h2 class="mb-4">Liste des avis clients</h2>

    {{-- Message flash en cas de succès (par exemple : suppression réussie) --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Tableau des avis --}}
    <table class="table table-bordered bg-white">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nom client</th>
                <th>Note</th>
                <th>Commentaire</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reviews as $review)
                <tr>
                    <td>{{ $review->id }}</td>

                    {{-- Affichage du nom du client si l'utilisateur est relié à l'avis --}}
                    <td>
                        @if($review->user)
                            {{ $review->user->name }}
                        @else
                            Non identifié
                        @endif
                    </td>

                    {{-- Note donnée par le client --}}
                    <td>{{ $review->rating }}</td>

                    {{-- Commentaire du client --}}
                    <td>{{ $review->comment }}</td>

                    {{-- Date de création de l'avis --}}
                    <td>{{ $review->created_at->format('d/m/Y') }}</td>

                    {{-- Bouton de suppression d’un avis avec confirmation --}}
                    <td>
                        <form action="{{ route('admin.reviews.destroy', $review) }}"
                              method="POST"
                              onsubmit="return confirm('Confirmer la suppression ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-eatease-outline btn-sm">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                {{-- Message si aucun avis n'est disponible --}}
                <tr>
                    <td colspan="6">Aucun avis trouvé.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
