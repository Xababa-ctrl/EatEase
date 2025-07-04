{{-- resources/views/emails/contact-form.blade.php --}}

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau message de contact</title>
    <style>
        /* Un peu de style inline pour améliorer le rendu des emails */
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }
        .email-container {
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        h2 {
            color: #E98074; 
        }
        p {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        {{-- Titre de l'email --}}
        <h2>📩 Nouveau message du formulaire de contact EatEase</h2>

        {{-- Je récupère et j'affiche les données du formulaire grâce à $data --}}
        <p><strong>Nom :</strong> {{ $data['name'] }}</p>
        <p><strong>Email :</strong> {{ $data['email'] }}</p>
        <p><strong>Sujet :</strong> {{ $data['subject'] }}</p>

        <hr>

        {{-- Message principal envoyé par l'utilisateur --}}
        <p><strong>Message :</strong></p>
        <p>{{ $data['message'] }}</p>

        <hr>

        <p style="font-size: 12px; color: #888;">Cet email a été généré automatiquement par la plateforme EatEase.</p>
    </div>
</body>
</html>
