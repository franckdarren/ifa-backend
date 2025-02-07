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
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'role' => 'required|in:Administrateur,Client,Boutique',
                'password' => 'required|string|min:6',
            ]);

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'role' => $validatedData['role'],
                'password' => bcrypt($validatedData['password']),
            ]);

            return response()->json($user->makeHidden(['password']), 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Impossible de créer l\'utilisateur'], 500);
        }
    }


    /**
     * Display the specified resource.
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


    public function boutique(string $id)
    {
        //Supprimer un user
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        $boutique = $user->boutique();

        return response()->json($boutique, 200);
    }


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
