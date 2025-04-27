<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Auth as FirebaseAuth;


class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Connexion avec email et mot de passe",
     *     tags={"Authentification"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="test@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="token", type="string", example="sanctum-token-123456"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Échec d'authentification",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Email ou mot de passe incorrect")
     *         )
     *     )
     * )
     */
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
        $user = Auth::user();

        // Crée un token pour cet utilisateur
        $token = $user->createToken('auth_token')->plainTextToken;

        // Renvoie le token dans la réponse
        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/firebase-login",
     *     summary="Connexion avec Firebase",
     *     description="Connecte un utilisateur via Firebase, crée l'utilisateur s'il n'existe pas et génère un token Sanctum.",
     *     operationId="firebaseLogin",
     *     tags={"Authentification"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="role", type="string", example="Client", description="Rôle de l'utilisateur (Client, Boutique, etc.)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Utilisateur authentifié"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *                 @OA\Property(property="role", type="string", example="Client"),
     *                 @OA\Property(property="firebase_uid", type="string", example="firebase_uid_123")
     *             ),
     *             @OA\Property(property="token", type="string", example="1|sometokenstring")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token Firebase invalide ou manquant",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Token Firebase manquant")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur interne",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Erreur interne")
     *         )
     *     )
     * )
     */

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
                    'role' => $request->boutique,
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
