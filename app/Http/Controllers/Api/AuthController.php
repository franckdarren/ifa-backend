<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Si authentifié, récupère l'utilisateur
        $user = Auth::user();

        // Crée un token pour cet utilisateur
        $token = $user->createToken('auth_token')->plainTextToken;

        // Renvoie le token dans la réponse
        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
        ]);
    }
}
