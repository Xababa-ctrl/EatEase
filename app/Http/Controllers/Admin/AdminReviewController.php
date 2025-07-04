<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    /**
     * Affiche la liste de tous les avis clients.
     * 
     * Cette méthode est utilisée pour la page d'administration
     * dédiée à la modération des avis clients.
     * 
     * Les avis sont récupérés depuis la base de données,
     * triés du plus récent au plus ancien grâce à latest().
     * 
     * L'administrateur peut ensuite décider de conserver ou de supprimer
     * chaque avis directement depuis la vue.
     */
    public function index()
    {
        $reviews = Review::latest()->get(); // Je récupère tous les avis en base, les plus récents en premier.

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Supprime un avis client de la base de données.
     * 
     * J'utilise l'injection de modèle (Review $review) pour récupérer directement l'avis concerné.
     * Une fois supprimé, je redirige l'administrateur vers la liste des avis,
     * avec un message de confirmation de la suppression.
     */
    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Avis supprimé avec succès.');
    }
}
