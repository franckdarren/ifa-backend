<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Liste tous les utilisateurs",
     *     tags={"Utilisateurs"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des utilisateurs"
     *     )
     * )
     */
    public function index()
    {
        //Lister tous les users
        try {
            return response()->json(User::all(), 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Créer un nouvel utilisateur",
     *     tags={"Utilisateurs"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","role","password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="role", type="string", example="Boutique"),
     *             @OA\Property(property="password", type="string", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Utilisateur créé"
     *     ),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'role' => 'required|in:Administrateur,Client,Boutique,Livreur',
                'password' => 'nullable|string|min:6',
                'phone' => 'nullable|string|max:20',
                'url_logo' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:1000',
                'firebase_uid' => 'nullable|string|max:255',
                // 'abonnement' => 'nullable|string|max:255',
                'heure_ouverture' => 'nullable|string|max:255',
                'heure_fermeture' => 'nullable|string|max:255',
            ]);

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'role' => $validatedData['role'],
                'password' => isset($validatedData['password'])
                    ? bcrypt($validatedData['password'])
                    : null,
                'phone' => $validatedData['phone'] ?? null,
                'url_logo' => $validatedData['url_logo'] ?? null,
                'description' => $validatedData['description'] ?? null,
                'firebase_uid' => $validatedData['firebase_uid'] ?? null,
                // 'abonnement' => $validatedData['abonnement'] ?? 'Simple',
                'heure_ouverture' => $validatedData['heure_ouverture'] ?? null,
                'heure_fermeture' => $validatedData['heure_fermeture'] ?? null,


            ]);

            return response()->json($user->makeHidden(['password']), 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Impossible de créer l\'utilisateur', 'message' => $e->getMessage()], 500);
        }
    }



    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Afficher un utilisateur",
     *     tags={"Utilisateurs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Utilisateur trouvé"),
     *     @OA\Response(response=404, description="Utilisateur non trouvé")
     * )
     */
    public function show(string $id)
    {
        // Afficher un utilisateur spécifique
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        return response()->json($user, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Mettre à jour un utilisateur",
     *     tags={"Utilisateurs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Updated"),
     *             @OA\Property(property="password", type="string", example="newpass123")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Utilisateur mis à jour"),
     *     @OA\Response(response=404, description="Utilisateur non trouvé")
     * )
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            $validatedData = $request->validate([
                'name' => 'string|max:255',
                'password' => 'nullable|string|min:6',
            ]);

            $user->update($validatedData);
            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
                $user->save();
            }
            return response()->json($user, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Utilisateur non trouvé'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Supprimer un utilisateur",
     *     tags={"Utilisateurs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Utilisateur supprimé"),
     *     @OA\Response(response=404, description="Utilisateur non trouvé")
     * )
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['message' => 'Utilisateur supprimé avec succès'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Utilisateur non trouvé'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/users/{id}/livraisons",
     *     summary="Afficher les livraisons de l'utilisateur",
     *     tags={"Utilisateurs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Liste des livraisons"),
     *     @OA\Response(response=404, description="Utilisateur non trouvé")
     * )
     */
    public function livraisons(string $id)
    {
        //Supprimer un user
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        $livraisons = $user->livraisons();

        return response()->json($livraisons, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}/reclamations",
     *     summary="Afficher les réclamations de l'utilisateur",
     *     tags={"Utilisateurs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Liste des réclamations"),
     *     @OA\Response(response=404, description="Utilisateur non trouvé")
     * )
     */
    public function reclamations(string $id)
    {
        //Supprimer un user
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        $reclamations = $user->reclamations();

        return response()->json($reclamations, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}/commandes",
     *     summary="Afficher les commandes de l'utilisateur",
     *     tags={"Utilisateurs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Liste des commandes"),
     *     @OA\Response(response=404, description="Utilisateur non trouvé")
     * )
     */
    public function commandes(string $id)
    {
        //Supprimer un user
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        $commandes = $user->commandes();

        return response()->json($commandes, 200);
    }




}