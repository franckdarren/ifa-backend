<?php

namespace App\Http\Controllers\Api;

use App\Models\Reclamation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ReclamationController extends Controller
{
    // Récupérer toutes les réclamations
    /**
     * @OA\Get(
     *     path="/api/reclamations",
     *     summary="Lister toutes les réclamations",
     *     tags={"Réclamations"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des réclamations",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Reclamation")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $reclamations = Reclamation::all();
        return response()->json($reclamations, 200);
    }

    // Récupérer une réclamation spécifique
    /**
     * @OA\Get(
     *     path="/api/reclamations/{id}",
     *     summary="Afficher une réclamation spécifique",
     *     description="Récupère une réclamation en fonction de son ID",
     *     operationId="getReclamationById",
     *     tags={"Réclamations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la réclamation",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Réclamation trouvée",
     *         @OA\JsonContent(ref="#/components/schemas/Reclamation")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Réclamation non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Réclamation non trouvée")
     *         )
     *     )
     * )
     */

    public function show($id)
    {
        $reclamation = Reclamation::find($id);
        if (!$reclamation) {
            return response()->json(['message' => 'Réclamation non trouvée'], 404);
        }
        return response()->json($reclamation, 200);
    }

    // Ajouter une nouvelle réclamation
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
            'phone' => 'required|string',
            'statut' => 'required|in:En attente de traitement,En cours,Rejetée,Remboursée',
            'commande_id' => 'required|exists:commandes,id',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $reclamation = Reclamation::create([
            'description' => $request->description,
            'phone' => $request->phone,
            'statut' => $request->statut,
            'commande_id' => $request->commande_id,
            'user_id' => $request->user_id,
        ]);

        return response()->json($reclamation, 201);
    }

    // Mettre à jour une réclamation
    /**
     * @OA\Post(
     *     path="/api/reclamations",
     *     summary="Créer une nouvelle réclamation",
     *     description="Permet de créer une réclamation associée à une commande et un utilisateur",
     *     operationId="storeReclamation",
     *     tags={"Réclamations"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"description", "phone", "statut", "commande_id", "user_id"},
     *             @OA\Property(property="description", type="string", example="Produit non conforme à la commande."),
     *             @OA\Property(property="phone", type="string", example="061234567"),
     *             @OA\Property(
     *                 property="statut",
     *                 type="string",
     *                 enum={"En attente de traitement", "En cours", "Rejetée", "Remboursée"},
     *                 example="En attente de traitement"
     *             ),
     *             @OA\Property(property="commande_id", type="integer", example=12),
     *             @OA\Property(property="user_id", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Réclamation créée avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Reclamation")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur de validation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={"description": {"Le champ description est requis."}}
     *             )
     *         )
     *     )
     * )
     */

    public function update(Request $request, $id)
    {
        $reclamation = Reclamation::find($id);
        if (!$reclamation) {
            return response()->json(['message' => 'Réclamation non trouvée'], 404);
        }

        $validator = Validator::make($request->all(), [
            'description' => 'string',
            'phone' => 'string',
            'statut' => 'in:En attente de traitement,En cours,Rejetée,Remboursée',
            'commande_id' => 'exists:commandes,id',
            'user_id' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $reclamation->update($request->all());
        return response()->json($reclamation, 200);
    }

    // Supprimer une réclamation
    /**
     * @OA\Delete(
     *     path="/api/reclamations/{id}",
     *     summary="Supprimer une réclamation",
     *     description="Supprime une réclamation en fonction de son ID",
     *     operationId="deleteReclamation",
     *     tags={"Réclamations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la réclamation à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Réclamation supprimée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Réclamation supprimée avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Réclamation non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Réclamation non trouvée")
     *         )
     *     )
     * )
     */

    public function destroy($id)
    {
        $reclamation = Reclamation::find($id);
        if (!$reclamation) {
            return response()->json(['message' => 'Réclamation non trouvée'], 404);
        }

        $reclamation->delete();
        return response()->json(['message' => 'Réclamation supprimée avec succès'], 200);
    }
}
