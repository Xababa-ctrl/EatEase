@extends('layout.layout')

@section('title', 'Contactez-nous')

@section('content')
<div class="container mt-4 mb-5">
    {{-- Titre principal de la page --}}
    <h1 class="mb-4">Contactez-nous</h1>

    

    {{-- Mon formulaire de contact.
         Il envoie les données vers la méthode "send" de mon ContactController --}}
    <form action="{{ route('contact.send') }}" method="POST">
        @csrf {{-- Protection CSRF, toujours obligatoire avec les formulaires Laravel --}}

        {{-- Champ NOM --}}
        <div class="mb-3">
            <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
            <input type="text" id="name" name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   {{-- Je préremplis le champ avec l'ancien input ou, si l'utilisateur est connecté, son nom --}}
                   value="{{ old('name', Auth::check() ? Auth::user()->name : '') }}"
                   required>
            {{-- Si Laravel détecte une erreur de validation sur ce champ, je l'affiche --}}
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Champ EMAIL --}}
        <div class="mb-3">
            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" id="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   {{-- Je préremplis avec la valeur précédente ou avec l'email de l'utilisateur connecté --}}
                   value="{{ old('email', Auth::check() ? Auth::user()->email : '') }}"
                   required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Champ SUJET --}}
        <div class="mb-3">
            <label for="subject" class="form-label">Sujet <span class="text-danger">*</span></label>
            <input type="text" id="subject" name="subject"
                   class="form-control @error('subject') is-invalid @enderror"
                   value="{{ old('subject') }}"
                   required>
            @error('subject')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Champ MESSAGE --}}
        <div class="mb-3">
            <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
            <textarea id="message" name="message"
                      class="form-control @error('message') is-invalid @enderror"
                      rows="5"
                      required>{{ old('message') }}</textarea>
            @error('message')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- BOUTON D'ENVOI --}}
        <button type="submit" class="btn btn-eatease">
            Envoyer
        </button>
    </form>
</div>
@endsection
