<?php

use App\Models\User;
use App\Models\Boutique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\BoutiqueController;
use App\Http\Controllers\Api\CommandeController;
use App\Http\Controllers\Api\CategorieController;
use App\Http\Controllers\Api\LivraisonController;
use App\Http\Controllers\Api\PubliciteController;
use App\Http\Controllers\Api\ReclamationController;
use App\Http\Controllers\Api\ImageArticleController;
use App\Http\Controllers\Api\SousCategorieController;
use App\Http\Controllers\Api\ArticleCommandeController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::post('/users', [UserController::class, 'store']);  // Créer un nouvel utilisateur

// Login with firebase
Route::post('/firebase-login', [AuthController::class, 'firebaseLogin']);

Route::middleware('auth:sanctum')->group(function () {
    // Actions utilisateurs
    Route::post('/logout', [AuthController::class, 'logout']);

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

    // Gestion Commandes
    Route::get('/commandes', [CommandeController::class, 'index']);
    Route::post('/commandes', [CommandeController::class, 'store']);
    Route::get('/commandes/{id}', [CommandeController::class, 'show']);
    // Route::put('/commandes/{id}', [CommandeController::class, 'update']);
    // Route::delete('/commandes/{id}', [CommandeController::class, 'destroy']);
    Route::patch('/commandes/{id}/statut', [CommandeController::class, 'updateStatus']);


    // Gestion des articles
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::post('/articles', [ArticleController::class, 'store']);
    Route::get('/articles/{id}', [ArticleController::class, 'show']);
    Route::put('/articles/{id}', [ArticleController::class, 'update']);
    Route::delete('/articles/{id}', [ArticleController::class, 'destroy']);
    Route::get('articles-disponibles', [ArticleController::class, 'articlesDisponibles']);
    Route::get('articles/boutique/{id}', [ArticleController::class, 'articlesBoutique']);



    // Gestion des article_commandes
    // Route::post('/commandes/{commande_id}/articles', [ArticleCommandeController::class, 'attachArticle']);
    // Route::delete('/commandes/{commande_id}/articles/{article_id}', [ArticleCommandeController::class, 'detachArticle']);

    // Gestion image articles
    Route::prefix('articles/{id}/images')->group(function () {
        Route::get('/', [ImageArticleController::class, 'index']); // Récupérer toutes les images d'un article
        Route::post('/', [ImageArticleController::class, 'store']); // Ajouter une nouvelle image pour un article
    });
    Route::prefix('image-articles')->group(function () {
        Route::put('/{id}', [ImageArticleController::class, 'update']); // Mettre à jour une image
        Route::delete('/{id}', [ImageArticleController::class, 'destroy']); // Supprimer une image
        Route::get('/{id}', [ImageArticleController::class, 'show']);
    });

    // Gestion des livraisons
    Route::get('livraisons', [LivraisonController::class, 'index']);
    Route::get('livraisons/{id}', [LivraisonController::class, 'show']);
    Route::post('livraisons', [LivraisonController::class, 'store']);
    Route::put('livraisons/{id}', [LivraisonController::class, 'update']);
    Route::delete('livraisons/{id}', [LivraisonController::class, 'destroy']);

    // Gestion des reclamations
    Route::get('reclamations', [ReclamationController::class, 'index']);
    Route::get('reclamations/{id}', [ReclamationController::class, 'show']);
    Route::post('reclamations', [ReclamationController::class, 'store']);
    Route::put('reclamations/{id}', [ReclamationController::class, 'update']);
    Route::delete('reclamations/{id}', [ReclamationController::class, 'destroy']);

    // Reports
    Route::get('dashboard/stats/{userId}', [ReportController::class, 'dashboardStats']);

});
