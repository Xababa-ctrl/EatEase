<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Admin</title>

    {{-- Chargement de Bootstrap via CDN pour un style rapide et responsive --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

    {{-- Carte centrée contenant le formulaire de connexion --}}
    <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
        <h3 class="text-center mb-4">Connexion Admin</h3>

        {{-- Affichage d’une erreur si la validation échoue (ex : mauvais mot de passe) --}}
        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Formulaire de connexion vers la route admin.login.submit --}}
        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf {{-- Jeton CSRF obligatoire pour sécuriser la requête --}}

            {{-- Champ email --}}
            <div class="mb-3">
                <label for="email" class="form-label">Adresse email</label>
                <input type="email" name="email" class="form-control" required autofocus>
            </div>

            {{-- Champ mot de passe --}}
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            {{-- Bouton de soumission --}}
            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
        </form>
    </div>

</body>
</html>
