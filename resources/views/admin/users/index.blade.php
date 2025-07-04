@extends('admin.layout')

@section('title', 'Utilisateurs')

@section('content')
    <h2 class="mb-4">Administrateurs</h2>

    {{-- Affichage des messages flash (succès ou erreur) --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Bouton pour ajouter un nouvel administrateur --}}
    <div class="mb-3">
        <a href="{{ route('admin.users.create') }}" class="btn btn-eatease">Ajouter un administrateur</a>
    </div>

    {{-- Tableau listant tous les administrateurs --}}
    <table class="table table-bordered bg-white mb-5">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Créé le</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($admins as $admin)
                <tr>
                    <td>{{ $admin->id }}</td>
                    <td>{{ $admin->name }}</td>
                    <td>{{ $admin->email }}</td>
                    <td>{{ $admin->created_at->format('d/m/Y') }}</td>
                    <td>
                        {{-- Lien vers la vue détaillée --}}
                        <a href="{{ route('admin.users.show', $admin->id) }}" class="btn btn-eatease-view btn-sm">Voir</a>

                        {{-- Lien vers le formulaire d’édition --}}
                        <a href="{{ route('admin.users.edit', $admin->id) }}" class="btn btn-eatease-secondary btn-sm">Modifier</a>

                        {{-- Formulaire de suppression avec confirmation --}}
                        <form action="{{ route('admin.users.destroy', $admin->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Confirmer la suppression ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-eatease-outline btn-sm">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Aucun administrateur trouvé.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Tableau listant les utilisateurs "classiques" (clients et restaurateurs) --}}
    <h2 class="mb-4">Utilisateurs (Clients et Restaurateurs)</h2>

    <table class="table table-bordered bg-white">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Créé le</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ ucfirst($user->role) }}</td>
                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                    <td>
                        {{-- Lien vers la fiche utilisateur --}}
                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-eatease-view btn-sm">Voir</a>

                        {{-- Lien vers le formulaire de modification --}}
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-eatease-secondary btn-sm">Modifier</a>

                        {{-- Formulaire de suppression avec confirmation --}}
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Confirmer la suppression ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-eatease-outline btn-sm">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Aucun utilisateur trouvé.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
