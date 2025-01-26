<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CategorieController;
use App\Http\Controllers\Api\PubliciteController;
use App\Http\Controllers\Api\SousCategorieController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/users', [UserController::class, 'store']);  // Créer un nouvel utilisateur

Route::middleware('auth:sanctum')->group(function () {
    // Actions utilisateurs
    Route::get('/users', [UserController::class, 'index']);   // Liste tous les utilisateurs
    Route::get('/users/{id}', [UserController::class, 'show']); // Afficher un utilisateur spécifique
    Route::put('/users/{id}', [UserController::class, 'update']); // Mettre à jour un utilisateur
    Route::delete('/users/{id}', [UserController::class, 'destroy']); // Supprimer un utilisateur

    Route::get('/users/{id}/boutique', [UserController::class, 'boutique']); // Liste la boutique
    Route::get('/users/{id}/commandes', [UserController::class, 'commandes']); // Liste les commandes
    Route::get('/users/{id}/livraisons', [UserController::class, 'livraisons']); // Liste les livraisons
    Route::get('/users/{id}/reclamations', [UserController::class, 'reclamations']); // Liste les reclamations

    // Gestion Publicités
    Route::prefix('publicites')->group(function () {
        Route::get('/', [PubliciteController::class, 'index']); // Récupérer toutes les publicités
        Route::get('{id}', [PubliciteController::class, 'show']); // Récupérer une publicité par son ID
        Route::post('/', [PubliciteController::class, 'store']); // Créer une nouvelle publicité
        Route::put('{id}', [PubliciteController::class, 'update']); // Mettre à jour une publicité
        Route::delete('{id}', [PubliciteController::class, 'destroy']); // Supprimer une publicité
    });

    // Gestion des Catégories
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategorieController::class, 'index']); // Récupérer toutes les catégories
        Route::get('{id}', [CategorieController::class, 'show']); // Récupérer une catégorie par son ID
        Route::post('/', [CategorieController::class, 'store']); // Créer une nouvelle catégorie
        Route::put('{id}', [CategorieController::class, 'update']); // Mettre à jour une catégorie
        Route::delete('{id}', [CategorieController::class, 'destroy']); // Supprimer une catégorie
    });

    // Gestion des Sous Catégories
    Route::prefix('sous-categories')->group(function () {
        Route::get('/', [SousCategorieController::class, 'index']); // Récupérer toutes les sous-catégories
        Route::get('{id}', [SousCategorieController::class, 'show']); // Récupérer une sous-catégorie par ID
        Route::post('/', [SousCategorieController::class, 'store']); // Créer une nouvelle sous-catégorie
        Route::put('{id}', [SousCategorieController::class, 'update']); // Mettre à jour une sous-catégorie
        Route::delete('{id}', [SousCategorieController::class, 'destroy']); // Supprimer une sous-catégorie
    });


});