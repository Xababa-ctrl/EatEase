<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\RestaurateurDashboardController;
use App\Http\Middleware\CheckRestaurateurRole;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminRestaurantController;
use App\Http\Controllers\Admin\AdminReservationController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminReviewController;

// --- Routes Publiques ---
// Ces routes sont accessibles à tous les visiteurs, connectés ou non.

// Page d'accueil
Route::get('/', [HomeController::class, 'index'])->name('home');

// Page de contact
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

// Recherche AJAX pour restaurants
Route::get('/search-restaurants', [RestaurantController::class, 'search'])->name('restaurants.search');

// Données du carrousel
Route::get('/carousel-data', [RestaurantController::class, 'carouselData'])->name('restaurants.carouselData');

// --- Gestion des Restaurants (CRUD Public) ---
Route::resource('restaurants', RestaurantController::class);

// --- Gestion des Réservations Publiques ---
// Réservations accessibles uniquement aux utilisateurs connectés
Route::middleware('auth')->group(function () {
    Route::post('/restaurants/{restaurant}/reservations', [ReservationController::class, 'store'])->name('reservations.store');
});

// --- Gestion des Avis ---
Route::post('/restaurants/{restaurant}/reviews', [ReviewController::class, 'store'])
    ->middleware(['auth'])
    ->name('reviews.store');

// --- Authentification Utilisateurs (Clients) ---
Route::get('/register', [RegisteredUserController::class, 'create'])->middleware('guest')->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('guest');

Route::get('/login', [AuthenticatedSessionController::class, 'create'])->middleware('guest')->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('guest');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

// --- Espace Restaurateur ---
Route::middleware(['auth', CheckRestaurateurRole::class])->prefix('restaurateur')->name('restaurateur.')->group(function () {
    Route::get('/dashboard', [RestaurateurDashboardController::class, 'index'])->name('dashboard');
    Route::get('restaurants', [RestaurateurDashboardController::class, 'listRestaurants'])->name('restaurants.list');
    Route::get('/reservations', [RestaurateurDashboardController::class, 'reservations'])->name('reservations');
    Route::patch('/reservations/{reservation}/confirm', [RestaurateurDashboardController::class, 'confirmReservation'])->name('reservations.confirm');
    Route::patch('/reservations/{reservation}/reject', [RestaurateurDashboardController::class, 'rejectReservation'])->name('reservations.reject');
});

// --- Authentification Administrateur ---
Route::get('admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// --- Espace Admin ---
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    // Tableau de bord
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Gestion des restaurants
    Route::resource('restaurants', AdminRestaurantController::class);

    // Gestion des réservations (sans create, store, show)
    Route::resource('reservations', AdminReservationController::class)->except(['create', 'store', 'show']);

    // Gestion des utilisateurs
    Route::resource('users', AdminUserController::class);

    // Modération des avis clients
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
});
