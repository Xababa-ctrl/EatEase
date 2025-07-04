<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Importer la façade Storage
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Models\Gallery; // Importer le modèle Gallery
use App\Models\Review;  // Importer le modèle Review (utilisé dans show)

class RestaurantController extends Controller
{
    /**
     * Constructeur : j'applique un middleware ici.
     * 'auth' est requis pour toutes les méthodes SAUF 'index', 'show', et 'search'
     * qui doivent rester publiques pour que tout le monde puisse voir les restaurants.
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show', 'search', 'carouselData']);
    }

    /**
     * Affiche la liste de tous les restaurants (avec pagination).
     * C'est la page publique que tout le monde peut voir.
     */
    public function index(): View
    {
        // Je récupère les restaurants, les plus récents d'abord, et je pagine les résultats (10 par page par défaut).
        $restaurants = Restaurant::latest()->paginate(10);
        return view('restaurants.index', compact('restaurants'));
    }

    /**
     * Gère la recherche de restaurants.
     * Peut répondre en HTML ou en JSON si c'est une requête AJAX.
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        // Je cherche le terme dans le nom, la description ou l'adresse.
        $restaurants = Restaurant::where('name', 'LIKE', "%{$query}%")
            ->paginate(10);

        // Si c'est une requête AJAX (par exemple, pour de l'autocomplétion), je renvoie du JSON.
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($restaurants);
        }

        // Sinon, j'affiche la vue normale des résultats de recherche.
        return view('restaurants.index', compact('restaurants', 'query'));
    }

    /**
     * Affiche le formulaire pour créer un nouveau restaurant.
     */
    public function create(): View
    {
        // Je vérifie si l'utilisateur a le droit de créer un restaurant (via la Policy).
        $this->authorize('create', Restaurant::class);
        return view('restaurants.create');
    }

    /**
     * Enregistre un nouveau restaurant dans la base de données.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Restaurant::class);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'photos' => 'nullable|array', // 'photos' doit être un tableau (même s'il est vide)
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validation pour chaque photo
        ]);

        // J'associe le restaurant à l'utilisateur actuellement connecté.
        $restaurantData = [
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'address' => $validatedData['address'],
            'phone_number' => $validatedData['phone_number'],
            'user_id' => Auth::id(),
        ];

        $restaurant = Restaurant::create($restaurantData);

        // Gestion des photos de la galerie
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photoFile) {
                // Stocker l'image et obtenir son chemin
                // Le chemin sera qqch comme 'galleries/restaurant_X/image_name.jpg'
                // Le stockage se fait dans storage/app/public/galleries/restaurant_X
                $path = $photoFile->store('galleries/restaurant_' . $restaurant->id, 'public');

                // Créer l'entrée dans la table galleries en utilisant la relation
                $restaurant->galleries()->create([
                    'photo_path' => $path,
                ]);
            }
        }

        // Gestion des horaires d'ouverture
        if ($request->has('opening_hours')) {
            foreach ($request->input('opening_hours') as $day => $hours) {
                if (isset($hours['is_open']) && !empty($hours['open_time']) && !empty($hours['close_time'])) {
                    $restaurant->openingHours()->create([
                        'day_of_week' => $day,
                        'open_time' => $hours['open_time'],
                        'close_time' => $hours['close_time'],
                    ]);
                }
            }
        }


        // Je redirige vers la page du restaurant nouvellement créé avec un message de succès.
        return redirect()->route('restaurants.show', $restaurant)->with('success', 'Restaurant ajouté avec succès.');
    }

    /**
     * Affiche les détails d'un restaurant spécifique.
     * Charge aussi les horaires d'ouverture, les réservations, les avis (avec l'utilisateur) et les galeries associées pour les afficher.
     */
    public function show(Restaurant $restaurant): View
    {
        // 'load' permet de charger les relations pour éviter le problème N+1 requêtes dans la vue.
        $restaurant->load(['openingHours', 'reservations', 'reviews.user', 'galleries']);
        $averageRating = $restaurant->averageRating(); // Calculer la note moyenne
        $canReview = false; // Initialisation par défaut
        if (Auth::check()) {
            // Vérifie si l'utilisateur peut créer un avis pour ce restaurant.
            // La policy ReviewPolicy doit avoir une méthode 'create' qui accepte Restaurant en paramètre.
            $canReview = Auth::user()->can('create', [Review::class, $restaurant]);
        }
        return view('restaurants.show', compact('restaurant', 'averageRating', 'canReview'));
    }

    /**
     * Affiche le formulaire pour modifier un restaurant existant.
     */
    public function edit(Restaurant $restaurant): View
    {
        // Je vérifie si l'utilisateur a le droit de modifier CE restaurant spécifique.
        $this->authorize('update', $restaurant);
        // Charger explicitement la relation 'galleries' et 'openingHours' pour la vue d'édition
        $restaurant->load(['galleries', 'openingHours']);
        return view('restaurants.edit', compact('restaurant'));
    }

    /**
     * Met à jour un restaurant existant dans la base de données.
     */
    public function update(Request $request, Restaurant $restaurant): RedirectResponse
    {
        $this->authorize('update', $restaurant);

        // Je valide les données du formulaire.
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'delete_photos' => 'nullable|array',
            'delete_photos.*' => 'integer|exists:galleries,id', // Validation pour chaque ID de photo à supprimer
            'photos' => 'nullable|array',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'opening_hours' => 'nullable|array',
            'opening_hours.*.is_open' => 'sometimes|boolean',
            'opening_hours.*.open_time' => 'required_if:opening_hours.*.is_open,1|nullable|date_format:H:i',
            'opening_hours.*.close_time' => 'required_if:opening_hours.*.is_open,1|nullable|date_format:H:i|after:opening_hours.*.open_time',
        ]);

        // Je mets à jour le restaurant avec les nouvelles données.
        $restaurant->update($validatedData);

        // Suppression des photos sélectionnées
        if ($request->has('delete_photos')) {
            foreach ($request->input('delete_photos') as $photoId) {
                $galleryImage = Gallery::find($photoId);
                if ($galleryImage && $galleryImage->restaurant_id === $restaurant->id) { // Sécurité: vérifier que l'image appartient bien au restaurant
                    Storage::disk('public')->delete($galleryImage->photo_path);
                    $galleryImage->delete();
                }
            }
        }

        // Ajout des nouvelles photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photoFile) {
                $path = $photoFile->store('galleries/restaurant_' . $restaurant->id, 'public');
                $restaurant->galleries()->create(['photo_path' => $path]);
            }
        }

        // Mise à jour des horaires d'ouverture
        $restaurant->openingHours()->delete(); // Supprimer les horaires existants
        if ($request->has('opening_hours')) {
            foreach ($request->input('opening_hours') as $day => $hours) {
                if (isset($hours['is_open']) && !empty($hours['open_time']) && !empty($hours['close_time'])) {
                    $restaurant->openingHours()->create([
                        'day_of_week' => $day,
                        'open_time' => $hours['open_time'],
                        'close_time' => $hours['close_time'],
                    ]);
                }
            }
        }
        return redirect()->route('restaurants.show', $restaurant)->with('success', 'Restaurant mis à jour avec succès.');
    }

    /**
     * Supprime un restaurant de la base de données.
     */
    public function destroy(Restaurant $restaurant): RedirectResponse
    {
        $this->authorize('delete', $restaurant);

        // Optionnel: Avant de supprimer le restaurant, supprimer les images associées du stockage.
        // La suppression en cascade dans la base de données (onDelete('cascade') sur la clé étrangère dans la migration galleries)
        // s'occupera des enregistrements dans la table 'galleries', mais pas des fichiers physiques.
        // foreach ($restaurant->galleries as $galleryImage) {
        //     Storage::disk('public')->delete($galleryImage->photo_path);
        // }
        $restaurant->delete();

        // Je redirige vers la liste des restaurants avec un message de succès.
        return redirect()->route('restaurants.index')->with('success', 'Restaurant supprimé avec succès.');
    }

    /**
     * Fournit des données JSON pour le carrousel de la page d'accueil.
     * Récupère quelques restaurants aléatoires avec leur première image de galerie et un avis.
     */
    public function carouselData(): JsonResponse
    {
        // Je récupère jusqu'à 5 restaurants choisis aléatoirement
        // Je filtre uniquement ceux qui ont AU MOINS UNE IMAGE dans leur galerie (avis facultatif)
        $restaurants = Restaurant::with(['galleries', 'reviews.user'])
            ->whereHas('galleries') // Oblige la présence d'au moins une photo
            ->inRandomOrder()       // Mélange aléatoirement les résultats
            ->limit(7)              // Je limite à 7 restaurants pour ne pas surcharger le carrousel
            ->get();

        // Je prépare le format des données que je vais renvoyer en JSON
        $carouselData = $restaurants->map(function ($restaurant) {
            // Je récupère la première image du restaurant
            $firstGalleryImage = $restaurant->galleries->first();

            // Si jamais il n'y a pas d'image (théoriquement impossible ici vu le whereHas), je prévois quand même un fallback.
            $imageUrl = $firstGalleryImage
                ? asset('storage/' . $firstGalleryImage->photo_path)
                : asset('images/default-restaurant.png'); // Image par défaut (à mettre dans public/images si elle n'existe pas encore)

            // Je choisis un avis au hasard si le restaurant a des avis
            $randomReview = $restaurant->reviews->isNotEmpty() ? $restaurant->reviews->random() : null;

            return [
                'name' => $restaurant->name,
                'image_url' => $imageUrl,
                'review_comment' => $randomReview
                    ? \Illuminate\Support\Str::limit($randomReview->comment, 100) // Si avis : je limite la longueur à 100 caractères
                    : 'Soyez le premier à laisser un avis !',                      // Si pas d'avis : message par défaut
                'reviewer_name' => ($randomReview && $randomReview->user)
                    ? $randomReview->user->name
                    : '', // Pas de nom si pas d'avis ou utilisateur inconnu
                'show_url' => route('restaurants.show', $restaurant) // Lien direct vers la fiche restaurant
            ];
        });

        // Je renvoie la réponse sous forme de JSON
        return response()->json($carouselData);
    }

}
