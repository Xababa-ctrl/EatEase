<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class AdminRestaurantController extends Controller
{
    /**
     * Affiche la liste de tous les restaurants.
     * 
     * Cette méthode est utilisée pour la page d'administration
     * qui affiche un tableau avec tous les restaurants enregistrés.
     */
    public function index()
    {
        $restaurants = Restaurant::all(); // Je récupère tous les restaurants en base.

        return view('admin.restaurants.index', compact('restaurants'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau restaurant.
     * 
     * Permet à l'administrateur d'ajouter un nouveau restaurant
     * via un formulaire HTML dédié.
     */
    public function create()
    {
        return view('admin.restaurants.create');
    }

    /**
     * Enregistre un nouveau restaurant dans la base de données.
     * 
     * Je commence par valider les données envoyées par le formulaire.
     * Si tout est correct, je crée un nouveau restaurant en base,
     * puis je redirige l'admin avec un message de succès.
     */
    public function store(Request $request)
    {
        // Validation des champs du formulaire
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ]);

        // Création du restaurant en base
        Restaurant::create($validated);

        return redirect()->route('admin.restaurants.index')->with('success', 'Restaurant ajouté avec succès');
    }

    /**
     * Affiche les détails d'un restaurant spécifique (non utilisé ici).
     * 
     * Méthode prévue par Laravel pour les RESTful controllers,
     * mais non utilisée dans mon projet pour le moment.
     */
    public function show(string $id)
    {
        // Non implémenté
    }

    /**
     * Affiche le formulaire d'édition pour un restaurant existant.
     * 
     * Cette méthode permet de pré-remplir le formulaire avec les données du restaurant,
     * pour que l'admin puisse les modifier.
     */
    public function edit(string $id)
    {
        $restaurant = Restaurant::findOrFail($id); // Je vérifie que le restaurant existe.

        return view('admin.restaurants.edit', compact('restaurant'));
    }

    /**
     * Met à jour les informations d'un restaurant dans la base de données.
     * 
     * Comme pour la création, je valide les nouvelles données.
     * Ensuite, je mets à jour le restaurant existant.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ]);

        $restaurant = Restaurant::findOrFail($id);
        $restaurant->update($validated);

        return redirect()->route('admin.restaurants.index')->with('success', 'Restaurant mis à jour');
    }

    /**
     * Supprime un restaurant de la base de données.
     * 
     * Avant la suppression, je vérifie que le restaurant existe.
     * Ensuite, je le supprime définitivement.
     */
    public function destroy($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $restaurant->delete();

        return redirect()->route('admin.restaurants.index')->with('success', 'Restaurant supprimé avec succès');
    }
}
