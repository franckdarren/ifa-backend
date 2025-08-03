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
     *     summary="Connexion avec Firebase UID",
     *     description="Connexion d'un utilisateur via Firebase UID. Si l'utilisateur n'existe pas, il est créé.",
     *     operationId="firebaseLogin",
     *     tags={"Authentification"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"firebase_uid", "email", "name", "role"},
     *             @OA\Property(property="firebase_uid", type="string", example="abc123UIDFirebase"),
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="role", type="string", enum={"Client", "Boutique", "Livreur"}, example="Client"),
     *             @OA\Property(property="password", type="string", nullable=true, example="secret")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur authentifié avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Utilisateur authentifié"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *                 @OA\Property(property="role", type="string", example="Client"),
     *                 @OA\Property(property="firebase_uid", type="string", example="abc123UIDFirebase")
     *             ),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOi...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Données invalides",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Données incomplètes ou rôle invalide")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Erreur d'authentification",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Authentification échouée")
     *         )
     *     )
     * )
     */
    public function firebaseLogin(Request $request)
    {
        $allowedRoles = ['Client', 'Boutique', 'Livreur'];

        $role = $request->input('role');
        $firebaseUid = $request->input('firebase_uid');
        $email = $request->input('email');
        $name = $request->input('name');
        // $password = $request->input('password');


        if (!in_array($role, $allowedRoles)) {
            return response()->json(['error' => 'Rôle invalide'], 422);
        }

        if (!$firebaseUid || !$email || !$name) {
            return response()->json(['error' => 'Données incomplètes'], 422);
        }

        // Rechercher l'utilisateur par UID ou par email
        $user = User::where('firebase_uid', $firebaseUid)
            ->orWhere('email', $email)
            ->first();

        if (!$user) {
            // Créer un nouvel utilisateur s'il n'existe pas
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'firebase_uid' => $firebaseUid,
                // 'password' => $password, // Pas de mot de passe
            ]);
        }

        // Générer un token Sanctum
        $sanctumToken = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Utilisateur authentifié',
            'user' => $user,
            'token' => $sanctumToken,
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Déconnexion",
     *     tags={"Authentification"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Déconnexion réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Déconnexion réussie")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        // Supprimer le token actuel utilisé pour l'authentification
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie',
        ]);
    }

}
