<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    /**
     * Affiche la liste des administrateurs et des utilisateurs (clients et restaurateurs).
     * 
     * Cette méthode permet à l'administrateur principal de visualiser :
     * - Tous les comptes administrateurs
     * - Tous les utilisateurs ayant le rôle 'client' ou 'restaurateur'
     * 
     * Cela centralise la gestion des comptes utilisateurs et administrateurs sur une seule page.
     */
    public function index()
    {   
        $admins = Admin::all(); // Je récupère tous les administrateurs.
        $users = User::whereIn('role', ['client', 'restaurateur'])->get(); // Je récupère les clients et restaurateurs.

        return view('admin.users.index', compact('admins', 'users'));
    }

    /**
     * Affiche le formulaire de création d'un nouvel administrateur.
     * 
     * Permet d'ajouter un nouvel administrateur à la plateforme.
     */
    public function create()
    {   
        return view('admin.users.create');
    }

    /**
     * Enregistre un nouvel administrateur en base de données.
     * 
     * Je commence par valider les données saisies dans le formulaire :
     * - Le nom est requis.
     * - L'email doit être unique parmi les administrateurs.
     * - Le mot de passe est obligatoire, avec confirmation.
     * 
     * Le mot de passe est ensuite hashé avant d'être sauvegardé.
     */
    public function store(Request $request)
    {   
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']), // Je sécurise le mot de passe en le cryptant.
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Administrateur ajouté avec succès');
    }

    /**
     * Affiche les détails d'un administrateur spécifique.
     * 
     * Permet de visualiser les informations d'un administrateur particulier.
     */
    public function show($id)
    {
        $admin = Admin::findOrFail($id);

        return view('admin.users.show', compact('admin'));
    }

    /**
     * Affiche le formulaire de modification d'un administrateur.
     * 
     * Permet de pré-remplir les champs avec les informations actuelles
     * pour faciliter la mise à jour.
     */
    public function edit($id)
    {
        $admin = Admin::findOrFail($id);

        return view('admin.users.edit', compact('admin'));
    }

    /**
     * Met à jour les informations d'un administrateur existant.
     * 
     * Je valide les nouvelles informations :
     * - Le nom et l'email sont obligatoires.
     * - Le mot de passe est facultatif (on ne le modifie que s'il est renseigné).
     * 
     * Je fais attention à ne pas écraser le mot de passe si le champ est laissé vide.
     */
    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $id, // L'email doit rester unique (sauf pour l'admin en cours d'édition)
            'password' => 'nullable|string|min:6|confirmed', // Le mot de passe est optionnel.
        ]);

        $admin->name = $validated['name'];
        $admin->email = $validated['email'];

        if (!empty($validated['password'])) {
            $admin->password = bcrypt($validated['password']); // Je ne mets à jour le mot de passe que s'il est renseigné.
        }

        $admin->save();

        return redirect()->route('admin.users.index')->with('success', 'Administrateur mis à jour avec succès');
    }

    /**
     * Supprime un administrateur de la base de données.
     * 
     * Avant suppression, je vérifie que l'administrateur existe bien.
     * Ensuite, je le supprime définitivement.
     */
    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->delete();

        return redirect()->route('admin.users.index')->with('success', 'Administrateur supprimé');
    }
}
