<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse; // Pour pouvoir faire des redirections après une action
// use App\Models\OpeningHour; // Commenté car non directement instancié, mais contextuellement lié.
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReservationController extends Controller
{
    /**
     * Affiche la liste de TOUTES les réservations (plutôt pour un admin).
     * Pour un utilisateur normal ou un restaurateur, on filtrerait ça.
     */
    public function index(): View
    {
        // Je récupère toutes les réservations, avec les infos du restaurant associé pour éviter des requêtes en plus dans la vue.
        // Et je les trie par date de création, les plus récentes d'abord.
        $reservations = Reservation::with('restaurant')->latest()->get();
        return view('reservations.index', compact('reservations'));
    }

    /**
     * Affiche le formulaire de création d'une réservation (plutôt pour un admin).
     * Un client ferait une réservation depuis la page d'un restaurant spécifique.
     */
    public function create(): View
    {
        // Je récupère la liste des restaurants pour les afficher dans un menu déroulant.
        $restaurants = Restaurant::orderBy('name')->pluck('name', 'id');
        return view('reservations.create', compact('restaurants'));
    }

    /**
     * Affiche les détails d'une réservation spécifique.
     */
    public function show(Reservation $reservation): View
    {
        // Je m'assure de charger les infos du restaurant associé à cette réservation.
        $reservation->load('restaurant');
        return view('reservations.show', compact('reservation'));
    }

    /**
     * Affiche le formulaire de modification d'une réservation (plutôt pour un admin).
     */
    public function edit(Reservation $reservation): View
    {
        $restaurants = Restaurant::orderBy('name')->pluck('name', 'id');
        return view('reservations.edit', compact('reservation', 'restaurants'));
    }

    /**
     * Enregistre les modifications d'une réservation (plutôt pour un admin).
     */
    public function update(Request $request, Reservation $reservation): RedirectResponse
    {
        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required|date_format:H:i',
            'number_of_guests' => 'required|integer|min:1', // 'party_size' est utilisé dans store, cohérence ?
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
        ]);

        // Je mets à jour la réservation avec les nouvelles données validées.
        $reservation->update($validated);

        // Je redirige vers la page de détails de la réservation avec un message de succès.
        return redirect()->route('reservations.show', $reservation->id)
                         ->with('success', 'Réservation mise à jour avec succès.');
    }

    /**
     * Supprime une réservation (plutôt pour un admin).
     * Un utilisateur ou un restaurateur aurait une logique d'annulation différente.
     */
    public function destroy(Reservation $reservation): RedirectResponse
    {
        $reservation->delete();

        // Je redirige vers la liste des réservations avec un message de succès.
        return redirect()->route('reservations.index')
                         ->with('success', 'Réservation annulée avec succès.');
    }

    /**
     * Enregistre une NOUVELLE réservation, typiquement depuis la page d'un restaurant.
     */
    public function store(Request $request, Restaurant $restaurant)
    {
        $validationRules = [
            'reservation_date' => ['required', 'date', 'after_or_equal:today'],
            'reservation_time' => ['required', 'date_format:H:i'],
            'party_size' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        // Si l'utilisateur n'est pas connecté, je dois lui demander son nom, email et téléphone.
        if (!Auth::check()) {
            $validationRules['customer_name'] = ['required', 'string', 'max:255'];
            $validationRules['customer_email'] = ['required', 'email', 'max:255'];
            $validationRules['customer_phone'] = ['required', 'string', 'max:20'];
        }

        $validator = Validator::make($request->all(), $validationRules);

        // Ma super validation personnalisée pour vérifier les horaires d'ouverture !
        $validator->after(function ($validator) use ($request, $restaurant) {
            // Je combine la date et l'heure demandées pour avoir un objet Carbon (plus facile à manipuler).
            $requestedDateTime = Carbon::parse($request->input('reservation_date') . ' ' . $request->input('reservation_time'));

            // Si la date/heure demandée est déjà passée, c'est pas bon.
            if ($requestedDateTime->isPast()) {
                $validator->errors()->add(
                    'reservation_time', 'L\'heure de réservation doit être dans le futur.'
                );
                return; // Pas la peine de vérifier plus loin si c'est déjà dans le passé.
            }

            // Je récupère le jour de la semaine (1 pour Lundi, ..., 7 pour Dimanche) et l'heure.
            $dayOfWeek = $requestedDateTime->dayOfWeekIso; // Lundi = 1, Dimanche = 7
            $requestedTime = $requestedDateTime->format('H:i:s');

            // Je vais chercher les horaires d'ouverture du restaurant pour CE jour de la semaine.
            $openingHours = $restaurant->openingHours()
                                      ->where('day_of_week', $dayOfWeek)
                                      ->get();

            $isOpen = false;
            if ($openingHours->isNotEmpty()) {
                foreach ($openingHours as $hours) {
                    // Cas spécial : si l'heure de fermeture est "avant" l'heure d'ouverture (ex: 22:00 - 02:00)
                    // Ça veut dire que ça ferme après minuit, le lendemain.
                    if ($hours->close_time < $hours->open_time) {
                        // Donc, c'est ouvert si l'heure demandée est après l'ouverture OU avant la fermeture (du "lendemain").
                        if ($requestedTime >= $hours->open_time || $requestedTime <= $hours->close_time) {
                            $isOpen = true;
                            break;
                        }
                    } else {
                        // Cas normal : l'heure demandée doit être entre l'ouverture et la fermeture.
                        if ($requestedTime >= $hours->open_time && $requestedTime <= $hours->close_time) {
                            $isOpen = true;
                            break;
                        }
                    }
                }
            }

            if (!$isOpen) {
                $validator->errors()->add(
                    'reservation_time', 'Le restaurant semble fermé à l\'heure et au jour demandés. Veuillez vérifier les horaires.'
                );
            }
        });

        // Si ma validation (y compris celle des horaires) échoue...
        if ($validator->fails()) {
            // ...je redirige l'utilisateur vers la page précédente avec les erreurs et les données qu'il avait déjà entrées.
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $validatedData = $validator->validated();

        // Je prépare les données pour créer la réservation.
        $reservationData = [
            'restaurant_id' => $restaurant->id,
            'reservation_time' => Carbon::parse($validatedData['reservation_date'] . ' ' . $validatedData['reservation_time']),
            'party_size' => $validatedData['party_size'],
            'notes' => $validatedData['notes'] ?? null,
            'status' => 'pending', // Nouvelle réservation = en attente de confirmation.
        ];

        // Si l'utilisateur est connecté, j'enregistre son ID.
        if (Auth::check()) {
            $reservationData['user_id'] = Auth::id();
        // Sinon, j'enregistre les infos qu'il a fournies.
        } else {
            $reservationData['customer_name'] = $validatedData['customer_name'];
            $reservationData['customer_email'] = $validatedData['customer_email'];
            $reservationData['customer_phone'] = $validatedData['customer_phone'];
        }

        try {
            // J'essaie de créer la réservation dans la base de données.
            Reservation::create($reservationData);
            // Si ça marche, je redirige vers la page du restaurant avec un message de succès.
            return redirect()->route('restaurants.show', $restaurant)
                        ->with('success', 'Votre demande de réservation a été envoyée.');
        } catch (\Exception $e) {
             // Si quelque chose se passe mal pendant la création...
             Log::error("Erreur lors de la création de la réservation: " . $e->getMessage());
             // ...je redirige vers la page précédente avec un message d'erreur générique.
             return redirect()->back()
                        ->with('error', 'Une erreur est survenue lors de la création de votre réservation.')
                        ->withInput();
        }
    }
}
