<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Auth as FirebaseAuth;


class AuthController extends Controller
{
    // Fonction pour gérer la connexion et renvoyer le token
    public function login(Request $request)
    {
        // Valider les champs envoyés dans la requête
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Tentative d'authentification
        if (!Auth::attempt($validatedData)) {
            return response()->json(['message' => 'Email ou mot de passe incorrect'], 401);
        }

        // Si authentifié, récupère l'utilisateur
        $user = Auth::user()->load('boutique');

        // Crée un token pour cet utilisateur
        $token = $user->createToken('auth_token')->plainTextToken;

        // Renvoie le token dans la réponse
        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function firebaseLogin(Request $request)
    {
        $firebaseAuth = app(FirebaseAuth::class);
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token Firebase manquant'], 401);
        }

        try {
            $verifiedIdToken = $firebaseAuth->verifyIdToken($token);
            $firebaseUser = $firebaseAuth->getUser($verifiedIdToken->claims()->get('sub'));

            // Vérifier si l'utilisateur existe déjà
            $user = User::where('firebase_uid', $firebaseUser->uid)->orWhere('email', $firebaseUser->email)->first();

            if (!$user) {
                // Si l'utilisateur n'existe pas, le créer
                $user = User::create([
                    'name' => $firebaseUser->displayName,
                    'email' => $firebaseUser->email,
                    'role' => 'Boutique',
                    'firebase_uid' => $firebaseUser->uid,
                    'password' => null, // Pas de mot de passe pour les utilisateurs Firebase
                ]);
            }

            // Générer un token Sanctum
            $sanctumToken = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Utilisateur authentifié',
                'user' => $user,
                'token' => $sanctumToken,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token Firebase invalide'], 401);
        }
    }
}