@extends('admin.layout')

@section('title', 'Réservations')

@section('content')
    <h2 class="mb-4">Liste des réservations</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped bg-white">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nom client</th>
                <th>Email</th>
                <th>Date & Heure</th>
                <th>Personnes</th>
                <th>Statut</th>
                <th>Restaurant</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reservations as $reservation)
                <tr>
                    <td>{{ $reservation->id }}</td>

                    {{-- Nom du client --}}
                    <td>
                        @if($reservation->user)
                            {{ $reservation->user->name }}
                        @else
                            {{ $reservation->customer_name ?? 'Inconnu' }}
                        @endif
                    </td>

                    {{-- Email du client --}}
                    <td>
                        @if($reservation->user)
                            {{ $reservation->user->email }}
                        @else
                            {{ $reservation->customer_email ?? 'Non fourni' }}
                        @endif
                    </td>

                    

                    {{-- Date et heure --}}
                    <td>{{ \Carbon\Carbon::parse($reservation->reservation_time)->format('d/m/Y H:i') }}</td>

                    {{-- Nombre de personnes --}}
                    <td>{{ $reservation->party_size }}</td>

                    {{-- Statut --}}
                    <td>{{ ucfirst($reservation->status) }}</td>

                    {{-- Restaurant --}}
                    <td>{{ $reservation->restaurant->name ?? 'N/A' }}</td>

                    {{-- Actions --}}
                    <td>
                        <a href="{{ route('admin.reservations.edit', $reservation->id) }}"
                            class="btn btn-eatease-secondary btn-sm">Éditer</a>

                        <form action="{{ route('admin.reservations.destroy', $reservation->id) }}" method="POST"
                            style="display:inline-block;" onsubmit="return confirm('Supprimer ?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-eatease-outline btn-sm">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
