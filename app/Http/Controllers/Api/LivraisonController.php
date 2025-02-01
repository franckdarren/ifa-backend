<?php

namespace App\Http\Controllers\Api;

use App\Models\Livraison;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LivraisonController extends Controller
{
    // Récupérer toutes les livraisons
    public function index()
    {
        $livraisons = Livraison::all();
        return response()->json($livraisons, 200);
    }

    // Récupérer une livraison spécifique
    public function show($id)
    {
        $livraison = Livraison::find($id);
        if (!$livraison) {
            return response()->json(['message' => 'Livraison non trouvée'], 404);
        }
        return response()->json($livraison, 200);
    }

    // Ajouter une nouvelle livraison
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'adresse' => 'required|string',
            'details' => 'required|string',
            'statut' => 'required|string',
            'date_livraison' => 'required|string',
            'ville' => 'required|string',
            'phone' => 'required|string',
            'commande_id' => 'required|exists:commandes,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $livraison = Livraison::create([
            'adresse' => $request->adresse,
            'details' => $request->details,
            'statut' => $request->statut,
            'date_livraison' => $request->date_livraison,
            'ville' => $request->ville,
            'phone' => $request->phone,
            'commande_id' => $request->commande_id,
            'user_id' => $request->user_id,
        ]);

        return response()->json($livraison, 201);
    }

    // Mettre à jour une livraison
    public function update(Request $request, $id)
    {
        $livraison = Livraison::find($id);
        if (!$livraison) {
            return response()->json(['message' => 'Livraison non trouvée'], 404);
        }

        $validator = Validator::make($request->all(), [
            'adresse' => 'string',
            'details' => 'string',
            'statut' => 'string',
            'date_livraison' => 'string',
            'ville' => 'string',
            'phone' => 'string',
            'commande_id' => 'exists:commandes,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $livraison->update($request->all());
        return response()->json($livraison, 200);
    }

    // Supprimer une livraison
    public function destroy($id)
    {
        $livraison = Livraison::find($id);
        if (!$livraison) {
            return response()->json(['message' => 'Livraison non trouvée'], 404);
        }

        $livraison->delete();
        return response()->json(['message' => 'Livraison supprimée avec succès'], 200);
    }
}
