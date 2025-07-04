<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable // Ma classe pour l'email de contact, elle hérite de Mailable de Laravel
{
    use Queueable, SerializesModels; // Ces traits sont utiles pour la mise en file d'attente et la sérialisation

    public array $data; // Je déclare une propriété publique pour stocker les données du formulaire
                        // Comme ça, je peux y accéder facilement dans ma vue d'email

    /**
     * Constructeur pour mon e-mail de contact. C'est ici que l'e-mail est initialisé avec les données du formulaire.
     *
     * @param array $data Les données validées que je récupère de mon ContactController
     */
    public function __construct(array $data) // Mon constructeur, il va prendre les données du formulaire en argument
    {
        $this->data = $data; // J'assigne les données reçues à ma propriété $data
    }

    /**
     * Configuration de l'enveloppe de l'e-mail : qui envoie, à qui, et le sujet.
     * Ici, je définis l'expéditeur et le sujet de l'email.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            // L'email sera envoyé "de la part de" l'adresse email fournie dans le formulaire
            from: $this->data['email'],
            // Le sujet de l'email inclura le sujet fourni dans le formulaire pour plus de clarté
            subject: 'Nouveau message de contact: ' . $this->data['subject'],
        );
    }

    /**
     * Définition du contenu de l'e-mail : quelle vue Blade va afficher les informations.
     * C'est ici que je dis à Laravel quelle vue Blade utiliser pour le contenu de l'email.
     */
    public function content(): Content
    {
        return new Content(
            // Ma vue Blade pour l'email se trouve dans resources/views/emails/contact-form.blade.php
            view: 'emails.contact-form',
            // Pas besoin de 'with()' ici, car les propriétés publiques de la classe Mailable
            // (comme $this->data) sont automatiquement disponibles dans la vue.
        );
    }

    /**
     * Gestion des pièces jointes pour cet e-mail.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     * Pour l'instant, je n'ai pas de pièces jointes pour ce formulaire de contact.
     */
    public function attachments(): array
    {
        return []; // Donc, je retourne un tableau vide.
    }
}
