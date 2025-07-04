<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;

class ContactController extends Controller
{
    /**
     * Ma méthode pour afficher la page du formulaire de contact.
     */
    public function show(): View
    {
        // Je retourne simplement la vue 'contact.blade.php'
        return view('contact');
    }

    /**
     * Ma méthode pour traiter les données quand un utilisateur soumet le formulaire de contact.
     */
    public function send(Request $request): RedirectResponse
    {
        // D'abord, je valide les données envoyées par le formulaire.
        // J'ajoute ici des messages d'erreur personnalisés EN FRANÇAIS.
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10', // Le message doit faire au moins 10 caractères.
        ], [
            // Ici, je définis les messages d'erreurs personnalisés pour chaque champ :
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'subject.required' => 'Le sujet est obligatoire.',
            'message.required' => 'Le message est obligatoire.',
            'message.min' => 'Le message doit contenir au moins 10 caractères.',
        ]);

        try {
            // J'essaie d'envoyer l'email en utilisant ma classe ContactFormMail
            Mail::to(config('mail.from.address'))->send(new ContactFormMail($validated));
        } catch (\Exception $e) {
            // En cas d'erreur pendant l'envoi de l'email (ex : problème SMTP)
            return redirect()->route('contact')
                             ->with('error', 'Erreur lors de l\'envoi du message. Veuillez réessayer.');
        }

        // Si tout s'est bien passé, je redirige vers la page de contact avec un message de succès.
        return redirect()->route('contact')
                         ->with('success', 'Votre message a été envoyé avec succès !');
    }
}
