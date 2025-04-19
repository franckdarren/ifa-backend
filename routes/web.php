<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthenticatedSessionController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'Administrateur') {
            return view('dashboard');
        } else {
            // Déconnecter l'utilisateur non autorisé via le garde 'web'
            Auth::guard('web')->logout();

            // Rediriger l'utilisateur vers la page de connexion avec un message d'erreur
            return redirect('/login')->withErrors([
                'access' => 'Accès réservé aux administrateurs.'
            ]);
        }
    })->name('dashboard');

    Route::get('/articles', action: function () {
        return view('articles');
    })->name('articles');

    Route::get('/users', action: function () {
        return view('users');
    })->name('users');

    Route::get('/categories', action: function () {
        return view('categories');
    })->name('categories');

    Route::get('/sous-categories', action: function () {
        return view('sous_categories');
    })->name('sous-categories');

    Route::get('/commandes', action: function () {
        return view('commandes');
    })->name('commandes');

    Route::get('/livraisons', action: function () {
        return view('livraisons');
    })->name('livraisons');

    Route::get('/reclamations', action: function () {
        return view('reclamations');
    })->name('reclamations');

    Route::get('/publicites', action: function () {
        return view('publicites');
    })->name('publicites');
});