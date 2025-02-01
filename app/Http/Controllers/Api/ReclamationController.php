<?php

namespace App\Http\Controllers\Api;

use App\Models\Reclamation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ReclamationController extends Controller
{
    // Récupérer toutes les réclamations
    public function index()
    {
        $reclamations = Reclamation::all();
        return response()->json($reclamations, 200);
    }

    // Récupérer une réclamation spécifique
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
