<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Ton CSS personnalisé -->
    @vite(['resources/css/custom.css'])
</head>

<body class="admin-body">

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column justify-content-between">
        <div>
            <div class="text-center p-3 border-bottom bg-white">
                <img src="{{ asset('images/Logo.png') }}" alt="Logo" width="100">
            </div>
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.restaurants.index') }}">Restaurants</a>
            <a href="{{ route('admin.reservations.index') }}">Réservations</a>
            <a href="{{ route('admin.users.index') }}">Utilisateurs</a>
            <a href="{{ route('admin.reviews.index') }}">Avis Clients</a>
        </div>

        <form method="POST" action="{{ route('admin.logout') }}" class="p-3">
            @csrf
            <button type="submit" class="btn btn-outline-light w-100">Déconnexion</button>
        </form>
    </div>

    <!-- Contenu principal -->
    <div class="main-content">
        @yield('content')
    </div>

</body>

</html>